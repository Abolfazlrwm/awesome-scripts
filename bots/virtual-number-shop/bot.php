<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */


$update = json_decode(file_get_contents('php://input'));
if(isset($update)){
    $telegram_ip_ranges = [['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],['lower' => '91.108.4.0','upper' => '91.108.7.255'],];
    $ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])); $ok = false;
        foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
            $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
            $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
                if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok = true;
        }
    if (!$ok) die(); 
}
error_reporting(0);
include('config.php');
define('API_KEY', $tokenbot); // توکن
//-----------------------------------------------------------------------------------------
if(!is_file("data/sod.txt")){
file_put_contents("data/sod.txt", "690");
}
if(!is_file("data/sod1.txt")){
file_put_contents("data/sod1.txt", "690");
}
//----------------------------------------------------------------------------------------
//فانکشن bot :
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    return json_decode($res);
    }
//-----------------------------------------------------------------------------------------
//متغیر ها :
$first = $update->message->from->first_name;
$blocklist = file_get_contents("data/blocklist.txt");
$mobot = file_get_contents("data/bot.txt");
$mosbot = file_get_contents("data/sbot.txt");
$mosbot1 = file_get_contents("data/sboto.txt");
$mosbot2 = file_get_contents("data/sbotoo.txt");
$mosod = file_get_contents("data/sod.txt");
$mosod1 = file_get_contents("data/sod1.txt");
$mosod2 = file_get_contents("data/sod2.txt");
$forward_chat_username = $update->message->forward_from_chat->username;
$username11 = $update->message->from->username;
$from_id = $message->from->id;
$chat_id = $message->chat->id;
$message = $update->message;
@$contact = $update->message->contact;
@$contactid = $contact->user_id;
@$contactnum = $contact->phone_number;
mkdir("data/$from_id");
$connect = mysqli_connect($servername, $username, $password, $dbname);$connect->set_charset('utf8mb4_general_ci');$connect->query('SET NAMES utf8mb4');
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
if(isset($message->from)){
$message_id = $message->message_id;
$text = $message->text;
$first_name = $message->from->first_name;
$username = $message->from->username;
$from_id = $message->from->id;
$chat_id = $message->chat->id;
$tc = $message->chat->type;
$user = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$from_id' LIMIT 1"));
}
if(isset($update->callback_query)){
$chatid = $update->callback_query->message->chat->id;
$fromid = $update->callback_query->from->id;
$firstname = $update->callback_query->from->first_name;
$data = $update->callback_query->data;
$messageid = $update->callback_query->message->message_id;
$membercall = $update->callback_query->id;
// data
$usercall = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$fromid' LIMIT 1")); 
}
function getnumber($service , $country) {
global $apikey;
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=$apikey&action=getNumber&service=$service&operator=virtual18&country=$country");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
$curl_exec = curl_exec($ch);
curl_close($ch);
return $curl_exec;
}

function getnumber2($service , $country) {
global $apikey2;
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getNumber&service=$service&country=$country");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
$curl_exec = curl_exec($ch);
curl_close($ch);
return $curl_exec;
}
function getstats($orderid) {
global $apikey;
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=$apikey&action=getStatus&id=$orderid");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
$curl_exec = curl_exec($ch);
curl_close($ch);
return $curl_exec;
}
function getstats2($orderid) {
global $apikey2;
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getStatus&id=$orderid");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
$curl_exec = curl_exec($ch);
curl_close($ch);
return $curl_exec;
}
function numbermoney($service , $country) {
$mosod = file_get_contents("data/sod.txt");
$test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/$country/virtual18"),true);
$stats = $test["$service"]["Price"];
$sage = $stats * $mosod;
$sos = ceil($sage);
return $sos;
}
$moneys = numbermoney;
@$button = json_encode(['keyboard'=>[
[['text'=>"📲 خرید شماره مجازی"]],
[['text'=>"💳 استعلام | قیمت ها"],['text'=>"👤 اطلاعات حساب"]],
[['text'=>"↗️ انتقال موجودی"],['text'=>"💸 شارژ حساب"],['text'=>"🚦 راهنما"]],
[['text'=>"👮🏻 پشتیبانی"],['text'=>"🗣 دعوت دیگران"]],
],'resize_keyboard'=>true]);
@$buttonj = json_encode(['inline_keyboard'=>[
[['text'=>"🔔 عضویت در کانال",'url'=>"https://t.me/$channel"]],
[['text'=>"📢 عضو شدم",'callback_data'=>'join']],]]);
@$buttonp = json_encode(['keyboard'=>[
[['text'=>"🏛 خانه"],['text'=>"💳 استعلام | قیمت ها"]],
[['text'=>"🇦🇫افغانستان🇦🇫"],['text'=>"🇦🇱آلبانی🇦🇱"],['text'=>"🇩🇿الجزایر🇩🇿"]],
[['text'=>"🇦🇴آنگولا🇦🇴"],['text'=>"🇦🇮آنگویلا🇦🇮"],['text'=>"🇦🇬آنتیگوا و باربودا🇦🇬"]],
[['text'=>"🇦🇷آرژانتین🇦🇷"],['text'=>"🇦🇲ارمنستان🇦🇲"],['text'=>"🇦🇼آروبا🇦🇼"]],
[['text'=>"🇦🇺استرالیا🇦🇺"],['text'=>"🇦🇹اتریش🇦🇹"],['text'=>"🇦🇿آذربایجان🇦🇿"]],
[['text'=>"🇧🇸باهاما🇧🇸"],['text'=>"🇧🇭بحرین🇧🇭"],['text'=>"🇧🇩بنگلادش🇧🇩"]],
[['text'=>"🇧🇧باربادوس🇧🇧"],['text'=>"🇧🇾بلاروس🇧🇾"],['text'=>"🇧🇪بلژیک🇧🇪"]],
[['text'=>"🇧🇿بلیز🇧🇿"],['text'=>"🇧🇯بنین🇧🇯"],['text'=>"🇧🇹بوتان🇧🇹"]],
[['text'=>"🇧🇦بوسنی و هرزگوین🇧🇦"],['text'=>"🇧🇴بولیوی🇧🇴"],['text'=>"🇧🇼بوتسوانا🇧🇼"]],
[['text'=>"🇧🇷برزیل🇧🇷"],['text'=>"🇧🇬بلغارستان🇧🇬"],['text'=>"🇧🇫بورکینافاسو🇧🇫"]],
[['text'=>"🇧🇮بوروندی🇧🇮"],['text'=>"🇰🇭کامبوج🇰🇭"],['text'=>"🇨🇲کامرون🇨🇲"]],
[['text'=>"🇨🇦کانادا🇨🇦"],['text'=>"🇨🇻کیپ ورد🇨🇻"],['text'=>"🇰🇾جزایر کیمن🇰🇾"]],
[['text'=>"🇹🇩چاد🇹🇩"],['text'=>"🇨🇱شیلی🇨🇱"],['text'=>"🇨🇳چین🇨🇳"]],
[['text'=>"🇨🇴کلمبیا🇨🇴"],['text'=>"🇰🇲کومور🇰🇲"],['text'=>"🇨🇬کنگو🇨🇬"]],
[['text'=>"🇨🇷کاستاریکا🇨🇷"],['text'=>"🇭🇷کرواسی🇭🇷"],['text'=>"🇨🇺کوبا🇨🇺"]],
[['text'=>"🇨🇾قبرس🇨🇾"],['text'=>"🇨🇿چک🇨🇿"],['text'=>"🇩🇯جیبوتی🇩🇯"]],
[['text'=>"🇩🇲دومینیکا🇩🇲"],['text'=>"🇩🇴جمهوری دومنیکن🇩🇴"],['text'=>"🇨🇩جمهوری دموکراتیک کنگو🇨🇩"]],
[['text'=>"🇹🇱تیمور شرقی🇹🇱"],['text'=>"🇪🇨اکوادور🇪🇨"],['text'=>"🇪🇬مصر🇪🇬"]],
[['text'=>"🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿"],['text'=>"🇬🇶گینه استوایی🇬🇶"],['text'=>"🇪🇷اریتره🇪🇷"]],
[['text'=>"🇪🇪استونی🇪🇪"],['text'=>"🇪🇹اتیوپی🇪🇹"],['text'=>"🇫🇮فنلاند🇫🇮"]],
[['text'=>"🇫🇷فرانسه🇫🇷"],['text'=>"??🇫گویان فرانسه🇬🇫"],['text'=>"🇬🇦گابن🇬🇦"]],
[['text'=>"🇬🇲گامبیا🇬🇲"],['text'=>"🇬🇪گرجستان🇬🇪"],['text'=>"🇩🇪آلمان🇩🇪"]],
[['text'=>"🇬🇭غنا🇬🇭"],['text'=>"🇬🇷یونان🇬🇷"],['text'=>"🇬🇩گرنادا🇬🇩"]],
[['text'=>"🇬🇵گوادلوپ🇬🇵"],['text'=>"🇬🇹گواتمالا🇬🇹"],['text'=>"🇬🇳گینه🇬🇳"]],
[['text'=>"🇬🇼گینه بیسائو🇬🇼"],['text'=>"🇬🇾گویان🇬🇾"],['text'=>"🇭🇹هائیتی🇭🇹"]],
[['text'=>"🇭🇳هندوراس🇭🇳"],['text'=>"🇭🇺مجارستان🇭🇺"],['text'=>"🇮🇳هند🇮🇳"]],
[['text'=>"🇮🇩اندونزی🇮🇩"],['text'=>"🇮🇷ایران🇮🇷"],['text'=>"🇮🇶عراق🇮🇶"]],
[['text'=>"🇮🇪ایرلند🇮🇪"],['text'=>"🇮🇱اسرائیل🇮🇱"],['text'=>"🇮🇹ایتالیا🇮🇹"]],
[['text'=>"??🇮ساحل عاج🇨🇮"],['text'=>"🇯🇲جامائیکا🇯🇲"],['text'=>"🇯🇵ژاپن🇯🇵"]],
[['text'=>"🇯🇴اردن🇯🇴"],['text'=>"🇰🇿قزاقستان🇰🇿"],['text'=>"🇰🇪کنیا🇰🇪"]],
[['text'=>"🇰🇼کویت🇰🇼"],['text'=>"🇰🇬قرقیزستان🇰🇬"],['text'=>"🇱🇦لائوس🇱🇦"]],
[['text'=>"🇱🇻لتونی🇱🇻"],['text'=>"🇱🇸لسوتو??🇸"],['text'=>"🇱🇷لیبریا🇱🇷"]],
[['text'=>"🇱🇾لیبی🇱🇾"],['text'=>"🇱🇹لیتوانی🇱🇹"],['text'=>"🇱🇺لوکزامبورگ🇱🇺"]],
[['text'=>"🇲🇴ماکائو🇲🇴"],['text'=>"🇲🇬ماداگاسکار🇲🇬"],['text'=>"🇲🇼مالاوی🇲🇼"]],
[['text'=>"🇲🇾مالزی🇲🇾"],['text'=>"🇲🇻مالدیو🇲🇻"],['text'=>"🇲🇱مالی🇲🇱"]],
[['text'=>"🇲🇷موریتانی🇲🇷"],['text'=>"🇲🇺موریس🇲🇺"],['text'=>"🇲🇽مکزیک🇲🇽"]],
[['text'=>"🇲🇩مولداوی🇲🇩"],['text'=>"🇲🇳مغولستان🇲🇳"],['text'=>"🇲🇪مونته نگرو🇲🇪"]],
[['text'=>"🇲🇸مونتسرات🇲🇸"],['text'=>"🇲🇦مراکش🇲🇦"],['text'=>"🇲🇿موزامبیک🇲🇿"]],
[['text'=>"🇲🇲میانمار🇲🇲"],['text'=>"🇳🇦نامیبیا🇳🇦"],['text'=>"🇳🇵نپال🇳🇵"]],
[['text'=>"🇳🇱هلند🇳🇱"],['text'=>"🇳🇨کالدونیای جدید🇳🇨"],['text'=>"🇳🇿نیوزیلند🇳🇿"]],
[['text'=>"🇳🇮نیکاراگوئه🇳🇮"],['text'=>🇳🇪نیجر🇳🇪],['text'=>"🇳🇬نیجریه🇳🇬"]],
[['text'=>"🇲🇰مقدونیه شمالی🇲🇰"],['text'=>"🇳🇴نروژ🇳🇴"],['text'=>"🇴🇲عمان🇴🇲"]],
[['text'=>"🇵🇰پاکستان🇵🇰"],['text'=>"🇵🇦پاناما🇵🇦"],['text'=>"🇵🇬پاپوآ گینه نو🇵🇬"]],
[['text'=>"🇵🇾پاراگوئه🇵🇾"],['text'=>"🇵🇪پرو🇵🇪"],['text'=>"🇵🇭فیلیپین🇵🇭"]],
[['text'=>"🇵🇱لهستان🇵🇱"],['text'=>"🇵🇹پرتغال🇵🇹"],['text'=>"🇵🇷پورتوریکو🇵🇷"]],
[['text'=>"🇶🇦قطر🇶🇦"],['text'=>"🇷🇪رئونیون🇷🇪"],['text'=>"🇷🇴رومانی🇷🇴"]],
[['text'=>"🇷🇺روسیه🇷🇺"],['text'=>"🇷🇼رواندا🇷🇼"],['text'=>"🇰🇳سنت کیتس و نویس🇰🇳"]],
[['text'=>"🇱🇨سنت لوسیا🇱🇨"],['text'=>"🇻🇨سنت وینسنت و گرنادین ها🇻🇨"]],
[['text'=>"🇸🇻سالوادور🇸🇻"],['text'=>"🇼🇸ساموآ🇼🇸"],['text'=>"🇸🇹سائوتومه و پرینسیپ🇸🇹"]],
[['text'=>"🇸🇦عربستان سعودی🇸🇦"],['text'=>"🇸🇳سنگال🇸🇳"],['text'=>"🇷🇸صربستان🇷🇸"]],
[['text'=>"🇸🇨جمهوری سیشل🇸🇨"],['text'=>"🇸🇱سیرالئون🇸🇱"],['text'=>"🇸🇬سنگاپور🇸🇬"]],
[['text'=>"🇸🇰اسلواکی🇸🇰"],['text'=>"🇸🇮اسلوونی🇸🇮"],['text'=>"🇸🇧جزایر سلیمان🇸🇧"]],
[['text'=>"🇸🇴سومالی🇸🇴"],['text'=>"🇿🇦آفریقای جنوبی🇿🇦"],['text'=>"🇸🇸سودان جنوبی🇸🇸"]],
[['text'=>"🇪🇸اسپانیا🇪🇸"],['text'=>"🇱🇰سریلانکا🇱🇰"],['text'=>"🇸🇩سودان🇸🇩"]],
[['text'=>"🇸🇷سورینام🇸🇷"],['text'=>"🇸🇿سوازیلند🇸🇿"],['text'=>"🇸🇪سوئد????"]],
[['text'=>"🇨🇭سوئیس🇨🇭"],['text'=>"🇸🇾سوریه🇸🇾"],['text'=>"🇹🇼تایوان🇹🇼"]],
[['text'=>"🇹🇯تاجیکستان🇹🇯"],['text'=>"🇹🇿تانزانیا🇹🇿"],['text'=>"🇹🇭تایلند🇹🇭"]],
[['text'=>"🇹🇹ترینیداد و توباگو🇹🇹"],['text'=>"🇹🇬توگو🇹🇬"],['text'=>"🇹🇴تونگا🇹🇴"]],
[['text'=>"🇹🇳تونس🇹🇳"],['text'=>"🇹🇷ترکیه🇹🇷"],['text'=>"🇹🇲ترکمنستان🇹🇲"]],
[['text'=>"🇹🇨جزیره تورکس و کایکوس🇹🇨"],['text'=>"🇦🇪امارات متحده عربی🇦🇪"],['text'=>"🇺🇬اوگاندا🇺🇬"]],
[['text'=>"🇺🇦اوکراین🇺🇦"],['text'=>"🇺🇾اروگوئه🇺🇾"],['text'=>"🇺🇸ایالات متحده آمریکا🇺🇸"]],
[['text'=>"🇺🇿ازبکستان🇺🇿"],['text'=>"🇻🇪ونزوئلا🇻🇪"],['text'=>"🇻🇳ویتنام🇻🇳"]],
[['text'=>"🇻🇬جزایر ویرجین انگلیس🇻🇬"],['text'=>🇾🇪یمن🇾🇪],['text'=>"🇿🇲زامبیا🇿🇲"],['text'=>"🇿🇼زیمبابوه🇿🇼"]],
[['text'=>"🏛 خانه"],['text'=>"💸 شارژ حساب"]],
],'resize_keyboard'=>true]);


