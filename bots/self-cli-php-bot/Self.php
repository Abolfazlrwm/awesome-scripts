<?php declare(strict_types=1);

//=-=-=-=-=-=-=-=-=-=-=-=[" Self-Cli "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v189
 * Robot  : Self
 * Date   : 2023/05/10
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" Start "]=-=-=-=-=-=-=-=-=-=-=-=

namespace Madeline\Mahdi;

use danog\MadelineProto\{Logger, ParseMode, Settings, SimpleEventHandler};
use danog\MadelineProto\EventHandler\SimpleFilter\{IsNotEdited,Outgoing};
use danog\MadelineProto\EventHandler\Message\{PrivateMessage, GroupMessage};
use danog\MadelineProto\EventHandler\Attributes\{Handler,Cron};
use danog\Loop\GenericLoop;
use danog\MadelineProto\EventHandler\Message;
use danog\MadelineProto\RPCError\FloodWaitError as FloodError;
use Throwable;
use function Amp\File\deleteFile;

if (!is_file('madeline.php')) {
copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

require_once 'madeline.php';

date_default_timezone_set('Asia/Tehran');


class MyEventHandler extends SimpleEventHandler
{

const ADMIN = "@Mahdi_a_8"; // !!! Change this to your username !!

public array $Information = array("Prof" => array("LastName" => [], "About" => []));
public array $TimeJson = array("LastName" => "", "About" => "", "Time" => []);
public array $Enemies = [];
public array $Answering = [];
public array $Data = array("OptHashtag"=>false,"OptBold"=>false,"OptUnderline"=>false,"OptCoding"=>false,"OptMention"=>false,"OptMention2"=>false,"OptPart"=>false,"OptReverse"=>false,"OptItalic"=>false,"OptDelete"=>false,"OptFontPersian"=>false,"OptFontEnghlish"=>false,"OptPoker"=>false,"ActionTyping"=>false,"ActionGame"=>false,"ActionVoice"=>false,"ActionVideo"=>false,"AutoTimeName"=>false,"AutoTimeAbout"=>false);

public function __sleep(): array
{
return ['Information', 'TimeJson' ,'Enemies', 'Answering','Data'];
}

public function Seter(string $Index, bool $Bet): void
{
$this->Data[$Index] = $Bet;
}

public function Seter2(string $Index, bool $Bet): void
{
$this->TimeJson[$Index] = $Bet;
}

public function genLoop()
{
$FontTime = [['𝟎', '𝟏', '𝟐', '𝟑', '𝟒', '𝟓', '𝟔', '𝟕', '𝟖', '𝟗'],
["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"],
["⁰", "¹", "²", "³", "⁴", "⁵", "⁶", "⁷", "⁸", "⁹"],
["0⃣", "1⃣", "2⃣", "3⃣", "4⃣", "5⃣", "6⃣", "7⃣", "8⃣", "9⃣"],
["⊘", "҉1", "҉2", "҉3", "҉4", "҉5", "҉6", "҉7", "҉8", "9҉"],
["𝟬", "𝟭", "𝟐", "❬3❭", "𝟜", "5", "❬𝟔❭", "𝟽", "𝟴", "❬𝟗❭"],
["⁰", "¹", "²", "³", "⁴", "⁵", "⁶", "⁷", "⁸", "⁹"],
["⊘", "𝟏", "ᄅ", "Ɛ", "ㄣ", "𝟝", "６", "ㄥ", "８", "𝟗"],
["𝟎", "𝟙", "𝟸", "𝟑", "𝟜", "𝟻", "𝟔", "𝟟", "𝟾", "𝟗"],
["❬𝟎❭", "❬𝟏❭", "❬𝟐❭", "❬𝟑❭", "❬𝟒❭", "❬𝟓❭", "❬𝟔❭", "❬𝟕❭", "❬𝟖❭", "❬𝟗❭"],
["⓪", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨"],
["𝟘", "𝟙", "𝟚", "𝟛", "𝟜", "𝟝", "𝟞", "𝟟", "𝟠", "𝟡"],
["𝟶", "𝟷", "𝟸", "𝟹", "𝟺", "𝟻", "𝟼", "𝟽", "𝟾", "𝟿"]];
$Data = $this->Data;
$Information = $this->Information;
$TimeJson = $this->TimeJson;
$Font = '';
$LastName = '';
$LastName2 = '';
$Bio = '';
$Bio2 = '';
$timee = date('H:i');
if(!empty($TimeJson['LastName'])){
$LastName = $TimeJson['LastName'];
$Bio      = $TimeJson['About'];
$Font     = $TimeJson['Time'];
}
if(!empty($Information['Prof']['LastName'])){
$LastName2 = $Information['Prof']['LastName'];
$Bio2      = $Information['Prof']['About'];
}
if($Data['AutoTimeName'] and !empty($TimeJson['LastName'])){
$Time = str_replace(range(0, 9), $Font[array_rand($Font)], $timee);
$this->account->updateProfile(last_name: "$LastName $Time");
}
elseif($Data['AutoTimeName'] and empty($TimeJson['LastName'])){
$Time = str_replace(range(0, 9), $FontTime[array_rand($FontTime)], $timee);
$LastName2 = $LastName2[array_rand($LastName2)];
$this->account->updateProfile(last_name: "$LastName2 $Time");
}
if($Data['AutoTimeAbout'] and !empty($TimeJson['LastName'])){
$Time = str_replace(range(0, 9), $Font[array_rand($Font)], $timee);
$this->account->updateProfile(about: "$Bio - $Time");
}
elseif($Data['AutoTimeAbout'] and empty($TimeJson['LastName'])){
$Time = str_replace(range(0, 9), $FontTime[array_rand($FontTime)], $timee);
$Bio2 = $Bio2[array_rand($Bio2)];
$this->account->updateProfile(about: "$Bio2 $Time");
}
return 60;
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
public function HandleMessage(Outgoing&Message&IsNotEdited $Message): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text         = $Message->message;
$MsgID        = $Message->id;
$isOut        = $Message->out;
$UserID       = $Message->senderId;
$replyTo      = $Message->replyToMsgId;
$ChatID       = $Message->chatId;
$Information  = $this->Information;
$Enemies      = $this->Enemies;
$Answering    = $this->Answering;
$Data         = $this->Data;
$Spam         = ["کیرم کون مادرت😂😂😂😂", "بالا باش کیرم کص مادرت😂😂😂", "مادرتو میگام نوچه جون بالا😂😂😂", "اب خارکصته تند تند تایپ کن ببینم", "مادرتو میگام بخای فرار کنی", "لال شو دیگه نوچه", "مادرتو میگام اف بشی", "کیرم کون مادرت", "کیرم کص مص مادرت بالا", "کیرم تو چشو چال مادرت", "کون مادرتو میگام بالا", "بیناموس  خسته شدی؟", "نبینم خسته بشی بیناموس", "ننتو میکنم", "کیرم کون مادرت 😂😂😂😂😂😂😂", "صلف تو کصننت بالا", "بیناموس بالا باش بهت میگم", "کیر تو مادرت", "کص مص مادرتو بلیسم؟", "کص مادرتو چنگ بزنم؟", "به خدا کصننت بالا ", "مادرتو میگام ", "کیرم کون مادرت بیناموس", "مادرجنده بالا باش", "بیناموس تا کی میخای سطحت گح باشه", "اپدیت شو بیناموس خز بود", "ای تورک خر بالا ببینم", "و اما تو بیناموس چموش", "تو یکیو مادرتو میکنم", "کیرم تو ناموصت ", "کیر تو ننت", "ریش روحانی تو ننت", "کیر تو مادرت😂😂😂", "کص مادرتو مجر بدم", "صلف تو ننت", "بات تو ننت ", "مامانتو میکنم بالا", "وای این تورک خرو", "سطحشو نگا", "تایپ کن بیناموس", "خشاب؟", "کیرم کون مادرت بالا", "بیناموس نبینم خسته بشی", "مادرتو بگام؟", "گح تو سطحت شرفت رف", "بیناموس شرفتو نابود کردم یه کاری کن", "وای کیرم تو سطحت", "بیناموس روانی شدی", "روانیت کردما", "مادرتو کردم کاری کن", "تایپ تو ننت", "بیپدر بالا باش", "و اما تو لر خر", "ننتو میکنم بالا باش", "کیرم لب مادرت بالا😂😂😂", "چطوره بزنم نصلتو گح کنم", "داری تظاهر میکنی ارومی ولی مادرتو کوص کردم", "مادرتو کردم بیغیرت", "هرزه", "وای خدای من اینو نگا", "کیر تو کصننت", "ننتو بلیسم", "منو نگا بیناموس", "کیر تو ننت بسه دیگه", "خسته شدی؟", "ننتو میکنم خسته بشی", "وای دلم کون مادرت بگام", "اف شو احمق", "بیشرف اف شو بهت میگم", "مامان جنده اف شو", "کص مامانت اف شو", "کص لش وا ول کن اینجوری بگو؟", "ای بیناموس چموش", "خارکوصته ای ها", "مامانتو میکنم اف نشی", "گح تو ننت", "سطح یه گح صفتو", "گح کردم تو نصلتا", "چه رویی داری بیناموس", "ناموستو کردم", "رو کص مادرت کیر کنم؟😂😂😂", "نوچه بالا", "کیرم تو ناموصتاا😂😂", "یا مادرتو میگام یا اف میشی", "لالشو دیگه", "بیناموس", "مادرکصته", "ناموص کصده", "وای بدو ببینم میرسی", "کیرم کون مادرت چیکار میکنی اخه", "خارکصته بالا دیگه عه", "کیرم کصمادرت😂😂😂", "کیرم کون ناموصد😂😂😂", "بیناموس من خودم خسته شدم توچی؟", "ای شرف ندار", "مامانتو کردم بیغیرت", "و اما مادر جندت", "تو یکی زیر باش", "اف شو", "خارتو کوص میکنم", "کوصناموصد", "ناموص کونی", "خارکصته ی بۍ غیرت", "شرم کن بیناموس", "مامانتو کرد ", "ای مادرجنده", "بیغیرت", "کیرتو ناموصت", "بیناموس نمیخای اف بشی؟", "ای خارکوصته", "لالشو دیگه", "همه کس کونی", "حرامزاده", "مادرتو میکنم", "بیناموس", "کصشر", "اف شو مادرکوصته", "خارکصته کجایی", "ننتو کردم کاری نمیکنی؟", "کیرتو مادرت لال", "کیرتو ننت بسه", "کیرتو شرفت", "مادرتو میگام بالا", "کیر تو مادرت", "کونی ننه ی حقیر زاده", "وقتی تو کص ننت تلمبه های سرعتی میزدم تو کمرم بودی بعد الان برا بکنه ننت شاخ میشی هعی", "تو یه کص ننه ای ک ننتو به من هدیه کردی تا خایه مالیمو کنی مگ نه خخخخ", "انگشت فاکم تو کونه ناموست", "تخته سیاهه مدرسه با معادلات ریاضیه روش تو کص ننت اصلا خخخخخخخ ", "کیرم تا ته خشک خشک با کمی فلفل روش تو کص خارت ", "کص ننت به صورت ضربدری ", "کص خارت به صورت مستطیلی", "رشته کوه آلپ به صورت زنجیره ای تو کص نسلت خخخخ ", "10 دقیقه بیشتر ابم میریخت تو کس ننت این نمیشدی", "فکر کردی ننت یه بار بهمـ داده دیگه شاخی", "اگر ننتو خوب کرده بودم حالا تو اینجوری نمیشدی", "حروم لقمع", "ننه سگ ناموس", "منو ننت شما همه چچچچ", "ننه کیر قاپ زن", "ننع اوبی", "ننه کیر دزد", "ننه کیونی", "ننه کصپاره", "زنا زادع", "کیر سگ تو کص نتت پخخخ", "ولد زنا", "ننه خیابونی", "هیس بع کس حساسیت دارم", "کص نگو ننه سگ که میکنمتتاااا", "کص نن جندت", "ننه سگ", "ننه کونی", "ننه زیرابی", "بکن ننتم", "ننع فاسد", "ننه ساکر", "کس ننع بدخواه", "نگاییدم", "مادر سگ", "ننع شرطی", "گی ننع", "بابات شاشیدتت چچچچچچ", "ننه ماهر", "حرومزاده", "ننه کص", "کص ننت باو", "پدر سگ", "سیک کن کص ننت نبینمت", "کونده", "ننه ولو", "ننه سگ", "مادر جنده", "کص کپک زدع", "ننع لنگی", "ننه خیراتی", "سجده کن سگ ننع", "ننه خیابونی", "ننه کارتونی", "تکرار میکنم کص ننت", "تلگرام تو کس ننت", "کص خوارت", "خوار کیونی", "پا بزن چچچچچ", "مادرتو گاییدم", "گوز ننع", "کیرم تو دهن ننت", "ننع همگانی", "کیرم تو کص زیدت", "کیر تو ممهای ابجیت", "ابجی سگ", "کس دست ریدی با تایپ کردنت چچچ", "ابجی جنده", "ننع سگ سیبیل", "بده بکنیم چچچچ", "کص ناموس", "شل ناموس", "ریدم پس کلت چچچچچ", "ننه شل", "ننع قسطی", "ننه ول", "دست و پا نزن کس ننع", "ننه ولو", "خوارتو گاییدم", "محوی!؟", "ننت خوبع!؟", "کس زنت", "شاش ننع", "ننه حیاطی", "نن غسلی", "کیرم تو کس ننت بگو مرسی چچچچ", "ابم تو کص ننت", "فاک یور مادر خوار سگ پخخخ", "کیر سگ تو کص ننت", "کص زن", "ننه فراری", "بکن ننتم من باو جمع کن ننه جنده /:::", "ننه جنده بیا واسم ساک بزن", "حرف نزن که نکنمت هااا :|", "کیر تو کص ننت😐", "کص کص کص ننت😂", "کصصصص ننت جووون", "سگ ننع", "کص خوارت", "کیری فیس", "کلع کیری", "تیز باش سیک کن نبینمت", "فلج تیز باش چچچ", "بیا ننتو ببر", "بکن ننتم باو ", "کیرم تو بدخواه", "چچچچچچچ", "ننه جنده", "ننه کص طلا", "ننه کون طلا", "کس ننت بزارم بخندیم!؟", "کیرم دهنت", "مادر خراب", "ننه کونی", "هر چی گفتی تو کص ننت خخخخخخخ", "کص ناموست بای", "کص ننت بای ://", "کص ناموست باعی تخخخخخ", "کون گلابی!", "ریدی آب قطع", "کص کن ننتم کع", "نن کونی", "نن خوشمزه", "ننه لوس", " نن یه چشم ", "ننه چاقال", "ننه جینده", "ننه حرصی ", "نن لشی", "ننه ساکر", "نن تخمی", "ننه بی هویت", "نن کس", "نن سکسی", "نن فراری", "لش ننه", "سگ ننه", "شل ننه", "ننه تخمی", "ننه تونلی", "ننه کوون", "نن خشگل", "نن جنده", "نن ول ", "نن سکسی", "نن لش", "کس نن ", "نن کون", "نن رایگان", "نن خاردار", "ننه کیر سوار", "نن پفیوز", "نن محوی", "ننه بگایی", "ننه بمبی", "ننه الکسیس", "نن خیابونی", "نن عنی", "نن ساپورتی", "نن لاشخور", "ننه طلا", "ننه عمومی", "ننه هر جایی", "نن دیوث", "تخخخخخخخخخ", "نن ریدنی", "نن بی وجود", "ننه سیکی", "ننه کییر", "نن گشاد", "نن پولی", "نن ول", "نن هرزه", "نن دهاتی", "ننه ویندوزی", "نن تایپی", "نن برقی", "نن شاشی", "ننه درازی", "شل ننع", "یکن ننتم که", "کس خوار بدخواه", "آب چاقال", "ننه جریده", "ننه سگ سفید", "آب کون", "ننه 85", "ننه سوپری", "بخورش", "کس ن", "خوارتو گاییدم", "خارکسده", "گی پدر", "آب چاقال", "زنا زاده", "زن جنده", "سگ پدر", "مادر جنده", "ننع کیر خور", "چچچچچ", "تیز بالا", "ننه سگو با کسشر در میره", "کیر سگ تو کص ننت", "kos kesh", "kiri", "nane lashi", "kos", "kharet", "blis kirmo", "دهاتی", "کیرم لا کص خارت", "کص ننت", "  مادر کونی مادر کص خطا کار کیر ب کون بابات ش تیز باش خرررررر خارتو از‌کص‌گایید نباص شاخ شی کص‌ننت چس‌پدر خارتو ننت زیر‌کیرم‌پناهنده شدن افصوص میخورم واصت ک خایه نداری از ننت دفاع کنی افصوص میخورم واصت ک خایه نداری از ننت دفاع کنی سسسسسسگ ننتو از کچن‌کرد نباص شاخ شی مادر کون خطا سیک کن تو کص خارت بی ناموس مادر‌کص‌جق شده کص ننت سالهای سالها بالا بیناموص خار کیر شده بالا باش بخندم ب کص خارت بالا باش بخندم ب کص خارت پصرم تو هیچ موقع ب من نمیرصی مادر هیز کص افی بیا کیرمو با خودت ببر بع کص ننت وقتی از ترس من میری اونجابرو تو کص خارت کص ننت سالهای سالها بالا کونی کیر به مادره خودتو کصی تورو شاخ کرد بردکونتو بده ", " خارکصه  خارجنده  کیرم دهنت  مادر کونی  مادر کص خطا کار  کیر ب کون بابات ش تیز باش  خرررررر خارتو از‌کص‌گایید نباص شاخ شی  افصوص میخورم واصت ک خایه نداری از ننت دفاع کنی  سسسسسسگ ننتو از کچن‌کرد نباص شاخ شی  بی ناموس مادر‌کص‌جق شده  کص ننت سالهای سالها بالا  خار خیز تخم خر  ننه کص مهتابی  ننه کص تیز  ننه کیر خورده شده  مادر هیز کص افی  بالا باش بخندم ب کص خارت  افصوص میخورم واصت ک خایه نداری از ننت دفاع کنی  پصرم تو هیچ موقع ب من نمیرصی  ننه کصو  کوصکش  کونده  پدرسگ  پدرکونی  پدرجنده  مادرت داره بهم میدع  کیرم تو ریش بابات  مداد تو کص مادرت  کیر خر تو کونت  کیر خر تو کص مادرت  کیر خر تو کص خواهرت ", "تونل تو کص ننت", "ننه خرکی", "خوار کصده", "ننه کصو", "مادر بيبي بالا باش ميخوام مادرت رو جوري بگام ديگه لب خند نياد رو لباش", "کیری ننه", "منو ننت شما همه چچچچ", "ولد زنا بی ننه", "میزنم ننتو کص‌پر میکنم ک ‌شاخ‌ نشی", "بی خودو بی جهت کص‌ننت", "صگ‌ممبر اوب مادر تیز باش", "بيناموص بالا باش  يه درصد هم فکر نکن ولت ميکنم", "اخخههه میدونصی خارت هی کص‌میده؟؟؟", "کیر سگ تو کص نتت پخخخ", "راهی نی داش کص ننت", "پا بزن یتیمک کص خل", "هیس بع کس حساسیت دارم", "کص نگو ننه سگ که میکنمتتاااا", "کص نن جندت", "ای‌کیرم ب ننت", "کص‌خارت تیز باش", "اتایپم تو کص‌ننت جا شه  ", "بکن ننتم", "کیرمو کردم‌کص‌ننت هار شدی؟", "انقد ضعیف نباش چصک", "مادر فلش شده جوری با کیر‌میزنم ب فرق سر ننت ک حافظش بپره", "خیلی اتفاقی کیرم‌ب خارت", "یهویی کص‌ننتو بکنم؟؟؟", "مادر بیمه ایی‌کص‌ننتو میگام", "بیا کیرمو بگیر بلیص شاید فرجی شد ننت از زیر کیرم فرار کنه", "بابات شاشیدتت چچچچچچ", "حیف کیرم‌که کص ننت کنم", "مادر‌کص شکلاتی تیز تر باش", "بیناموص زیر نباش مادر کالج رفته", "کص ننت باو", "همت کنی کیرمو بخوری", "سیک کن کص ننت نبینمت", "ناموص اختاپوص رو ننت قفلم‌میفمی؟؟؟؟", "کیر هافبک دفاعی تیم فرانسه که اصمش‌ یادم نی ب کص‌ننت", "برص و بالا باش خار‌کصه", "مادر جنده", "داش میخام چوب بیصبال رو تو کون ننت کنم محو نشو:||", "خار‌کص شهوتی نباید شاخ میشدی", "خخخخخخخخههههخخخخخخخ کص‌ننت بره پا بزن داداش", "سجده کن سگ ننع", "کیرم از چهار جهت فرعی یراص تو کص‌ناموصت", "داش برص راهی نی کیری شاخ شدی", "تکرار میکنم کص ننت", "تلگرام تو کس ننت", "کص خوارت", "کیر‌ب سردر دهاتتون واص من شاخ میشی", "پا بزن چچچچچ", "مادرتو گاییدم", "بدو برص تا خایهامو تا ته نکردم‌تو کص‌ننت", "کیرم تو دهن ننت", "کص‌ننت ول کن خایهامو راهی نی باید ننت بکنم", "کیرم تو کص زیدت", "کیر تو ممهای ابجیت", "بی‌ننه‌ ممبر خار بیمار", "تو کیفیت کار‌منو زیر‌سوال میبریچچ", "داش تو خودت خاسی بیناموص شی میفمی؟؟", "داش تو در‌میری ولی‌مادرت چی؟؟؟", "خارتو با کیر میزنم‌تو صورتش جوری ک‌با دیورا بحرفه", "ننه کیر‌خور تو ب کص‌خارت خندیدی شاخیدی", "بالا باش تایپ بده بخندم‌بهت", "ریدم پس کلت چچچچچ", "بالا باش کیرمو ناخودآگاه تو کص‌ننت کنم", "ننت ب زیرم  واس درد کیرم", "خیخیخیخیخخیخخیخیخخییخیخیخخ", "دست و پا نزن کس ننع", "الهی خارتو بکنم‌ بی خار ممبر", "مادرت از کص‌جر‌بدم ‌ک ‌دیگ نشاخی؟؟؟ننه لاشی", "ممه", "کص", "کیر", "بی خایه", "ننه لش", "بی پدرمادر", "خارکصده", "مادر جنده", "کصکش"];
$SpamEn       = ["MADAR SAG BALA BASH", "MADAR GAV BALA BASH", "MADAR KHAR BALA BASH", "MADAR HEYVUN BALA BASH", "MADAR GORAZ BALA BASH", "MADAR SHOTOR MORGH BALA BASH", "MIKHAY TIZ BEGAMET HALA BEBI KHHKHKHKHK SORAATI NANATO MIKONAM", "CHIYE KOS MEMBER BABT TAZE BARAT PC KHRIDE BI NAMOS MEMBER?", "NANE MOKH AZAD NANE SHAM PAYNI NANE AROS MADAR KENTAKI PEDAR HALAZONI KIR MEMBERAK TIZ BASH YALA  TIZZZZZ😂", "FEK KONAM NANE NANATI NAGAIIDAM KE ENGHAD SHAKHHI????????????????????????????????????HEN NANE GANGANDE PEDAR LASHI", "to yatimi enghad to pv man daso pa mizani koskesh member man doroste nanato ye zaman mikardam vali toro beh kiramam nemigiram", "KIRAM TU KUNE NNT BALA BASH KIRAM TU DAHANE MADARET BALA BASH", "KHARETO BE GA MIDAM BALA BASH", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "NABINAM DIGE GOHE EZAFE BOKHORIA", "KOS NANAT GAYIDE SHOD BINAMUS!!! SHOT SHODI BINAMUS!BYE"];
$Admin        = $this->getSelf()['id'];
$Helper    = '@'; // !!! Change this to your UserName-Helper With @ !!
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN) use ($Message) {
$this->Message($Message, 'reply', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Function-Edit "]=-=-=-=-=-=-=-=-=-=-=-=
$EditMessage = function (string $text, ?ParseMode $parseMode = ParseMode::TEXT) use ($Message) {
$this->Message($Message, 'editText', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Try "]=-=-=-=-=-=-=-=-=-=-=-=
try {
//=-=-=-=-=-=-=-=-=-=-=-=[" Ping "]=-=-=-=-=-=-=-=-=-=-=-=
if ($text == 'Ping' or $text == 'ربات' or $text == '.' or $text == '+') {
$EditMessage("جونم قربان درخدمتم ؟!");
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
$EditMessage('Memory Usage : ' . round(memory_get_peak_usage(true) / 1021 / 1024, 2) . ' MB');
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Panel "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match('/^(Panel|Help|راهنما|پنل)$/i', $text):
$Res = $this->GetInline($ChatID, $Helper, "helpbot_");
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][0]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Name . . . "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Prof'):
$ip = trim(str_replace("Prof ", "", $text));
$ip = explode("|", $ip . "|||||");
$LastName = !empty(trim($ip[0])) ? trim($ip[0]) : "تنظیم نشد !";
$About = !empty(trim($ip[1])) ? trim($ip[1]) : "تنظیم نشد !";
if (!in_array($LastName, $Information['Prof']['LastName']) or !in_array($About, $Information['Prof']['About'])) {
$this->Information['Prof']['LastName'][] = $ip[0];
$this->Information['Prof']['About'][] = $ip[1];
$EditMessage("✠ - نام خانوادگی جدید : `$LastName`
✠ - بیوگرافی جدید : `$About`
✅ - باموفقیت تنظیم شد !", ParseMode::MARKDOWN);
} else {
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
}
break;
case preg_match("/^[\/#!]?(DelRof) (.*?) (.*)$/i", $text, $Deel):
$index = $Deel[2];
$Del = $Deel[3];
if (isset($Information['Prof'][$index])) {
if (in_array($Del, $Information['Prof'][$index])) {
$this->Information['Prof'][$index][$Del] = null;
$EditMessage("حذف شد", ParseMode::MARKDOWN);
}
else
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
}
else
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" List-Prof "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "List"):
$index = substr($text, 5);
if (isset($Information['Prof'][$index])) {
if (count($Information['Prof'][$index]) > 0) {
$Text = "✅  :";
$counter = 1;
foreach ($Information['Prof'][$index] as $ans) {
$Text .= "
$counter => $ans \n";
$counter++;
}
$EditMessage($Text);
}
else
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
}
else
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Prof "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "Clean"):
$index = substr($text, 6);
if (isset($Information['Prof'][$index])) {
$this->Information['Prof'][$index] = [];
$EditMessage("پاکسازی شد", ParseMode::MARKDOWN);
}
else
$EditMessage("وجود دارد", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Info-Accaunt "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'info' or $text == '/info':
$Me = $this->getSelf();
$MeId = $Me['id'];
$Name = $Me['first_name'];
$Phone = '+' . $Me['phone'];
$EditMessage("
🔄 مشخصات ربات ساخته شده به شرح زیر میباشد :

✠ - سازنده : [$Admin](tg://user?id=$Admin)
✠ - نام اکانت : `$Name`
✠ - آیدی عددی : `$MeId`
✠ - شماره اکانت : `$Phone`", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Config "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Config" or $text == "پیکربندی":
$EditMessage("انجام شد", ParseMode::MARKDOWN);
$this->joinInChannel(array("@group_a_8"));
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Check-Username "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(CheckUsername) (@.*)$/i", $text, $CheckUser):
$Check = $this->account->checkUsername(username: str_replace("@", "", $CheckUser[2]));
if (!$Check)
$Text = "وجود دارد !";
else
$Text = "آزاد !";
$EditMessage($Text);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Save "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "save" or $text == "Save" and isset($replyTo):
$EditMessage("» sᴀᴠᴇᴅ =)");
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $this->getSelf()['id']);
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-2 "]=-=-=//=-=-=//=-=-=//=-=-=//
case str_starts_with($text, "/info"):
$ID        = substr($text, 6);
$Info      = $this->getFullinfo($ID);
$me_status = $Info['status']['_'];
$me_bio    = $Info['full']['full_user']['about'];
$me_common = $Info['full']['full_user']['common_chats_count'];
$me_name   = $Info['first_name'];
$me_uname  = $Info['username'];
$EditMessage("
» ɪᴅ : `$ID`
» ɴᴀᴍᴇ : `$me_name`
» ᴜsᴇʀɴᴀᴍᴇ : @$me_uname
» sᴛᴀᴛᴜs : `$me_status`
» ʙɪᴏ : `$me_bio`
» ᴄᴏᴍᴍᴏɴ ɢʀᴏᴜᴘs ᴄᴏᴜɴᴛ : `$me_common`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Flood "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(flood) ([0-9]+) (.*)$/si", $text, $Flood):
$Count = $Flood[2];
$Text = $Flood[3];
$EditMessage("» ғʟᴏᴏᴅɪɴɢ ᴛᴇxᴛ ( `$Text` ) ᴄᴏᴜɴᴛ ( `$Count` ) . . . !");
for ($i = 1; $i <= $Count; $i++) {
$ReplyMessage($Text);
}
unset($i);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Run-Code "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(php) (@.*)$/i", $text, $match):
$result = null;
$errors = null;
$match[2] = "return (function () use(&\$update){{$match[2]}})();";
ob_start();
try {
(eval($match[2]));
$result .= ob_get_contents() . "\n";
} catch (Throwable $e) {
$errors .= $e->getMessage() . "\n";
}
ob_end_clean();
if (empty($result)) {
$ReplyMessage("No Results ...");
return;
}
$errors = !empty($errors) ? "\nErrors :\n$errors" : null;
$answer = "Results : \n" . $result . $errors;
$ReplyMessage("$answer");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Whois "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/whois"):
$whois = substr($text, 7);
$EditMessage("» ᴡʜᴏɪsɪɴɢ ( `$whois` ) ᴅᴏᴍᴀɪɴ . . . !");
$Get   = json_decode($this->fileGetContents("https://api.codebazan.ir/whois/index.php?type=json&domain=$whois"), true);
$owner   = $Get['owner'];
$ip      = $Get['ip'];
$address = $Get['address'];
$dns     = $Get['dns'];
$s1      = $dns['1'];
$s2      = $dns['2'];
$ReplyMessage("
ᴅᴏᴍᴀɪɴ : $whois
ᴏᴡɴᴇʀ : $owner
ɪᴘ : $ip
ᴀᴅᴅʀᴇss : $address
ᴅɴs : $s1 | $s2");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Scr "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/scr"):
$scr = substr($text, 5);
$EditMessage("» ɢᴇᴛᴛɪɴɢ sᴄʀᴇᴇɴ sʜᴏᴛ ғʀᴏᴍ ( `$scr` ) ᴡᴇʙsɪᴛᴇ . . . !");
$Media = ['_' => 'inputMediaGifExternal', 'url' => "https://ApiDataTm.site/ScreenShot.php?url=$scr"];
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ sᴄʀᴇᴇɴ sʜᴏᴛ ɪs ʀᴇᴀᴅʏ =)");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Ping-Site "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/ping"):
$ping = substr($text, 6);
if ($this->fileGetContents("https://api.codebazan.ir/ping/?url=$ping") != NULL)
$ReplyMessage("» ᴘɪɴɢ ɪs $ping !");
else
$ReplyMessage("» ʏᴏᴜʀ ᴀᴅᴅʀᴇss ɪs ɪɴᴠᴀʟɪᴅ !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Brc "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/brc"):
$brc  = substr($text, 5);
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ǫʀ ᴄᴏᴅᴇ ғʀᴏᴍ ( `$brc` ) ᴀᴅᴅʀᴇss . . . !");
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => $Message->media->getDownloadLink("https://api.codebazan.ir/qr/?size=500x500&text=$brc")];
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ ǫʀ ᴄᴏᴅᴇ ɪs ʀᴇᴀᴅʏ =)");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Down-Git "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/downgit"):
$DownGit = substr($text, 9);
$EditMessage("» ɢᴇᴛᴛɪɴɢ ᴛʜᴇ ( `$DownGit` ) ɢɪᴛʜᴜʙ ғɪʟᴇ . . . ! ");
$Media = ['_' => 'inputMediaDocumentExternal', 'url' => "$DownGit/archive/master.zip"];
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ ɢɪᴛʜᴜʙ ғɪʟᴇ ɪs ʀᴇᴀᴅʏ =)");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Info-Page "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/instainfo"):
$ID  = substr($text, 11);
$Get = json_decode($this->fileGetContents("https://data-api.site/instainfo.php?id=$ID"), true);
$id        = $Get['Results']['id'];
$name      = $Get['Results']['name'];
$username  = $Get['Results']['username'];
$bio       = $Get['Results']['bio'];
$postcount = $Get['Results']['post count'];
$following = $Get['Results']['following count'];
$followers = $Get['Results']['followers count'];
$photo     = $Get['Results']['pic'];
$Media     = ['_' => 'inputMediaUploadedPhoto', 'file' => $photo];
$this->SendMedia($ChatID, $Media, $MsgID,"
➡️ id » $id
➡️ name » $name
➡️ username » $username
➡️ bio » $bio
➡️ post count » $postcount
➡️ following » $following
➡️ followers » $followers");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Down-Insta "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/instadown"):
$Link = substr($text, 11);
$Get = json_decode($this->fileGetContents("https://data-api.site/instagram1.php?url=$Link"), true)['Results']['post'];
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ǫʀ ᴄᴏᴅᴇ ғʀᴏᴍ ( `$Link` ) ᴀᴅᴅʀᴇss . . . !");
$Media = ['_' => 'inputMediaDocumentExternal', 'url' => $Get];
$this->SendMedia($ChatID, $Media, $MsgID, "s");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Info-Channel "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/infochannel"):
$ID = substr($text, 13);
$Get = json_decode($this->fileGetContents("https://data-api.site/channelinfo.php?channel=$ID"), true)['Results']['Main_Information'];
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ǫʀ ᴄᴏᴅᴇ ғʀᴏᴍ ( `$ID` ) ᴀᴅᴅʀᴇss . . . !");
$Title      = $Get['Channel_Title'];
$UserName   = $Get['UserName'];
$Profile    = $Get['Profile_Picture'];
$Member     = $Get['Member'];
$First_Post = $Get['First_Post_ID'];
$Last_Post  = $Get['Last_Post_ID'];
$Post_Count = $Get['Last_Post_ID'];
$SeensPost  = $Get['AllPost_Seens_Count'];
$photos     = $Get['Fine_Information']['photos'];
$videos     = $Get['Fine_Information']['videos'];
$file       = $Get['Fine_Information']['file'];
$links      = $Get['Fine_Information']['links'];
$Media = ['_' => 'inputMediaDocumentExternal', 'url' => $Profile];
$this->SendMedia($ChatID, $Media, $MsgID, "sa");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-3 "]=-=-=//=-=-=//=-=-=//=-=-=//
case str_starts_with($text, "/video"):
$video = substr($text, 7);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$video` ) ᴍᴜsɪᴄ . . . !");
$Res = $this->GetInline($ChatID, "@PapkornBot", $video);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Photo "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/photo"):
$photo = substr($text, 7);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$photo` ) ᴍᴜsɪᴄ . . . !");
$Res = $this->GetInline($ChatID, "@bing", $photo);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Music "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/music"):
$music = substr($text, 7);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$music` ) ᴍᴜsɪᴄ . . . !");
$Res = $this->GetInline($ChatID, "@melobot", substr($text, 6));
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Gif "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/gif"):
$gif = substr($text, 5);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$gif` ) ɢɪғ . . . !");
$Res = $this->GetInline($ChatID, "@gif", $gif);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Pic "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/pic"):
$pic = substr($text, 5);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$pic` ) ᴘɪᴄᴛᴜʀᴇ . . . !");
$Res = $this->GetInline($ChatID, "@pic", $pic);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Search-Google "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/google"):
$google = substr($text, 8);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$google` ) ᴘɪᴄᴛᴜʀᴇ . . . !");
$Res = $this->GetInline($ChatID, "@GoogleDEBot", $google);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Search-Youtube "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/youtube"):
$youtube = substr($text, 9);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$youtube` ) ᴘɪᴄᴛᴜʀᴇ . . . !");
$Res = $this->GetInline($ChatID, "@uVidBot", $youtube);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Weather "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/weather"):
$City = substr($text, 9);
$Get  = json_decode($this->fileGetContents("https://api.openweathermap.org/data/2.5/weather?q=$City&appid=YOUR_OPENWEATHER_API_KEY&units=metric"), true);
$City    = $Get['name'];
$Degree  = $Get['main']['temp'];
$Air     = $Get['weather'][0]['main'];
$TypeAir = '';
switch ($Air) {
case 'Clear':
$TypeAir = 'آفتابی☀';
break;
case 'Clouds':
$TypeAir = 'ابری ☁☁';
break;
case 'Rain':
$TypeAir = 'بارانی ☔';
break;
case 'Thunderstorm':
$TypeAir = 'طوفانی ☔☔☔☔';
break;
case 'Mist':
$TypeAir = 'مه 💨';
break;
}
if ($City != null)
$Text = "دمای شهر $City هم اکنون $Degree درجه سانتی گراد می باشد

شرایط فعلی آب و هوا: $TypeAir";
else
$Text = "⚠️شهر مورد نظر شما يافت نشد";
$EditMessage($Text);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Apk "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/apk"):
$apk = substr($text, 5);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$apk` ) ᴀᴘᴋ . . . !");
$Res = $this->GetInline($ChatID, "@apkdl_bot", $apk);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Like "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/like"):
$like = substr($text, 5);
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ʏᴏᴜʀ ɪɴʟɪɴᴇ ʙᴜᴛᴛᴏɴs . . . !");
$Res = $this->GetInline($ChatID, "@like", $like);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Meme "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/meme"):
$meme = substr($text, 6);
$EditMessage("» sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ( `$meme` ) ᴍᴇᴍᴇ . . . !");
$Res = $this->GetInline($ChatID, "@Persian_Meme_Bot", $meme);
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Fonet "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/fonet"):
$fonet = substr($text, 7);
$Name = str_replace(' ', '+', $fonet);
$FontName = json_decode($this->fileGetContents("https://api.codebazan.ir/font/?text=$Name"), true)['result'];
$ShowFonet = '';
for ($i = 1; $i < 139; $i++) {
$ShowFonet = $ShowFonet . " $i => " . $FontName[$i] . "\n";
}
$ReplyMessage($ShowFonet);
unset($ShowFonet);
unset($i);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-FaFonet "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/fafont"):
$Text = strtoupper(substr($text, 8));
$EditMessage("» ʙᴜɪʟᴅɪɴɢ 10 ғᴀʀsɪ ғᴏɴᴛs ғᴏʀ ( `$Text` ) ɴᴀᴍᴇ . . . ! ");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Priarz "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/priarz":
$ShowPrice = json_decode($this->fileGetContents("https://api.codebazan.ir/arz/?type=arz"), true)['Result'];
$ShowText = ' ';
for ($i = 0; $i < 30; $i++) {
$ShowText = $ShowText . "💵  $ShowPrice[$i]['name'] => " . $ShowPrice[$i]['price'] . "\n";
}
$ReplyMessage("💵 قیمت ارز های کشور های مختلف:
-» توجه قیمت ها ریال می باشد.
➖➖➖➖➖➖➖➖➖
$ShowText");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Rev "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "rev"):
$ReplyMessage($this->fileGetContents("https://api.codebazan.ir/strrev/?text=".str_replace(" ", "%20", substr($text, 4))));
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Meane "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "meane"):
$meane = substr($text, 6);
preg_match('~<p class="">(.+?)</p>~si', $this->fileGetContents('https://www.vajehyab.com/?q=' . urlencode($meane)), $p);
$p = trim(strip_tags(preg_replace(['~<[a-z0-9]+?>.+?</[a-z0-9]+?>|&.+?;~', '~<br.+?>~s'],['', "\n"], $p[1])));
$EditMessage("» ᴍᴇᴀɴɪɴɢ ( `$meane` ) ғᴀʀsɪ ᴡᴏʀᴅ . . . !");
if ($p != NULL)
$ReplyMessage("» کلمه اولیه : `$meane`
» معنی :
» $p");
else
$ReplyMessage("» ʏᴏᴜʀ ᴡᴏʀᴅ ɴᴏᴛ ғᴏᴜɴᴅ !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Kalame "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/kalame"):
$Harf = substr($text, 8);
if ($Harf == "مبتدی")
$muu = 0;
elseif($Harf == "ساده")
$muu = 1;
elseif($Harf == "متوسط")
$muu = 2;
elseif($Harf == "سخت")
$muu = 3;
elseif($Harf == "وحشتناک")
$muu = 4;
else
$muu = "Mahdi";
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ғᴏʀ ( `$Harf` ) ᴋᴀʟᴀᴍᴇ ɢᴀᴍᴇ . . . !");
if ($muu == 0 or $muu == 1 or $muu == 2 or $muu == 3 or $muu == 4) {
$Res = $this->GetInline($ChatID, "@KalameBot", $Harf);
$this->SendInline($ChatID,$MsgID,$Res['query_id'],$Res['results'][$muu]['id']);
} else
$ReplyMessage("» ʏᴏᴜʀ ʟᴇᴠᴇʟ ɪs ɪɴᴠᴀʟɪᴅ !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Fall "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/fall" or $text == "fall":
$EditMessage("» ɢᴇᴛᴛɪɴɢ ᴀ ᴏᴍᴇɴ ʜᴀғᴇᴢ ғᴏʀ ʏᴏᴜ . . . !");
$Media = ['_' => 'inputMediaPhotoExternal', 'url' => "https://www.beytoote.com/images/Hafez/" . rand(1, 149) . ".gif"];
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ ᴏᴍᴇɴ ʜᴀғᴇᴢ ɪs ʀᴇᴀᴅʏ =)");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Icon "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/icon"):
$mu = str_replace(" ", "%20", substr($text, 6));
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ғᴏʀ ( `$mu` ) ɪᴄᴏɴ . . . !");
$bot = ["https://dynamic.brandcrowd.com/asset/logo/1b18cb55-adbe-4239-ac3f-4e22d967d434/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1a2e3c8f-08db-4fad-b0f2-de3e58f24ce9/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7925e4fe-d125-4d7f-a3f6-12ecfb7fa641/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ad871f75-cf28-4e97-8580-f72f2844db67/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5f5dfa37-29e3-4a9f-ba5b-31f8214b8d40/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/bc419bf7-5723-4380-836e-26c55aa795c5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/086c0526-0be0-48b0-adee-f17844ac911c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/07d54ba4-4489-48cc-9a00-fe7e9cb52276/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c699f864-5fac-4cb7-b201-712259727a72/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d74c5889-e17a-44a1-852a-3bc1c0f64483/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/00359d52-ef6b-41bf-ae27-4339609fede3/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ed409e0a-9816-4b65-a3b9-e8f361798227/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7ea43112-2b71-4784-a6f1-9cb95f61e673/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/90880bf9-35ca-406d-aec2-af41e327b26a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8785de07-dc7b-4b47-86ff-270d14586345/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e49fa5be-1a3b-48f3-bc39-3109ce6c4bfa/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/26b1f627-ad53-408f-b023-3b0e77da78f7/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8a192263-eb69-48d0-a1bd-2599769e2787/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5313cf95-4ab7-4ff3-895e-ca21681e452d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/da767a21-6d72-4a2b-8a04-7b8c448e53b8/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0424daff-7df1-4bfb-aa07-ed52cfc99e1f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/eaa2cf5e-7df1-4224-b627-4a4094a2b44c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dcdaf4b4-2158-459b-a290-66d266fd3003/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/4030324b-894c-4ccf-906d-7a039b10d7c3/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/79450b06-4c42-4669-88c8-6a5f843f3b08/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8f52d556-af31-489b-90a2-5a1f9653f07c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/443aa5c4-6556-468c-9d44-cc31320aca59/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/739440b5-4846-438e-9e21-9a43e2099034/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d7076540-b78d-4092-bec3-84d0b5b6cf35/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/20333bac-5343-404d-83fe-49e54a591e5a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f78a6d4d-ca0b-4d59-92bd-5dde30dc5beb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ba3e427e-c7e2-45fd-8583-ae39792b520a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/bfda2f02-cf16-4a9a-8174-5a1c474fa8b4/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ebea98c1-507c-4cb6-8aea-332f330add3e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/88107639-8c59-48d7-aa72-b5ba622f2d2f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b2aa5492-009b-4b1a-85e5-e945c193361e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3b6db5a4-6408-43db-8155-7828258c7dfb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/06a2017e-24b4-4dc9-921a-4b93bd3aaa41/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/a7313939-d69e-4204-b0e8-1a6099c48b22/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d70cdc43-79ea-4bff-bd87-d8edaf4e691b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/930b5655-bde9-4f44-a31c-198367190eb8/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/2d1a8bbb-1c9e-4516-9be5-fa3d05693757/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/90c9913d-ade6-45af-8371-c91a9b07964c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/644391b8-e59d-422f-a81c-a7d5428c8efb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9182c620-b265-491e-bda1-6db153a5fb94/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/931f8416-aa36-4a01-af0d-35b731f898db/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dbf78f01-a741-4c92-a6e4-668129dca2bb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f4953040-e80b-49cf-a347-1cda77a97190/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d66113bf-3e06-4729-bbce-67fcf0d1848c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/a3f20deb-e126-48f4-a972-3877f69360fe/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ba6724d8-4138-4263-a434-fe7b7acd6b0b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5ea52fd4-10aa-4a70-9d25-3cbfb56c8bb4/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f5180411-054b-4b76-bb2b-6265981fbe11/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ec4faa35-d0f7-434e-8c25-c3a28b956049/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3a06896d-6a8e-4b61-a124-e0ab0453d07e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c5140ac3-0a5c-45f1-bf6b-203f02c3c4e4/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c7cf0c9e-3e48-40bb-81b5-4cc40df5a2a6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/752778e8-6197-4a13-8900-dcb666ca2bd1/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e0f5a980-b751-4b81-8425-ac2ecb77259a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ccf02e3a-6d03-44a8-9ec0-b5e33001bbce/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/21bed36c-cb81-407a-86b0-8333e357c59e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9d0bfaab-7506-41b9-8721-46555c7798df/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/794f593c-f03c-47ee-be57-a177409a1618/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/017d56c9-aaf5-4e1c-b0d5-e016b9f49e46/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0e981fc4-accf-4070-b8d0-9ac279f8e808/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d14e8ade-80d8-4e96-8d47-ed8a5cfbe180/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/cfaa5fac-c17d-4e75-9218-fe6673b3b40d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c00358da-24f7-451f-95f3-65f3f3d9bf14/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/97be57bb-13de-44c5-8000-9498feb3789b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8725b125-0434-421e-863e-9c94618943f6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/aa0eccb0-8dd5-48e5-940a-0157ad466072/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c5d0430c-6ecc-4278-a5a3-3b0e2cb6c6f5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/000e9616-8763-4add-acff-60754b711c0d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/a1966764-79c0-4adb-a7c7-5372dcbb63f1/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8e40623a-cb2b-406f-a91b-c47f6fb306f9/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/42c98814-fdda-46d1-a4e1-2e2011fb9b65/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0bf69dc7-3925-4825-b00f-8b66d7b30721/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/151adcab-dad2-41e6-883b-a579d726c5bb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9ac17003-596e-446d-b715-fbc245036803/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/2c0269cb-ad5f-464a-8cd0-227ecf8a77a6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7a2dca3f-e337-47fc-aba0-469c4fabeb63/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1a930669-1c02-47d8-bbe0-cf04975b8522/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1a248710-0d91-4aa7-8141-6da939c841e9/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1f83800a-0dbf-410b-954c-e19c2dab1ef8/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/17753682-84c3-4447-866c-ea170fc7b7d5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d71a7cf9-a684-4b34-a75e-ffb6bf641a7d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/eec764d5-ae8e-4ebf-affb-32082312f42e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/011a6521-23cf-40b6-88b3-990c6ec22a6e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/cf3f675f-e615-4f5e-a595-49332aacdb81/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3df1a69c-85ad-4dc8-9b00-3bd8e4db8383/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3df1a69c-85ad-4dc8-9b00-3bd8e4db8383/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/86c9985d-8949-44d8-9dc6-47a86f993993/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/c2e19663-ef1e-475f-8208-e22473849445/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e79b4266-bfa9-40da-aef7-d2eb90656d3b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0a8d749e-9df5-4476-9a10-dc1ac86a149c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/acaede2b-1c05-465f-9a33-1c11ac293f11/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/aa6390ec-4752-416b-9b77-034dcc34b17f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/37cc6ec8-b36e-41bd-bc72-4aa6363f0ebc/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5b9e7746-36eb-4c66-9bcd-1e252699d1f2/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/62de87f1-1257-46c7-9590-99a568115545/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/909ab155-c255-4d08-9918-69b0fcbef647/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ee799336-529d-4b36-9ebc-f2009d21e545/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d3a6e797-2c55-4b35-adf0-4ac763b95808/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d8bb2364-0350-4e2f-9095-0e093c504445/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/04cb4959-84cd-4beb-ae55-59884139603b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0e386f0d-907a-4a3e-9ce8-ae7b3f68d66a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/12531e0d-96ef-4b68-993e-cb4179a2ff29/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1c8935c3-e145-4890-ba64-91735c8dfe4f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/52f1623a-4af8-4065-bf8c-a746dff09fef/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5b2cb293-249e-46cd-901e-d190dc002e89/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/670e63fb-4dd9-4d17-9ba3-f2c944d45f28/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9013d098-93e2-4346-9656-6b63c24b440b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b2e761bd-82ea-4350-a752-fa556cef2dd0/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b5843fcf-37a3-44e7-9938-91addefa09fc/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dbd21a15-b0db-4ae9-a561-fd112aba6fcd/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/eb194df6-c069-4a33-82b6-4f4383877988/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f0223266-f576-40c7-a31d-d2c17c584a46/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/055241ff-dc4f-4743-90be-1c9caa0c900b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1fe7224c-8946-48e9-9d11-c978d0069fdb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3e0ee4c9-8165-42eb-801c-fb26aa2ecf0a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/4b4b9948-7c07-4f07-a1d1-d33b44084cc0/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/72241f70-7f3d-459d-8638-75b3cf6e12ee/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7b98994d-e50c-409c-ab2a-af1a568c16ad/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/888b0d00-f6a6-4c56-a744-9d5b3b6965f6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9467cb72-d11e-4462-804f-c7b34bf895d7/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b1c634dd-aacc-4190-986c-7ace14ed3ec6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/db41be37-350e-40f7-a3bf-7247e2a11948/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e31b1fb6-0f38-4c75-bc3f-3373aaaf3571/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f287cbe2-9389-4de0-9bd3-6b8eacf2768c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/01866580-0a27-4fae-8529-595b3d08c3c6/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/098a3e12-9643-417f-b14e-9c0929c21b1e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/449247de-6d8d-44a9-90e1-e54d4ee72137/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/65652ce5-16fd-45f1-b5bb-257b1b95be2c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/889a604d-aa1b-4486-b09c-7d0f9368becb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/89c21f53-1a93-41b4-b0e0-e7233ce40c27/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8c18fdd5-9007-4fb8-85bd-549e21c6ceea/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/97191afc-e552-42a7-a96f-5796e306ae1f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/a74b621b-fb9c-49d4-a7b9-48c702dee154/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ab948d82-e22b-4ec2-a4ae-eb93f55ddaf8/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/adcb5161-3b1e-4b2c-b658-42cdbef64c93/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b05d717d-a4a8-4350-a98e-4e6635271d2d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d5415cbf-418d-45ba-9e6c-05f9385457f0/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dcc17996-39bf-45d1-8b9d-f66e0b75d693/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e33108a3-9c4f-4ebe-a031-8304071f6888/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ea3439b4-3ae8-4789-9fb8-acc5745bde0d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f7e73e79-7ee6-42cf-9af2-7ac147c6c78f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/11e9e67b-723d-4320-9481-7df27efd143e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/09699c93-f687-4c58-b6dc-cb8010de7df9/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1cc2db6f-d3e7-425b-8b2a-d1349d3739d5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/37922c94-880a-4d6f-8070-914087acc09a/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/4a69a160-fe1d-4391-8af1-2d7ac9580953/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/5465ad8f-d9c4-4a4c-b587-23c98d231ae8/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/55c9faad-542c-4c56-b101-f3e21bbfb95f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/96b7e527-d141-442d-babb-fda190233a1e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ce545f6b-c441-4848-a02a-ca8779e52f29/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/e8fcd3b0-0ce8-41f1-abf4-a7283ee40ffc/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f18ae32f-ce31-4946-9704-72e193f5cad2/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/fc5aa3ab-e782-456e-b7e5-f93dfcd325ee/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1a5e85a2-ae4e-411d-ab13-43a3b918f478/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3c337f69-2066-4abe-b9ae-228ddf86bd4b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/56d42ddd-1c3d-4787-a7fe-cc6e9960c875/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7feb63c0-0210-4bb4-8a52-56849b495b8c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/8ee82bd4-4869-4fad-84c8-68f60f10959f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/95b5c8a5-d62d-4474-ba64-e726faa1bb97/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/a791985b-1b64-4f23-bd2d-be67bdc27577/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/bb8044ba-5367-47de-8c4b-9ca90bd67c4d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dbcdc939-e87b-45ce-8eb7-3e85d6a71bfa/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/dbfdb19c-5c38-43e2-a500-61426d4fd768/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/fcda8baf-e858-47ca-8e55-e945cebaf838/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/88aa303b-dbb1-40a3-ada7-c138d457df7d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/7b84c12f-6060-4f93-a0cb-6cfbfb0d649f/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d1510dc5-ac8d-497d-9ad9-c9fdec93796d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/484e6686-0062-4926-ba81-0b81353b4ed0/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b538b140-c1a4-4188-a160-b76e140b4ad5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0e73bf05-13a0-41aa-9b57-00d6670b4952/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/f0f53e57-7dda-469a-9513-273c8d2bb514/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/2d81292d-7c5a-41a2-9dfd-9d434a413c63/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3bf52b81-9940-4fd2-b326-ef90cc077272/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/864efb77-e149-4fd0-a058-976c7c5e492e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/07f5f5a5-ea09-4e94-88fa-d9ee9060b458/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/eaf58c74-5f43-48c3-9de5-2a0b94e1f8a2/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/3e1331ed-fc20-49d2-a55e-c3ced0e47c56/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/34372e0c-47ab-4f95-b136-2de09c21b8ed/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/fc5269e7-6172-4007-a47f-a183d8d7f3cd/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/cf1d7785-935c-4d28-a1f9-8d94321c6fba/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/9fcb5110-8b0e-4c6f-9764-b38dbd6e0112/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/00f0c0dc-7af4-441a-ab9e-cf5bb78fe220/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/6805ec29-0e17-4da2-ba12-1f170bc0ce45/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/d84859df-c614-4135-a55d-b9f95a19e2ff/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/ca2ff2db-806b-499f-b3b1-c0a5e1428a94/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/b0b0828d-dd3b-4c9f-a8c7-366f005590cb/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/696d69a2-8c49-4bd8-82c7-2cc6b14d3b28/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/770dbe6d-420f-4860-953a-69e763aafa00/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/00023174-20f6-4e58-9b10-65fe054bfbc4/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/02ffc18d-1bbe-4bd7-b177-3c79082a6a04/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0300c219-2ad6-47af-bb68-e3e0f241c34b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/04e8e3bd-0cff-4a68-98e1-b0f412c46611/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/059b8c80-052f-419b-9baa-26b62f7405cc/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/071ae338-60be-4a21-9437-cb15ec7ab4e9/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0748d91a-ac32-4b37-a27f-89ee68e8753b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0843ed95-3f00-4737-8f9c-af83b0fb92b3/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/08c3aa53-d862-41c9-adb1-fa10bd6a0fdd/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/08ffb721-d5fc-4675-9cd7-539893d17d8c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/09c8e48d-16c9-4fd6-aeec-0b87fdfee159/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0ad29a62-01cb-4f96-8643-a7eab0eb84f7/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0affd79b-f5df-4a61-a22f-2dc7cbab569d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0bba65a5-15b9-4da0-bf96-7ea879bf7081/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0c8acf74-1b27-4545-b46c-54327dc4f38e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0e88be07-4898-432f-b634-5a5df787416d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/0f0e7abb-5d45-4f31-9848-6b27f7fbf76d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1058614e-b9be-409b-962c-8f541cba0dd0/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/120ba62c-5a91-4c6a-a6c9-673d2baa35fe/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/13953056-ace8-4a1b-9b7d-949ed1798c0d/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/13c42cc5-eb6b-4587-8581-c55813bcf37e/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/13d16dbe-77f4-4a05-b0a0-ee6922941e0b/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/145f8d81-1f17-4cc4-b35c-44da350be2f5/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/15654083-1f64-4b60-bb53-3eb916141c3c/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/172fd7df-cb66-4aa9-a1ce-fbccf26d05f2/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/176993a8-22ac-44f1-a735-af004fd7d8dd/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/17bd5e20-9941-4177-b2a6-8ff0e932abda/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/17d56cfe-ca05-4de2-9648-ffbb3d27bb76/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1842af2e-44f8-4429-b840-5377904a7620/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/18cbcbad-b87b-4af7-9389-5c3cc19b6fc7/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/192be4b6-5a8a-42bd-8ec4-580c063d7f21/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1a487867-0157-4e8c-a568-aeeea80fce00/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1ada54d4-e64a-4e45-9d31-1706a6ada796/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1b65d0dc-43dd-4710-aa4b-e69aa3066982/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1b76e39d-7f17-4fb0-b12c-b68e1301a559/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1bd1306f-8b8f-4515-93b9-0438f6ff8130/logo?v=4&text=$mu", "https://dynamic.brandcrowd.com/asset/logo/1ca25ddf-40de-40fa-b93d-e29af3e46c05/logo?v=4&text=$mu"];
$Res   = $bot[rand(0, count($bot) - 1)];
$Media = ['_' => 'inputMediaDocumentExternal', 'url' => $Res];
$Res   = str_replace("https://dynamic.brandcrowd.com/asset/logo/", "", $Res);
$Res   = str_replace("https://dynamic.brandcrowd.com/asset/logo/", "", $Res);
$Res   = str_replace("/logo?v=4&text=$mu", "", $Res);
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ ɪᴄᴏɴ ɪs ʀᴇᴀᴅʏ =)
ɪᴅ : `$Res`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Lid "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/lid"):
$link = substr($text, 5);
$link = "https://dynamic.brandcrowd.com/asset/logo/".$link."/logo?v=4&text=R0BOTIC";
$EditMessage("» ɪᴄᴏɴ ʟɪɴᴋ sᴇɴᴅ ɪɴ ʏᴏᴜʀ ᴘᴠ . . . !");
$ReplyMessage("» ɪᴄᴏɴ ʟɪɴᴋ ɪs : `$link`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-BioRandom "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/biorandom":
$EditMessage($this->fileGetContents("https://api.codebazan.ir/bio/"));
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-4 "]=-=-=//=-=-=//=-=-=//=-=-=//
case $text == "jorat" or $text == "جح":
$Res = $this->GetInline($ChatID, "@hjrobot");
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][0]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Bazi "]=-=-=-=-=-=-=-=-=-=-=-=
case str_starts_with($text, "/bazi"):
$Res  = $this->GetInline($ChatID, "@bodobazibot",substr($text, 6));
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][rand(0, count($Res['results']))]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Get-Giff "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("~^giff (\w+)$~i", $text, $giff):
$EditMessage("» ʙᴜɪʟᴅɪɴɢ ғᴏʀ ( `$giff[1]` ) ɢɪғ . . . !");
$mu = str_replace(" ", "%20", $giff[1]);
$bot = [
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=alien-glow-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=flash-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=shake-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=highlight-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=blue-fire&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=burn-in-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=inner-fire-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=glitter-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=flaming-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120",
"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=memories-anim-logo&text=$mu&doScale=true&scaleWidth=240&scaleHeight=120"];
copy($bot[rand(0, count($bot) - 1)], "File.mp4");
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "File.mp4",
'attributes' => [['_' => 'documentAttributeAnimated']]];
$this->SendMedia($ChatID, $Media, $MsgID, "» ʏᴏᴜʀ ɢɪғ ɪs ʀᴇᴀᴅʏ =)");
deleteFile("File.mp4");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Date-Miladi "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'تاریخ میلادی':
date_default_timezone_set('UTC');
$rooz   = date("l"); // روز
$tarikh = date("Y/m/d"); // سال
$mah    = date("F"); // نام ماه
$hour   = date('H:i:s - A'); // ساعت
$EditMessage("today  $rooz |$tarikh|

month name🌙: $mah

time⌚️: $hour");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Search "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(search) (.*)$/i", $text, $search):
$res_search = $this->messages->search(filter: ['_' => 'inputMessagesFilterEmpty'], peer: $ChatID, q: $search[2], max_date: time(), max_id: $MsgID, min_id: 1);
$texts_count = count($res_search['messages']);
$users_count = count($res_search['users']);
$ReplyMessage("Msgs Found: $texts_count \nFrom Users Count: $users_count");
foreach ($res_search['messages'] as $text) {
$textid = $text['id'];
$this->messages->forwardMessages(from_peer: $text, id: [$textid], to_peer: $ChatID);
}
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-5 "]=-=-=//=-=-=//=-=-=//=-=-=//
case $text == "/fohsh" or $text == "Fohsh":
for ($i = 1; $i <= 80; $i++) {
$Send = $Spam[array_rand($Spam)];
$this->SendMessage($ChatID, "$i");
$this->SendMessage($ChatID, $Send);
}
break;
case $text == "/fohsh1" or $text == "Fohsh1":
for ($i = 1; $i <= 50; $i++) {
$Send = $Spam[array_rand($Spam)];
$this->SendMessage($ChatID, $Send);
}
break;
case $text == "/foren" or $text == "Foren":
for ($i = 1; $i <= 50; $i++) {
$Send = $SpamEn[array_rand($SpamEn)];
$this->SendMessage($ChatID, $Send);
}
break;
case $text == "/Atack" or $text == "Spam2":
$ReplyMessage('خب آماده باش قرار 150 تا کیر بره تو کص ننت !!!');
for ($i = 1; $i <= 150; $i++) {
$ReplyMessage("$i => کص ننت");
}
$ReplyMessage('بزن به چاک مادرجنده !');
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
$Text = "✅  :";
$counter = 1;
foreach ($Answering as $k => $ans) {
$Text .= "$counter : $k => $ans \n";
$counter++;
}
$ReplyMessage($Text);
}
else
$ReplyMessage("🙂 - پاسخی وجود ندارد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/cleananswers':
unset($this->Answering);
$ReplyMessage("💢 - لیست پاسخ پاکسازی شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Enemy "]=-=-=-=-=-=-=-=-=-=-=-=

//=-=-=-=-=-=-=-=-=-=-=-=[" List-Enemy "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/enemylist":
if (count($Enemies) > 0) {
$Text = "❌  :";
$counter = 1;
foreach ($Enemies as $ene) {
$me      = $this->getFullinfo($ene);
$me      = $me['User'];
$me_name = $me['first_name'];
$me_id   = $me['id'];
$Text   .= "• $counter » [$me_name](tg://user?id=$me_id) \n";
$counter++;
}
$EditMessage($Text);
} else
$EditMessage("➲ ᴇɴᴇᴍʏ ʟɪsᴛ ɪs ᴇᴍᴘᴛʏ !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Enemy "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/cleanenemylist":
unset($this->Enemies);
$EditMessage("➲ ᴇɴᴇᴍʏ ʟɪsᴛ ɴᴏᴡ ɪs ᴇᴍᴘᴛʏ !");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-7 "]=-=-=//=-=-=//=-=-=//=-=-=//
case $text == "روانی" or $text == "الو تیمارستان":
$EditMessage('🚶🏿‍♀________________🚑');
$EditMessage('🚶🏿‍♀_______________🚑');
$EditMessage('🚶🏿‍♀______________🚑');
$EditMessage('🚶🏿‍♀_____________🚑');
$EditMessage('🚶🏿‍♀____________🚑');
$EditMessage('🚶🏿‍♀___________🚑');
$EditMessage('🚶🏿‍♀__________🚑');
$EditMessage('🚶🏿‍♀_________🚑');
$EditMessage('🚶🏿‍♀________🚑');
$EditMessage('🚶🏿‍♀_______🚑');
$EditMessage('🚶🏿‍♀______🚑');
$EditMessage('🚶🏿‍♀____🚑');
$EditMessage('🚶🏿‍♀___🚑');
$EditMessage('🚶🏿‍♀__🚑');
$EditMessage('🚶🏿‍♀_🚑');
$EditMessage('قان قان گرفتیمش خودع کزخلشع😐🚶‍♂️');
break;
case $text == "ساک":
$EditMessage('🗣 <=====');
$EditMessage('🗣<=====');
$EditMessage('🗣=====');
$EditMessage('🗣====');
$EditMessage('🗣===');
$EditMessage('🗣==');
$EditMessage('🗣===');
$EditMessage('🗣====');
$EditMessage('🗣=====');
$EditMessage('🗣<=====');
$EditMessage('<=====');
$EditMessage('اخ اخ گاز گرفتی ک😐');
break;
case $text == "جق":
$EditMessage('درحال جق....');
$EditMessage('👌🏻<=====');
$EditMessage('<👌🏻=====');
$EditMessage('<=👌🏻====');
$EditMessage('<==👌🏻===');
$EditMessage('<===👌🏻==');
$EditMessage('<==👌🏻===');
$EditMessage('<=👌🏻====');
$EditMessage('<👌🏻=====');
$EditMessage('👌🏻<=====');
$EditMessage('<=👌🏻====');
$EditMessage('<===👌🏻==');
$EditMessage('<=👌🏻====');
$EditMessage('👌🏻<=====');
$EditMessage('<=👌🏻====');
$EditMessage('<==??🏻===');
$EditMessage('<=👌🏻====');
$EditMessage('👌🏻<=====');
$EditMessage('💦💦<=====');
$EditMessage('کمر نموند برامون بمولا😐');
break;
case $text == "عشق":
$EditMessage('🚶‍♀________________🏃‍♂');
$EditMessage('🚶‍♀_______________🏃‍♂');
$EditMessage('🚶‍♀______________🏃‍♂');
$EditMessage('🚶‍♀_____________🏃‍♂');
$EditMessage('🚶‍♀____________🏃‍♂');
$EditMessage('🚶‍♀___________🏃‍♂');
$EditMessage('🚶‍♀__________🏃‍♂');
$EditMessage('🚶‍♀_________🏃‍♂');
$EditMessage('🚶‍♀________🏃‍♂');
$EditMessage('🚶‍♀_______🏃‍♂');
$EditMessage('🚶‍♀______🏃‍♂');
$EditMessage('🚶‍♀____🏃‍♂');
$EditMessage('🚶‍♀___🏃‍♂');
$EditMessage('🚶‍♀__🏃‍♂');
$EditMessage('🚶‍♀_🏃‍♂');
$EditMessage('💞Ｉ ＬＯＶＥ ＹＵＯＥ');
break;
case $text == "کص ننت":
$EditMessage("کــص");
$EditMessage("کــص ن");
$EditMessage("کـــص نـــنـ");
$EditMessage("کـــص نـنـتـ");
$EditMessage("💝 نـنـت");
$EditMessage("☘کـــص نـنـت دیگه☘");
break;
case $text == "ادم فضایی":
$EditMessage("👽                     🔦😼");
$EditMessage("👽                    🔦😼");
$EditMessage("👽                   🔦😼");
$EditMessage("👽                  🔦😼");
$EditMessage("👽                 🔦😼");
$EditMessage("👽                🔦😼");
$EditMessage("👽               🔦😼");
$EditMessage("👽              🔦😼");
$EditMessage("👽             🔦😼");
$EditMessage("👽            🔦😼");
$EditMessage("👽           🔦😼");
$EditMessage("👽          🔦😼");
$EditMessage("👽         ??😼");
$EditMessage("👽        🔦😼");
$EditMessage("👽       🔦😼");
$EditMessage("👽      🔦😼");
$EditMessage("👽     🔦😼");
$EditMessage("👽    🔦😼");
$EditMessage("👽   🔦😼");
$EditMessage("👽  🔦😼");
$EditMessage("👽 🔦😼");
$EditMessage("👽🔦🙀");
break;
case $text == "خخخ":
$EditMessage('😂😂');
$EditMessage('🤣🤣');
$EditMessage('😀');
$EditMessage('😃');
$EditMessage('😄');
$EditMessage('😁');
$EditMessage('😆');
$EditMessage('😅');
$EditMessage('😊');
$EditMessage('🙃');
$EditMessage('😛');
$EditMessage('😝');
$EditMessage('😜');
$EditMessage('🤪');
$EditMessage('😺');
$EditMessage('😹');
$EditMessage('😸');
$EditMessage('😇');
$EditMessage('😂');
$EditMessage('🥳');
break;
case $text == "کل":
for ($i = 1; $i <= 10; $i++) {
$EditMessage("$i");
}
$EditMessage('باختی کونی');
$EditMessage('یک تا ده شمارش خوردی بنری دیه');
break;
case $text == "موشک":
$EditMessage("🌍🚀                                🛸");
$EditMessage("🌍🚀                               🛸");
$EditMessage("🌍🚀                              🛸");
$EditMessage("🌍🚀                             🛸");
$EditMessage("🌍🚀                            🛸");
$EditMessage("🌍🚀                           🛸");
$EditMessage("🌍🚀                          🛸");
$EditMessage("🌍🚀                         🛸");
$EditMessage("🌍🚀                        🛸");
$EditMessage("🌍🚀                       🛸");
$EditMessage("🌍🚀                      🛸");
$EditMessage("🌍🚀                     🛸");
$EditMessage("🌍🚀                   🛸");
$EditMessage("🌍🚀                  🛸");
$EditMessage("🌍🚀                 🛸");
$EditMessage("🌍🚀                🛸");
$EditMessage("🌍🚀               🛸");
$EditMessage("🌍🚀              🛸");
$EditMessage("🌍🚀            🛸");
$EditMessage("🌍🚀           🛸");
$EditMessage("🌍🚀          🛸");
$EditMessage("🌍🚀         🛸");
$EditMessage("🌍🚀        🛸");
$EditMessage("🌍🚀       🛸");
$EditMessage("🌍🚀      🛸");
$EditMessage("🌍🚀     🛸");
$EditMessage("🌍🚀    🛸");
$EditMessage("🌍🚀   🛸");
$EditMessage("🌍🚀  🛸");
$EditMessage("🌍🚀 🛸");
$EditMessage("🌍🚀🛸");
$EditMessage("🌍💥Boom💥");
break;
case $text == "پول":
$EditMessage("🔥            ‌                    💵");
$EditMessage("🔥            ‌                   💵");
$EditMessage("🔥            ‌                 💵");
$EditMessage("🔥            ‌                💵");
$EditMessage("🔥            ‌               💵");
$EditMessage("🔥            ‌              💵");
$EditMessage("🔥            ‌             💵");
$EditMessage("🔥            ‌            💵");
$EditMessage("🔥            ‌           💵");
$EditMessage("🔥            ‌          💵");
$EditMessage("🔥                         💵");
$EditMessage("🔥            ‌        💵");
$EditMessage("🔥            ‌       💵");
$EditMessage("🔥            ‌      💵");
$EditMessage("🔥            ‌     💵");
$EditMessage("🔥            ‌    💵");
$EditMessage("🔥            ‌   💵");
$EditMessage("🔥            ‌  💵");
$EditMessage("🔥            ‌ 💵");
$EditMessage("🔥            ‌💵");
$EditMessage("🔥           💵");
$EditMessage("🔥          💵");
$EditMessage("🔥         💵");
$EditMessage("🔥        💵");
$EditMessage("🔥       💵");
$EditMessage("🔥      💵");
$EditMessage("🔥     💵");
$EditMessage("🔥    💵");
$EditMessage("🔥   💵");
$EditMessage("🔥  💵");
$EditMessage("🔥 💵");
$EditMessage("💸");
break;
case $text == "حالم بده":
$EditMessage("💩               🤢");
$EditMessage("💩              🤢");
$EditMessage("💩             🤢");
$EditMessage("💩            🤢");
$EditMessage("💩           🤢");
$EditMessage("💩          🤢");
$EditMessage("💩         🤢");
$EditMessage("💩        🤢");
$EditMessage("💩       🤢");
$EditMessage("💩      🤢");
$EditMessage("💩     🤢");
$EditMessage("💩    🤢");
$EditMessage("💩   🤢");
$EditMessage("💩  🤢");
$EditMessage("💩 🤢");
$EditMessage("🤮🤮");
break;
case $text == "جن":
$EditMessage("👻                                   🙀");
$EditMessage("👻                                  🙀");
$EditMessage("👻                                 🙀");
$EditMessage("👻                                🙀");
$EditMessage("👻                               🙀");
$EditMessage("👻                              🙀");
$EditMessage("👻                             🙀");
$EditMessage("👻                            🙀");
$EditMessage("👻                           🙀");
$EditMessage("👻                          🙀");
$EditMessage("👻                         🙀");
$EditMessage("👻                        🙀");
$EditMessage("👻                       🙀");
$EditMessage("👻                      🙀");
$EditMessage("👻                     🙀");
$EditMessage("👻                    🙀");
$EditMessage("👻                   🙀");
$EditMessage("👻                  🙀");
$EditMessage("👻                 🙀");
$EditMessage("👻               🙀");
$EditMessage("👻              🙀");
$EditMessage("👻             🙀");
$EditMessage("👻            🙀");
$EditMessage("👻           🙀");
$EditMessage("👻          🙀");
$EditMessage("👻         🙀");
$EditMessage("👻        🙀");
$EditMessage("👻       🙀");
$EditMessage("👻      🙀");
$EditMessage("👻     🙀");
$EditMessage("👻    🙀");
$EditMessage("👻   🙀");
$EditMessage("👻  🙀");
$EditMessage("👻 🙀");
$EditMessage("👻🙀");
$EditMessage("☠بگارف☠");
break;
case $text == "برم خونه":
$EditMessage("🏠              🚶‍♂");
$EditMessage("🏠             🚶‍♂");
$EditMessage("🏠            🚶‍♂");
$EditMessage("🏠           🚶‍♂");
$EditMessage("🏠          🚶‍♂");
$EditMessage("🏠         🚶‍♂");
$EditMessage("🏠        🚶‍♂");
$EditMessage("🏠       🚶‍♂");
$EditMessage("🏠      🚶‍♂");
$EditMessage("🏠     🚶‍♂");
$EditMessage("🏠    🚶‍♂");
$EditMessage("🏠   🚶‍♂");
$EditMessage("🏠  🚶‍♂");
$EditMessage("🏠 🚶‍♂");
$EditMessage("🏠🚶‍♂");
break;
case $text == "قلب":
$EditMessage("❤️🧡💛💚");
$EditMessage("💜💙🖤💛");
$EditMessage("🤍🤎💛💜");
$EditMessage("💚❤️🖤🧡");
$EditMessage("💜💚🧡🖤");
$EditMessage("🤍🧡🤎💜");
$EditMessage("💙🧡💜🧡");
$EditMessage("💚💛💙💜");
$EditMessage("🖤💛💙🤍");
$EditMessage("❣");
break;
case $text == "فرار از خونه":
$EditMessage("🏡 💃");
$EditMessage("🏡  💃");
$EditMessage("🏡   💃");
$EditMessage("🏡    💃");
$EditMessage("🏡     💃");
$EditMessage("🏡      💃");
$EditMessage("🏡       💃");
$EditMessage("🏡        💃");
$EditMessage("🏡         💃");
$EditMessage("🏡          💃");
$EditMessage("🏡           💃");
$EditMessage("🏡            💃");
$EditMessage("🏡              💃💔👫");
$EditMessage("🏡                 🚶‍♀");
$EditMessage("🏡               🚶‍♀");
$EditMessage("🏡             🚶‍♀");
$EditMessage("🏡           🚶‍♀");
$EditMessage("🏡         🚶‍♀");
$EditMessage("🏡       🚶‍♀");
$EditMessage("🏡     🚶‍♀");
$EditMessage("🏡  🚶‍♀");
$EditMessage("🏡🚶‍♀");
break;
case $text == "عقاب":
$EditMessage("🐍                         🦅");
$EditMessage("🐍                      🦅");
$EditMessage("🐍                    🦅");
$EditMessage("🐍                  🦅");
$EditMessage("🐍                🦅");
$EditMessage("🐍               🦅");
$EditMessage("🐍              🦅");
$EditMessage("🐍            🦅");
$EditMessage("🐍           🦅");
$EditMessage("🐍          🦅");
$EditMessage("🐍         🦅");
$EditMessage("🐍        🦅");
$EditMessage("🐍       🦅");
$EditMessage("🐍      🦅");
$EditMessage("🐍     🦅");
$EditMessage("🐍    🦅");
$EditMessage("🐍   🦅");
$EditMessage("🐍 🦅");
$EditMessage("🐍🦅");
$EditMessage("پیشی برد😹");
break;
case $text == "حموم":
$EditMessage("🛁🚪                  🗝🤏");
$EditMessage("🛁🚪                 🗝🤏");
$EditMessage("🛁🚪                🗝🤏");
$EditMessage("🛁🚪              🗝🤏");
$EditMessage("🛁🚪             🗝🤏");
$EditMessage("🛁🚪            🗝🤏");
$EditMessage("🛁🚪           🗝🤏");
$EditMessage("🛁🚪          🗝🤏");
$EditMessage("🛁🚪         🗝🤏");
$EditMessage("🛁🚪        🗝🤏");
$EditMessage("🛁🚪       🗝🤏");
$EditMessage("🛁🚪      🗝🤏");
$EditMessage("🛁🚪     🗝🤏");
$EditMessage("🛁🚪    🗝🤏");
$EditMessage("🛁🚪   🗝🤏");
$EditMessage("🛁🚪  🗝🤏");
$EditMessage("🛁🚪 🗝🤏");
$EditMessage("🛁🚪🗝🤏");
$EditMessage("🛀💦😈");
break;
case $text == "بکشش":
$EditMessage("😂                 • 🔫🤠");
$EditMessage("😂                •  🔫🤠");
$EditMessage("😂               •   🔫🤠");
$EditMessage("😂              •    🔫🤠");
$EditMessage("😂             •     🔫🤠");
$EditMessage("😂            •      🔫🤠");
$EditMessage("😂           •       🔫🤠");
$EditMessage("😂          •        🔫🤠");
$EditMessage("😂         •         🔫🤠");
$EditMessage("😂        •          🔫🤠");
$EditMessage("😂       •           🔫🤠");
$EditMessage("😂      •            🔫🤠");
$EditMessage("😂     •             🔫🤠");
$EditMessage("😂    •              🔫🤠");
$EditMessage("😂   •               🔫🤠");
$EditMessage("😂  •                🔫🤠");
$EditMessage("😂 •                 🔫🤠");
$EditMessage("😂•                  🔫🤠");
$EditMessage("🤯                  🔫 🤠");
$EditMessage("فرد جنایتکار کشته شد :)");
break;
case $text == "مسجد":
$EditMessage("🕌                  🚶‍♂");
$EditMessage("🕌                 🚶‍♂");
$EditMessage("🕌                🚶‍♂");
$EditMessage("🕌               🚶‍♂");
$EditMessage("🕌              ??‍♂");
$EditMessage("🕌             🚶‍♂");
$EditMessage("🕌            🚶‍♂");
$EditMessage("🕌           🚶‍♂");
$EditMessage("🕌          🚶‍♂");
$EditMessage("🕌         🚶‍♂");
$EditMessage("🕌        🚶‍♂");
$EditMessage("🕌       🚶‍♂");
$EditMessage("🕌      🚶‍♂");
$EditMessage("🕌     ??‍♂");
$EditMessage("🕌    🚶‍♂");
$EditMessage("🕌   ??‍♂");
$EditMessage("🕌  🚶‍♂");
$EditMessage("?? 🚶‍♂");
$EditMessage("🕌🚶‍♂");
$EditMessage("اشهدان الا الا الله📢");
break;
case $text == "کوسه":
$EditMessage("🏝┄┅┄┅┄┄┅🏊‍♂┅┄┄┅🦈");
$EditMessage("🏝┄┅┄┅┄┄🏊‍♂┅┄┄🦈");
$EditMessage("🏝┄┅┄┅┄🏊‍♂┅┄🦈");
$EditMessage("🏝┄┅┄┅🏊‍♂┅┄🦈");
$EditMessage("🏝┄┅┄🏊‍♂┅┄🦈");
$EditMessage("🏝┄┅🏊‍♂┅┄🦈");
$EditMessage("🏝┄🏊‍♂┅┄🦈");
$EditMessage("🏝🏊‍♂┅┄🦈");
$EditMessage("اوخیش شانس آوردما :)");
break;
case $text == "بارون":
$EditMessage("☁️                ⚡️");
$EditMessage("☁️               ⚡️");
$EditMessage("☁️              ⚡️");
$EditMessage("☁️             ⚡️");
$EditMessage("☁️            ⚡️");
$EditMessage("☁️           ⚡️");
$EditMessage("☁️          ⚡️");
$EditMessage("☁️         ⚡️");
$EditMessage("☁️        ⚡️");
$EditMessage("☁️       ⚡️");
$EditMessage("☁️      ⚡️");
$EditMessage("☁️     ⚡️");
$EditMessage("☁️    ⚡️");
$EditMessage("☁️   ⚡️");
$EditMessage("☁️  ⚡️");
$EditMessage("☁️ ⚡️");
$EditMessage("⛈");
break;
case $text == "بادکنک":
$EditMessage("🔪                🎈");
$EditMessage("🔪               🎈");
$EditMessage("🔪              🎈");
$EditMessage("🔪             🎈");
$EditMessage("🔪            🎈");
$EditMessage("🔪           🎈");
$EditMessage("🔪          🎈");
$EditMessage("🔪         🎈");
$EditMessage("🔪        🎈");
$EditMessage("🔪       🎈");
$EditMessage("🔪      🎈");
$EditMessage("🔪     🎈");
$EditMessage("🔪    🎈");
$EditMessage("🔪   🎈");
$EditMessage("🔪  🎈");
$EditMessage("🔪 🎈");
$EditMessage("🔪🎈");
$EditMessage("💥Bomm💥");
break;
case $text == "شب خوش":
$EditMessage("🌜              🙃");
$EditMessage("🌜             🙃");
$EditMessage("🌜            🙃");
$EditMessage("🌜           🙃");
$EditMessage("🌜          🙃");
$EditMessage("🌜         🙃");
$EditMessage("🌜        🙃");
$EditMessage("🌜       😕");
$EditMessage("🌜      ☹️");
$EditMessage("🌜     😣");
$EditMessage("🌜    😖");
$EditMessage("🌜   😩");
$EditMessage("🌜  🥱");
$EditMessage("🌜 🥱");
$EditMessage("😴");
break;
case $text == "فیش":
$EditMessage("👺🎣           💳");
$EditMessage("👺🎣          💳");
$EditMessage("👺🎣         💳");
$EditMessage("👺🎣        💳");
$EditMessage("👺🎣      💳");
$EditMessage("👺🎣     💳");
$EditMessage("👺🎣    ??");
$EditMessage("👺🎣   💳");
$EditMessage("👺🎣  💳");
$EditMessage("👺🎣 💳");
$EditMessage("👺🎣💳");
$EditMessage("💵🤑میشورم 100درصد ورمیدارم تبرم نیسم🤑💵");
break;
case $text == "فوتبال":
$EditMessage("👟          ⚽️");
$EditMessage("👟         ⚽️");
$EditMessage("👟        ⚽️");
$EditMessage("👟       ⚽️");
$EditMessage("👟      ⚽️");
$EditMessage("👟     ⚽️");
$EditMessage("👟    ⚽️");
$EditMessage("👟   ⚽️");
$EditMessage("👟  ⚽️");
$EditMessage("👟⚽️");
$EditMessage("👟 ⚽️");
$EditMessage("👟  ⚽️");
$EditMessage("👟   ⚽️");
$EditMessage("👟    ⚽️");
$EditMessage("👟     ⚽️");
$EditMessage("👟      ⚽️");
$EditMessage("👟       ⚽️");
$EditMessage("👟        ⚽️");
$EditMessage("👟         ⚽️");
$EditMessage("👟          ⚽️");
$EditMessage("(توی دروازه🔥)");
break;
case $text == "برم بخابم":
$EditMessage("🛏                🚶🏻");
$EditMessage("🛏               🚶🏻");
$EditMessage("🛏              🚶🏻");
$EditMessage("🛏             🚶🏻");
$EditMessage("🛏            🚶🏻");
$EditMessage("🛏           🚶🏻‍♂️");
$EditMessage("🛏          🚶🏻");
$EditMessage("🛏         🚶🏻");
$EditMessage("🛏        🚶🏻");
$EditMessage("🛏       🚶🏻");
$EditMessage("🛏      🚶🏻");
$EditMessage("🛏     🚶🏻");
$EditMessage("🛏    🚶🏻");
$EditMessage("🛏   🚶🏻");
$EditMessage("🛏  🚶🏻");
$EditMessage("🛏 🚶🏻");
$EditMessage("🛌");
break;
case $text == "غرقش کن":
$EditMessage("🌬🌊              🏄🏻‍♂");
$EditMessage("🌬🌊             🏄🏻‍♂");
$EditMessage("🌬🌊            🏄🏻‍♂");
$EditMessage("🌬🌊           🏄🏻‍♂");
$EditMessage("🌬🌊          🏄🏻‍♂");
$EditMessage("🌬🌊         🏄🏻‍♂");
$EditMessage("🌬🌊        🏄🏻‍♂");
$EditMessage("🌬🌊       🏄🏻‍♂");
$EditMessage("🌬🌊      🏄🏻‍♂");
$EditMessage("🌬🌊     🏄🏻‍♂");
$EditMessage("🌬🌊    🏄🏻‍♂");
$EditMessage("🌬🌊   🏄🏻‍♂");
$EditMessage("🌬🌊  🏄🏻‍♂");
$EditMessage("🌬🌊 🏄🏻‍♂");
$EditMessage("غرق شد🙈");
break;
case $text == "فضانورد":
$EditMessage("🧑‍🚀              🪐");
$EditMessage("🧑‍🚀             🪐");
$EditMessage("🧑‍🚀            🪐");
$EditMessage("🧑‍🚀           🪐");
$EditMessage("🧑‍🚀          🪐");
$EditMessage("🧑‍🚀         🪐");
$EditMessage("🧑‍🚀        🪐");
$EditMessage("🧑‍🚀       🪐");
$EditMessage("🧑‍🚀      🪐");
$EditMessage("🧑‍🚀     🪐");
$EditMessage("🧑‍🚀    🪐");
$EditMessage("🧑‍🚀   🪐");
$EditMessage("🧑‍🚀  🪐");
$EditMessage("🧑‍🚀 🪐");
$EditMessage("🇮🇷من میگم ایران قویه🇮🇷");
break;
case $text == "ایول":
$EditMessage("🤜🏿                       🤛🏻");
$EditMessage("🤜🏻                    🤛🏿");
$EditMessage("🤜🏻                  🤛🏻");
$EditMessage("🤜🏿                   🤛🏻");
$EditMessage("🤜🏻                🤛🏿");
$EditMessage("🤜🏻               🤛🏻");
$EditMessage("🤜🏻              🤛🏻");
$EditMessage("🤜🏿             🤛🏿");
$EditMessage("🤜🏻            🤛🏻");
$EditMessage("🤜🏻           🤛🏻");
$EditMessage("🤜🏿          🤛🏻");
$EditMessage("🤜🏻         🤛🏻");
$EditMessage("🤜🏻        🤛🏿");
$EditMessage("🤜🏻       🤛🏻");
$EditMessage("🤜🏻      🤛🏻");
$EditMessage("🤜🏿     🤛🏻");
$EditMessage("🤜🏻    🤛🏻");
$EditMessage("🤜🏻   🤛🏻");
$EditMessage("🤜🏻  🤛🏻");
$EditMessage("🤜🏻🤛🏿");
break;
case $text == "فیل":
$EditMessage("
░░▄███▄███▄
░░█████████
░░▒▀█████▀░
░░▒░░▀█▀ ");
$EditMessage("
░░▄███▄███▄
░░█████████
░░▒▀█████▀░
░░▒░░▀█▀
░░▒░░█░
░░▒░█
░░░█
░░█░░░░███████
░██░░░██▓▓███▓██▒
██░░░█▓▓▓▓▓▓▓█▓████
██░░██▓▓▓(◐)▓█▓█▓█
███▓▓▓█▓▓▓▓▓█▓█▓▓▓▓█
▀██▓▓█░██▓▓▓▓██▓▓▓▓▓█");
$EditMessage("
░░▄███▄███▄
░░█████████
░░▒▀█████▀░
░░▒░░▀█▀
░░▒░░█░
░░▒░█
░░░█
░░█░░░░███████
░██░░░██▓▓███▓██▒
██░░░█▓▓▓▓▓▓▓█▓████
██░░██▓▓▓(◐)▓█▓█▓█
███▓▓▓█▓▓▓▓▓█▓█▓▓▓▓█
▀██▓▓█░██▓▓▓▓██▓▓▓▓▓█
░▀██▀░░█▓▓▓▓▓▓▓▓▓▓▓▓▓█
░░░░▒░░░█▓▓▓▓▓█▓▓▓▓▓▓█
░░░░▒░░░█▓▓▓▓█▓█▓▓▓▓▓█
░▒░░▒░░░█▓▓▓█▓▓▓█▓▓▓▓█
░▒░░▒░░░█▓▓▓█░░░█▓▓▓█
░▒░░▒░░██▓██░░░██▓▓██");
break;
case $text == "بشمار":
$ReplyMessage("¹");
$ReplyMessage("²");
$ReplyMessage("³");
$ReplyMessage("⁴");
$ReplyMessage("⁵");
$ReplyMessage("⁶");
$ReplyMessage("⁷");
$ReplyMessage("⁸");
$ReplyMessage("⁹");
$ReplyMessage("¹⁰");
$ReplyMessage("sʜᴏᴛ sʜᴏᴅɪ😉");
break;
case $text == "بمیر کرونا":
$EditMessage('🦠  •   •   •   •   •   •   •   •   •   •  🔫');
$EditMessage('🦠  •   •   •   •   •   •   •   •   •   ◀  🔫');
$EditMessage('🦠  •   •   •   •   •   •   •   •   ◀   •  🔫');
$EditMessage('🦠  •   •   •   •   •   •   •   ◀   •   •  🔫');
$EditMessage('🦠  •   •   •   •   •   •   ◀   •   •   •  🔫');
$EditMessage('🦠  •   •   •   •   •   ◀   •   •   •   •  🔫');
$EditMessage('🦠  •   •   •   •   ◀   •   •   •   •   •  🔫');
$EditMessage('🦠  •   •   •   ◀   •   •   •   •   •   •  🔫');
$EditMessage('🦠  •   •   ◀   •   •   •   •   •   •   •  🔫');
$EditMessage('🦠  •   ◀   •   •   •   •   •   •   •   •  🔫');
$EditMessage('🦠  ◀   •   •   •   •   •   •   •   •   •  🔫');
$EditMessage('💥  •   •   •   •   •   •   •   •   •   •  🔫');
$EditMessage('💉💊💉💊💉💊💉💊');
$EditMessage('we wine');
$EditMessage('Corona Is Dead');
$EditMessage('وای کرونارو گاییدیم');
break;
case $text == "انگش":
$EditMessage('🍑________________👈');
$EditMessage('🍑_______________👈');
$EditMessage('🍑______________👈');
$EditMessage('🍑_____________👈');
$EditMessage('🍑____________👈');
$EditMessage('🍑___________👈');
$EditMessage('🍑__________👈');
$EditMessage('🍑_________👈');
$EditMessage('🍑________👈');
$EditMessage('🍑_______👈');
$EditMessage('🍑______👈');
$EditMessage('🍑____👈');
$EditMessage('🍑___👈');
$EditMessage('🍑__👈');
$EditMessage('🍑_👈');
$EditMessage('✌انگشت شد✌');
break;
case $text == "جقیم":
$EditMessage('B=======✊🏻=D');
$EditMessage('B=====✊🏻===D');
$EditMessage('B==✊🏻======D');
$EditMessage('B✊🏻========D');
$EditMessage('B===✊??=====D');
$EditMessage('B=====✊🏻===D');
$EditMessage('B=======✊🏻=D');
$EditMessage('B====✊🏻====D');
$EditMessage('B==✊??======D');
$EditMessage('B✊🏻========D');
$EditMessage('B==✊🏻======D');
$EditMessage('B====✊🏻====D');
$EditMessage('B======✊🏻==D');
$EditMessage('B========✊🏻D');
$EditMessage('B========✊🏻D💦💦');
$EditMessage('کمر نموند برامون بمولا');
break;
case $text == "ریدم":
$EditMessage('🐒
💩









🧑‍🦯');
$EditMessage('🐒

💩








🧑‍🦯');
$EditMessage('🐒


💩






🧑‍🦯');
$EditMessage('🐒



💩





🧑‍🦯');
$EditMessage('🐒




💩




🧑‍🦯');
$EditMessage('🐒






💩


🧑‍🦯');
$EditMessage('🐒







💩

🧑‍🦯');
$EditMessage('🐒








💩
🧑‍🦯');
$EditMessage('چیو نگاه میکنی ریدیم ب هیکل یاروع دیگ😂');
break;
case $text == "مربع":
$EditMessage('
🟥🟥🟥🟥
🟥🟥🟥🟥
🟥🟥🟥🟥
🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥
🟥⬜️⬛️🟥
🟥⬛️⬜️🟥
🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥
🟥⬛️⬜️🟥
🟥⬜️⬛️🟥
🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥⬛️
🟥⬜️⬛️🟥
🟥⬛️⬜️🟥
⬛️🟥🟥🟥');
$EditMessage('
🟥⬜️⬛️🟥
🟥⬛️⬜️🟥
🟥⬜️⬛️🟥
🟥⬛️⬜️🟥');
$EditMessage('
🟥⬛️⬜️🟥
🟥⬜️⬛️🟥
🟥⬛️⬜️🟥
🟥⬜️⬛️🟥');
$EditMessage('
⬜️⬛️⬜️⬛️
⬛️⬜️⬛️⬜️
⬜️⬛️⬜️⬛️
⬛️⬜️⬛️⬜️');
$EditMessage('
⬛️⬜️⬛️⬜️
⬜️⬛️⬜️⬛️
⬛️⬜️⬛️⬜️
⬜️⬛️⬜️⬛️');
$EditMessage('
🟥⬜️⬛️⬜️🟥
🟥⬛️⬜️⬛️🟥
🟥⬜️⬛️⬜️🟥
🟥⬛️⬜️⬛️🟥
🟥⬜️⬛️⬜️🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥
🟥🟨🟨🟨🟨🟨🟥
🟥🟩🟩🟩🟩🟩🟥
🟥⬛️⬛️⬛️⬛️⬛️🟥
🟥🟦🟦🟦🟦🟦🟥
🟥⬜️⬜️⬜️⬜️⬜️🟥
🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥
🟥💚💚💚💚💚🟥
🟥💙💙💙💙💙🟥
🟥❤️❤️❤️❤️❤️🟥
🟥💖💖💖💖💖🟥
🟥🤍🤍🤍🤍🤍🟥
🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥
🟥▫️◼️▫️◼️▫️🟥
🟥◼️▫️◼️▫️◼️🟥
🟥◽️◼️◽️◼️◽️🟥
🟥◼️◽️◼️◽️◼️🟥
🟥◽️◼️◽️◼️◽️🟥
🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥
🟥🔶🔷🔶🔷🔶🟥
🟥🔷🔶🔷🔶🔷🟥
🟥🔶🔷🔶🔷🔶🟥
🟥🔷🔶🔷🔶🔷🟥
🟥🔶🔷🔶🔷🔶🟥
🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥
🟥♥️❤️♥️❤️♥️🟥
🟥❤️♥️❤️♥️❤️🟥
🟥♥️❤️♥️❤️♥️🟥
🟥❤️♥️❤️♥️❤️🟥
🟥♥️❤️♥️❤️♥️🟥
🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('💙💙💙💙');
$EditMessage('❣️I Love❣️');
break;
case $text == "دیک":
$EditMessage('.                      💦💦💦
.                    💦💦💦💦
                   💦💦💦💦💦
                 💦💦💦💦💦💦
                 💦💦💦  💦💦💦
                 💦💦💦        💦💦
                  ◼️◼️◼️         💦💦
           ◼️📜◼️📜◼️     💦💦
     ◼️📜📜◼️📜📜◼️   💦
     ◼️📜📜📜📜📜◼️     💦
           ◼️◼️◼️◼️◼️          💦
           ◼️📜📜📜◼️          💦
           ◼️📜📜📜◼️       💦
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️‌
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
           ◼️📜📜📜◼️
     ◼️📜📜📜📜📜◼️
◼️📜📜📜📜📜📜📜◼️
◼️📜📜📜◼️📜📜📜◼️
     ◼️◼️◼️     ◼️◼️◼️');
break;
case $text == "فاک":
$EditMessage('🖕🏿🖕🖕🖕🖕🖕');
$EditMessage('🖕🖕🏿🖕🖕🖕🖕');
$EditMessage('🖕🖕🖕🏿🖕🖕🖕');
$EditMessage('🖕🖕🖕🖕🏿🖕🖕');
$EditMessage('🖕🖕🖕🖕🖕🏿🖕');
$EditMessage('🖕🖕🖕🖕🖕🖕🏿');
$EditMessage('🖕🖕🖕🖕🖕🏾🖕');
$EditMessage('🖕🖕🖕🖕🏿🖕🖕');
$EditMessage('🖕🖕🖕🏿🖕🖕🖕');
$EditMessage('🖕🖕🏿🖕🖕🖕🖕');
$EditMessage('🖕🏿🖕🖕🖕🖕🖕');
$EditMessage('🖕🖕🏿🖕🖕🏿🖕🖕🏿');
$EditMessage('🖕🏿🖕🖕🏿🖕🖕🏿🖕');
$EditMessage('🖕🖕🖕🖕🖕🖕');
$EditMessage('🖕🏿🖕🏿🖕🏿🖕🏿🖕🏿🖕🏿');
$EditMessage('🖤fucking you🖤');
break;
case $text == "ساعت":
$EditMessage('
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛');
$EditMessage('
🕐🕐🕐🕐🕐
🕐🕐🕐🕐🕐
🕐🕐🕐🕐🕐
🕐🕐🕐🕐🕐
🕐🕐🕐🕐🕐');
$EditMessage('
🕑🕑🕑🕑🕑
🕑🕑🕑🕑🕑
🕑🕑🕑🕑🕑
🕑🕑🕑🕑🕑
🕑🕑🕑🕑🕑');
$EditMessage('
🕒🕒🕒🕒🕒
🕒🕒🕒🕒🕒
🕒🕒🕒🕒🕒
🕒🕒🕒🕒🕒
🕒🕒🕒🕒🕒');
$EditMessage('
🕔🕔🕔🕔🕔
🕔🕔🕔🕔🕔
🕔🕔🕔🕔🕔
🕔🕔🕔🕔🕔
🕔🕔🕔🕔🕔');
$EditMessage('
🕕🕕🕕🕕🕕
🕕🕕🕕🕕🕕
🕕🕕🕕🕕🕕
🕕🕕🕕🕕🕕
🕕🕕🕕🕕🕕');
$EditMessage('
🕖🕖🕖🕖🕖
🕖🕖🕖🕖🕖
🕖🕖🕖🕖🕖
🕖🕖🕖🕖🕖
🕖🕖🕖🕖🕖');
$EditMessage('
🕗🕗🕗🕗🕗
🕗🕗🕗🕗🕗
🕗🕗🕗🕗🕗
🕗🕗🕗🕗🕗
🕗🕗🕗🕗🕗');
$EditMessage('
🕙🕙🕙🕙🕙
🕙🕙🕙🕙🕙
🕙🕙🕙🕙🕙
🕙🕙🕙🕙🕙
🕙🕙🕙🕙🕙');
$EditMessage('
🕚🕚🕚🕚🕚
🕚🕚🕚🕚🕚
🕚🕚🕚🕚🕚
🕚🕚🕚🕚🕚
🕚🕚🕚🕚🕚');
$EditMessage('
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛
🕛🕛🕛🕛🕛');
$EditMessage('⏰⏰⏰⏰⏰');
break;
case $text == "تایم":
for ($i = 1; $i <= 60; $i++) {
$EditMessage(date('H:i:s'));
}
break;
case
$text == "برگام" or $text == "پشم" or $text == "پشمام":
$EditMessage('🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂');
$EditMessage('🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁');
$EditMessage('🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃');
$EditMessage('🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿');
$EditMessage('🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱');
$EditMessage('☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️');
$EditMessage('🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀️');
$EditMessage('پشم دیگه ندارم ولی برگام ریخت بمولا');
$EditMessage('🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂');
$EditMessage('🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱');
$EditMessage('🍂🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿');
$EditMessage('☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️');
$EditMessage('🍂🍁🌱🌿🍂🍁🌱🌿🍂🍁🌱🌿🍂🍁🌱🌿');
$EditMessage('🍃🍂🍁🌱🌿☘️🍀🍃🍁🍂🌿🌱☘️🍀🍃');
$EditMessage('دیگه برگی برام نمونده ');
$EditMessage('پشمام ریخ ☹');
break;
case $text == "رقص مربع" or $text == "دنس":
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟪🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟪🟪🟪🟧🟧🟧
🟧🟧🟧🟪🟧🟪🟧🟧🟧
🟧🟧🟧🟪🟪🟪🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟪🟪🟪🟪🟪🟧🟧
🟧🟧🟪🟧🟧🟧🟪🟧🟧
🟧🟧🟪🟧🟦🟧🟪🟧🟧
🟧🟧🟪🟧🟧🟧🟪🟧🟧
🟧🟧🟪🟪🟪🟪🟪🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟪🟪🟪🟪🟪🟪🟪🟧
🟧🟪🟧🟧🟧🟧🟧🟪🟧
🟧🟪🟧🟦🟦🟦🟧🟪🟧
🟧🟪🟧🟦🟧🟦🟧🟪🟧
🟧🟪🟧🟦🟦🟦🟧🟪🟧
🟧🟪🟧🟧🟧🟧🟧🟪🟧
🟧🟪🟪🟪🟪🟪🟪🟪🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟪🟪🟪🟪🟪🟪🟪🟪🟪
🟪🟧🟧🟧🟧🟧🟧🟧🟪
🟪🟧🟦🟦🟦🟦🟦🟧🟪
🟪🟧🟦🟧🟧🟧🟦🟧🟪
🟪🟧🟦🟧⬜️🟧🟦🟧🟪
🟪🟧🟦🟧🟧🟧🟦🟧🟪
🟪🟧🟦🟦🟦🟦🟦🟧🟪
🟪🟧🟧🟧🟧🟧🟧🟧🟪
🟪🟪🟪🟪🟪🟪🟪🟪🟪');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧🟦🟦🟦🟦🟦🟦🟦🟧
🟧🟦🟧🟧🟧🟧🟧🟦🟧
🟧🟦🟧⬜️⬜️⬜️🟧🟦🟧
🟧🟦🟧⬜️⬜️⬜️🟧🟦🟧
🟧🟦🟧⬜️⬜️⬜️🟧🟦🟧
🟧🟦🟧🟧🟧🟧🟧🟦🟧
🟧🟦🟦🟦🟦🟦🟦🟦🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
🟦🟦🟦🟦🟦🟦🟦🟦🟦
🟦🟧🟧🟧🟧🟧🟧🟧🟦
🟦🟧⬜️⬜️⬜️⬜️⬜️🟧🟦
🟦🟧⬜️⬜️⬜️⬜️⬜️🟧🟦
🟦🟧⬜️⬜️⬜️⬜️⬜️🟧🟦
🟦🟧⬜️⬜️⬜️⬜️⬜️🟧🟦
🟦🟧⬜️⬜️⬜️⬜️⬜️🟧🟦
🟦🟧🟧🟧🟧🟧🟧🟧🟦
🟦🟦🟦🟦🟦🟦🟦🟦🟦');
$EditMessage('
🟧🟧🟧🟧🟧🟧🟧🟧🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧⬜️⬜️⬜️⬜️⬜️⬜️⬜️🟧
🟧🟧🟧🟧🟧🟧🟧🟧🟧');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜️🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥⬜⬜⬜⬜⬜⬜⬜⬜🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥⬜⬜⬜⬜⬜⬜🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥⬜⬜⬜⬜️🟥🟥🟥
🟥🟥🟥⬜⬜⬜⬜🟥🟥🟥
🟥🟥🟥⬜⬜⬜⬜🟥🟥🟥
🟥🟥🟥⬜⬜⬜⬜🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥⬜️⬜️🟥🟥🟥🟥
🟥🟥🟥🟥⬜⬜️🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥💙💙🟥🟥🟥🟥
🟥🟥🟥🟥💙💙🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟦🟦🟥🟥🟥🟥
🟥🟥🟥🟥🟦🟦🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟦🟦🟦🟦🟥🟥🟥
🟥🟥🟥🟦🟦🟦🟦🟥🟥🟥
🟥🟥🟥🟦🟦🟦🟦🟥🟥🟥
🟥🟥🟥🟦🟦🟦🟦🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟨🟨🟨🟨🟨🟨🟥🟥
🟥🟥🟨🟦🟦🟦🟦🟨🟥🟥
🟥🟥🟨🟦🟦🟦🟦🟨🟥🟥
🟥🟥🟨🟦🟦🟦🟦🟨🟥🟥
🟥🟥🟨🟦🟦🟦🟦🟨🟥🟥
🟥🟥🟨🟨🟨🟨🟨🟨🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟨🟨🟨🟨🟨🟨🟨🟨🟥
🟥🟨🟨🟨🟨🟨🟨🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟨🟨🟨🟨🟨🟨🟥
🟥🟨🟨🟨🟨🟨🟨🟨🟨🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟨🟨🟨🟨🟨🟨🟪🟥
🟥🟨🟪🟨🟨🟨🟨🟪🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟨🟦🟦🟦🟦🟨🟨🟥
🟥🟨🟪🟨🟨🟨🟨🟪🟨🟥
🟥🟪🟨🟨🟨🟨🟨🟨🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟨🟨🟨🟨🟨🟨🟪🟥
🟥🟪🟪🟨🟨🟨🟨🟪🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟪🟨🟨🟨🟨🟪🟪🟥
🟥🟪🟨🟨🟨🟨🟨🟨🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪🟨🟨🟨🟨🟪🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟨🟦🟦🟦🟦🟨🟪🟥
🟥🟪🟪🟨🟨🟨🟨🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟧🟦🟦🟦🟦🟧🟪🟥
🟥🟪🟧🟦🟦🟦🟦🟧🟪🟥
🟥🟪🟧🟦🟦🟦🟦🟧🟪🟥
🟥🟪🟧🟦🟦🟦🟦🟧🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟧🟨🟦🟦🟨🟧🟪🟥
🟥🟪🟧🟦🟨🟨🟦🟧🟪🟥
🟥🟪🟧🟦🟨🟨🟦🟧🟪🟥
🟥🟪🟧🟨🟦🟦🟨🟧🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟧💛🟦🟦💛🟧🟪🟥
🟥🟪🟧🟦💛💛🟦🟧🟪🟥
🟥🟪🟧🟦💛💛🟦🟧🟪🟥
🟥🟪🟧💛🟦🟦💛🟧🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟧💛💙💙💛🟧🟪🟥
🟥🟪🟧💙💛💛💙🟧🟪🟥
🟥🟪🟧💙💛💛💙🟧🟪🟥
🟥🟪🟧💛💙💙💛🟧🟪🟥
🟥🟪🟪⬛️⬛️⬛️⬛️🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟪🟪🖤🖤🖤🖤🟪🟪🟥
🟥🟪🟧💛💙💙💛🟧🟪🟥
🟥🟪🟧💙💛💛💙🟧🟪🟥
🟥🟪🟧💙💛💛💙🟧🟪🟥
🟥🟪🟧💛💙💙💛🟧🟪🟥
🟥🟪🟪🖤🖤🖤🖤🟪🟪🟥
🟥🟪🟩🟩🟩🟩🟩🟩🟪🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥💜🟩🟩🟩🟩🟩🟩💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜🟧💛💙💙💛🟧💜🟥
🟥💜🟧💙💛💛💙🟧💜🟥
🟥💜🟧💙💛💛💙🟧💜🟥
🟥💜🟧💛💙💙💛🟧💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜🟩🟩🟩🟩🟩🟩💜🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥💜🟩🟩🟩🟩🟩🟩💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜🧡💛💙💙💛🧡💜🟥
🟥💜🧡💙💛💛💙🧡💜🟥
🟥💜🧡💙💛💛💙🧡💜🟥
🟥💜🧡💛💙💙💛🧡💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜🟩🟩🟩🟩🟩🟩💜🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥
🟥💜💚💚💚💚💚💚💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜🧡💛💙💙💛🧡💜🟥
🟥💜🧡💙💛💛💙🧡💜🟥
🟥💜🧡💙💛💛💙🧡💜🟥
🟥💜🧡💛💙💙💛🧡💜🟥
🟥💜💜🖤🖤🖤🖤💜💜🟥
🟥💜💚💚💚💚💚💚💜🟥
🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥');
$EditMessage('
❤️❤️❤️❤️❤️❤️❤️❤️❤️❤️
❤️💜💚💚💚💚💚💚💜❤️
❤️💜💜🖤🖤🖤🖤💜💜❤️
❤️💜🧡💛💙💙💛🧡💜❤️
❤️💜🧡💙💛💛💙🧡💜❤️
❤️💜🧡💙💛💛💙🧡💜❤️
❤️💜🧡💛💙💙💛🧡💜❤️
❤️💜💜🖤🖤🖤🖤💜💜❤️
❤️💜💚💚💚💚💚💚💜❤️
❤️❤️❤️❤️❤️❤️❤️❤️❤️❤️');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️⬜️⬜️◻️◽️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️⬜️◻️◽️▫️
⬜️⬜️⬜️⬜️⬜️⬜️◻️◽️◽️
⬜️⬜️⬜️⬜️⬜️⬜️◻️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️⬜️⬜️◻️◽️▫️▫️
⬜️⬜️⬜️⬜️⬜️◻️◽️▫️▫️
⬜️⬜️⬜️⬜️⬜️◻️◽️◽️◽️
⬜️⬜️⬜️⬜️⬜️◻️◻️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️⬜️◻️◽️▫️▫️▫️
⬜️⬜️⬜️⬜️◻️◽️▫️▫️▫️
⬜️⬜️⬜️⬜️◻️◽️▫️▫️▫️
⬜️⬜️⬜️⬜️◻️◽️◽️◽️◽️
⬜️⬜️⬜️⬜️◻️◻️◻️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️⬜️◻️◽️▫️▫️▫️▫️
⬜️⬜️⬜️◻️◽️▫️▫️▫️▫️
⬜️⬜️⬜️◻️◽️▫️▫️▫️▫️
⬜️⬜️⬜️◻️◽️▫️▫️▫️▫️
⬜️⬜️⬜️◻️◽️◽️◽️◽️◽️
⬜️⬜️⬜️◻️◻️◻️◻️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️⬜️◻️◽️▫️▫️▫️▫️▫️
⬜️⬜️◻️◽️▫️▫️▫️▫️▫️
⬜️⬜️◻️◽️▫️▫️▫️▫️▫️
⬜️⬜️◻️◽️▫️▫️▫️▫️▫️
⬜️⬜️◻️◽️▫️▫️▫️▫️▫️
⬜️⬜️◻️◽️◽️◽️◽️◽️◽️
⬜️⬜️◻️◻️◻️◻️◻️◻️◻️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️▫️▫️▫️▫️▫️▫️
⬜️◻️◽️◽️◽️◽️◽️◽️◽️
⬜️◻️◻️◻️◻️◻️◻️◻️◽️
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜');
$EditMessage('
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️▫️▫️▫️▫️▫️▫️▫️
◻️◽️◽️◽️◽️◽️◽️◽️◽️
◻️◻️◻️◻️◻️◻️◻️◻️◻️');
$EditMessage('
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️▫️▫️▫️▫️▫️▫️▫️▫️
◽️◽️◽️◽️◽️◽️◽️◽️◽');
$EditMessage('
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️▫️▫️▫️▫️');
break;
case $text == "رقص":
$EditMessage('
🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥
🟥🔲🔳🔲🟥
🟥🟥🟥🟥🟥
🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥
🟥🟥🔲🟥🟥
🟥🟥🔳🟥🟥
🟥🟥🔲🟥🟥
🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥
🟥🟥🟥🔲🟥
🟥🟥🔳🟥🟥
🟥🔲🟥🟥🟥
🟥🟥🟥🟥🟥');
$EditMessage('
🟥🟥🟥🟥🟥
🟥🔲🟥🟥🟥
🟥🟥🔳🟥🟥
🟥🟥🟥🔲🟥
🟥🟥🟥🟥🟥');
$EditMessage('
🟪🟪🟪🟪🟪
🟪🟪🟪🟪🟪
🟪🔲🔳🔲🟪
🟪🟪🟪🟪🟪
🟪🟪🟪🟪🟪');
$EditMessage('
🟪🟪🟪🟪🟪
🟪🟪🔲🟪🟪
🟪🟪🔳🟪🟪
🟪🟪🔲🟪🟪
🟪🟪🟪🟪🟪');
$EditMessage('
🟪🟪🟪🟪🟪
🟪🟪🟪🔲🟪
🟪🟪🔳🟪🟪
🟪🔲🟪🟪🟪
🟪🟪🟪🟪🟪');
$EditMessage('
🟪🟪🟪🟪🟪
🟪🔲🟪🟪🟪
🟪🟪🔳🟪🟪
🟪🟪🟪🔲🟪
🟪🟪🟪🟪🟪');
$EditMessage('
🟦🟦🟦🟦🟦
🟦🟦🟦🟦🟦
🟦🔲🔳🔲🟦
🟦🟦🟦🟦🟦
🟦🟦🟦🟦🟦');
$EditMessage('
🟦🟦🟦🟦🟦
🟦🟦🔲🟦🟦
🟦🟦🔳🟦🟦
🟦🟦🔲🟦🟦
🟦🟦🟦🟦🟦');
$EditMessage('
🟦🟦🟦🟦🟦
🟦🟦🟦🔲🟦
🟦🟦🔳🟦🟦
🟦🔲🟦🟦🟦
🟦🟦🟦🟦🟦');
$EditMessage('
🟦🟦🟦🟦🟦
🟦🔲🟦🟦🟦
🟦🟦🔳🟦🟦
🟦🟦🟦🔲🟦
🟦🟦🟦🟦🟦');
$EditMessage('
◻️🟩🟩◻️◻️
◻️◻️🟩◻️🟩
🟩🟩🔳🟩🟩
🟩◻️🟩◻️◻️
◻️◻️🟩🟩◻️');
$EditMessage('
🟩⬜️⬜️🟩⬜️
🟩🟩⬜️🟩⬜️
⬜️⬜️🔲⬜️⬜️
⬜️🟩⬜️🟩🟩
🟩🟩⬜️⬜️🟩');
$EditMessage('▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️
▫️▫️▫️▫️▫️');
$EditMessage('🌹entire🌹');
break;
case $text == "خار":
$EditMessage('🌵ــــــــــــــــــــــــــــــــــــــــ 🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــــ🎈');
$EditMessage('🌵ـــــــــــــــ🎈');
$EditMessage('🌵ــــــــــــ🎈');
$EditMessage('🌵ــــــــــ🎈');
$EditMessage('🌵ـــــــــ🎈');
$EditMessage('🌵ــــــــ🎈');
$EditMessage('🌵ــــــ🎈');
$EditMessage('🌵ــــ🎈');
$EditMessage('🌵ـــ🎈');
$EditMessage('🌵ــ🎈');
$EditMessage('🌵ـ🎈');
$EditMessage('🌵💥🎈');
$EditMessage('💥Bommmm💥');
break;
case $text == "گلب":
$EditMessage('💚💛🧡❤️');
$EditMessage('💙💚💜🖤');
$EditMessage('❤️🤍🧡💚');
$EditMessage('🖤💜💙💚');
$EditMessage('🤍🤎❤️💙');
$EditMessage('🖤💜💚💙');
$EditMessage('💝💘💗💘');
$EditMessage('❤️🤍🤎🧡');
$EditMessage('💕💞💓🤍');
$EditMessage('💜💙❤️🤍');
$EditMessage('💙💜💙💚');
$EditMessage('🧡💚🧡💙');
$EditMessage('💝💜💙❤️');
$EditMessage('💞🖤💙💚');
$EditMessage('💛🧡❤️💚');
$EditMessage('😍Im crazy about you😍');
break;
case $text == "اها":
$EditMessage(':/');
$EditMessage(':|');
$EditMessage(':(');
$EditMessage(':)');
$EditMessage(':/');
$EditMessage(':|');
$EditMessage(':(');
$EditMessage(':)');
break;
case $text == "ماشین":
$EditMessage('💣________________🏎');
$EditMessage('💣_______________🏎');
$EditMessage('💣______________🏎');
$EditMessage('💣_____________🏎');
$EditMessage('💣____________🏎');
$EditMessage('💣___________🏎');
$EditMessage('💣__________🏎');
$EditMessage('💣_________🏎');
$EditMessage('💣________🏎');
$EditMessage('💣_______🏎');
$EditMessage('💣______🏎');
$EditMessage('💣____🏎');
$EditMessage('💣___🏎');
$EditMessage('💣__🏎');
$EditMessage('💣_🏎');
$EditMessage('💥BOOM💥');
break;
case $text == "موتور":
$EditMessage('🚧___________________🛵');
$EditMessage('🚧_________________🛵');
$EditMessage('🚧_______________🛵');
$EditMessage('🚧_____________🛵');
$EditMessage('🚧___________🛵');
$EditMessage('🚧_________🛵');
$EditMessage('🚧_______🛵');
$EditMessage('🚧_____🛵');
$EditMessage('🚧____🛵');
$EditMessage('🚧__🛵');
$EditMessage('🚧_🛵');
$EditMessage('🚧🛵');
$EditMessage('وای تصادف شد');
$EditMessage('وای موتورم بـگا رف');
$EditMessage('ریدم تو موتورم');
$EditMessage('💥BOOM💥');
break;
case $text == "پنالتی":
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️





😐          ⚽️
👕
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️




😐
👕       ⚽️
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️




😐
👕 ⚽️
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️



⚽️
😐
👕
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️

⚽️


😐
👕
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⚽️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️




😐
👕
👖
////////////////////
");
$EditMessage("
////////////////////
⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⚽️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️



💭Gooooooooolllllllll
😐
👕
👖
////////////////////
");
break;
case $text == "تانک":
$EditMessage(".        (҂_´)
         <,︻╦̵̵ ╤─ ҉     ~  •
█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●");
$EditMessage(".        (҂_´)
         <,︻╦̵̵ ╤─ ҉     ~  •
█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●
▂▄▅█████████▅▄▃▂…");
$EditMessage(".        (҂_´)
         <,︻╦̵̵ ╤─ ҉     ~  •
█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●●
▂▄▅█████████▅▄▃▂…
[███████████████████]");
$EditMessage(".        (҂_´)
         <,︻╦̵̵ ╤─ ҉     ~  •
█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●●●●
▂▄▅█████████▅▄▃▂…
[███████████████████]
◥⊙▲⊙▲⊙▲⊙▲⊙▲⊙▲⊙");
$EditMessage("تانک رو دیدی؟؟🤔");
$EditMessage("دیگه نمیبینی😆");
$EditMessage("💥🔥بوم💥🔥");
$EditMessage(".        (҂`_´)
         <,︻╦̵̵ ╤─ ҉     ~  •
█۞███████]▄▄▄▄▄▄▄▄▄▄▃ 💥●●●●●●●●●●●
▂▄▅█████████▅▄▃▂…
[███████████████████]
◥⊙▲⊙▲⊙▲⊙▲⊙▲⊙▲⊙");
break;
case $text == "بکش":
$EditMessage("😐                       •🔫");
$EditMessage("😐                     • 🔫");
$EditMessage("😐                   •   🔫");
$EditMessage("😐                •     🔫");
$EditMessage("😐              •       🔫");
$EditMessage("😐            •         🔫");
$EditMessage("😐           •          🔫");
$EditMessage("😐         •            🔫");
$EditMessage("😐       •              🔫");
$EditMessage("😐     •🔫");
$EditMessage("😐   •  🔫");
$EditMessage("😐 •    🔫");
$EditMessage("😐•     🔫");
$EditMessage("😵       🔫😏");
break;
case $text == "کون":
$EditMessage("⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⠄⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⠄⣱⣾⣿⣿⣿⣿⣿⣿⠄
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣦⠙⡅⣿⠚⣡⣴⣿⣿⣿⡆⠄
⠄⠄⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⠄⣱⣾⣿⣿⣿⣿⣿⣿⠄
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣶⣌⡛⢿⣽⢘⣿⣷⣿⡻⠏⣛⣀⠄⠄
⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣦⠙⡅⣿⠚⣡⣴⣿⣿⣿⡆⠄
⠄⠄⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⠄⣱⣾⣿⣿⣿⣿⣿⣿⠄
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⠄⠄⢹⠣⣛⣣⣭⣭⣭⣁⡛⠻⢽⣿⣿⣿⣿⢻⣿⣿⣿⣽⡧⡄⠄⠄⠄
⠄⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣶⣌⡛⢿⣽⢘⣿⣷⣿⡻⠏⣛⣀⠄⠄
⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣦⠙⡅⣿⠚⣡⣴⣿⣿⣿⡆⠄
⠄⠄⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⠄⣱⣾⣿⣿⣿⣿⣿⣿⠄
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
$EditMessage("
⠄⠄⠸⣿⣿⢣⢶⣟⣿⣖⣿⣷⣻⣮⡿⣽⣿⣻⣖⣶⣤⣭⡉⠄⠄⠄⠄⠄
⠄⠄⠄⢹⠣⣛⣣⣭⣭⣭⣁⡛⠻⢽⣿⣿⣿⣿⢻⣿⣿⣿⣽⡧⡄⠄⠄⠄
⠄⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣶⣌⡛⢿⣽⢘⣿⣷⣿⡻⠏⣛⣀⠄⠄
⠄⠄⠄⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣦⠙⡅⣿⠚⣡⣴⣿⣿⣿⡆⠄
⠄⠄⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⠄⣱⣾⣿⣿⣿⣿⣿⣿⠄
⠄⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⢸⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠣⣿⣿⣿⣿⣿⣿⣿⣿⣿⠄
⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠑⣿⣮⣝⣛⠿⠿⣿⣿⣿⣿⠄
⢠⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⠄⠄⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⣿⡟⠄
⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠇⠄⠄⠄⠄⢹⣿⣿⣿⣿⣿⣿⣿⣿⠁⠄
⣸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠄⠄⠄⠄⠄⠸⣿⣿⣿⣿⣿⡿⢟⣣⣀");
break;
case $text == "بیا بالا":
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
— — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
- — — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
-- — — — — — — — — — — — —");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
— — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
- — — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
-- — — — — — — — — — — — —");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
— — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
- — — — — — — — — — — — — ");
$EditMessage("
.          ▄▌▐▀▀▀▀▀▀▀▀▀▀▀▀▀▌
.  ▄▄ █ gan gan bia bala brim
███▌█▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▌
▀(@)▀▀▀(@)(@)▀▀▀(@)▀
-- — — — — — — — — — — — —");
break;
case $text == "فاکک":
$EditMessage("              \             \ ' ");

$EditMessage("            \              (
              \             \ ' ");

$EditMessage("          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("          /'/   /    /  /     /¨¯\
        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("             /´¯/'   '/´¯¯•¸
          /'/   /    /  /     /¨¯\
        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("                     /    /
             /´¯/'   '/´¯¯•¸
          /'/   /    /  /     /¨¯\
        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage("                        /   /
                     /    /
             /´¯/'   '/´¯¯•¸
          /'/   /    /  /     /¨¯\
        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");

$EditMessage(" .                        /¯)
                        /   /
                     /    /
             /´¯/'   '/´¯¯•¸
          /'/   /    /  /     /¨¯\
        ('(   (   (   (  ¯~/'  ' /
         \                         /
          \                _.•´
            \              (
              \             \ ' ");
break;
case $text == "قلب2":
$EditMessage('
❤️❤️❤️❤️❤️❤️
❤️❤️❤️❤️❤️❤️
❤️❤️💛💛❤️❤️
❤️❤️💛💛❤️❤️
❤️❤️❤️❤️❤️❤️
❤️❤️❤️❤️❤️❤️');
$EditMessage('
❤️❤️❤️❤️❤️❤️
❤️💚💚💚💚❤️
❤️💚💛💛💚❤️
❤️💚💛💛💚❤️
❤️💚💚💚💚❤️
❤️❤️❤️❤️❤️❤️');
$EditMessage('
💙💙💙💙💙💙
💙💚💚💚💚💙
💙💚💛💛💚💙
💙💚💛💛💚💙
💙💚💚💚💚💙
💙💙💙💙💙💙');
$EditMessage('
💙💙💙💙💙💙
💙🖤🖤🖤🖤💙
💙🖤💛💛🖤💙
💙🖤💛💛🖤💙
💙🖤🖤🖤🖤💙
💙💙💙💙💙💙');
$EditMessage('
💙💙💙💙💙💙
💙🖤🖤🖤🖤💙
💙🖤🤍🤍🖤💙
💙🖤🤍🤍🖤💙
💙🖤🖤🖤🖤💙
💙💙💙💙💙💙');
$EditMessage('
💔💔💔💔💔💔
💔🖤🖤🖤🖤💔
💔🖤🤍🤍🖤💔
💔🖤🤍🤍🖤💔
💔🖤🖤🖤🖤💔
💔💔💔💔💔💔');
$EditMessage('
❤️❤️❤️❤️❤️❤️
❤️❤️❤️❤️❤️❤️
❤️❤️💛💛❤️❤️
❤️❤️💛💛❤️❤️
❤️❤️❤️❤️❤️❤️
❤️❤️❤️❤️❤️❤️');
$EditMessage('
❤️❤️❤️❤️❤️❤️
❤️💚💚💚💚❤️
❤️💚💛💛💚❤️
❤️💚💛💛💚❤️
❤️💚💚💚💚❤️
❤️❤️❤️❤️❤️❤️');
$EditMessage('
💙💙💙💙💙💙
💙💚💚💚💚💙
💙💚💛💛💚💙
💙💚💛💛💚💙
💙💚💚💚💚💙
💙💙💙💙💙💙');
$EditMessage('
💙💙💙💙💙💙
💙🖤🖤🖤🖤💙
💙🖤💛💛🖤💙
💙🖤💛💛🖤💙
💙🖤🖤🖤🖤💙
💙💙💙💙💙💙');
$EditMessage('
💙💙💙💙💙💙
💙🖤🖤🖤🖤💙
💙🖤🤍🤍🖤💙
💙🖤🤍🤍🖤💙
💙🖤🖤🖤🖤💙
💙💙💙💙💙💙');
$EditMessage('
💔💔💔💔💔💔
💔🖤🖤🖤🖤💔
💔🖤🤍🤍🖤💔
💔🖤🤍🤍🖤💔
💔🖤🖤🖤🖤💔
💔💔💔💔💔💔');
$EditMessage('
🖤🖤🖤🖤
🖤🤍🤍🖤
🖤🤍🤍🖤
🖤🖤🖤🖤');
$EditMessage('🤍');
$EditMessage('❤️');
break;
case $text == "لامپ":
$EditMessage('💡                 ⚡');
$EditMessage('💡                ⚡');
$EditMessage('💡               ⚡');
$EditMessage('💡              ⚡');
$EditMessage('💡             ⚡');
$EditMessage('💡            ⚡');
$EditMessage('💡           ⚡');
$EditMessage('💡          ⚡');
$EditMessage('💡         ⚡');
$EditMessage('💡        ⚡');
$EditMessage('💡       ⚡');
$EditMessage('💡      ⚡');
$EditMessage('💡     ⚡');
$EditMessage('💡    ⚡');
$EditMessage('💡   ⚡');
$EditMessage('💡  ⚡');
$EditMessage('💡 ⚡');
$EditMessage('💡⚡');
$EditMessage('💡');
$ReplyMessage('با رعد و برق لامپ روشن کردیم😐، پشمای فیزیک بمولا😅');
break;
case $text == "شب":
$EditMessage('🌕');
$EditMessage('🌔');
$EditMessage('🌖');
$EditMessage('🌓');
$EditMessage('🌓');
$EditMessage('🌒');
$EditMessage('🌘');
$EditMessage('🌑');
break;
case $text == "بگاش بده":
$EditMessage('فاضلاب شمال شرق تهران تو کص ننت');
$EditMessage('کیر گراز وحشی تو مادرت');
$EditMessage('اونجا که شاعر میگه یه کیر دارم شاه نداره تو ننت');
$EditMessage('پایه تختم تو کونت');
$EditMessage('کلا کص ننت');
$EditMessage('الکی بی دلیل کص ننت');
$EditMessage('بابات چه ورقیه');
$EditMessage('دست زدم به کون بابات دلم رفت');
$EditMessage('به بابات بگو سفید کنه شب میام بکنم');
$EditMessage('کص ننت؟');
$EditMessage('ایمیل عمتو لطف کن');
$EditMessage('کوننده خونه ای که عمت توش پول درمیاره نوشتم رو کیرم');
$EditMessage('کص ننت');
$EditMessage('کص پدرت');
$EditMessage('😂 امیدوارم از فحش ها لذت برده باشی');
break;
case $text == "گونخور":
$EditMessage('G.......');
$EditMessage('.O......');
$EditMessage('..H.....');
$EditMessage('...B....');
$EditMessage('....O...');
$EditMessage('.....KH..');
$EditMessage('......O.');
$EditMessage('.......R');
$EditMessage('GOH BOKHOR💩');
break;
case $text == "بکششش":
$EditMessage("🙃                 • 🔫🤠");
$EditMessage("🙃                •  🔫🤠");
$EditMessage("🙃               •   🔫🤠");
$EditMessage("🙃              •    🔫🤠");
$EditMessage("🙃             •     🔫🤠");
$EditMessage("🙃            •      🔫🤠");
$EditMessage("🙃           •       🔫🤠");
$EditMessage("🙃          •        🔫🤠");
$EditMessage("🙃         •         🔫🤠");
$EditMessage("🙃        •          🔫🤠");
$EditMessage("🙃       •           🔫🤠");
$EditMessage("🙃      •            🔫🤠");
$EditMessage("🙃     •             🔫🤠");
$EditMessage("🙃    •              🔫🤠");
$EditMessage("🙃   •               🔫🤠");
$EditMessage("🙃  •                🔫🤠");
$EditMessage("🙃 •                 🔫🤠");
$EditMessage("🙃•                  🔫🤠");
$EditMessage("🤯                   🔫🤠");
$EditMessage("سرانجام جنایتکار کشته شد 😂😐");
break;
case $text == "تاس":
$tas = "
-+-+-+-+-+-+
| 012  |
| 345  |
| 678  |
-+-+-+-+-+-+";
$rand002 = rand(1, 6);
if ($rand002 == 1) $tas = str_replace([0, 4], "🖤", $tas);
elseif( $rand002 == 2) $tas = str_replace([0, 8], "❤️", $tas);
elseif( $rand002 == 3) $tas = str_replace([0, 4, 8], "💛", $tas);
elseif( $rand002 == 4) $tas = str_replace([0, 2, 6, 8], "💙", $tas);
elseif( $rand002 == 5) $tas = str_replace([0, 2, 6, 8, 4], "💜", $tas);
elseif( $rand002 == 6) $tas = str_replace([0, 2, 6, 8, 3, 5], "💚", $tas);
$tas = str_replace(range(0, 8), '   ', $tas);
$EditMessage($tas);
break;
case $text == "جر":
$ReplyMessage('😂');
$EditMessage('😂🤣');
$EditMessage('😂🤣😂');
$EditMessage('😂🤣😂🤣');
$EditMessage('😂🤣😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣');
$EditMessage('😂🤣😂🤣😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂😂');
$EditMessage('😂😂🤣😂🤣😂🤣😂🤣😂😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂');
$EditMessage('😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂🤣😂😂');
break;
case $text == "بای":
$EditMessage('خداحافظ');
$EditMessage('Bye');
$EditMessage('Totsiens');
$EditMessage('अलविदा');
$EditMessage('Tchau');
$EditMessage('ባይ');
$EditMessage('Pa');
$EditMessage('وداعا');
$EditMessage('bless');
$EditMessage('до свидания');
$EditMessage('ցտեսություն');
$EditMessage('ka ọ dị');
$EditMessage('addio');
$EditMessage('さようなら');
$EditMessage('здраво');
$EditMessage('doei');
$EditMessage('хайр');
$EditMessage('vale');
$EditMessage('Чао');
$EditMessage('Hoşçakal');
$EditMessage('au revoir');
$EditMessage('Tschüss');
$EditMessage('баяртай');
$EditMessage('αντίο');
$EditMessage('ବିଦାୟ');
$EditMessage('o dabọ');
$EditMessage('ביי');
$EditMessage('usale kahle');
$EditMessage('د خدای په امان');
$EditMessage('farvel');
$EditMessage('Hejdå');
$EditMessage('kwaheri');
$EditMessage('再见');
$EditMessage('sala hantle');
$EditMessage('slán');
$EditMessage('sağol');
$EditMessage('خداحافظظظ');
break;
case $text == 'chetory' or $text == 'چطوری' or $text == 'Chetory':
$EditMessage('چطوریی');
$EditMessage('how are you');
$EditMessage('क्या हाल है');
$EditMessage('Bawo ni o se wa');
$EditMessage('וואס מאכסטו');
$EditMessage('jak się masz');
$EditMessage('מה שלומך');
$EditMessage('Pehea oe');
$EditMessage('څنګه یاست');
$EditMessage('તમે કેમ છો');
$EditMessage('तिमीलाई कस्तो छ ');
$EditMessage('bạn khỏe không');
$EditMessage('apa khabar');
$EditMessage('nasılsın');
$EditMessage('hoe gaat het met je');
$EditMessage('Шумо чӣ хелед');
$EditMessage('quid agis');
$EditMessage('Hur mår du');
$EditMessage('你好吗');
$EditMessage('어떻게 지내');
$EditMessage('u phela joang');
$EditMessage('Қалайсыз');
$EditMessage('お元気ですか');
$EditMessage('како си');
$EditMessage('Conas tá tú');
$EditMessage('Come stai');
$EditMessage('как поживаешь');
$EditMessage('ce mai faci');
$EditMessage('እንዴት ነህ');
$EditMessage('كيف حالك');
$EditMessage('Kedu ka ị mere');
$EditMessage('koj nyob li cas');
$EditMessage('Como você está');
$EditMessage('คุณเป็นอย่างไรบ้าง');
$EditMessage('jak się masz');
$EditMessage('Pehea oe');
$EditMessage('چطوریی');
break;
case $text == "رقص3":
for ($i = 0;$i < 40;$i+=2) {
$EditMessage("
-~(._. )--

");
$EditMessage("
--( ._.)~-

");
}
$EditMessage("
--( ._.)-~
تامام
");
break;
case $text == "مربع3":
$EditMessage("

.                                🟦🟦🟦🟦🟦



");
$EditMessage("

.                                🟦🟦🟦🟦🟦
         🟦
         🟦
         🟦
         🟦



");
$EditMessage("

.                                🟦🟦🟦🟦🟦
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦


");
$EditMessage("

.                                🟦🟦🟦🟦🟦
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦
🟦


");
$EditMessage("


.                                🟦🟦🟦🟦🟦
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦


");
$EditMessage("

.                                🟦🟦🟦🟦🟦
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟦🟦🟦🟦🟥
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟦🟦🟦🟥🟥
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟦🟦🟥🟥🟥
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟦🟥🟥🟥🟥
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟦
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟦
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟦
🟦     🟦
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟦     🟦دادوعل
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥??
         🟥
         🟥
         🟥
🟦     🟥
🟦🟦🟦🟦🟦🟦
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟦     🟥
🟦🟦🟦🟦🟦🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟦     🟥
🟦🟦🟦🟦🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟦     🟥
🟦🟦🟦🟥🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟦🟦??🟥🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟦🟦🟥🟥🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         ??
         🟥
         🟥
🟥     🟥
🟦🟥🟥🟥🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟦🟦
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟦🟥
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥??🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟥🟥
🟦🟦        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟥🟥
🟦🟥        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟥🟥
🟥🟥        🟦🟦


");
$EditMessage("

.                                🟥🟥🟥🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟥🟥
🟥🟥        🟥🟦


");
$EditMessage("

.                                🟥🟥??🟥🟥
         🟥
         🟥
         🟥
🟥     🟥
🟥🟥🟥🟥🟥🟥
🟥🟥
🟥🟥        🟥🟥


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟥🟥
🟥🟥        🟥🟥


");
$EditMessage("

.                                🟧🟨🟩🟦??
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦⬛️
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩⬛️🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                ??🟨⬛️🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧⬛️🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                ⬛️🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟦
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         ⬛️
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         ⬛️
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         ⬛️
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     ⬛️
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨⬛️
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩⬛️🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨??🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪⬛️🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
⬛️     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️⬛️🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬛️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
⬛️⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬛️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
⬛️⬜️
🟩🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩⬛️        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
⬛️🟦        🟨🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        ⬛️🟧


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨⬛️


");
$EditMessage("

.                                🟧🟨🟩🟦🟪
         🟪
         🟦
         🟩
🟦     🟨
🟫⬜️🟪🟩🟨🟧
🟪⬜️
🟩🟦        🟨🟧

یاح یاح یاح
");
break;
case $text == "بکیرم":
$EditMessage("|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
$EditMessage("
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒█▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒██▒▒▒▒████▒▒▒▒▒▒|
|▒▒██▒▒██▒██▒▒▒▒▒▒|
|▒▒▒████▒▒██▒▒▒▒▒▒|
|▒▒▒▒██▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒██▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒████████▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒██▒|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒█▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒█████▒▒▒▒▒▒|
|▒▒▒▒▒▒▒▒▒██▒▒▒▒▒▒|
|▒▒▒▒▒▒‌▒▒▒████████|
|▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒|");
break;
case $text == "اوخی":
$ReplyMessage('🥺اوخییی');
$EditMessage("🥺");
$EditMessage("🥺🥺");
$EditMessage("🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺🥺");
$EditMessage("🥺🥺🥺");
$EditMessage("🥺🥺");
$EditMessage("🥺");
break;
case $text == "قهرم":
$ReplyMessage('😢😢😢😢');
$EditMessage("🙁🙁🙁🙁");
$EditMessage("☹️☹️☹️☹️");
$EditMessage("😣😣😣😣");
$EditMessage("😖😖😖😖");
$EditMessage("😫😫😫😫");
$EditMessage("🥺🥺🥺🥺");
$EditMessage("😭😭😭😭");
$EditMessage("😒");
break;
case $text == "بوس":
$ReplyMessage('loading please wait...');
$EditMessage("💋 ");
$EditMessage("💋                         💋");
$EditMessage("💋                   💋 ");
$EditMessage("💋             💋");
$EditMessage("💋          💋");
$EditMessage("💋        💋");
$EditMessage("💋      💋");
$EditMessage("💋   💋");
$EditMessage("💋  💋");
$EditMessage("💋");
break;
case $text == "تپش":
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓");
$EditMessage("💗");
$EditMessage("💓💗💓💗💓💗💓💗");
break;
case $text == "بکیرمم":
$EditMessage("
🤤🤤🤤
🤤         🤤
🤤           🤤
🤤        🤤
🤤🤤🤤
🤤         🤤
🤤           🤤
🤤           🤤
🤤        🤤
🤤🤤🤤
");
$EditMessage("
😂         😂
😂       😂
😂     😂
😂   😂
😂😂
😂   😂
😂      😂
😂        😂
😂          😂
😂            😂");
$EditMessage("
👽👽👽          👽         👽
😍         😍      😍       😍
😎           😎    😎     😎
🤬        🤬       🤬   🤬
😄😄😄          🤓 🤓
🤨         😊      😋   😋
🤯           🤯    🤯     🤯
🤘           🤘    😘        😘
🤫       🤫        🙊          🙊
🤡🤡🤡          😗             🙊");
$EditMessage("
💋💋💋          💋         💋
😏         😏      😏       😏
😏           😏    😏     😏
😄        😄       😄   😄
😄😄😄          😄😄
🤘         🤘      🤘   🤘
🤘           🤘    🤘      🤘
🙊           🙊    🙊        🙊
🙊       🙊        🙊          🙊
💋💋💋          💋            💋");
$EditMessage("
😏😏😏          😏         😏
😏         😏      😏       😏
😄           😄    😄     😄
😄        😄       😄   😄
🤘🤘🤘          🤘🤘
🤘         🤘      🤘   🤘
🙊           🙊    🙊      🙊
🙊           🙊    🙊        🙊
💋       💋        💋          💋
💋💋??          💋            💋");
$EditMessage("
😏😏😏          😏         😏
😄         😄      ??       😄
😄           😄    😄     😄
🤘        🤘       🤘   🤘
🤘🤘🤘          🤘🤘
🙊         🙊      🙊   🙊
🙊           🙊    🙊      🙊
💋           💋    💋        💋
💋       💋        💋          💋
😏😏😏          😏            😏");
$EditMessage("
😄😄😄          😄         😄
😄         😄      😄       😄
🤘           🤘    🤘     🤘
🤘        🤘       🤘   🤘
🙊🙊🙊          🙊🙊
🙊         🙊      🙊   🙊
💋           💋    💋      💋
💋           💋    💋        💋
😏       😏        😏          😏
😏😏😏          😏            😏
");
$EditMessage("
😄😄😄          😄         😄
🤘         🤘      🤘       🤘
🤘           🤘    🤘     🤘
🙊        🙊       🙊   🙊
🙊🙊🙊          🙊🙊
💋         💋      🙊   💋
💋           💋    💋      💋
😏           😏    😏        😏
😏       😏        😏          😏
😄😄😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🤘         🤘      🤘       🤘
🙊           🙊    🙊     🙊
🙊        🙊       🙊   🙊
💋💋💋          💋💋
💋         💋      💋   💋
😏           😏    🙊      😏
😏           😏    😏        😏
😄       😄        😄          😄
😄😄😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🙊         🙊      🙊       🙊
🙊           🙊    🙊     🙊
💋        💋       💋   💋
💋💋💋          💋💋
😏         😏      😏   😏
😏           😏    😏      😏
😄           😄    🙊        😄
😄       😄        😄          😄
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🙊🙊🙊          🙊         🙊
🙊         🙊      🙊       🙊
💋           💋    💋     💋
💋        💋       💋   💋
😏😏😏          😏😏
😏         😄      😏   😏
😄           😄    😄      😄
😄           😄    😄        😄
🤘       🤘        🤘          🤘
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🙊🙊🙊          🙊         🙊
💋         💋      💋       💋
💋           💋    💋     💋
😏        😏       😏   😏
😏😏😏          😏😏
😄         😄      😄   😄
😄           😄    😄      😄
🤘           🤘    🤘        🤘
🤘       🤘        🤘          🤘
🙊🙊🙊          🙊            🙊
");
$EditMessage("
💋??💋          💋         💋
💋         💋      💋       💋
😏           😏    😏     😏
😏        😏       😏   😏
😄😄😄          😄😄
😄         😄      😄   😄
🤘           🤘    🤘      🤘
🤘           🤘    🤘        🤘
🙊       🙊        🙊          🙊
🙊🙊🙊          🙊            🙊
");
$EditMessage("
💋💋💋          💋         💋
😏         😏      😏       😏
😏           😏    😏     😏
😄        😄       😄   😄
😄😄😄          😄😄
🤘         🤘      🤘   🤘
🤘           🤘    🤘      🤘
🙊           🙊    🙊        🙊
🙊       🙊        🙊          🙊
💋💋💋          💋            💋
");
$EditMessage("
😏😏😏          😏         😏
😏         😏      😏       😏
😄           😄    😄     😄
😄        😄       😄   😄
🤘🤘🤘          🤘🤘
🤘         🤘      🤘   🤘
🙊           🙊    🙊      🙊
🙊           🙊    🙊        🙊
💋       💋        💋          💋
💋💋💋          💋            💋
");
$EditMessage("
😏😏😏          😏         😏
😄         😄      😄       😄
😄           😄    😄     😄
🤘        🤘       🤘   🤘
🤘🤘🤘          🤘🤘
🙊         🙊      🙊   🙊
🙊           🙊    🙊      🙊
💋           💋    💋        💋
💋       💋        💋          💋
😏😏😏          😏            😏
");
$EditMessage("
😄😄😄          😄         😄
😄         😄      😄       😄
🤘           🤘    🤘     🤘
🤘        🤘       🤘   🤘
🙊🙊🙊          🙊🙊
🙊         🙊      🙊   🙊
💋           💋    💋      💋
💋           💋    💋        💋
😏       😏        😏          😏
😏😏😏          😏            😏
");
$EditMessage("
😄😄😄          😄         😄
🤘         🤘      🤘       🤘
🤘           🤘    🤘     🤘
🙊        🙊       🙊   🙊
🙊🙊🙊          🙊🙊
💋         💋      💋   💋
💋           💋    💋      💋
😏           😏    😏        😏
😏       😏        😏          😏
😄😄😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🤘         🤘      🤘       🤘
🙊           🙊    🙊     🙊
🙊        🙊       🙊   🙊
💋💋💋          💋💋
💋         💋      💋   💋
😏           😏    😏      😏
😏           😏    😏        😏
😄       😄        😄          😄
😄😄😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🙊         🙊      🙊       🙊
🙊           🙊    🙊     🙊
💋        💋       💋   💋
💋💋💋          💋💋
😏         😏      😏   😏
😏           😏    😏      😏
😄           😄    😄        😄
😄       😄        😄          😄
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🙊🙊🙊          🙊         🙊
🙊         🙊      🙊       🙊
🙊           💋    💋     💋
💋        💋       💋   💋
😏😏😏          😏😏
😏         😏      😏   😏
😄           😄    😄      😄
😄           😄    😄        😄
🤘       😄        🤘          🤘
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🙊🙊🙊          🙊         🙊
💋         💋      💋       💋
💋           💋    💋     💋
😏        😏       😏   😏
😏😏😏          😏😏
😄         😄      😄   😄
😄           😄    😄      😄
🤘           🤘    🤘        🤘
🤘       🤘        🤘          🤘
🙊🙊🙊          🙊            🙊
");
$EditMessage("
💋💋💋          💋         💋
💋         💋      💋       💋
😏           😏    😏     😏
😏        😄       😏   😏
😄😄😄          😄😄
😄         😄      😄   😄
😄           🤘    🤘      🤘
🤘           🤘    🤘        🤘
🙊       🙊        🙊          🙊
🙊🙊🙊          🙊            🙊
");
$EditMessage("
💋💋💋          💋         💋
😏         😏      😏       😏
😏           😏    😏     😏
😄        😄       😄   😄
😄😄😄          😄😄
🤘         🤘      🤘   🤘
🤘           🤘    🤘      🤘
🙊           🙊    🙊        🙊
🙊       🙊        🙊          🙊
💋💋💋          💋            💋
");
$EditMessage("
😏😏😏          😏         😏
😏         😏      😏       😏
😄           😄    😄     😄
😄        😄       😄   😄
🤘🤘🤘          🤘🤘
🤘         🤘      🤘   🤘
🙊           🙊    🙊      🙊
🙊           🙊    🙊        🙊
💋       💋        💋          💋
💋💋💋          💋            💋
");
$EditMessage("
😏😏😏          😏         😏
😄         😄      😄       😄
😄           😄    😄     😄
🤘        🤘       🤘   🤘
🤘🤘🤘          🤘🤘
🙊         🙊      🙊   🙊
🙊           🙊    🙊      🙊
💋           💋    💋        💋
??       💋        💋          💋
😏😏😏          😏            😏
");
$EditMessage("
😄😄😄          😄         😄
😄         😄      😄       😄
🤘           🤘    🤘     🤘
🤘        🤘       🤘   🤘
🙊🙊🙊          🙊🙊
🙊         🙊      🙊   🙊
💋           💋    💋      💋
💋           💋    💋        💋
😏       😏        😏          😏
😏😏😏          😏            😏
");
$EditMessage("
😄😄😄          😄         😄
🤘         🤘      🤘       🤘
🤘           🤘    🤘     🤘
🙊        🙊       🙊   🙊
🙊🙊🙊          🙊🙊
💋         💋      💋   💋
💋           💋    💋      💋
😏           😏    😏        😏
😏       😏        😏          😏
😄😄😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🤘         ??      🤘       🤘
🙊           🙊    🙊     🙊
🙊        🙊       🙊   🙊
💋💋💋          💋💋
💋         💋      💋   💋
😏           😏    😏      ??
😏           😏    😏        😏
😄       😄        😄          😄
😄??😄          😄            😄
");
$EditMessage("
🤘🤘🤘          🤘         🤘
🙊         🙊      🙊       🙊
🙊           🙊    🙊     🙊
💋        💋       💋   💋
💋💋💋          💋💋
😏         😏      😏   😏
😏           😏    😏      😏
😄           😄    😄        😄
😄       😄        😄          😄
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🙊🙊🙊          🙊         🙊
🙊         🙊      🙊       🙊
💋           💋    💋     💋
💋        💋       💋   💋
😏😏😏          😏😄
😏         😏      😏   😏
😄           😄    😄      😄
😄           😄    😄        😄
🤘       😄        🤘          🤘
🤘🤘🤘          🤘            🤘
");
$EditMessage("
🤬🤬🤬          🤬         🤬
😡         😡      😡       😡
🤬           🤬    🤬     🤬
😡        😡       😡   😡
🤬🤬🤬          🤬🤬
😡         😡      😡   😡
🤬           🤬    🤬      🤬
😡           😡    😡        😡
🤬       🤬        🤬          🤬
😡😡😡          😡            😡

بانک کشاورزی 😐");
break;
case $text == "سگ":
$EditMessage("┈┈┈┈┈┈┈┈┈     ╲╱╲╱");
$EditMessage("┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("╱╲╱╲▏┈┈┈┈┈▕▔╰━╯
╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯
╱╲╱╲▏┈┈┈┈┈▕▔╰━╯
╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆
┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯
╱╲╱╲▏┈┈┈┈┈▕▔╰━╯
╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("┈▏▏┈┈┈┈┈▏╲▕▋▕▋▏
┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆
┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯
╱╲╱╲▏┈┈┈┈┈▕▔╰━╯
╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");

$EditMessage("┈╱▏┈┈┈┈┈╱▔▔▔▔╲
┈▏▏┈┈┈┈┈▏╲▕▋▕▋▏
┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆
┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯
╱╲╱╲▏┈┈┈┈┈▕▔╰━╯
╲╲╱╱▔╱▔▔╲╲╲╲
┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂
┈┈┈┈┈┈┈┈┈     ╲╱╲╱");
break;
case $text == "صکصی" or $text == "سکسی":
$EditMessage(" ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠧⠤⠂⠄⣼⢧⢻⣿⣿⣞⢸⣮⠳⣕⢤⡆⠄⠄
 ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⡜⠄⢀⠆⢠⣿⣿⣿⣿⢡⢣⢿⡱⡀⠈⠆⠄⠄
 ⠄⠧⠤⠂⠄⣼⢧⢻⣿⣿⣞⢸⣮⠳⣕⢤⡆⠄⠄
 ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄

⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⡌⠄⢰⠉⢙⢗⣲⡖⡋⢐⡺⡄⠈⢆⠄⠄⠄
 ⠄⡜⠄⢀⠆⢠⣿⣿⣿⣿⢡⢣⢿⡱⡀⠈⠆⠄⠄
 ⠄⠧⠤⠂⠄⣼⢧⢻⣿⣿⣞⢸⣮⠳⣕⢤⡆⠄⠄
 ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(" ⠄⠄⡔⠙⠢⡀⠄⠄⠄⢀⠼⠅⠈⢂⠄⠄⠄⠄
 ⠄⠄⡌⠄⢰⠉⢙⢗⣲⡖⡋⢐⡺⡄⠈⢆⠄⠄⠄
 ⠄⡜⠄⢀⠆⢠⣿⣿⣿⣿⢡⢣⢿⡱⡀⠈⠆⠄⠄
 ⠄⠧⠤⠂⠄⣼⢧⢻⣿⣿⣞⢸⣮⠳⣕⢤⡆⠄⠄
 ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
$EditMessage(": ⠄⠄⠄⠄ ⠄⠄⠄⠄ ⠄⠄⠄⠄
 ⠄⠄⡔⠙⠢⡀⠄⠄⠄⢀⠼⠅⠈⢂⠄⠄⠄⠄
 ⠄⠄⡌⠄⢰⠉⢙⢗⣲⡖⡋⢐⡺⡄⠈⢆⠄⠄⠄
 ⠄⡜⠄⢀⠆⢠⣿⣿⣿⣿⢡⢣⢿⡱⡀⠈⠆⠄⠄
 ⠄⠧⠤⠂⠄⣼⢧⢻⣿⣿⣞⢸⣮⠳⣕⢤⡆⠄⠄
 ⢺⣿⣿⣶⣦⡇⡌⣰⣍⠚⢿⠄⢩⣧⠉⢷⡇⠄⠄
 ⠘⣿⣿⣯⡙⣧⢎⢨⣶⣶⣶⣶⢸⣼⡻⡎⡇⠄⠄
 ⠄⠘⣿⣿⣷⡀⠎⡮⡙⠶⠟⣫⣶⠛⠧⠁⠄⠄⠄
 ⠄⠄⠘⣿⣿⣿⣦⣤⡀⢿⣿⣿⣿⣄⠄⠄⠄⠄⠄
 ⠄⠄⠄⠈⢿⣿⣿⣿⣿⣷⣯⣿⣿⣷⣾⣿⣷⡄⠄
 ⠄⠄⠄⠄⠄⢻⠏⣼⣿⣿⣿⣿⡿⣿⣿⣏⢾⠇⠄
 ⠄⠄⠄⠄⠄⠈⡼⠿⠿⢿⣿⣦⡝⣿⣿⣿⠷⢀⠄
 ⠄⠄⠄⠄⠄⠄⡇⠄⠄⠄⠈⠻⠇⠿⠋⠄⠄⢘⡆
 ⠄⠄⠄⠄⠄⠄⠱⣀⠄⠄⠄⣀⢼⡀⠄⢀⣀⡜⠄
 ⠄⠄⠄⠄⠄⠄⠄⢸⣉⠉⠉⠄⢀⠈⠉⢏⠁⠄⠄
 ⠄⠄⠄⠄⠄⠄⡰⠃⠄⠄⠄⠄⢸⠄⠄⢸⣧⠄⠄
 ⠄⠄⠄⠄⠄⣼⣧⠄⠄⠄⠄⠄⣼⠄⠄⡘⣿⡆⠄
 ⠄⠄⠄⢀⣼⣿⡙⣷⡄⠄⠄⠄⠃⠄⢠⣿⢸⣿⡀
 ⠄⠄⢀⣾⣿⣿⣷⣝⠿⡀⠄⠄⠄⢀⡞⢍⣼⣿⠇
 ⠄⠄⣼⣿⣿⣿⣿⣿⣷⣄⠄⠄⠠⡊⠴⠋⠹⡜⠄
 ⠄⠄⣿⣿⣿⣿⣿⣿⣿⣿⡆⣤⣾⣿⣿⣧⠹⠄⠄
 ⠄⠄⢿⣿⣿⣿⣿⣿⣿⣿⢃⣿⣿⣿⣿⣿⡇⠄⠄
 ⠄⠄⠐⡏⠉⠉⠉⠉⠉⠄⢸⠛⠿⣿⣿⡟⠄⠄⠄
 ⠄⠄⠄⠹⡖⠒⠒⠒⠒⠊⢹⠒⠤⢤⡜⠁⠄⠄⠄
 ⠄⠄⠄⠄⠱⠄⠄⠄⠄⠄⢸⠄⠄⠄⠄⠄⠄⠄⠄");
break;
case $text == "هلیکوپتر":
$EditMessage('
█▬▬▬.◙.▬▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬☻/
╬═╬/▌
╬═╬/  \
');
$EditMessage('
█▬▬▬.◙.▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬☻/
╬═╬/▌
╬═╬/  \
╬═╬
');
$EditMessage('
█▬▬.◙.▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬
╬═╬
╬═╬☻/
╬═╬/▌
╬═╬/  \
╬═╬
╬═╬
');
$EditMessage('
█▬.◙.▬▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬
╬═╬☻/
╬═╬/▌
╬═╬/  \
╬═╬
╬═╬
╬═╬
');
$EditMessage('
█▬▬.◙.▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬☻/
╬═╬/▌
╬═╬/  \
╬═╬
╬═╬
╬═╬
╬═╬
');
$EditMessage('
█▬▬▬.◙.▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬☻/
╬═╬/▌
╬═╬/  \
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
');
$EditMessage('
█▬▬.◙.▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
╬═╬
');
$EditMessage('
█▬.◙.▬▬█
═▂▄▄▓▄▄▂
◢◤ █▀▀████▄▄▄▄◢◤
█▄ █ █▄ ███▀▀▀▀▀▀▀╬
◥█████◤
══╩══╩═
');
break;
case $text == "قلبز":
$EditMessage('.           ❤️                  ❤️
        ❤️  ❤️          ❤️  ❤️
    ❤️          ❤️  ❤️          ❤️
       ❤️           ❤️           ❤️
           ❤️                    ❤️
               ❤️            ❤️
                   ❤️    ❤️
                        ❤️
.');


$EditMessage('.           🧡                  🧡
        🧡  🧡          🧡  🧡
    🧡          🧡  🧡          🧡
       🧡           🧡           🧡
           🧡                    🧡
               🧡            🧡
                   🧡    🧡
                        🧡
.');


$EditMessage('.           💛                  💛
        💛  💛          💛  ??
    💛          💛  💛          💛
       💛           💛           💛
           💛                    💛
               💛            💛
                   💛    💛
                        💛
.');


$EditMessage('.           💚                  💚
        💚  💚          💚  💚
    💚          💚  💚          💚
       💚           💚           💚
           💚                    💚
               💚            💚
                   💚    💚
                        💚
.');


$EditMessage('.           💙                  💙
        💙  💙          💙  💙
    💙          💙  💙          💙
       💙           💙           💙
           💙                    💙
               💙            💙
                   💙    💙
                        💙
.');


$EditMessage('.           💜                  💜
        💜  💜          💜   💜
    💜          💜  💜          💜
       💜           💜           💜
           💜                    💜
               ??            💜
                   💜    💜
                        💜
.');


$EditMessage('.           🖤                  🖤
        🖤  🖤          🖤   🖤
    🖤          🖤  🖤          🖤
       🖤           🖤           🖤
           🖤                    🖤
               🖤            🖤
                   🖤    🖤
                        🖤
.');


$EditMessage('.           🤍                  🤍
        🤍  🤍          🤍   🤍
    🤍          🤍  🤍          🤍
       🤍           🤍           🤍
           🤍                    🤍
               🤍            🤍
                   🤍    🤍
                        🤍
.');


$EditMessage('.           💗                  💗
        💗  💗          💗   💗
    💗          💗  💗          💗
       💗           💗           💗
           💗                    💗
               💗            💗
                   💗    💗
                        💗
.');

$EditMessage('.           ❤️                  ❤️
        ❤️  ❤️          ❤️  ❤️
    ❤️          ❤️  ❤️          ❤️
       ❤️           ❤️           ❤️
           ❤️                    ❤️
               ❤️            ❤️
                   ❤️    ❤️
                        ❤️
.');

$EditMessage('.           🧡                  🧡
        🧡  🧡          🧡  🧡
    🧡          🧡  🧡          🧡
       🧡           🧡           🧡
           🧡                    🧡
               🧡            🧡
                   🧡    🧡
                        🧡
.');


$EditMessage('.           💛                  💛
        💛  💛          💛  💛
    💛          💛  💛          💛
       💛           💛           💛
           💛                    💛
               💛            💛
                   💛    💛
                        💛
.');


$EditMessage('.           💚                  💚
        💚  💚          💚  💚
    💚          💚  💚          💚
       💚           💚           💚
           💚                    💚
               💚            💚
                   💚    💚
                        💚
.');


$EditMessage('.           💙                  💙
        💙  💙          💙  💙
    💙          💙  💙          💙
       💙           💙           💙
           💙                    💙
               💙            💙
                   💙    💙
                        💙
.');


$EditMessage('.           💜                  💜
        💜  💜          💜   💜
    💜          💜  💜          💜
       💜           💜           💜
           💜                    💜
               💜            💜
                   💜    💜
                        💜
.');


$EditMessage('.           ❤️                  ❤️
        ❤️  ❤️          ❤️  ❤️
    ❤️          ❤️  ❤️          ❤️
       ❤️           ❤️           ❤️
           ❤️                    ❤️
               ❤️            ❤️
                   ❤️    ❤️
                        ❤️
.');


$EditMessage('.           🧡                  🧡
        🧡  🧡          🧡  🧡
    🧡          🧡  🧡          🧡
       🧡           🧡           🧡
           🧡                    🧡
               🧡            🧡
                   🧡    🧡
                        🧡
.');


$EditMessage('.           💛                  💛
        💛  💛          💛  💛
    💛          💛  💛          💛
       💛           💛           💛
           💛                    💛
               💛            💛
                   💛    💛
                        💛

.');

$EditMessage('💜');
break;
case $text == "پلیس":
for ($i = 0;$i < 15;$i++){
$EditMessage('🔴🔴ＰＯＬＩＣＥ🔵🔵');
$EditMessage('🔵🔵ＰＯＬＩＣＥ🔴🔴');
}
break;
case $text == "هزارپا":
$EditMessage("          (█)");
$EditMessage("      ╚(██)╝
          (█)");
$EditMessage("     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage(" ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage(" ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("  ╚═(███)═╝
   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage(" ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
$EditMessage("╚═( ͡° ͜ʖ ͡°)═╝

╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
   ╚═(███)═╝
  ╚═(███)═╝
 ╚═(███)═╝
╚═(███)═╝
 ╚═(███)═╝
  ╚═(███)═╝
  ╚═(███)═╝
   ╚═(███)═╝
     ╚(███)╝
      ╚(██)╝
          (█)");
break;
case $text == "دوست دارم":
$EditMessage('
  ▀██▀─▄███▄─▀██─██▀██▀▀█
  ─██─███─███─██─██─██▄█');
$EditMessage('
  ─██─▀██▄██▀─▀█▄█▀─██▀█
  ▄██▄▄█▀▀▀─────▀──▄██▄▄█');
$EditMessage('
  ▀██▀─▄███▄─▀██─██▀██▀▀█
  ─██─███─███─██─██─██▄█
  ─██─▀██▄██▀─▀█▄█▀─██▀█
  ▄██▄▄█▀▀▀─────▀──▄██▄▄█');
break;
case $text == "زنبور":
$EditMessage('🏥__________🏃‍♂️______________🐝');
$EditMessage('🏥______🏃‍♂️_______🐝');
$EditMessage('🏥______🏃‍♂️_____🐝');
$EditMessage('🏥___🏃‍♂️___🐝');
$EditMessage('🏥_🏃‍♂️_🐝');
$EditMessage('در رفت عه☹️🐝');
break;
case $text == "تتلو":
$ReplyMessage("
⏭▶️⏸⏮
");
$ReplyMessage("
یا اینوری یا اونوری ، یا بیکینی یا روسری
چشم چشم دو ابرو ، صورتش گیلاسه

");
$ReplyMessage("
پایین ولی یه گردو ، (چرا؟) هندونه نگفتم ریا نشه
یه آذر ماهیِ کرمو ، یا یه مردادیِ حیوون

");
$ReplyMessage("
فرقی نداره فقط بگو ، کم بماله میمون
بگو تنگ و کوتاه بپوشه ، یه جور که همه بشن زوم

");
$ReplyMessage("
بگو وسط جمع یهو یک دو سه بگو بپَر روم
یک دو سه بگو بپَر روم ، من یه سلبریتیِ کم رو
");
$ReplyMessage("
اونم که بکَن بود ، حالا که فازِ بکَنه ، بکَن بکَن بکَن زود
پس بکَن بکَن بکَن زود
");
$ReplyMessage("
آا ، گوششو انداختی بیرون گوشایِ من سرخ شدش
دستِ خودم نیست دیگه این ، مغزِ حروم کنترلش
");
$ReplyMessage("
فکرای س*سی که اینو هی بگیر
من مالِ تو رو فشار میدم ، تو مالِ منو بخورش
");
$ReplyMessage("
من تو رو هستم زیاد ، نونِ شبه لوندیات
بزنم پنچر شی ، یا بزنم نفت در بیاد؟
");
$ReplyMessage("
بیاین بیاین بیاین بیاین …
بیا با هم پرواز کنیم که من دوس دارم کفتر زیاد
");
$ReplyMessage("
صبح تا شب پارتی کنیم شب بزنم نفت در بیاد
بعد بیا با هم بریم حموم ، بزنیم کف در دیوار
");
$ReplyMessage("
هر چی بیشتر وَر بری باهام ، میکِشم کمتر سیگار
بیا با هم داگی رو استایل کنیم
");
$ReplyMessage("
با این حجمِ باسن میشه خاکی سو آسفالت کنیم
بیا با هم کُشتی بگیریم همو بارانداز کنیم
");
$ReplyMessage("
خوب و کمر پُر منم ، چشم و چال انداز تویی
بیا با هم فا*ینگ رو استارت کنیم
");
$ReplyMessage("
من تو رو هستم زیاد ، نونِ شبه لوندیات
بزنم پنچر شی ، یا بزنم نفت در بیاد
");
$ReplyMessage("
بَه ، آقا مبارکا باشه ، بزنید نفت در بیاد
(برو بالا بالا لالا)
تولید انرژی هم میکنین
");
$ReplyMessage("
مبارکه آقا مبارکه ، بزنید نفت در بیاد
گرفتن شاخکام از تو فرکانسای سک*ی
");
$ReplyMessage("
و الآن آجبی و داداشی نداره رو ما اثر
نگیر فازِ فرند و فامیل رو ما اصاً
");
$ReplyMessage("
فقط شیرجه بزن روم با عسل (با عسل و ندا)
حاجی ، گورِ بابا همسایتون ، کو* لق فامیلات
");
$ReplyMessage("
آشنا ماشنا نگا*دم ، بگو تنها بیاد
گور بابا خالم اینا ، کو* لق دائیات
");
$ReplyMessage("
من زدم نفت در بیاد ،دختر دایی رو پا میداد
بزنید نفت در بیاد
");
break;
case $text == "کصخل":
$EditMessage('کص خل');
$EditMessage('ک');
$EditMessage('ص');
$EditMessage('خ');
$EditMessage('ل');
$EditMessage('ک_____ص_____خ_____ل');
$EditMessage('ک____ص____خ____ل');
$EditMessage('ک___ص___خ___ل');
$EditMessage('ک__ص__خ__ل');
$EditMessage('ک_ص_خ_ل');
$EditMessage('کص خل');
$EditMessage('ک_____ص_____خ_____ل');
$EditMessage('ک_ص_خ_ل');
$EditMessage('کص');
$EditMessage('خل');
$EditMessage('💥تو یه کصخلی لنتی💥');
break;
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Auto "]=-=-=//=-=-=//=-=-=//=-=-=//
if (self::mbStrlen($text) < 200) {
if ($Data['OptHashtag']) {
$text = str_replace(" ", "_", $text);
$EditMessage("#$text");
}
if ($Data['OptBold']) {
$EditMessage("**$text**",ParseMode::MARKDOWN);
}
elseif( $Data['OptUnderline']) {
$EditMessage("<u>$text</u>", ParseMode::HTML);
}
elseif( $Data['OptCoding']) {
$EditMessage("`$text`");
}
elseif( $Data['OptMention']) {
$EditMessage("[$text](tg://user?id={$this->getSelf()['id']})",ParseMode::MARKDOWN);
}
elseif($Data['OptPart']) {
$text = str_replace(" ", "‌", $text);
static $newText = '';
foreach (self::mbStrSplit($text, 1) as $char) {
$EditMessage($newText);
$this->sleep(0.1);
}
$newText = '';
}
elseif( $Data['OptReverse']) {
$text = str_replace(" ", "%20", $text);
$Reverse = $this->fileGetContents("https://api.codebazan.ir/strrev/?text=$text");
$EditMessage("$Reverse");
}
elseif( $Data['OptItalic']) {
$EditMessage("<i>$text</i>", ParseMode::HTML);
}
elseif( $Data['OptDelete']) {
$EditMessage("<del>$text</del>", ParseMode::HTML);
}
}
if ($Data['OptPoker'] and str_contains($text, '😐') and !$isOut) {
$ReplyMessage('😐');
}
if (isset($this->Answering[$text]) and !$isOut) {
$this->messages->readHistory(peer: $UserID, max_id: $MsgID);
$ReplyMessage($this->Answering[$text]);
}
if (in_array($UserID, $this->Enemies)) {
$Send = $Spam[array_rand($Spam)];
$ReplyMessage($Send);
}
///=-=-=-=-=-=-=-=-=-=-=-=[" Catch-Flood "]=-=-=-=-=-=-=-=-=-=-=-=
} catch (FloodError $exception){
$i = ceil($exception->getWaitTime() / 60);
$this->SendMessage($Admin, "
💢 محدودیت ... ! ادمین عزیز ربات شما به مدت $i دقیقه خاموش شد !
👈 لطفا تا پایان محدودیت دستوری ارسال نکنید امکان دیلیت شدن اکانت شما هست !");
$exception->wait();
}
//=-=-=//=-=-=//=-=-=[" End-Handler-Message "]=-=-=//=-=-=//=-=-=
}
//=-=-=//=-=-=//=-=-=[" Start-Handler-Group "]=-=-=//=-=-=//=-=-=
//=-=-=//=-=-=//=-=-=[" Start-Handler-Private "]=-=-=//=-=-=//=-=-=
#[Handler]
public function HandleGroup((Outgoing&GroupMessage)|(Outgoing&PrivateMessage) $Message): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text         = $Message->message;
$ChatID       = $Message->chatId;
$Enemies      = $this->Enemies;
$Data         = $this->Data;
$user         = $Message->getReply()?->senderId;
$Admin        = $this->getSelf()['id'];
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::TEXT) use ($Message) {
$this->Message($Message, 'reply', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Function-Edit "]=-=-=-=-=-=-=-=-=-=-=-=
$EditMessage = function (string $text, ?ParseMode $parseMode = ParseMode::TEXT) use ($Message) {
$this->Message($Message, 'editText', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Switch "]=-=-=-=-=-=-=-=-=-=-=-=
switch ($text) {
case 'block':
if(!$user){
$EditMessage("Not Found");
}
$this->contacts->block(id: $user);
$EditMessage("» ʙʟᴏᴄᴋᴇᴅ !");
break;
case 'unblock':
if(!$user){
$EditMessage("Not Found");
}
$this->contacts->unblock(id: $user);
$EditMessage("» ᴜɴʙʟᴏᴄᴋᴇᴅ !");
break;
case 'id':
if(!$user){
$EditMessage("Not Found");
}
$EditMessage("» ʏᴏᴜʀ ɪᴅ : `$user`");
break;
case '/setenemy':
if (!in_array($user, $Enemies) and $user != $Admin) {
$this->Enemies[] = $user;
$this->contacts->block(id: $user);
$EditMessage("➲ ᴜsᴇʀ [ᴜsᴇʀ](tg://user?id=$user) ɴᴏᴡ ɪɴ ᴇɴᴇᴍʏ ʟɪsᴛ !",ParseMode::MARKDOWN);
} else
$EditMessage("➲ᴛʜɪs [ᴜsᴇʀ](tg://user?id=$user) ᴡᴀs ɪɴ ᴇɴᴇᴍʏ ʟɪsᴛ !",ParseMode::MARKDOWN);
break;
case '/delenemy':
if (in_array($user, $Enemies)) {
$k = array_search($user, $Enemies);
unset($this->Enemies[$k]);
$this->contacts->unblock(id: $user);
$EditMessage("➲ ᴜsᴇʀ [ᴜsᴇʀ](tg://user?id=$user) ᴅᴇʟᴇᴛᴇᴅ ғʀᴏᴍ ᴇɴᴇᴍʏ ʟɪsᴛ !",ParseMode::MARKDOWN);
} else
$EditMessage("➲ ᴛʜɪs [ᴜsᴇʀ](tg://user?id=$user) ɪs ɴᴏᴛ ɪɴ ᴛʜᴇ ᴇɴᴇᴍʏ ʟɪsᴛ !",ParseMode::MARKDOWN);
break;
case 'Cleanall':
$ReplyMessage("ᴀʟʟ ɢʀᴏᴜᴘ ᴍᴇssᴀɢᴇs ᴡᴇʀᴇ ᴅᴇʟᴇᴛᴇᴅ !");
$array = range($Message->replyToMsgId, 1);
$chunk = array_chunk($array, 100);
foreach ($chunk as $v) {
$this->channels->deleteMessages(channel: $Message->senderId, id: $v);
}
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-6 "]=-=-=//=-=-=//=-=-=//=-=-=//
case 'Infogap':
$peerinfo  = $this->getFullInfo($Message->senderId);
$idinfo    = $peerinfo['Chat'];
$peerid    = $idinfo['id'];
$nameinfo  = $idinfo['title'];
$typeinfo  = $peerinfo['type'];
$countinfo = $peerinfo['full']['participants_count'];
$bioinfo   = $peerinfo['full']['about'];
$EditMessage("**♻️ مشخصات گروه به شرح زیر میباشد :
» اسم گروه : $nameinfo
» آیــدی گروه : -100$peerid
» نوع گروه : $typeinfo
» تعدادممبر های گروه : $countinfo
» بیوگرافی گروه :
$bioinfo**");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Gaps "]=-=-=-=-=-=-=-=-=-=-=-=
case 'Delgaps':
foreach ($this->getDialogs() as $peer) {
$this->channels->leaveChannel(channel: $peer);
}
$EditMessage("📌 عملیات مورد نظر باموفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Gaps-Count "]=-=-=-=-=-=-=-=-=-=-=-=
case '/delgap':
$Count = substr($text, 8);
$i = 0;
foreach ($this->getDialogs() as $peer) {
$this->channels->leaveChannel(channel: $peer);
$i++;
if ($i == $Count)
break;
}
$EditMessage("📌 عملیات مورد نظر باموفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Left-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case 'Left':
$EditMessage("لف بای  . . . !");
$this->channels->leaveChannel(channel: $ChatID);
break;
}
if (preg_match('/^[\/#!]?(tag|تگ) (\d+)$/i', $text, $tagcount)) {
$users = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $ChatID, limit: (int)$tagcount[2])['users'];
$counter = 0;
foreach (array_chunk($users, 15) as $tag) {
$out = null;
foreach ($tag as $t) {
if (!$t['bot'] && !$t['deleted'] && $t['id'] != $this->getSelf()['id']) {
$counter++;
$out .= "<a href='tg://user?id={$t['id']}'>{$t['first_name']}</a> ";
}
}
if (!empty($out))
$ReplyMessage("$out",ParseMode::HTML);
}
if ($counter != 0)
$ReplyMessage("♻️ تعداد $counter نفر با موفقیت تگ شدند . . . !");
else
$ReplyMessage("خطا ! در انجام عملیات . . . !");
}
if ($Data['OptMention2']) {
if (!$user) {
$EditMessage('No User Found');
}
$EditMessage("[$text](tg://user?id=$user)");
}
if( $Data['ActionTyping']) {
$SendMessageTypingAction = ['_' => 'sendMessageTypingAction'];
$this->SetTyping($ChatID, $SendMessageTypingAction);
}
elseif( $Data['ActionGame']) {
$SendMessageGamePlayAction = ['_' => 'sendMessageGamePlayAction'];
$this->SetTyping($ChatID, $SendMessageGamePlayAction);
}
elseif( $Data['ActionVoice']) {
$SendMessageRecordAudioAction = ['_' => 'sendMessageRecordAudioAction'];
$this->SetTyping($ChatID, $SendMessageRecordAudioAction);
}
elseif( $Data['ActionVideo']) {
$SendMessageRecordVideoAction = ['_' => 'sendMessageRecordVideoAction'];
$this->SetTyping($ChatID, $SendMessageRecordVideoAction);
}
//=-=-=//=-=-=//=-=-=[" End-Handler-Group "]=-=-=//=-=-=//=-=-=
//=-=-=//=-=-=//=-=-=[" End-Handler-Private "]=-=-=//=-=-=//=-=-=
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" Functions "]=-=-=//=-=-=//=-=-=//=-=-=//
protected function Message(object $Message, string $type, string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): void
{
$Message->$type(message: $text, parseMode: $parseMode);
}

//=-=-=-=-=-=-=-=-=-=-=-=[" SendMedia "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SendMedia($ChatID, array $Media, int $Message_id, string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): array
{
return $this->messages->sendMedia(
peer: $ChatID,
reply_to_msg_id: $Message_id,
media: $Media,
message: $text,
parse_mode: $parseMode
);
}

//=-=-=-=-=-=-=-=-=-=-=-=[" SetTyping "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SetTyping($ChatID, array $Action): bool
{
return $this->messages->setTyping(
action: $Action,
peer: $ChatID
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

//=-=-=-=-=-=-=-=-=-=-=-=[" joinInChannel "]=-=-=-=-=-=-=-=-=-=-=-=
protected function joinInChannel(array $Channels): void
{
foreach ($Channels as $Channel) {
$this->channels->joinChannel(channel: $Channel);
}
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
MyEventHandler::startAndLoop('Self', $Settings);
//=-=-=-=-=-=-=-=-=-=-=-=[" Self "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v189
 * Robot  : Self
 * Date   : 2023/05/10
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" End "]=-=-=-=-=-=-=-=-=-=-=-=