@$buttonp2 = '{"keyboard":[[{"text":"🏛 خانه"},{"text":"💳 استعلام | قیمت ها"}],[{"text":"🇦🇫افغانستان🇦🇫"},{"text":"🇦🇱آلبانی🇦🇱"},{"text":"🇩🇿الجزایر🇩🇿"}],[{"text":"🇦🇴آنگولا🇦🇴"},{"text":"🇦🇮آنگویلا🇦🇮"},{"text":"🇦🇬آنتیگوا و باربودا🇦🇬"}],[{"text":"🇦🇷آرژانتین🇦🇷"},{"text":"🇦🇲ارمنستان🇦🇲"},{"text":"🇦🇼آروبا🇦🇼"}],[{"text":"🇦🇺استرالیا🇦🇺"},{"text":"🇦🇹اتریش🇦🇹"},{"text":"🇦🇿آذربایجان🇦🇿"}],[{"text":"🇧🇸باهاما🇧🇸"},{"text":"🇧🇭بحرین🇧🇭"},{"text":"🇧🇩بنگلادش🇧🇩"}],[{"text":"🇧🇧باربادوس🇧🇧"},{"text":"🇧🇾بلاروس🇧🇾"},{"text":"🇧🇪بلژیک🇧🇪"}],[{"text":"🇧🇿بلیز🇧🇿"},{"text":"🇧🇯بنین🇧🇯"},{"text":"🇧🇹بوتان🇧🇹"}],[{"text":"🇧🇦بوسنی و هرزگوین🇧🇦"},{"text":"🇧🇴بولیوی🇧🇴"},{"text":"🇧🇼بوتسوانا🇧🇼"}],[{"text":"🇧🇷برزیل🇧🇷"},{"text":"🇧🇬بلغارستان🇧🇬"},{"text":"🇧🇫بورکینافاسو🇧🇫"}],[{"text":"🇧🇮بوروندی🇧🇮"},{"text":"🇰🇭کامبوج🇰🇭"},{"text":"🇨🇲کامرون🇨🇲"}],[{"text":"🇨🇦کانادا🇨🇦"},{"text":"🇨🇻کیپ ورد🇨🇻"},{"text":"🇰🇾جزایر کیمن🇰🇾"}],[{"text":"🇹🇩چاد🇹🇩"},{"text":"🇨🇱شیلی🇨🇱"},{"text":"🇨🇳چین🇨🇳"}],[{"text":"🇨🇴کلمبیا🇨🇴"},{"text":"🇰🇲کومور🇰🇲"},{"text":"🇨🇬کنگو🇨🇬"}],[{"text":"🇨🇷کاستاریکا🇨🇷"},{"text":"🇭🇷کرواسی🇭🇷"},{"text":"🇨🇺کوبا🇨🇺"}],[{"text":"🇨🇾قبرس🇨🇾"},{"text":"🇨🇿چک🇨🇿"},{"text":"🇩🇯جیبوتی🇩🇯"}],[{"text":"🇩🇲دومینیکا🇩🇲"},{"text":"🇩🇴جمهوری دومنیکن🇩🇴"},{"text":"🇨🇩جمهوری دموکراتیک کنگو🇨🇩"}],[{"text":"🇹🇱تیمور شرقی🇹🇱"},{"text":"🇪🇨اکوادور🇪🇨"},{"text":"🇪🇬مصر🇪🇬"}],[{"text":"🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿"},{"text":"🇬🇶گینه استوایی🇬🇶"},{"text":"🇪🇷اریتره🇪🇷"}],[{"text":"🇪🇪استونی🇪🇪"},{"text":"🇪🇹اتیوپی🇪🇹"},{"text":"🇫🇮فنلاند🇫🇮"}],[{"text":"🇫🇷فرانسه🇫🇷"},{"text":"🇬🇫گویان فرانسه🇬🇫"},{"text":"🇬🇦گابن🇬🇦"}],[{"text":"🇬🇲گامبیا🇬🇲"},{"text":"🇬🇪گرجستان🇬🇪"},{"text":"🇩🇪آلمان🇩🇪"}],[{"text":"🇬🇭غنا🇬🇭"},{"text":"🇬🇷یونان🇬🇷"},{"text":"🇬🇩گرنادا🇬🇩"}],[{"text":"🇬🇵گوادلوپ🇬🇵"},{"text":"🇬🇹گواتمالا🇬🇹"},{"text":"🇬🇳گینه🇬🇳"}],[{"text":"🇬🇼گینه بیسائو🇬🇼"},{"text":"🇬🇾گویان🇬🇾"},{"text":"🇭🇹هائیتی🇭🇹"}],[{"text":"🇭🇳هندوراس🇭🇳"},{"text":"🇭🇺مجارستان🇭🇺"},{"text":"🇮🇳هند🇮🇳"}],[{"text":"🇮🇩اندونزی🇮🇩"},{"text":"🇮🇷ایران🇮🇷"},{"text":"🇮🇶عراق🇮🇶"}],[{"text":"🇮🇪ایرلند🇮🇪"},{"text":"🇮🇱اسرائیل🇮🇱"},{"text":"🇮🇹ایتالیا🇮🇹"}],[{"text":"🇯🇲جامائیکا🇯🇲"},{"text":"🇯🇴اردن🇯🇴"},{"text":"🇰🇿قزاقستان🇰🇿"}],[{"text":"🇰🇪کنیا🇰🇪"},{"text":"🇰🇼کویت🇰🇼"},{"text":"🇰🇬قرقیزستان🇰🇬"}],[{"text":"🇱🇦لائوس🇱🇦"},{"text":"🇱🇻لتونی🇱🇻"},{"text":"🇱🇸لسوتو🇱🇸"}],[{"text":"🇱🇷لیبریا🇱🇷"},{"text":"🇱🇾لیبی🇱🇾"},{"text":"🇱🇹لیتوانی🇱🇹"}],[{"text":"🇲🇬ماداگاسکار🇲🇬"},{"text":"🇲🇼مالاوی🇲🇼"},{"text":"🇲🇾مالزی🇲🇾"}],[{"text":"🇲🇻مالدیو🇲🇻"},{"text":"🇲🇱مالی🇲🇱"},{"text":"🇲🇷موریتانی🇲🇷"}],[{"text":"🇲🇺موریس🇲🇺"},{"text":"🇲🇽مکزیک🇲🇽"},{"text":"🇲🇩مولداوی🇲🇩"}],[{"text":"🇲🇳مغولستان🇲🇳"},{"text":"🇲🇸مونتسرات🇲🇸"},{"text":"🇲🇦مراکش🇲🇦"}],[{"text":"🇲🇿موزامبیک🇲🇿"},{"text":"🇲🇲میانمار🇲🇲"},{"text":"🇳🇦نامیبیا🇳🇦"}],[{"text":"🇳🇵نپال🇳🇵"},{"text":"🇳🇱هلند🇳🇱"},{"text":"🇳🇨کالدونیای جدید🇳🇨"}],[{"text":"🇳🇿نیوزیلند🇳🇿"},{"text":"🇳🇮نیکاراگوئه🇳🇮"},{"text":"🇳🇪نیجر🇳🇪"}],[{"text":"🇳🇬نیجریه🇳🇬"},{"text":"🇲🇰مقدونیه شمالی🇲🇰"},{"text":"🇳🇴نروژ🇳🇴"}],[{"text":"🇴🇲عمان🇴🇲"},{"text":"🇵🇰پاکستان🇵🇰"},{"text":"🇵🇦پاناما🇵🇦"}],[{"text":"🇵🇾پاراگوئه🇵🇾"},{"text":"🇵🇪پرو🇵🇪"},{"text":"🇵🇭فیلیپین🇵🇭"}],[{"text":"🇵🇱لهستان🇵🇱"},{"text":"🇵🇹پرتغال🇵🇹"},{"text":"🇵🇷پورتوریکو🇵🇷"}],[{"text":"🇶🇦قطر🇶🇦"},{"text":"🇷🇪رئونیون🇷🇪"},{"text":"🇷🇴رومانی🇷🇴"}],[{"text":"🇷🇺روسیه🇷🇺"},{"text":"🇷🇼رواندا🇷🇼"},{"text":"🇰🇳سنت کیتس و نویس🇰🇳"}],[{"text":"🇸🇻سالوادور🇸🇻"},{"text":"🇸🇹سائوتومه و پرینسیپ🇸🇹"},{"text":"🇸🇦عربستان سعودی🇸🇦"}],[{"text":"🇸🇳سنگال🇸🇳"},{"text":"🇷🇸صربستان🇷🇸"},{"text":"🇸🇨جمهوری سیشل🇸🇨"}],[{"text":"🇸🇱سیرالئون🇸🇱"},{"text":"🇸🇰اسلواکی🇸🇰"},{"text":"🇸🇴سومالی🇸🇴"}],[{"text":"🇿🇦آفریقای جنوبی🇿🇦"},{"text":"🇸🇸سودان جنوبی🇸🇸"},{"text":"🇪🇸اسپانیا🇪🇸"}],[{"text":"🇱🇰سریلانکا🇱🇰"},{"text":"🇸🇩سودان🇸🇩"},{"text":"🇸🇷سورینام🇸🇷"}],[{"text":"🇸🇿سوازیلند🇸🇿"},{"text":"🇸🇪سوئد🇸🇪"},{"text":"🇸🇾سوریه🇸🇾"}],[{"text":"🇹🇼تایوان🇹🇼"},{"text":"🇹🇯تاجیکستان🇹🇯"},{"text":"🇹🇿تانزانیا🇹🇿"}],[{"text":"🇹🇭تایلند🇹🇭"},{"text":"??🇬توگو🇹🇬"},{"text":"🇹🇳تونس🇹🇳"}],[{"text":"🇹🇷ترکیه🇹🇷"},{"text":"🇹🇲ترکمنستان🇹🇲"},{"text":"🇦🇪امارات متحده عربی🇦🇪"}],[{"text":"??🇬اوگاندا🇺🇬"},{"text":"🇺🇦اوکراین🇺🇦"},{"text":"🇺🇸ایالات متحده آمریکا🇺🇸"}],[{"text":"🇺🇿ازبکستان🇺🇿"},{"text":"🇻🇪ونزوئلا🇻🇪"},{"text":"🇻🇳ویتنام🇻🇳"}],[{"text":"🇾🇪یمن🇾🇪"},{"text":"🇿🇲زامبیا🇿🇲"},{"text":"🇿🇼زیمبابوه🇿🇼"}],[{"text":""},{"text":""},{"text":""}],[{"text":"🏛 خانه"},{"text":"💸 شارژ حساب"}]]}';
if($user["step"] == 'bon'){
exit();
}
if($mobot == 'off' and !in_array($from_id,$admin)){
    bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤ربات توسط مدیریت خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,
  ]); 
exit();
}
if($user["step"] == 'shomop'){
if(!in_array($text, array("❌ لغو خرید","💬 دریافت کد","🚫گزارش مسدودی"))){
exit();
}}

if($user["step"] == 'shooooomop'){
if(!in_array($text, array("❌ لغو خرید","💬 دریافت کد","🚫گزارش مسدودی"))){
exit();
}}
if(strpos($text,"'") !== false or strpos($text,'"') !== false or strpos($text,"}") !== false or strpos($text,"{") !== false or strpos($text,")'") !== false or strpos($text,"(") !== false or strpos($text,",") !== false){ 
$addus= $from_id . "\n";
file_put_contents("data/blocklist.txt", $addus);
  bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"❌دستور اشتباه میباشد لطفا از کیبورد پایین استفاده کنید❌",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,
  ]); 
  bot('sendMessage',[
 'chat_id'=>$admin,
 'text'=>"
یک فرد با اطلاعات زیر قصد هک کردن ربات را داشت اما نتوانست و ما جلو گیری کردیم👮🏻‍♂️

🔻اطلاعات کاربر🔻
👈🏽نام فرد : ( $first_name )👉🏽
👈🏽نام کاربری فرد : (  @$username  )👉🏽
👈🏽آیدی عددی فرد : ( $from_id )👉🏽
👈🏽کد فرستاده شد توسط فرد : ( $text )👉🏽
",
 'parse_mode'=>"MarkDown",
  ]); 
 }
 //-------------------------------------
if($text=="/start"){
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"😄 سلام $first_name
	
به ربات شماره مجازی $far خوش آمدید. 🌹 

🌟 این ربات بصورت اتوماتیک است و میتوانید فقط در ظرف چند ثانیه شماره مجازی و کد اختصاصی شمارهٔ مجازی خودتون رو دریافت کنید.

🗣 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .

👇🏻 از دکمه های زیر استفاده کنید 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
if ($user["id"] != true){
$connect->query("INSERT INTO `user` (`id`, `step`, `stock`, `member`, `listby`, `inviter`, `service` , `number`) VALUES ('$from_id', 'none', '0', '0', '', '', '', NULL)");
}
}
elseif(strpos($text , '/start ') !== false  ) {
if ($user["id"] == true){
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"😄 سلام $first_name
	
به ربات شماره مجازی $far خوش آمدید. 🌹 

🌟 این ربات بصورت اتوماتیک است و میتوانید فقط در ظرف چند ثانیه شماره مجازی و کد اختصاصی شمارهٔ مجازی خودتون رو دریافت کنید.

🗣 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .

👇🏻 از دکمه های زیر استفاده کنید 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
}
else
{
$start = str_replace("/start ","",$text);
$user = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$start' LIMIT 1"));
$plusmember = $user["member"] + 1;
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"😄 سلام $first_name
	
به ربات شماره مجازی $far خوش آمدید. 🌹 

🌟 این ربات بصورت اتوماتیک است و میتوانید فقط در ظرف چند ثانیه شماره مجازی و کد اختصاصی شمارهٔ مجازی خودتون رو دریافت کنید.

🗣 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .

👇🏻 از دکمه های زیر استفاده کنید 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
$name = str_replace(["`","*","_","[","]","(",")","```"],"",$first_name);
bot('sendmessage',[
	'chat_id'=>$start,
	'text'=>"🌟 تبریک ! کاربر [$name](tg://user?id=$from_id) با استفاده از لینک دعوت شما وارد ربات شده
❄️ یک نفر به مجموع زیر مجموعه های شما اضافه شد !

📋 در صورتی که زیر مجموعه شما از ربات خرید کند شما مطلع خواهید شد
💰 5 درصد از هر خرید زیر مجموعه به عنوان هدیه به موجودی شما اضافه میگردد
👥 تعداد زیر مجموعه ها : $plusmember",
	'parse_mode'=>'Markdown',  	]);
$connect->query("UPDATE user SET member = '$plusmember' WHERE id = '$start' LIMIT 1");$connect->query("INSERT INTO `user` (`id`, `step`, `stock`, `member`, `listby`, `inviter`, `service`) VALUES ('$from_id', 'none', '0', '0', '', '$start', '')");
}}
elseif($user['step'] == "contact" and $contact){
if($contactid == $from_id and strpos($contactnum,'98')===0||strpos($contactnum,'+98')===0){
$phone_t='0'.strrev(substr(strrev($contactnum),0,10));
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"احراز هویت شما تایید شد",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
$connect->query("UPDATE user SET number = '$phone_t', step = 'none' WHERE id = '$from_id' LIMIT 1");	
}else{
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"📱برای احراز هویت شماره شما باید حتما ایران باشد!",
'reply_to_message_id'=>$message_id,]);
$connect->query("UPDATE user SET step = 'contact' WHERE id = '$from_id' LIMIT 1");	
}}
elseif($text=="🏛 خانه"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🔘 به منوی اصلی بازگشتید 
	
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
$connect->query("UPDATE user SET step = 'none' , service = '' WHERE id = '$from_id' LIMIT 1");	
}
elseif($text=="📲 خرید شماره مجازی"){
if($mosbot == "off"){
bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤بخش فروش ربات به علت اتمام موجودی پنل خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,  ]); exit();
}else{
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"💢پنل مورد نظر خود را انتخاب کنید⬇️",
'reply_to_message_id'=>$message_id,
	'reply_markup'=>json_encode([
            	'keyboard'=>[[
				['text'=>"🛍 پنل اول"],['text'=>"🛍 پنل دوم"]],
               [['text'=>"💳 استعلام | قیمت ها"],['text'=>"🏛 خانه"]],],'resize_keyboard'=>true])]);
$connect->query("UPDATE user SET step = 'choosePanel' WHERE id = '$from_id' LIMIT 1");
}
else
{
 bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}}

//-------panel

elseif($text=="♻️تعویض پنل"){
if($mosbot == "off"){
bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤بخش فروش ربات به علت اتمام موجودی پنل خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,  ]); exit();
}else{
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"💢پنل مورد نظر خود را انتخاب کنید⬇️",
'reply_to_message_id'=>$message_id,
	'reply_markup'=>json_encode([
            	'keyboard'=>[[
				['text'=>"🛍 پنل اول"],['text'=>"🛍 پنل دوم"]],
              [['text'=>"🏛 خانه"]],],'resize_keyboard'=>true])]);
$connect->query("UPDATE user SET step = 'choosePanel' WHERE id = '$from_id' LIMIT 1");
}
else
{
 bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}}


elseif($text=="🛍 پنل اول"){
	$stepNextA = str_replace(["🛍 پنل اول","🛍 پنل دوم"],["moshop","moshzzzzzop"],$text);
if($mosbot1 == "off"){
bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤بخش فروش پنل اول ربات توسط مدیریت خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,  ]); exit();
}else{
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🔘 میخواهید در چه برنامه ای ثبت نام کنید ؟ سرویس یا اپلکیشن مورد نظر خود را انتخاب کنید 👇🏻

⚠️ توجه کنید در انتخاب سرویس دقت کنید ! زیر کد تایید ارسالی بر اساس سرویس دسته بندی خواهد شد .
ℹ️ درصورتی که در این قسمت نیاز به راهنما دارید از دکمه '🚦 راهنما' استفاده کنید",
'reply_to_message_id'=>$message_id,
	'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"💎 تلگرام"],['text'=>"📨 گوگل"]
				],
				           [
                   ['text'=>"📸 اینستاگرام"],['text'=>"📱 واتساپ"]
                ],
                 [
                   ['text'=>"📗 لاین"],['text'=>"🐦 توییتر"],['text'=>"💡 وایبر"]
                ],
				                 [
                   ['text'=>"💻 ماکروسافت"],['text'=>"📪 فیسبوک"],['text'=>"💬 تیک تاک"]
                ],
								                 [
                   ['text'=>"📞 یاهو"],['text'=>"❄️ امازون"],['text'=>"☎️ پی پال"]
                ],
				                 [
                   ['text'=>"🏛 خانه"],['text'=>"🚦 راهنما"]
                ],
               [
      ['text'=>"♻️تعویض پنل"]
]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
$connect->query("UPDATE user SET step = '$stepNextA' WHERE id = '$from_id' LIMIT 1");
}
else
{
 bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);

}
}}



elseif($text=="🛍 پنل دوم"){
	$stepNextA = str_replace(["🛍 پنل اول","🛍 پنل دوم"],["moshop","moshzzzzzop"],$text);
if($mosbot2 == "off"){
bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤بخش فروش پنل دوم ربات توسط مدیریت خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,  ]); exit();
}else{
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🔘 میخواهید در چه برنامه ای ثبت نام کنید ؟ سرویس یا اپلکیشن مورد نظر خود را انتخاب کنید 👇🏻

⚠️ توجه کنید در انتخاب سرویس دقت کنید ! زیر کد تایید ارسالی بر اساس سرویس دسته بندی خواهد شد .
ℹ️ درصورتی که در این قسمت نیاز به راهنما دارید از دکمه '🚦 راهنما' استفاده کنید",
'reply_to_message_id'=>$message_id,
	'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"💎 تلگرام"],['text'=>"📨 گوگل"]
				],
				           [
                   ['text'=>"📸 اینستاگرام"],['text'=>"📱 واتساپ"]
                ],
                 [
                   ['text'=>"📗 لاین"],['text'=>"🐦 توییتر"],['text'=>"💡 وایبر"]
                ],
				                 [
                   ['text'=>"💻 ماکروسافت"],['text'=>"📪 فیسبوک"],['text'=>"💬 تیک تاک"]
                ],
								                 [
                   ['text'=>"📞 یاهو"],['text'=>"❄️ امازون"],['text'=>"☎️ پی پال"]
                ],
				                 [
                   ['text'=>"🏛 خانه"],['text'=>"🚦 راهنما"]
                ],
               [
      ['text'=>"♻️تعویض پنل"]
]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
$connect->query("UPDATE user SET step = '$stepNextA' WHERE id = '$from_id' LIMIT 1");
}
else
{
 bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);

}
}}




elseif($text=="🛍 پنل اول" or $text == "🛍 پنل دوم" and $user["step"] == 'choosePanel'){
	$stepNextA = str_replace(["🛍 پنل اول","🛍 پنل دوم"],["moshop","moshzzzzzop"],$text);
if($mosbot == "off"){
bot('sendMessage',[
 'chat_id'=>$chat_id,
 'text'=>"👤بخش فروش پنل اول ربات توسط مدیریت خاموش شده است ؛ لطفا در ساعات دیگر امتحان کنید🍃",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,  ]); exit();
}else{
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🔘 میخواهید در چه برنامه ای ثبت نام کنید ؟ سرویس یا اپلکیشن مورد نظر خود را انتخاب کنید 👇🏻

⚠️ توجه کنید در انتخاب سرویس دقت کنید ! زیر کد تایید ارسالی بر اساس سرویس دسته بندی خواهد شد .
ℹ️ درصورتی که در این قسمت نیاز به راهنما دارید از دکمه '🚦 راهنما' استفاده کنید",
'reply_to_message_id'=>$message_id,
	'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"💎 تلگرام"],['text'=>"📨 گوگل"]
				],
				           [
                   ['text'=>"📸 اینستاگرام"],['text'=>"📱 واتساپ"]
                ],
                 [
                   ['text'=>"📗 لاین"],['text'=>"🐦 توییتر"],['text'=>"💡 وایبر"]
                ],
				                 [
                   ['text'=>"💻 ماکروسافت"],['text'=>"📪 فیسبوک"],['text'=>"💬 تیک تاک"]
                ],
								                 [
                   ['text'=>"📞 یاهو"],['text'=>"❄️ امازون"],['text'=>"☎️ پی پال"]
                ],
				                 [
                   ['text'=>"🏛 خانه"],['text'=>"🚦 راهنما"]
                ],
               [
      ['text'=>"♻️تعویض پنل"]
]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
$connect->query("UPDATE user SET step = '$stepNextA' WHERE id = '$from_id' LIMIT 1");
}
else
{
 bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);

}
}}
	
	











elseif($text=="💳 استعلام | قیمت ها"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"
☑️ قیمت و استعلام موجودی شماره ‌ها 👇🏻
ℹ️ لیست شماره های موجود و استعلام شماره ها هر 10 دقیقه یک بار بروز خواهد شد .

✅ = موجود میباشد
❌ = موجود نیست
❗️ = نامشخص است

🔄 اگر از تلفن همراه استفاده میکنید ! برای نمایش بهتر لیست استعلام گوشی خود را در حالت افقی نگه دارید⬇️
",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[[['text'=>"🛍 پنل دوم",'callback_data'=>"gold"],['text'=>"🛍 پنل اول",'callback_data'=>"silver"]],		]])]);
}else{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}}elseif($data=="backmob"){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$chatid' LIMIT 1");
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>json_encode([
    'inline_keyboard'=>[[['text'=>"🛍 پنل دوم",'callback_data'=>"gold"],['text'=>"🛍 پنل اول",'callback_data'=>"silver"]],		]])]);exit ();}
elseif($data=="silver"){
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>json_encode([
    'inline_keyboard'=>[[['text'=>"📨 گوگل",'callback_data'=>"google"],['text'=>"📞 یاهو",'callback_data'=>"yahoo"],['text'=>"📸 اینستاگرام",'callback_data'=>"instagram"]],
				
[['text'=>"💎 تلگرام",'callback_data'=>"telegram"],['text'=>"📱 واتساپ",'callback_data'=>"whatsapp"],['text'=>"📗 لاین",'callback_data'=>"line"]],
						
[['text'=>"🐦 توییتر",'callback_data'=>"twitter"],['text'=>"💡 وایبر",'callback_data'=>"viber"],['text'=>"💻 ماکروسافت",'callback_data'=>"microsoft"]],	

[['text'=>"📪 فیسبوک",'callback_data'=>"facebook"],['text'=>"💬 تیک تاک",'callback_data'=>"tiktok"],['text'=>"❄️ امازون",'callback_data'=>"amazon"]],		

[['text'=>"☎️ پی پال",'callback_data'=>"paypal"]],

[['text'=>"🔙برگشت",'callback_data'=>"backmob"]],]])]);exit ();
}
elseif($data=="gold"){
$connect->query("UPDATE user SET step = 'gold' WHERE id = '$chatid' LIMIT 1");
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>json_encode([
    'inline_keyboard'=>[[['text'=>"📨 گوگل",'callback_data'=>"google"],['text'=>"📞 یاهو",'callback_data'=>"yahoo"],['text'=>"📸 اینستاگرام",'callback_data'=>"instagram"]],
				
[['text'=>"💎 تلگرام",'callback_data'=>"telegram"],['text'=>"📱 واتساپ",'callback_data'=>"whatsapp"],['text'=>"📗 لاین",'callback_data'=>"line"]],
						
[['text'=>"🐦 توییتر",'callback_data'=>"twitter"],['text'=>"💡 وایبر",'callback_data'=>"viber"],['text'=>"💻 ماکروسافت",'callback_data'=>"microsoft"]],	

[['text'=>"📪 فیسبوک",'callback_data'=>"facebook"],['text'=>"💬 تیک تاک",'callback_data'=>"tiktok"],['text'=>"❄️ امازون",'callback_data'=>"amazon"]],		

[['text'=>"☎️ پی پال",'callback_data'=>"paypal"]],

[['text'=>"🔙برگشت",'callback_data'=>"backmob"]],]])]);exit ();
}
elseif(in_array($data, array("paypal","viber","amazon","tiktok","facebook","microsoft","instagram","yahoo","google","telegram","twitter","whatsapp","line"))){ 
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه اول سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه اول سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه اول سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه اول سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه اول سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه اول سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه اول سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه اول سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه اول سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه اول سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه اول سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه اول سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه اول سرویس 🐦 توییتر منتقل شدید";
}
if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 0,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` LIMIT 0,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}

$key[] = [['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."1"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal1","viber1","amazon1","tiktok1","facebook1","microsoft1","instagram1","yahoo1","google1","telegram1","twitter1","whatsapp1","line1"))){
$mo = "دوم";
$data = explode("1", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}
if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 19,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 19,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."2"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal2","viber2","amazon2","tiktok2","facebook2","microsoft2","instagram2","yahoo2","google2","telegram2","twitter2","whatsapp2","line2"))){
$mo = "سوم";
$data = explode("2", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}
if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 39,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 39,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."1"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."3"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal3","viber3","amazon3","tiktok3","facebook3","microsoft3","instagram3","yahoo3","google3","telegram3","twitter3","whatsapp3","line3"))){
$mo = "چهارم";
$data = explode("3", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس ?? تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}
if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 59,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 59,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."2"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."4"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal4","viber4","amazon4","tiktok4","facebook4","microsoft4","instagram4","yahoo4","google4","telegram4","twitter4","whatsapp4","line4"))){
$mo = "پنجم";
$data = explode("4", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}
if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 79,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 79,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."3"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."5"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal5","viber5","amazon5","tiktok5","facebook5","microsoft5","instagram5","yahoo5","google5","telegram5","twitter5","whatsapp5","line5"))){
$mo = "ششم";
$data = explode("5", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 99,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 99,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
if($i == 20){
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."4"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."6"]];
}else{
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."4"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"]];
}
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal6","viber6","amazon6","tiktok6","facebook6","microsoft6","instagram6","yahoo6","google6","telegram6","twitter6","whatsapp6","line6"))){
$mo = "هفتم";
$data = explode("6", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس ?? اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 119,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 119,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
if($i == 20){
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."5"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."7"]];
}else{
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."5"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"]];
}
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal7","viber7","amazon7","tiktok7","facebook7","microsoft7","instagram7","yahoo7","google7","telegram7","twitter7","whatsapp7","line7"))){
$mo = "هشتم";
$data = explode("7", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 139,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 139,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
if($i == 20){
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."6"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."9"]];
}else{
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."6"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"]];
}
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal9","viber9","amazon9","tiktok9","facebook9","microsoft9","instagram9","yahoo9","google9","telegram9","twitter9","whatsapp9","line9"))){
$mo = "نهم";
$data = explode("9", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 159,20");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 159,20");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
if($i == 20){
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."7"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"],['text'=>"➡️ صفحه بعد",'callback_data'=>"$setsd"."10"]];
}else{
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."7"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"]];
}
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif(in_array($data, array("paypal10","viber10","amazon10","tiktok10","facebook10","microsoft10","instagram10","yahoo10","google10","telegram10","twitter10","whatsapp10","line10"))){
$mo = "دهم";
$data = explode("10", $data)[0];
if($data=="paypal"){
$setsd = 'paypal';
$motext = "ℹ به صفحه $mo سرویس ☎️ پی پال منتقل شدید";
}
if($data=="viber"){
$setsd = 'viber';
$motext = "ℹ به صفحه $mo سرویس 💡 وایبر منتقل شدید";
}
if($data=="amazon"){
$setsd = 'amazon';
$motext = "ℹ به صفحه $mo سرویس ❄️ امازون منتقل شدید";
}
if($data=="tiktok"){
$setsd = 'tiktok';
$motext = "ℹ به صفحه $mo سرویس 💬 تیک تاک منتقل شدید";
}
if($data=="facebook"){
$setsd = 'facebook';
$motext = "ℹ به صفحه $mo سرویس 📪 فیسبوک منتقل شدید";
}
if($data=="microsoft"){
$setsd = 'microsoft';
$motext = "ℹ به صفحه $mo سرویس 💻 ماکروسافت منتقل شدید";
}
if($data=="instagram"){
$setsd = 'instagram';
$motext = "ℹ به صفحه $mo سرویس 📸 اینستاگرام منتقل شدید";
}
if($data=="yahoo"){
$setsd = 'yahoo';
$motext = "ℹ به صفحه $mo سرویس 📞 یاهو منتقل شدید";
}
if($data=="google"){
$setsd = 'google';
$motext = "ℹ به صفحه $mo سرویس 📨 گوگل منتقل شدید";
}
if($data=="telegram"){
$setsd = 'telegram';
$motext = "ℹ به صفحه $mo سرویس 💎 تلگرام منتقل شدید";
}
if($data=="whatsapp"){
$setsd = 'whatsapp';
$motext = "ℹ به صفحه $mo سرویس 📱 واتساپ منتقل شدید";
}
if($data=="line"){
$setsd = 'line';
$motext = "ℹ به صفحه $mo سرویس لاین منتقل شدید";
}
if($data=="twitter"){
$setsd = 'twitter';
$motext = "ℹ به صفحه $mo سرویس 🐦 توییتر منتقل شدید";
}if($usercall["step"] == "gold"){$mobasel = mysqli_query($connect,"SELECT * FROM `gold` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 179,7");
}else{
$mobasel = mysqli_query($connect,"SELECT * FROM `silver` WHERE $setsd != '0' ORDER BY `$setsd` ASC LIMIT 179,7");
}
$key[] = [['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"💰 قیمت",'callback_data'=>"text"],['text'=>"✅ وضعیت",'callback_data'=>"text"]];
$i = 0;
while($row = mysqli_fetch_array($mobasel)){
    $i++;
    $name = $row['name'] ?? null;
    $gheimat = $row["$setsd"] ?? null;
    $count = $row[$setsd."Count"] ?? 0;
    if(isset($count) && !empty($count) && $count > 0){
        if(!empty($name) && !empty($gheimat)){
            $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=> "✅ [$count]" ,'callback_data'=>"text"]];
        }
    } else {
        if(!empty($name) && !empty($gheimat)){
             $key[] = [['text'=> $name, 'callback_data'=>"text"],['text'=> $gheimat, 'callback_data'=>"text"],['text'=>"✅ [1000]" ,'callback_data'=>"text"]];
        }
    }
}
$key[] = [['text'=>"⬅️ صفحه قبل",'callback_data'=>"$setsd"."9"],['text'=>"↩️ منوی اصلی",'callback_data'=>"backer"]];
$mobutton = json_encode(['inline_keyboard'=>$key]);
bot('answercallbackquery', ['callback_query_id' =>$membercall,'text' => $motext,]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,'message_id'=>$messageid,'reply_markup'=>$mobutton]);exit ();
}
elseif($data=="backer"){
       bot('answercallbackquery', [
            'callback_query_id' =>$membercall,
            'text' => "ℹ به منوی اصلی منتقل شدید",        ]);
bot('editMessageReplyMarkup',['chat_id'=>$chatid,   'message_id'=>$messageid,'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=>"📨 گوگل",'callback_data'=>"google"],['text'=>"📞 یاهو",'callback_data'=>"yahoo"],['text'=>"?? اینستاگرام",'callback_data'=>"instagram"]],
				[['text'=>"💎 تلگرام",'callback_data'=>"telegram"],['text'=>"📱 واتساپ",'callback_data'=>"whatsapp"],['text'=>"📗 لاین",'callback_data'=>"line"]],
						[['text'=>"🐦 توییتر",'callback_data'=>"twitter"],['text'=>"💡 وایبر",'callback_data'=>"viber"],['text'=>"💻 ماکروسافت",'callback_data'=>"microsoft"]],	
[['text'=>"📪 فیسبوک",'callback_data'=>"facebook"],['text'=>"💬 تیک تاک",'callback_data'=>"tiktok"],['text'=>"❄️ امازون",'callback_data'=>"amazon"]],		
[['text'=>"☎️ پی پال",'callback_data'=>"paypal"]],
[['text'=>"🔙برگشت",'callback_data'=>"backmob"]],]])]);exit ();
}
elseif($text=="👤 اطلاعات حساب"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
$listby = count(explode("^", $user["listby"])) - 1;
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🎫 حساب کاربری شما در ربات خرید شماره مجازی :

🗣 نام : $first_name
🆔 شناسه : $from_id
💰 موجودی : {$user["stock"]} تومان
🛍 تعداد خرید : $listby
👥 تعداد زیر مجموعه ها : {$user["member"]} نفر

💎 با دعوت هر نفر به ربات 5 درصد از هر خرید هر زیر مجموعه را هدیه بگیرید
🌟 برای کسب اطمینان نسبت به ربات و خرید های موفق میتوانید به کانال خرید ها مراجعه کنید 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
			[
	['text'=>"🗣 دعوت دیگران",'callback_data'=>"member"],['text'=>"💎 خرید من",'callback_data'=>"myby"]
	],
				[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="↗️ انتقال موجودی"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
if($user["number"] == null){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"📲  جهت خرید لازم است شماره خود را با دکمه پایین به اشتراک کنید ! شماره ارسال شده شما فقط جهت احراز هویت است.",
'reply_markup'=>json_encode(['keyboard'=>[[['text'=>'⏳ تایید حساب کاربری ⏳' , 'request_contact' => true]],],'resize_keyboard'=>true])
            ]);
$connect->query("UPDATE user SET step = 'contact' WHERE id = '$from_id' LIMIT 1");
}else{
$stock = $user["stock"] - 200;
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"⤴️ برای انتقال موجودی ابتدا شناسه فرد را وارد کنید و در خط پایین مقدار موجودی ارسالی را به تومان وارد کنید !	
🆔 شناسه کاربری هر فرد در قسمت اطلاعات حساب وی مشخص هست 
💰 حداکثر موجودی قابل انتقال : $stock تومان

⚠️ توجه کنید که هزینه انتقال موجودی برای هر بار 200 تومان میباشد ! و حداقل انتقال موجودی 100 تومان میباشد
ℹ️ مثال :
267785153
1000",
'reply_markup'=>json_encode([
            	'keyboard'=>[
								    [
                ['text'=>"🏛 خانه"]
                ]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
$connect->query("UPDATE user SET step = 'sendcoin' WHERE id = '$from_id' LIMIT 1");
}}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="💸 شارژ حساب"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
if($user["number"] == null){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"📲  جهت خرید لازم است شماره خود را ارسال کنید !
شماره ارسال شده شما فقط جهت احراز هویت است. . .",
'reply_markup'=>json_encode(['keyboard'=>[[['text'=>'⏳ تایید حساب کاربری ⏳' , 'request_contact' => true]],],'resize_keyboard'=>true])
            ]);
$connect->query("UPDATE user SET step = 'contact' WHERE id = '$from_id' LIMIT 1");
}else{
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"💵 به قسمت شارژ حساب خوش آمدید 

💎 برای افزایش موجودی حساب خود بر روی هر یک از مبالغ دلخواه کلیک کرده و پس از منتقل شدن به درگاه امن بانک، آن را پرداخت کنید .
☑️ تمامی پرداخت ها به صورت اتوماتیک بوده و پس از تراکنش موفق مبلغ آن به موجودی حساب شما در ربات افزوده خواهد شد .

🆔 شناسه : $from_id
💰 موجودی : {$user["stock"]} تومان

🗣 درصورتی که امکان پرداخت آنلاین و همراه با رمز دوم را ندارد میتوانید از پرداخت آفلاین و همراه با کارت به کارت استفاده کنید !

👮🏻 در صورت بروز هرگونه مشکل و یا انجام نشدن پرداخت کافیست با پشتیبانی در تماس باشید .
🌟 لطفا بسته مورد نظر خود را انتخاب کنید تا به صفحه خرید منتقل شوید 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
	[
	['text'=>"💰 1000 تومان",'url'=>"$web/pay/pay.php?amount=1000&id=$from_id"],['text'=>"💰 2000 تومان",'url'=>"$web/pay/pay.php?amount=2000&id=$from_id"]
	],
		[
	['text'=>"💰 3000 تومان",'url'=>"$web/pay/pay.php?amount=3000&id=$from_id"],['text'=>"💰 5000 تومان",'url'=>"$web/pay/pay.php?amount=5000&id=$from_id"]
	],
			[
	['text'=>"💰 8000 تومان",'url'=>"$web/pay/pay.php?amount=8000&id=$from_id"],['text'=>"💰 10000 تومان",'url'=>"$web/pay/pay.php?amount=10000&id=$from_id"]
	],
				[
	['text'=>"💰 20000 تومان",'url'=>"$web/pay/pay.php?amount=20000&id=$from_id"],['text'=>"💰 50000 تومان",'url'=>"$web/pay/pay.php?amount=50000&id=$from_id"]
	],
					[
	['text'=>"💳 خرید آفلاین",'callback_data'=>'cart'],['text'=>"💎 مبالغ دیگر",'callback_data'=>'otheramount']
	],
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="🗣 دعوت دیگران"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
		$id = bot('sendphoto',[
	'chat_id'=>$chat_id,
	'photo'=>"https://t.me/fatherweb/4",
	'caption'=>"☎️ ربات شماره مجازی $far✔️

✅ شماره خام و اختصاصی
💵 تضمین ارزان ‌ترین قیمت و بیشترین کیفیت 💯
☑️ تحویل کاملا اتوماتیک و خودکار
💎 امکان دریافت شماره مجازی رایگان با جمع اوری زیرمجموعه
🌐 دریافت شماره از 190 کشور دنیا برای سرویس های محبوب!
⭕️ پیشنهاد ویژه شماره ایران تنها {$moneys($setsd,iran)} تومان😍 البته تموم کشورا نیز قیمتش کمه توی استعلام میتونید ببینید!
شماره مجازی سایر کشور ها هم به این ارزونی هستند🤩

💢 باور نداری خودت ببین👇🏼👇??

t.me/$usernamebot?start=$from_id",
    		])->result->message_id;
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"💬 پیام بالا حاوی لینک دعوت اختصاصی شما به ربات است 	
🗣 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .

?? موجودی شما : {$user["stock"]} تومان
👥 تعداد زیر مجموعه ها : {$user["member"]} نفر",
	'reply_to_message_id'=>$id,
    		]);
}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="/start"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
'reply_markup'=>$button ]);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
elseif($text=="🏛 خانه"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
'reply_markup'=>$button]);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
elseif($text=="👮🏻 پشتیبانی"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"👮🏻 همکاران ما در خدمت شما هستن !
	
🔘 در صورت وجود نظر , ایده , گزارش مشکل , پیشنهاد , ایراد سوال , یا انتقاد میتوانید با ما در ارتباط باشید 
💬 لطفا پیام خود را به صورت فارسی و روان ارسال کنید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
            	'keyboard'=>[
								    [
                ['text'=>"🏛 خانه"]
                ]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
$connect->query("UPDATE user SET step = 'sup' WHERE id = '$from_id' LIMIT 1");	
}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="🚦 راهنما"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$from_id"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🚸 به بخش راهنما خوش امدید

🤗 شما با استفاده از این ربات هوشمند شماره مجازی کشور ها مختلف را به صورت ارزان خریدار می کنید.
♋️ تمام روند خرید و دریافت شماره و ثبت نام در برنامه مورد نظر کاملا اتوماتیک انجام می شود.
📴 با کم ترین هزینه ممکن در سریع ترین زمان و امن ترین حالت ممکن شماره مجازی خود را خریداری نمایید.
👮🏻 در صورت بروز هرگونه مشکل با کلید بر روی دکمه پشتیبانی در منو اصلی با ما ارتباط برقرار نمایید.

👇🏼 از منو زیر جهت راهنمایی استفاده کنید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"📲 شماره مجازی چیست"],['text'=>"💵 اموزش افزایش موجودی"],
				],
				           [
                   ['text'=>"💎 کسب درآمد"],['text'=>"📽 اموزش خرید شماره مجازی"]
                ],
                 [
                   ['text'=>"ℹ️ قوانین"],['text'=>"💡 درباره"],['text'=>"🔔 سوالات"]
                ],
                
				                 [
                   ['text'=>"🏛 خانه"]
                ]
 	],
            	'resize_keyboard'=>true
       		])
            ]);
}
else
{
bot('sendmessage',[
        "chat_id"=>$chat_id,
        "text"=>"📞 برای استفاده از ربات « خرید شماره مجازی » ابتدا باید وارد کانال زیر شوید
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

👇 بعد از « عضویت » بروی دکمه زیر کلیک کنید 👇",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$buttonj]);
}
}
elseif($text=="❌ لغو خرید" and $user["step"] == 'shomop'){

$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats($allinfo[2]));
if($info[0] =="STATUS_OK" ) {
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"❗️کاربر عزیز به علت وارد کردن شماره در نرم افزار مربوطه توسط شما و آمدن کد برای ربات ( و کم شدن موجودی ما در پنل خارجی ) ، مبلغ شماره مورد نظر از حساب شما کسر شد و کد تحویل داده شد.

🔅چنانچه کد توسط شما وارد نشده و یا اشتباه است، تا ده دقیقه مهلت دارید به پشتیبانی مراجعه کنید تا در صورت آنلاین بودن پشتیبانی ، کد جدید را به شما بدهند.♻️.
	
	✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
$test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/$allinfo[1]/virtual18"),true);
$bale = $test["$allinfo[0]"]["Price"];
$sage = $bale * $mosod;
$sos = ceil($sage);
//-
$webpool = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);
//-
$amount = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);
$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");
$name = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧??","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","??🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس??🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳??","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],$allinfo[1]);
$servic = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","?? ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل اول",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	exit();
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
	$tst = explode("^",$user["service"])[2];
file_get_contents("http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=$apikey&action=setStatus&status=-1&id=$tst");
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"✅ خرید شما با موفقیت لغو شد و مبلغی از حساب شما کسر نشد !
	
🔘 به منوی اصلی بازگشتید 
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
}}

//-----------

elseif($text=="🚫گزارش مسدودی" and $user["step"] == 'shomop'){

$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats($allinfo[2]));
if($info[0] =="STATUS_OK" ) {
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"❗️کاربر عزیز به علت وارد کردن شماره در نرم افزار مربوطه توسط شما و آمدن کد برای ربات ( و کم شدن موجودی ما در پنل خارجی ) ، مبلغ شماره مورد نظر از حساب شما کسر شد و کد تحویل داده شد.

🔅چنانچه کد توسط شما وارد نشده و یا اشتباه است، تا ده دقیقه مهلت دارید به پشتیبانی مراجعه کنید تا در صورت آنلاین بودن پشتیبانی ، کد جدید را به شما بدهند.♻️.
	
	✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
$test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/$allinfo[1]/virtual18"),true);
$bale = $test["$allinfo[0]"]["Price"];
$sage = $bale * $mosod;
$sos = ceil($sage);
//-
$webpool = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);

$amount = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);
$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");
$name = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧??","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس??🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","??🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],$allinfo[1]);
$servic = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","?? ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل اول",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	exit();
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
	$tst = explode("^",$user["service"])[2];
file_get_contents("http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=$apikey&action=setStatus&status=-1&id=$tst");
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"گزارش مسدودی شماره ثبت شد✨
	
🔘 به منوی اصلی بازگشتید 
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
}}

//------------
elseif($text=="❌ لغو خرید" and $user["step"] == 'shooooomop'){
$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats2($allinfo[2]));
if($info[0] =="STATUS_OK" ) {
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"❗️کاربر عزیز به علت وارد کردن شماره در نرم افزار مربوطه توسط شما و آمدن کد برای ربات ( و کم شدن موجودی ما در پنل خارجی ) ، مبلغ شماره مورد نظر از حساب شما کسر شد و کد تحویل داده شد.

🔅چنانچه کد توسط شما وارد نشده و یا اشتباه است، تا ده دقیقه مهلت دارید به پشتیبانی مراجعه کنید تا در صورت آنلاین بودن پشتیبانی ، کد جدید را به شما بدهند.♻️.
	
	✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
$test = json_decode(file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getPrices&service=$allinfo[0]&country=$allinfo[1]"),true);
$bale = $test["$allinfo[1]"]["$allinfo[0]"]["cost"];
$sage = $bale * $mosod1;
$sos = ceil($sage);

$tst = explode("^",$user["service"])[2];
//$webpool = str_replace(["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);
//$amount = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);

$amount = $sos;
$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");

$patern = ["/^74$/","/^155$/","/^58$/","/^76$/","/^181$/","/^39$/","/^148$/","/^179$/","/^175$/","/^50$/","/^35$/","/^122$/","/^145$/","/^60$/","/^118$/","/^51$/","/^82$/","/^120$/","/^92$/","/^123$/","/^73$/","/^83$/","/^119$/","/^24$/","/^41$/","/^36$/","/^186$/","/^42$/","/^3$/","/^33$/","/^133$/","/^150$/","/^93$/","/^45$/","/^113$/","/^77$/","/^63$/","/^126$/","/^easttimor$/","/^105$/","/^21$/","/^16$/","/^167$/","/^176$/","/^34$/","/^ethiopia$/","/^finland$/","/^france$/","/^frenchguiana$/","/^154$/","/^28$/","/^128$/","/^43$/","/^38$/","/^129$/","/^127$/","/^guadeloupe$/","/^94$/","/^68$/","/^130$/","/^131$/","/^26$/","/^88$/","/^84$/","/^22$/","/^6$/","/^57$/","/^47$/","/^23$/","/^13$/","/^86$/","/^ivorycoast$/","/^103$/","/^japan$/","/^116$/","/^2$/","/^8$/","/^100$/","/^11$/","/^25$/","/^49$/","/^136$/","/^135$/","/^102$/","/^44$/","/^luxembourg$/","/^macau$/","/^17$/","/^137$/","/^7$/","/^159$/","/^69$/","/^114$/","/^157$/","/^54$/","/^85$/","/^72$/","/^montenegro$/","/^180$/","/^37$/","/^80$/","/^5$/","/^138$/","/^81$/","/^48$/","/^185$/","/^67$/","/^90$/","/^139$/","/^19$/","/^183$/","/^174$/","/^107$/","/^66$/","/^112$/","/^papuanewguinea$/","/^87$/","/^65$/","/^4$/","/^15$/","/^117$/","/^97$/","/^111$/","/^146$/","/^32$/","/^0$/","/^140$/","/^134$/","/^saintlucia$/","/^101$/","/^178$/","/^53$/","/^61$/","/^29$/","/^184$/","/^115$/","/^141$/","/^149$/","/^31$/","/^177$/","/^56$/","/^64$/","/^98$/","/^142$/","/^106$/","/^46$/","/^110$/","/^55$/","/^143$/","/^9$/","/^52$/","/^99$/","/^89$/","/^62$/","/^161$/","/^turksandcaicos$/","/^95$/","/^75$/","/^1$/","/^187$/","/^187$/","/^40$/","/^70$/","/^10$/","/^30$/","/^147$/","/^96$/"];
$flags = ["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦??استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇯بنین🇧🇯","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇹🇩چاد🇹🇩","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇲دومینیکا🇩🇲","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"];
$name = preg_replace($patern, $flags, $allinfo[0]);

$servic = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل دوم",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    	exit();
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
	
		$tst = explode("^",$user["service"])[2];
file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=setStatus&status=8&id=$tst");

bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"✅ خرید شما با موفقیت لغو شد و مبلغی از حساب شما کسر نشد !

🔘 به منوی اصلی بازگشتید 
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
}}


//-----

elseif($text=="🚫گزارش مسدودی" and $user["step"] == 'shooooomop'){
$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats2($allinfo[2]));
if($info[0] =="STATUS_OK" ) {
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"❗️کاربر عزیز به علت وارد کردن شماره در نرم افزار مربوطه توسط شما و آمدن کد برای ربات ( و کم شدن موجودی ما در پنل خارجی ) ، مبلغ شماره مورد نظر از حساب شما کسر شد و کد تحویل داده شد.

🔅چنانچه کد توسط شما وارد نشده و یا اشتباه است، تا ده دقیقه مهلت دارید به پشتیبانی مراجعه کنید تا در صورت آنلاین بودن پشتیبانی ، کد جدید را به شما بدهند.♻️.
	
	✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
$test = json_decode(file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getPrices&service=$allinfo[0]&country=$allinfo[1]"),true);
$bale = $test["$allinfo[1]"]["$allinfo[0]"]["cost"];
$sage = $bale * $mosod1;
$sos = ceil($sage);
	$tst = explode("^",$user["service"])[2];

file_get_contents("	https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=setStatus&status=6&id=$tst");
//-
$webpool = str_replace(["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);
//-
$amount = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);

$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");
$name = str_replace(["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","??🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان??🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],$allinfo[1]);


$servic = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل دوم",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    	exit();
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
	
		$tst = explode("^",$user["service"])[2];
file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=setStatus&status=8&id=$tst");

bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"گزارش مسدودی شماره ثبت شد✨

🔘 به منوی اصلی بازگشتید 
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
}}

//----





elseif($text=="❌ لغو"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"✅ پروسه افزایش موجودی با موفقیت لغو شد .
	
🔘 به منوی اصلی بازگشتید 
🌟 گزینه مورد نظر خودت رو انتخاب کن 👇🏻",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
elseif($text=="💬 دریافت کد" and $user["step"] == 'shomop'){
$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats($allinfo[2]));
switch ($info[0]) {
    case "STATUS_OK":
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
$test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/$allinfo[1]/virtual18"),true);
$bale = $test["$allinfo[0]"]["Price"];
$sage = $bale * $mosod;
$sos = ceil($sage);
//-
$webpool = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);
//-
$amount = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);
$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");
$name = str_replace(["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا??🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن??🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی??🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱??لیبی??🇾","🇱??لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲??","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲??","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","??🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","??🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳??نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو??🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷??صربستان🇷🇸","🇸??جمهوری سیشل🇸??","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸??سودان جنوبی🇸🇸","🇪??اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸??","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],$allinfo[1]);
$servic = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل اول",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
        break;
		case "STATUS_WAIT_CODE":
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"⚠️ کد فعال سازی هنوز ارسال نشده است !

ℹ️ لطفا دقت فرمایید شماره به همراه با پیش شماره و به صورت صحیح وارد کرده باشید و بعد از 30 ثانیه روی دریافت کد ضربه بزنید

🔆 درصورت وجود هرگونه مشکل کافیست سفارش خود را لغو کنید . در صورت دریافت نشدن کد مبلغی از شما کسر نخواهد شد !

❗️ از ارسال پشت سر هم دریافت کد خودداری کنید . در صورت ارسال اسپم از ربات مسدود خواهید شد !
[آموزش](https://t.me/fatherweb/37)",
'reply_to_message_id'=>$message_id,
'parse_mode'=>'MarkDown',
			'reply_markup'=>json_encode([
            	'keyboard'=>[
				                 [
                   ['text'=>"❌ لغو خرید"],['text'=>"💬 دریافت کد"]
                ],
                [
                ['text'=>"🚫گزارش مسدودی"]],
 	],
            	'resize_keyboard'=>true
       		])	
            ]);
//$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
        break;
    default:
	$tst = explode("^",$user["service"])[2];
file_get_contents("http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=$apikey&action=setStatus&status=-1&id=$tst");
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"⏰ زمان برای دریافت کد برای سفارش شما به پایان رسید !
ℹ️ حداکثر زمان برای دریافت کد 3 دقیقه میباشد و پس از آن سفارش لغو خواهد شد
	
❌ سفارش شما لغو شد و مبلغی از حساب شما کسر نشد ! میتوانید نسبت به خرید دوباره اقدام کنید
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}
}



elseif($text=="💬 دریافت کد" and $user["step"] == 'shooooomop'){
$allinfo = explode("^",$user["service"]);
$info = explode(":",getstats2($allinfo[2]));
switch ($info[0]) {
    case "STATUS_OK":
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"✅ کد با موفقیت دریافت شد
💭 کد ورود شما به برنامه : $info[1]

🛍 با تشکر از خرید شما ! گزارش خرید به کانال ما @$channelby ارسال شد . 
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);
			
			
$test = json_decode(file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getPrices&service=$allinfo[0]&country=$allinfo[1]"),true);
$bale = $test["$allinfo[1]"]["$allinfo[0]"]["cost"];
$sage = $bale * $mosod1;
$sos = ceil($sage);
	$tst = explode("^",$user["service"])[2];

file_get_contents("	https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=setStatus&status=6&id=$tst");
//-
$webpool = str_replace(["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$allinfo[1]);
//-
$amount = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);

$plusstock = $user["stock"] - $amount;
$connect->query("UPDATE user SET stock = '$plusstock' , listby = CONCAT (listby,'+$allinfo[3] -> $info[1]^') WHERE id = '$from_id' LIMIT 1");
$name = str_replace(["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","??🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","??🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲??","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],$allinfo[1]);

$servic = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$allinfo[0]);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
$number = mb_substr("$allinfo[3]","0","7")."***";
$getid = mb_substr("$from_id","0","5")."***";
$listby = count(explode("^", $user["listby"]));
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰نوع پنل",'callback_data'=>"text"],['text'=>"🛍 پنل دوم",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$name",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$servic",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"+$number",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$amount",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$getid",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$listby",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"{$user["member"]}",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
        break;
		case "STATUS_WAIT_CODE":
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"⚠️ کد فعال سازی هنوز ارسال نشده است !

ℹ️ لطفا دقت فرمایید شماره به همراه با پیش شماره و به صورت صحیح وارد کرده باشید و بعد از 30 ثانیه روی دریافت کد ضربه بزنید

🔆 درصورت وجود هرگونه مشکل کافیست سفارش خود را لغو کنید . در صورت دریافت نشدن کد مبلغی از شما کسر نخواهد شد !

❗️ از ارسال پشت سر هم دریافت کد خودداری کنید . در صورت ارسال اسپم از ربات مسدود خواهید شد !
[آموزش](https://t.me/fatherweb/37)",
'reply_to_message_id'=>$message_id,
'parse_mode'=>'MarkDown',
			'reply_markup'=>json_encode([
            	'keyboard'=>[
				                 [
                   ['text'=>"❌ لغو خرید"],['text'=>"💬 دریافت کد"]
                ],
                [
                ['text'=>"🚫گزارش مسدودی"]],
 	],
            	'resize_keyboard'=>true
       		])	
            ]);
//$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
        break;
    default:
	$tst = explode("^",$user["service"])[2];
file_get_contents("	https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=setStatus&status=8&id=$tst");
	bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"⏰ زمان برای دریافت کد برای سفارش شما به پایان رسید !
ℹ️ حداکثر زمان برای دریافت کد 3 دقیقه میباشد و پس از آن سفارش لغو خواهد شد
	
❌ سفارش شما لغو شد و مبلغی از حساب شما کسر نشد ! میتوانید نسبت به خرید دوباره اقدام کنید
👮🏻 درصورت وجود هرگونه مشکل کافیست با پشتیبانی در تماس باشید",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$button]);$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}
}


elseif($text=="📲 شماره مجازی چیست"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"📱 شماره مجازی چیست ؟

☎️ هنگام ثبت‌نام در اپلیکیشن‌های پیام ‌رسان و شبکه‌های اجتماعی موبایل، باید از شماره تلفن خود به عنوان شناسه استفاده کنید. اگر از کاربرانی هستید که علاقه‌ای به اشتراک‌گذاری شماره‌ی اصلی خود ندارید یا اینکه نیاز به ثبت‌نام بیش از یک بار در این برنامه‌ها دارید، می‌توانید از شماره‌های مجازی استفاده کنید. همچنین شماره مجازی این امکان را می‌دهد که بدون ثبت سیم کارت و اهراز هویت و بدون صرف وقت و هزینه صاحب شماره از کشور های مختلف شوید.

ℹ️ مزایا و کاربرد شماره مجازی چیست

➊  تلگرام، واتساپ،  وایبر، اینستاگرام  و... برای ثبت‌نام به شماره تلفن شما نیاز دارند تا کدفعال‌سازی مربوطه را برای تشخیص هویت به تلفن‌تان ارسال کنند که به جای شماره اصلی خود میتوان از شماره مجازی برای فعال کردن حساب خود استفاده کرد.

➋ بسیاری از افراد به دلایل مختلف مانند مدیریت یک اکانت دیگر برای مباحث کاری یا... نیاز به اکانت دوم دارند تا بتوانند در عین ارتباط داشتن با مشتریان، از تلگرام شخصی و خصوصی خود نیز استفاده کنند.
 
➌ بدون ثبت نام در اپراتور و بدون صرف وقت و هزینه و اهراز هویت صاحب شماره مجازی می شوید .

➍ استفاده در تمامی اپلیکیشن های اجتماعی با شماره از کشورهای مختلف! همراه با هویتی ناشناس

🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="🔔 سوالات"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"❓ سوالات متداول

❓شماره خریدم کد نمیده چیکار کنم؟
▫️جواب : ابتدا فیلم آموزش نحوه خرید را مشاهده کنید. جهت دریافت کد پس از اطمینان از ارسال کد توسط اپلیکیشن درخواستی به شماره مورد نظر 30 ثانیه صبر کنید و سپس بر روی دکمه دریافت کد کلیک کنید ، اگر پس از گذشت 5 دقیقه از دریافت شماره، کد را دریافت نکردید بر روی دکمه بازگشت کلیک کنید سپس مجددا نسبت به دریافت شماره جدید و کد اقدام نمایید.

❓شماره رو وارد آپ کردم میگه شماره اشتباهه(مسدوده) و پیام Banned Number میدهد چکار کنم؟
▫️جواب : این حالت بیشتر برای شماره چین ، روسیه و آمریکا پیش میاد. بر روی دکمه بازگشت کلیک کنید سپس مجددا نسبت به دریافت شماره جدید و کد اقدام نمایید.

❓کد تایید گرفتم اما وارد آپ نکردم، باید چیکار کنم؟
▫️جواب : متاسفانه امکان بازگشت وجه در چنین وضعیتی وجود ندارد. چون پول شماره در پنل خارجی همزمان با دریافت کد از حساب ما کم میشود‌.

❓شماره از ربات خریدم اما بعد از چند دقیقه شماره دلیت اکانت شد علت چیه؟
▫️جواب : علت دلیت اکانت شدن شماره‌ حساس شدن تلگرام نسبت به ip شماست.
از آنجایی که تلگرام مخالف با عضو فیک است نباید بیش از 3 شماره مجازی بر روی یک ip ثبت نام کنید.
اگر قصد دارید تعداد بالا شماره مجازی خریداری و ثبت نام کنید، باید آی پی خود را پس از ثبت هر شماره تغییر دهید.
🗣 برای تغییر آی پی دو راه وجود دارد :
1️⃣ استفاده از VPN
2️⃣خاموش و روشن کردن مودم ، یا خاموش و روشن کردن دیتا در تلفن همراه برای چند دقیقه موجب تغییر آی پی شما خواهد شد.

❓شماره‌ای که برای تلگرام خریدم بعد از دریافت کد یه نفر دیگه داخل اکانت بود یاtwo-step verificationروی اکانت فعال بود، و نتوستم وارد اکانت بشم ، الان چکار کنم؟
▫️جواب : با توجه به اینکه شماره ها مستقیما از پنل های خارجی دریافت می شوند و ربات $far تنها واسط بین کاربر و پنل است امکان بررسی شماره ها توسط ما امکان پذیر نیست! به همین علت گاها ممکن است بعد از دریافت کد اکانتی از قبل توسط شماره مورد نظر در اپلیکیشن مورد نظرتان خصوصا تلگرام فعال باشد؛ تنها راه حل برای جلوگیری از بروز این مشکل، چک کردن شماره مجازی قبل از دریافت کد است، بررسی این که شماره مجازی در اپلیکیشن مورد نظرتان قبلا ثبت شده است یا خیر به راحتی امکان پذیر است، برای تلگرام اگر شماره ثبت شده باشد بلافاصله با وارد کردن شماره در  تلگرام پیغام ارسال کد به  تلگرام فعال دیگر نمایش داده می شود و مانند شماره های ریجستر نشده نیست و این مورد به راحتی برای  تلگرام و دیگر اپلیکیشن ها قابل بررسی است؛ در هر صورت اگر شماره ای دریافت کردید که از قبل ثبت شده بود (به ندرت پیش می آید) هرگونه عواقب آن بر عهده خریدار خواهد بود و ربات هیچ گونه مسئولیتی در رابطه با بی دقتی کاربران را برعهده نمی گیرد.

❓شماره بدون ریپورت یعنی چی؟
▫️جواب : شماره های مجازی اغلب محدودیت ارسال پیام به اکانت هاایرانی را به مدت 4 روز دارند، بعد از 4 روز این محدودیت از بین خواهد رفت اما نوعی شماره ها در ربات وجود دارد که از همان ابتدا این محدودیت را ندارند و مانند خطوط ایرانی میتوان از همان ابتدا به اکانت های ایرانی پیام داد که اصطلاحا بدون ریپورت نام گذاری شده اند.

💎 سوالاتان در این بخش نبود ؟ به منوی اصلی برگردید و با پشتیبانی در تماس باشید 

🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="💎 کسب درآمد"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"🔰 راهنمای کسب درآمد

💎 با دریافت لینک مخصوص زیر مجموعه گیری خود ؛ به ازای هر خرید زیرمجموعه های شما از ربات، اعتبار شما به میزان 5 درصد از مبلغ خریداری شده شارژ می شود.
(برای مثال؛ اگر زیر مجموعه شما 10 هزارتومان خرید کند ، حساب شما 500 تومان شارژ خواهد شد)

🍁 برای مثال اگر روزانه 10 نفر از لینک شما عضو ربات شوند، و میانگین هر زیر مجموعه بطور ماهانه 10000 تومان خرید کند.
بعد از یک ماه درآمد شما 150 هزارتومان خواهد رسید!!!

🔰در ضمن ربات ما نمایندگی ارائه میدهد میتونید شرایطشو توی این کانال بخونید : 
@TN_Namayandgi

🤗 با کمی تبلیغات به راحتی میتوانید میلیونر شوید. 
🗣 جهت دریافت لینک مخصوص زیر مجموعه گیری خود به منو اصلی برگشته و دکمه دعوت دیگران را انتخاب نمایید.

🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="ℹ️ قوانین"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"💁🏻‍♂️ قوانین و توضیحات :

1️⃣ شماره ها اختصاصی هستند و معمولا فقط به یک نفر داده میشوند توجه داشته باشید که ما مسئول از بین رفتن هیچ اکانتی نیستیم به این دلیل که ما سازنده این شماره ها نیستم و از مدیریت ما خارج میباشد 

2️⃣ شماره ها مصرفین پس تموم میشن ، پس دلیل وجود نداشتن شماره اینه که تموم شدن ؛ اما سریعا باز هم اورده میشه.

3️⃣ اوردن شماره ها دست ما نیست و باید اپراتور اون کشور شماره ارائه بده تا بتونیم بیاریم.

4️⃣ تمامی ربات های توی  تلگرام و حتی سایت ها به یک منبع مشخص شماره ها متصل هستند پس اگر توی این ربات شماره ای موجود نباشه ، توی هیچ ربات و سایت دیگه ای هم موجود نیست‌.

5️⃣ تمامی شماره های ربات خام هستند یعنی قبل از شما ثبت نام نکرده اند ، بجز برخی شماره های کشور چین و روسیه که برای جلوگیری از این شماره رو توی  تلگرام چک کنید و سپس اقدام به گرفتن کد کنید

6️⃣ اما لازم به ذکر هست که پس از دریافت کد دیگه به هیچکس اون شماره فروخته نمیشه و کسی به اکانت شما نمیتواند از طریق خط وارد شود.

7️⃣ شماره هایی هم کمیاب هستن و خیلی ها دنبال این شماره ها هستن پس برای گرفتن این شماره ها درصورتی که در ربات موجود نباشن باید حداقل هر ساعت یک بار چک کنید که اگر موجود شد سریعا خریداری کنید. (مثل شماره های انگلستان که درخواست زیادی دارند)

8️⃣ لطفا شماره رو به صورت کامل وارد کنید و  برای این که دچار اشتباه نشید شماره رو کپی کنید

9️⃣در صورتی که شماره های زیادی با IP شما ثبت نام شده باشند از شماره های چین یا روسیه استفاده نکنید چون ممکنه IP شما توسط تلگرام بلاک شده باشه و اکانت دلیت شود.

🔟 در صورتی که کد دریافت نکردید باید برگشت بزنید و شماره جدید دریافت کنید ، برای دریافت کد باید شماره را در اپ اصلی آن نرم افزار وارد کنید تا کد دریافت نکنید پولی از شما کسر نخواهد شد

⚠️ برای اینکه امکان بلاک شدن شماره ها به صفر برسه توصیه میشه از وی پی ان استفاده کنید و موقع ساخت شماره اسم اکانتتون رو به زبون همون کشور بنویسید با گوگل ترنسلیت این امر بسیار راحته  بعد چند روز اسم خودتونو بزارید.

1️⃣1️⃣ با توجه به اینکه تلگرام مخالف عضو فیک هست لذا $far هیچ مسئولیتی در قبال دلیت شدن اکانت (Delete account) یا لاگ اوت (Log out) شدن اکانت ندارد. همچنین پس از تحویل کد، ربات دیگر هیچ مسئولیتی در مورد شماره ندارد.

2️⃣1️⃣ در صورت عمل نکردن به بندهای بالا عواقب آن بر عهده شماست و وجهی بازگشت داده نمی‌شود.

🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="💵 اموزش افزایش موجودی"){
bot('sendvideo',[
	'chat_id'=>$chat_id,
	'video'=>"https://t.me/fatherweb/50",
	'caption'=>"🎥 ویدئو اموزشی افزایش موجودی در ربات
🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="💡 درباره"){
bot('sendmessage',[
	'chat_id'=>$chat_id,
	'text'=>"ℹ️ درباره ربات فروش شماره مجازی  :

🤖 ربات شماره مجازی کاملا به صورت اختصاصی برنامه نویسی شده جهت ارائه شماره مجازی برای کشور های مختلف
🔘 تمام حقوق و متون پیام ها و سورس کد ربات محفوظ است و هر نوع کپی بر داری پیگرد قانونی دارد

🎈 برنامه نویسی شده جهت خرید شماره مجازی به صورت خودکار و سهولت در خرید شماره مجازی مطمعن و اسان

🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
elseif($text=="📽 اموزش خرید شماره مجازی"){
bot('sendvideo',[
	'chat_id'=>$chat_id,
	'video'=>"https://t.me/fatherweb/38",
	'caption'=>"🎥 ویدئو اموزشی خرید شماره مجازی
🤖 @$usernamebot",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
					[
	['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
            ]);
}
////// اولین پنل
elseif(in_array($text, array("💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال")) and $mosbot == "on" and $user["step"] == 'moshop'){
$strname = str_replace(["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","بیتالک","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$text);
        $connect->query("UPDATE user SET step = 'moshop1' WHERE id = '$from_id' LIMIT 1");	
 bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"✅ سرویس شما با موفقیت $strname تنظیم شد
💰 موجودی : {$user["stock"]} تومان
			
🌟 همه شماره کشور ها اختصاصی و بدون فروش قبل و بعدن هستن ! و محدودیت ریپورت ندارد .
🌍 کشور مورد نظر را انتخاب کنید 👇🏻",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp]);
$str = str_replace(["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],$text);
$connect->query("UPDATE user SET service = '$str^' WHERE id = '$from_id' LIMIT 1");	
}
elseif(in_array($text, array("🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧??بنین🇧🇯","??🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج??🇭","🇨🇲کامرون🇨??","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰??","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨??کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨??","🇨🇿چک??🇿","🇩??جیبوتی🇩🇯","🇩??دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴??󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮??ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨??ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","??🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس????","🇱??سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪??اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"))   and $mosbot == "on" and $user["step"] == 'moshop1'){
$allinfo = explode("^",$user["service"]);
$ddds = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","??🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","??🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],$text);
$test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/$ddds/virtual18"),true);
$bale = $test["$allinfo[0]"]["Price"];
$sage = $bale * $mosod;
$sos = ceil($sage);
$webpool = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧??","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین??🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧??","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰??کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨??ساحل عاج🇨??","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸??اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$text);
$amount = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);
if($user["stock"] >= $amount){
$name = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر??🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","????جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","??🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰??","🇱🇨سنت لوسیا🇱??","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور????","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","??🇦عربستان سعودی🇸🇦","????سنگال🇸??","??🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸??","🇸🇬سنگاپور🇸🇬","??🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸??جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱??","🇸🇩سودان🇸🇩","🇸🇷سورینام??🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","??🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"],$text);
$info = explode(":",getnumber(explode("^",$user["service"])[0] , $name));
$servic = str_replace(["telegram","google","instagram","whatsapp","line","twitter","viber","microsoft","facebook","tiktok","yahoo","amazon","paypal"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],explode("^",$user["service"])[0]);
if($info[0] == "ACCESS_NUMBER"){
$connect->query("UPDATE user SET step = 'shomop' WHERE id = '$from_id' LIMIT 1");
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"
✅ شماره کشور '$text' با موفقیت ساخته شد			
📞 شماره مجازی شما : +$info[2]


ℹ️ شماره را همراه با پیش شماره در سرویس '$servic' وارد کنید و پس از 30 ثانیه روی دریافت کد ضربه بزنید !

❗️ درصورت وجود هر گونه مشکل و تمایل نداشتن به خرید میتونید خرید خود را لغو کنید ! مبلغی از شما کسر نخواهد شد",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>json_encode([
            	'keyboard'=>[
				                 [
                   ['text'=>"❌ لغو خرید"],['text'=>"💬 دریافت کد"]
                ],
[
                ['text'=>"🚫گزارش مسدودی"]],
 	],
            	'resize_keyboard'=>true
       		])	
 ]);
$connect->query("UPDATE user SET service = CONCAT (service,'$name^$info[1]^$info[2]')  WHERE id = '$from_id' LIMIT 1");	
}else{
       bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"⚠️ شماره ای برای ارائه در حال حاظر برای این کشور وجود ندارد !
ℹ️ برای مشاهده لیست کشور های قابل ارائه از دکمه '💳 استعلام | قیمت ها' استفاده کنید
			
🌟 لطفا کشور دیگری را انتخاب کنید یا ساعاتی دیگر مجدد امتحان کنید 👇🏻",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp ]);
}}else{
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"💳 موجودی شما برای خرید شماره کشور '$text' کافی نمیباشد !			
💎 قیمت شماره مورد نظر : $amount تومان
💰 موجودی حساب شما : {$user["stock"]} تومان

💳 ابتدا باید موجوی خود را افزایش دهید . برای افزایش موجودی کافیست از دکمه '💸 شارژ حساب' استفاده کنید 👇🏻
👥 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp]);
}}



elseif(in_array($text, array("💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال")) and $mosbot == "on" and $user["step"] == 'moshzzzzzop'){
$strname = str_replace(["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","?? فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","بیتالک","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],$text);
        $connect->query("UPDATE user SET step = 'moshopoo1' WHERE id = '$from_id' LIMIT 1");	
 bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"✅ سرویس شما با موفقیت $strname تنظیم شد
💰 موجودی : {$user["stock"]} تومان
			
🌟 همه شماره کشور ها اختصاصی و بدون فروش قبل و بعدن هستن ! و محدودیت ریپورت ندارد .
🌍 کشور مورد نظر را انتخاب کنید 👇🏻",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp2]);
$str = str_replace(["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],$text);
$connect->query("UPDATE user SET service = '$str^' WHERE id = '$from_id' LIMIT 1");	
}
elseif(in_array($text, array("🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲??مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","??🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"))   and $mosbot == "on" and $user["step"] == 'moshopoo1'){
$allinfo = explode("^",$user["service"]);
$ddds = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند??🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک??🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],$text);
$test = json_decode(file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getPrices&service=$allinfo[0]&country=$ddds"),true);
$bale = $test["$ddds"]["$allinfo[0]"]["cost"] ?? 0;
$sage = $bale * $mosod1;
$sos = ceil($sage);
$webpool = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴??عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر??🇦","🇷??رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇻🇨سنت وینسنت و گرنادین ها🇻🇨","🇸🇻سالوادور🇸🇻","🇼🇸ساموآ🇼🇸","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇬سنگاپور🇸🇬","🇸🇰اسلواکی🇸🇰","🇸🇮اسلوونی🇸🇮","🇸🇧جزایر سلیمان🇸🇧","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇨🇭سوئیس🇨🇭","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇹ترینیداد و توباگو🇹🇹","🇹🇬توگو🇹🇬","🇹🇴تونگا🇹🇴","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇾اروگوئه🇺🇾","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇻🇬جزایر ویرجین انگلیس🇻🇬",🇾🇪یمن🇾🇪,"🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos","$sos"],$text);
$amount = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool","$webpool"],$allinfo[0]);
if(!empty($bale) && $bale >0){
if($user["stock"] >= $amount){
$name = str_replace(["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇬آنتیگوا و باربودا🇦🇬","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦🇺استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇿بلیز🇧🇿","🇧🇯بنین🇧🇯","🇧🇹بوتان🇧🇹","🇧🇦بوسنی و هرزگوین🇧🇦","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇫بورکینافاسو🇧🇫","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨??کیپ ورد🇨🇻","🇰🇾جزایر کیمن🇰🇾","🇹🇩چاد🇹🇩","🇨🇱شیلی🇨🇱","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇯جیبوتی🇩🇯","🇩🇲دومینیکا🇩🇲","🇩🇴جمهوری دومنیکن🇩🇴","🇨🇩جمهوری دموکراتیک کنگو🇨🇩","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان??🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل??🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"],["74","155","58","76","181","antiguaandbarbuda","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","burkinafaso","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"],$text);
$info = explode(":",getnumber2(explode("^",$user["service"])[0] , $name));
$servic = str_replace(["tg","go","ig","wa","me","tw","vi","mm","fb","lf","mb","am","ts"],["💎 تلگرام","📨 گوگل","📸 اینستاگرام","📱 واتساپ","📗 لاین","🐦 توییتر","💡 وایبر","💻 ماکروسافت","📪 فیسبوک","💬 تیک تاک","📞 یاهو","❄️ امازون","☎️ پی پال"],explode("^",$user["service"])[0]);
if($info[0] == "ACCESS_NUMBER"){
$connect->query("UPDATE user SET step = 'shooooomop' WHERE id = '$from_id' LIMIT 1");
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"
✅ شماره کشور '$text' با موفقیت ساخته شد			
📞 شماره مجازی شما : +$info[2]

ℹ️ شماره را همراه با پیش شماره در سرویس '$servic' وارد کنید و پس از 30 ثانیه روی دریافت کد ضربه بزنید !

❗️ درصورت وجود هر گونه مشکل و تمایل نداشتن به خرید میتونید خرید خود را لغو کنید ! مبلغی از شما کسر نخواهد شد",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>json_encode([
            	'keyboard'=>[
				                 [
                   ['text'=>"❌ لغو خرید"],['text'=>"💬 دریافت کد"]
                ],
                [
                ['text'=>"🚫گزارش مسدودی"]],
 	],
            	'resize_keyboard'=>true
       		])	
 ]);
$connect->query("UPDATE user SET service = CONCAT (service,'$name^$info[1]^$info[2]')  WHERE id = '$from_id' LIMIT 1");	
}else{
       bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"⚠️ شماره ای برای ارائه در حال حاظر برای این کشور وجود ندارد !
ℹ️ برای مشاهده لیست کشور های قابل ارائه از دکمه '💳 استعلام | قیمت ها' استفاده کنید
			
🌟 لطفا کشور دیگری را انتخاب کنید یا ساعاتی دیگر مجدد امتحان کنید 👇🏻",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp2 ]);
}
}else{
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"💳 موجودی شما برای خرید شماره کشور '$text' کافی نمیباشد !			
💎 قیمت شماره مورد نظر : $amount تومان
💰 موجودی حساب شما : {$user["stock"]} تومان

💳 ابتدا باید موجوی خود را افزایش دهید . برای افزایش موجودی کافیست از دکمه '💸 شارژ حساب' استفاده کنید 👇🏻
👥 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp]);
}
}else{
       bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"⚠️ شماره ای برای ارائه در حال حاظر برای این کشور وجود ندارد !
ℹ️ برای مشاهده لیست کشور های قابل ارائه از دکمه '💳 استعلام | قیمت ها' استفاده کنید
			
🌟 لطفا کشور دیگری را انتخاب کنید یا ساعاتی دیگر مجدد امتحان کنید 👇🏻",
'reply_to_message_id'=>$message_id,
			'reply_markup'=>$buttonp2 ]);
}
}




elseif($text=="." and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"📍 ادمین عزیز به پنل مدریت ربات خوش امدید",
   'reply_markup'=>json_encode([
    'keyboard'=>[  [ ['text'=>"❌ امار ربات"],['text'=>"❌ شارژ پنل"]        ],
[['text'=>"❌ خاموش کردن ربات"],['text'=>"❌ روشن کردن ربات"]      ],
[['text'=>"❌ خاموش کردن فروش"],['text'=>"❌ روشن کردن فروش"]      ],
[['text'=>"❌ بلاک"],['text'=>"❌ آنبلاک"]      ],
  [   ['text'=>"❌ارسال به همه"],['text'=>"❌فروارد همگانی"]   ],
     [
    ['text'=>"❌افزایش | کاهش موجودی"] , ['text'=>"❌ اطلاعات"] 
   ],
   [['text'=>"❌فروش پنل یک خاموش"],['text'=>"❌فروش پنل دو خاموش"]],[['text'=>"❌فروش پنل یک روشن"],['text'=>"❌فروش پنل دو روشن"]],
   [['text'=>"❌ تنظیم سود پنل اول"],['text'=>"❌ تنظیم سود پنل دوم"]],
   [['text'=>"❌ رسید فیک"]],
   ], 'resize_keyboard'=>true   ]) ]);
}
elseif($text== "❌ خاموش کردن ربات" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/bot.txt", "off");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"خاموش شد",
		]);}
elseif($text== "❌ روشن کردن ربات" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/bot.txt", "on");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"روشن شد",
		]);}
elseif($text== "❌ خاموش کردن فروش" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sbot.txt", "off");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"خاموش شد",
		]);}
elseif($text== "❌ روشن کردن فروش" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sbot.txt", "on");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"روشن شد",
		]);}
		
//------panel1
elseif($text== "❌فروش پنل یک خاموش" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sboto.txt", "off");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"خاموش شد",
		]);}
elseif($text== "❌فروش پنل یک روشن" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sboto.txt", "on");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"روشن شد",
		]);}
		
//-----panel2
elseif($text== "❌فروش پنل دو خاموش" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sbotoo.txt", "off");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"خاموش شد",
		]);}
elseif($text== "❌فروش پنل دو روشن" and $tc == "private" and in_array($from_id,$admin)){
file_put_contents("data/sbotoo.txt", "on");
bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"روشن شد",
		]);}
		
		
		
elseif($text== "❌ رسید فیک" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍اطلاعات را مانند زیر بفرستید
--------------------------------
پنل
کشور
سرویس
شماره
قیمت
شناسه کاربر
تعداد خرید
تعداد زیرمجموعه
--------------------------------
مثال:
پنل اول
ایران
تلگرام
+98967***7644
7000
2457***78
10
0",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'residm' WHERE id = '$from_id' LIMIT 1");
}
elseif ($user["step"] == 'residm' && $tc == "private" and $text !="برگشت 🔙" ) {
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍انجام شد ",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$text = explode("\n",$text);
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش خرید #موفق
⏰ در ساعت : $time
🛍 اطلاعات خرید 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [
	['text'=>"🔰پنل",'callback_data'=>"text"],['text'=>"$text[0]",'callback_data'=>"text"]
	],
				[
	['text'=>"🌏 کشور",'callback_data'=>"text"],['text'=>"$text[1]",'callback_data'=>"text"]
	],
					[
	['text'=>"🔘 سرویس",'callback_data'=>"text"],['text'=>"$text[2]",'callback_data'=>"text"]
	],
						[
	['text'=>"📞 شماره",'callback_data'=>"text"],['text'=>"$text[3]",'callback_data'=>"text"]
	],
							[
	['text'=>"💎 قیمت",'callback_data'=>"text"],['text'=>"$text[4]",'callback_data'=>"text"]
	],
								[
	['text'=>"👤 شناسه کاربر",'callback_data'=>"text"],['text'=>"$text[5]",'callback_data'=>"text"]
	],
									[
	['text'=>"🛍 تعداد خرید",'callback_data'=>"text"],['text'=>"$text[6]",'callback_data'=>"text"]
	],
										[
['text'=>"👥 تعداد زیرمجموعه",'callback_data'=>"text"],['text'=>"$text[7]",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	
}
elseif($text== "❌ تنظیم سود پنل اول" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"📍لطفا عدد دلخواه را ارسال نمایید",
       'reply_markup'=>json_encode([
    'keyboard'=>[
    [
    ['text'=>"برگشت 🔙"] 
    ]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'sodmo' WHERE id = '$from_id' LIMIT 1");
}
elseif ($user["step"] == 'sodmo' && $tc == "private" and $text !="برگشت 🔙" ) {
if(is_numeric($text) and $text >= 501){
file_put_contents("data/sod.txt", $text);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"📍انجام شد ",
       'reply_markup'=>json_encode([
    'keyboard'=>[
    [
    ['text'=>"برگشت 🔙"] 
    ]
   ],
      'resize_keyboard'=>true ])]);}}
      
elseif($text== "❌ تنظیم سود پنل دوم" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"📍لطفا عدد دلخواه را ارسال نمایید",
       'reply_markup'=>json_encode([
    'keyboard'=>[
    [
    ['text'=>"برگشت 🔙"] 
    ]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'sodmooo' WHERE id = '$from_id' LIMIT 1");
}
elseif ($user["step"] == 'sodmooo' && $tc == "private" and $text !="برگشت 🔙" ) {
if(is_numeric($text) and $text >= 501){
file_put_contents("data/sod1.txt", $text);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
            'chat_id'=>$chat_id,
            'text'=>"📍انجام شد ",
       'reply_markup'=>json_encode([
    'keyboard'=>[
    [
    ['text'=>"برگشت 🔙"] 
    ]
   ],
      'resize_keyboard'=>true ])]);}}      
      
      
      
elseif($text== "برگشت 🔙" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"📍 ادمین عزیز به پنل مدریت ربات خوش امدید",
   'reply_markup'=>json_encode([
    'keyboard'=>[
        [
  ['text'=>"❌ امار ربات"],['text'=>"❌ شارژ پنل"]     
   ],
[['text'=>"❌ خاموش کردن ربات"],['text'=>"❌ روشن کردن ربات"]      ],
[['text'=>"❌ خاموش کردن فروش"],['text'=>"❌ روشن کردن فروش"]      ],
[['text'=>"❌ بلاک"],['text'=>"❌ آنبلاک"]      ],
  [
    ['text'=>"❌ارسال به همه"],['text'=>"❌فروارد همگانی"]
   ],
     [
    ['text'=>"❌افزایش | کاهش موجودی"] , ['text'=>"❌ اطلاعات"] 
   ],
      [['text'=>"❌فروش پنل یک خاموش"],['text'=>"❌فروش پنل دو خاموش"]],[['text'=>"❌فروش پنل یک روشن"],['text'=>"❌فروش پنل دو روشن"]],
      [['text'=>"❌ تنظیم سود پنل اول"],['text'=>"❌ تنظیم سود پنل دوم"]],
   [['text'=>"❌ رسید فیک"]],
   ],
      'resize_keyboard'=>true
   ])
 ]);$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
elseif($text== "❌ اطلاعات" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍آیدی عددی فرد مورد نظر را ارسال نمایید",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'moinfo' WHERE id = '$from_id' LIMIT 1");
}
elseif ($user["step"] == 'moinfo' && $tc == "private" and $text !="برگشت 🔙" ) {
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$setting1 = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$text' LIMIT 1"));
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"??اطلاعات کاربر

🆔شناسه: <a href = 'tg://user?id=$text'>$text</a>
☎️شماره همراه: 0{$setting1['number']}
 ",
 'parse_mode'=>"HTML",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);}
elseif($text== "❌ بلاک" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍آیدی عددی فرد مورد نظر را ارسال نمایید",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'boni' WHERE id = '$from_id' LIMIT 1");
}
elseif($text== "❌ آنبلاک" and $tc == "private" and in_array($from_id,$admin)){
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍آیدی عددی فرد مورد نظر را ارسال نمایید",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'oboni' WHERE id = '$from_id' LIMIT 1");
}
elseif ($user["step"] == 'boni' && $tc == "private" and $text !="برگشت 🔙" ) {
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE user SET step = 'bon' WHERE id = '$text' LIMIT 1");
bot('sendmessage',[
        	'chat_id'=>$text,
        	'text'=>"♨️کاربر محترم شما به دلیل زیر پا گذاشتن قوانین ربات از ربات مسدود شدید❗️ 
        	میتوانید با پشتیبانی راجب دلیل مسدود شدن صحبت کنید: 
        	    🗣 @$sup",
 ]);
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍انجام شد ",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);}
elseif ($user["step"] == 'oboni' && $tc == "private" and $text !="برگشت 🔙" ) {
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE user SET step = 'none' WHERE id = '$text' LIMIT 1");
bot('sendmessage',[
        	'chat_id'=>$text,
        	'text'=>"♨️کاربر محترم حساب شما از حالت مسدود خارج شد💯",
 ]);
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍انجام شد ",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);}
elseif($text== "❌ امار ربات" and $tc == "private" and in_array($from_id,$admin)){
$alltotal = mysqli_num_rows(mysqli_query($connect,"select id from user"));
				bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"👤 امار ربات شما : $alltotal",
		]);
}
elseif ($text == "❌ارسال به همه" and $tc == "private" and in_array($from_id,$admin)) {
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس , گیف یا فایل باشد]  همچنین میتوانید رسانه را همراه با کشپن [متن چسپیده به رسانه ارسال کنید]",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'sendtoall' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE sendall SET step = 'none' , text = '' , msgid = '' , user = '0' , chat = '' LIMIT 1");	
}
elseif ($text == "❌فروارد همگانی" and $tc == "private" and in_array($from_id,$admin)){
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'fortoall' WHERE id = '$from_id' LIMIT 1");		
$connect->query("UPDATE sendall SET step = 'none' , text = '' , msgid = '' , user = '0' , chat = '' LIMIT 1");	
}
elseif($text== "❌ شارژ پنل" and $tc == "private" and in_array($from_id,$admin)){
$mobasel = file_get_contents("http://sms-activate.api.5sim.net/stubs/handler_api.php?api_key=".$apikey."&action=getBalance");
$get = explode(":",$mobasel);
$mobasel2 = file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=".$apikey2."&action=getBalance");
$get2 = explode(":",$mobasel2);
				bot('sendmessage',[
		'chat_id'=>$chat_id,
		'text'=>"📍 مقدار شارژ پنل 5 سیم : $get[1] روبل \n?? مقدار شارژ پنل SMS active : $get2[1] روبل",
		]);
		}
elseif ($text == '❌افزایش | کاهش موجودی' and $tc == "private" and in_array($from_id,$admin)) {
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍 در خط اول ایدی عددی فرد و در خط دوم مقدار موجودی را اسال کنید
📍 اگر میخواهید موجودی فر را کم کنید از علامت - منفی استفاده کنید",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'senddowncoin' WHERE id = '$from_id' LIMIT 1");
}
//=====================================================================
elseif($data=="join"){
$tch = bot('getChatMember',['chat_id'=>"@$channel",'user_id'=>"$fromid"])->result->status;
if($tch == 'member' or $tch == 'creator' or $tch == 'administrator'){
	bot('sendmessage',[
	'chat_id'=>$chatid,
	'text'=>"☑️ عضویت شما تایید شد . به منوی اصلی ربات خوش آمدید	
🌟 این ربات بصورت اتوماتیک است و میتوانید فقط در ظرف چند ثانیه شماره مجازی و کد اختصاصی شمارهٔ مجازی خودتون رو دریافت کنید.

👇🏻 خوب چه کاری برات انجام بدم ؟ از منوی پایین انتخاب کن",
'reply_to_message_id'=>$messageid,
'reply_markup'=>$button]);
}
else
{
       bot('answercallbackquery', [
            'callback_query_id' =>$membercall,
            'text' => "❌ هنوز داخل کانال @$channel عضو نیستید",
            'show_alert' =>true
        ]);
}
}
elseif($data=="member"){
		$id = bot('sendphoto',[
	'chat_id'=>$chatid,
	'photo'=>"https://t.me/fatherweb/4",
	'caption'=>"☎️ ربات شماره مجازی $far✔️

✅ شماره خام و اختصاصی
💵 تضمین ارزان ‌ترین قیمت و بیشترین کیفیت 💯
☑️ تحویل کاملا اتوماتیک و خودکار
💎 امکان دریافت شماره مجازی رایگان با جمع اوری زیرمجموعه
🌐 دریافت شماره از 190 کشور دنیا برای سرویس های محبوب!
⭕️ پیشنهاد ویژه شماره ایران تنها {$moneys($setsd,iran)} تومان😍 البته تموم کشورا نیز قیمتش کمه توی استعلام میتونید ببینید!
شماره مجازی سایر کشور ها هم به این ارزونی هستند🤩

💢 باور نداری خودت ببین👇🏼👇🏼

t.me/$usernamebot?start=$fromid",
    		])->result->message_id;
bot('sendmessage',[
	'chat_id'=>$chatid,
	'text'=>"💬 پیام بالا حاوی لینک دعوت اختصاصی شما به ربات است 	
🗣 با معرفی ربات به دوستان خود 5 درصد از هر افزایش موجودی به عنوان هدیه به شما داده خواهد شد .

💰 موجودی شما : {$usercall["stock"]} تومان
👥 تعداد زیر مجموعه ها : {$usercall["member"]} نفر",
	'reply_to_message_id'=>$id,
    		]);
}
elseif($data=="myby"){
$getby = explode("^", $usercall["listby"]);
if(count($getby) > 1){
for($z = 0;$z <= count($getby) - 2;$z++){
$zplus+=1;
$result = $result."$zplus - $getby[$z]"."\n";
}
bot('sendmessage',[
	'chat_id'=>$chatid,
	'text'=>"🛍 لیست تمام خرید های شما :
	
$result",
'reply_to_message_id'=>$messageid,
    		]);
}
else
{
       bot('answercallbackquery', [
            'callback_query_id' =>$membercall,
            'text' => "❌ شما هنوز خریدی ثبت نکرده اید",
            'show_alert' =>true
        ]);
}
}
elseif($data=="cart"){
bot('sendmessage',[
	'chat_id'=>$chatid,
	'text'=>"💰 به بخش افزایش موجودی به صورت آفلاین خوش آمدید

🗣 درصورتی که امکان خرید به صورت آنلاین و با رمز دوم ندارید میتوانید پرداخت را آفلاین انجام دهید !

💠به پیوی پشتیبانی بروید و احراز هویت خود را انجام دهید و سپس به شماره کارتی که داده میشود واریز کنید😉
(عکس از شماره کارتی که میخواهید واریز کنید روی cvv2 و تاریخ بپوشونید مهم نیست + شماره اکانتی که باهاش ربات رو استارت کردید +۹۸ باشه)

🆔 @$sup",
'reply_to_message_id'=>$messageid,
'reply_markup'=>$button]);
}
elseif($data=="otheramount"){
bot('sendmessage',[
	'chat_id'=>$chatid,
	'text'=>"🗣 در صورتی که مبلغ مورد نظر شما در بین بسته ها نبود در این قسمت میتوانید به میزان دلخواه حساب خود را شارژ کنید
	
⚠️ توجه کنید مبلغ را به تومان وارد کنید و حداقل مبلغی که میتوانید خرید کنید 500 تومان و حداکثر 200000 تومان است
💰 مبلغی که میخواهید حساب خود را شارژ کنید وارد کنید 👇🏻",
'reply_to_message_id'=>$messageid,
'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"❌ لغو"]
				],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
$connect->query("UPDATE user SET step = 'otheramount' WHERE id = '$fromid' LIMIT 1");	
}

elseif($data=="text"){
       bot('answercallbackquery', [
            'callback_query_id' =>$membercall,
            'text' => "ℹ️ این دکمه برای نمایش اطلاعات است ! و کاربرد دیگری ندارد",
            'show_alert' =>true
        ]);
}
//==================================================
elseif($user["step"] == "otheramount" && $tc == "private"){
if($text >= 500 and $text <= 200000){
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"✅ صفحه افزایش موجودی با مبلغ $text تومان با موفقیت برای شما ساخته شد
			
☑️ تمامی پرداخت ها به صورت اتوماتیک بوده و پس از تراکنش موفق مبلغ آن به موجودی حساب شما در ربات افزوده خواهد شد .

🆔 شناسه : $from_id
💰 موجودی : {$user["stock"]} تومان

🗣 درصورتی که امکان پرداخت آنلاین و همراه با رمز دوم را ندارد میتوانید از پرداخت آفلاین و همراه با کارت به کارت استفاده کنید !

👮🏻 در صورت بروز هرگونه مشکل و یا انجام نشدن پرداخت کافیست با پشتیبانی در تماس باشید .
🌟 لطفا روی دکمه زیر ضربه بزنید تا به صفحه خرید منتقل شوید ??🏻",
			'reply_to_message_id'=>$message_id,
			'reply_markup'=>json_encode([
    'inline_keyboard'=>[
	[
	['text'=>"💰 $text تومان",'url'=>"$web/pay/pay.php?amount=$text&id=$from_id"]
	],
					[
	['text'=>"💳 خرید آفلاین",'callback_data'=>'cart'],['text'=>"🛍 کانال خرید ها",'url'=>"https://t.me/$channelby"]
	],
              ]
        ])
	]);	
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}
else
{
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"❗️ مبلغ وارد شده نادرست است ! لطفا اعداد را به لاتین و از وارد کردن حروف اضافی خودداری کنید
			
⚠️ توجه کنید مبلغ را به تومان وارد کنید و حداقل مبلغی که میتوانید خرید کنید 500 تومان و حداکثر 200000 تومان است
💰 مبلغی که میخواهید حساب خود را شارژ کنید وارد کنید 👇🏻",
			'reply_to_message_id'=>$message_id,
			'reply_markup'=>json_encode([
            	'keyboard'=>[
				[
				['text'=>"❌ لغو"]
				],
 	],
            	'resize_keyboard'=>true
       		])
	]);	
}
}
elseif($user["step"] == "sup" && $tc == "private"){
			bot('sendmessage',[       
			'chat_id'=>$admin[0],
			'text'=>"یک پیام از طرف این کاربر برای شما ارسال شده
:نام کاربر  $first_name
ایدی کاربر: @$username11
ایدی عددی کاربر : <pre>$from_id</pre>
متن پیام :
- - - - - - - - - - - -
$text",'parse_mode'=>'html',
'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"پاسخ",'callback_data'=>"pm|$chat_id"]],],])]);
bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"🗣 پیام شما با موفقیت ارسال شد منتظر پاسخ پشتیبانی باشید",
			'reply_to_message_id'=>$message_id,
	]);	
}
elseif(strpos($data,"pm|" ) !== false ){
$exit = explode("|",$data);
$kkmo = $exit[1];
bot('sendmessage',[
        	'chat_id'=>$chatid,
        	'text'=>"📍پیام رو بفرست
درحال مکالمه با $kkmo ",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
$connect->query("UPDATE user SET step = 'mosupp|$kkmo' WHERE id = '$chatid' LIMIT 1");
}
elseif(strpos($user["step"],"mosupp|" ) !== false and $text !="برگشت 🔙" ) {
$exit = explode("|",$user["step"]);
bot('sendmessage',[
        	'chat_id'=>$exit[1],
        	'text'=>"پیام از طرف ادمین:
$text",'parse_mode'=>'html']);
bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"📍ارسال شد",
	   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
 ]);
}
elseif($user["step"] == "sendcoin" && $tc == "private"){
$all = explode("\n", $text);
if($all[1] >= 100){	
$plusstock = $all[1] + 200;
if($plusstock <= $user["stock"]){
$userget = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$all[0]' LIMIT 1"));
if($userget["id"] == true and $all[0] != $from_id){
$pluscoin = $user["stock"] - $plusstock;
$pluscoinusergets = $userget["stock"] + $all[1] ;
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"✅ انتقال موجودی با موفقیت انجام شد
			
❄️ مقدار موجودی انتقال داده شده : $all[1]
💰 میزان جدید موجودی شما : $pluscoin
☂️ میزان قبلی موجودی شما : {$user["stock"]}
👤 کاربر مورد نظر : [$all[0]](tg://user?id=$all[0])

⚠️ توجه کنید هزینه انتقال 200 تومان میباشد که از حساب شما کسر شد .
↗️ گزارش انتقال شما در کانال @$channelby ارسال شد",
'reply_to_message_id'=>$message_id,
	'parse_mode'=>'Markdown',
	'reply_markup'=>$button]);	
				bot('sendmessage',[       
			'chat_id'=>$all[0],
			'text'=>"🎁 $all[1] تومان موجودی به شما هدیه داده شد !

💰 میزان جدید موجودی شما : $pluscoinusergets
☂️ میزان قبلی موجودی شما : {$userget["stock"]}
🎈 کاربر ارسال کننده : [$from_id](tg://user?id=$from_id)

↗️ گزارش دریافت شما در کانال `@$channelby` ارسال شد",
	'parse_mode'=>'Markdown',
	]);	
$connect->query("UPDATE user SET step = 'none' , stock = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE user SET stock = '$pluscoinusergets' WHERE id = '$all[0]' LIMIT 1");
$sendid = mb_substr("$from_id","0","5")."***";
$reid = mb_substr("$all[0]","0","5")."***";
date_default_timezone_set('Asia/Tehran'); 
$time = date("Y-m-d | H:i:s");
bot('sendmessage',[
	'chat_id'=>"@$channelby",
	'text'=>"✅ گزارش انتقال #موفق
⏰ در ساعت : $time
⤴️ اطلاعات انتقال موجودی 👇🏻
🤖 @$usernamebot",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
				[
	['text'=>"📤 فرستنده",'callback_data'=>"text"],['text'=>"$sendid",'callback_data'=>"text"]
	],
					[
	['text'=>"📥 دریافت کننده",'callback_data'=>"text"],['text'=>"$reid",'callback_data'=>"text"]
	],
						[
	['text'=>"💰 میزان",'callback_data'=>"text"],['text'=>"$all[1] تومان",'callback_data'=>"text"]
	],
																[
	['text'=>"☎️ ربات خرید شماره مجازی",'url'=>"https://t.me/$usernamebot"],
	],
              ]
        ])
            ]);	
}
else
{
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"❌ کاربر مورد نظر یافت نشد ! 
ℹ️ شناسه فرد را با دقت وارد کنید و از وجود داشتن حساب برای شناسه در ربات اطمینان کسب کنید			
🆔 شناسه کاربری هر فرد در قسمت اطلاعات حساب وی مشخص هست 

⚠️ توجه کنید که هزینه انتقال موجودی برای هر بار 200 تومان میباشد ! و حداقل انتقال موجودی 100 تومان میباشد
ℹ️ مثال :
267785153
1000",
'reply_to_message_id'=>$message_id,
	]);	
}
}
else
{
$restock = $user["stock"] - 200 ;
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"❗️ میزان موجودی که میخواهید انتقال دهید از موجودی حساب شما بیش تر است !
💰 حداکثر موجودی قابل انتقال : $restock
☂️ مبلغ وارد شده شما : $all[1]

⚠️ توجه کنید که هزینه انتقال موجودی برای هر بار 200 تومان میباشد ! و حداقل انتقال موجودی 100 تومان میباشد
ℹ️ مثال :
267785153
1000",
'reply_to_message_id'=>$message_id,
	]);	
}
}
else
{
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"❗️ حداقل موجودی برای انتقال 100 تومان میباشد !
⚠️ توجه کنید که هزینه انتقال موجودی برای هر بار 200 تومان میباشد .

ℹ️ مثال :
267785153
1000",
'reply_to_message_id'=>$message_id,
	]);	
}
}
elseif($user["step"] == "senddowncoin" && $tc == "private"){
$all = explode("\n", $text);	
$usergets = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$all[0]' LIMIT 1"));
if($usergets["id"] == true){
$pluscoinusergets = $usergets["stock"] + $all[1] ;
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"انتقال موجودی با موفقیت انجام شد ✅",
				   'reply_markup'=>json_encode([
    'keyboard'=>[
	[
	['text'=>"برگشت 🔙"] 
	]
   ],
      'resize_keyboard'=>true
   ])
	]);	
				bot('sendmessage',[       
			'chat_id'=>$all[0],
			'text'=>"💎 $all[1] موجودی به شما هدیه داده شد !

💰 میزان جدید موجودی شما : $pluscoinusergets
☂️ میزان قبلی موجودی شما : {$usergets["stock"]}
👮🏻 هدیه از طرف مدیریت ربات !",
	]);	
$connect->query("UPDATE user SET stock = '$pluscoinusergets' WHERE id = '$all[0]' LIMIT 1");
}
else
{
			bot('sendmessage',[       
			'chat_id'=>$chat_id,
			'text'=>"📍 کاربر مورد نظر یافت نشد ! شاید هنوز ربات را استارت نکرده باشد !			
🎈 شناسه کاربری هر فرد در قسمت اطلاعات حساب وی مشخص هست 
🌟 مثال :
267785153",
	]);	
}
}
elseif ($user["step"] == 'sendtoall' && $tc == "private") {
$filephoto = $message->photo;
$photo = $filephoto[count($filephoto)-1]->file_id;
$file = $update->message->document->file_id;
$caption = $update->message->caption;
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"پیام شما با موفقیت برای ارسال همگانی تنظیم شد  ✔️",
 ]);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE sendall SET step = 'sendall' , text = '$text$caption' , msgid = '$file$photo' LIMIT 1");			
}
elseif ($user["step"] == 'fortoall' && $tc == "private") {
         bot('sendmessage',[
        	'chat_id'=>$chat_id,
        	'text'=>"پیام شما با موفقیت به عنوان فوروارد همگانی تنظیم شد ✔️",
 ]);
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
$connect->query("UPDATE sendall SET step = 'forall' , msgid = '$message_id' , chat = '$chat_id' LIMIT 1");		
}

?>