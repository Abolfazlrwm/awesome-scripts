<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);

include ('config.php');
$mosod = file_get_contents("data/sod.txt");
$mosod1 = file_get_contents("data/sod1.txt");

$connect = mysqli_connect($servername, $username, $password, $dbname);
$connect->set_charset('utf8mb4_general_ci');
$connect->query('SET NAMES utf8mb4');

$connect->query("DROP TABLE `silver`");
$connect->query("DROP TABLE `gold`");
mysqli_multi_query($connect,"CREATE TABLE `silver` (`name` text NOT NULL,`viber` int(200) NOT NULL,`amazon` int(200) NOT NULL,`tiktok` int(200) NOT NULL,`facebook` int(200) NOT NULL,`microsoft` int(200) NOT NULL,`instagram` int(200) NOT NULL,`yahoo` int(200) NOT NULL,`google` int(200) NOT NULL,`telegram` int(200) NOT NULL,`whatsapp` int(200) NOT NULL,`line` int(200) NOT NULL,`twitter` int(200) NOT NULL,`paypal` int(200) NOT NULL)CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
mysqli_multi_query($connect,"CREATE TABLE `gold` (`name` text NOT NULL,`viber` int(200) NOT NULL,`amazon` int(200) NOT NULL,`tiktok` int(200) NOT NULL,`facebook` int(200) NOT NULL,`microsoft` int(200) NOT NULL,`instagram` int(200) NOT NULL,`yahoo` int(200) NOT NULL,`google` int(200) NOT NULL,`telegram` int(200) NOT NULL,`whatsapp` int(200) NOT NULL,`line` int(200) NOT NULL,`twitter` int(200) NOT NULL,`paypal` int(200) NOT NULL,`viberCount` varchar(200) DEFAULT '0',`amazonCount` varchar(200) DEFAULT '0',`tiktokCount` varchar(200) DEFAULT '0',`facebookCount` varchar(200) DEFAULT '0',`microsoftCount` varchar(200) DEFAULT '0',`instagramCount` varchar(200) DEFAULT '0',`yahooCount` varchar(200) DEFAULT '0',`googleCount` varchar(200) DEFAULT '0',`telegramCount` varchar(200) DEFAULT '0',`whatsappCount` varchar(200) DEFAULT '0',`lineCount` varchar(200) DEFAULT '0',`twitterCount` varchar(200) DEFAULT '0',`paypalCount` varchar(200) DEFAULT '0')CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");

$moba = ["afghanistan","albania","algeria","angola","anguilla","antiguaandbarbuda","argentina","armenia","aruba","australia","austria","azerbaijan","bahamas","bahrain","bangladesh","barbados","belarus","belgium","belize","benin","bhutane","bih","bolivia","botswana","brazil","bulgaria","burkinafaso","burundi","cambodia","cameroon","canada","capeverde","caymanislands","chad","chile","china","colombia","comoros","congo","costarica","croatia","cuba","cyprus","czech","djibouti","dominica","dominicana","drcongo","easttimor","ecuador","egypt","england","equatorialguinea","eritrea","estonia","ethiopia","finland","france","frenchguiana","gabon","gambia","georgia","germany","ghana","greece","grenada","guadeloupe","guatemala","guinea","guineabissau","guyana","haiti","honduras","hungary","india","indonesia","iran","iraq","ireland","israel","italy","ivorycoast","jamaica","japan","jordan","kazakhstan","kenya","kuwait","kyrgyzstan","laos","latvia","lesotho","liberia","libya","lithuania","luxembourg","macau","madagascar","malawi","malaysia","maldives","mali","mauritania","mauritius","mexico","moldova","mongolia","montenegro","montserrat","morocco","mozambique","myanmar","namibia","nepal","netherlands","newcaledonia","newzealand","nicaragua","niger","nigeria","northmacedonia","norway","oman","pakistan","panama","papuanewguinea","paraguay","peru","philippines","poland","portugal","puertorico","qatar","reunion","romania","russia","rwanda","saintkittsandnevis","saintlucia","saintvincentandgrenadines","salvador","samoa","saotomeandprincipe","saudiarabia","senegal","serbia","seychelles","sierraleone","singapore","slovakia","slovenia","solomonislands","somalia","southafrica","southsudan","spain","srilanka","sudan","suriname","swaziland","sweden","switzerland","syria","taiwan","tajikistan","tanzania","thailand","tit","togo","tonga","tunisia","turkey","turkmenistan","turksandcaicos","uae","uganda","ukraine","uruguay","usa","uzbekistan","venezuela","vietnam","virginislands","yemen","zambia","zimbabwe"];
$patern = ['/^afghanistan$/','/^albania$/','/^algeria$/','/^angola$/','/^anguilla$/','/^antiguaandbarbuda$/','/^argentina$/','/^armenia$/','/^aruba$/','/^australia$/','/^austria$/','/^azerbaijan$/','/^bahamas$/','/^bahrain$/','/^bangladesh$/','/^barbados$/','/^belarus$/','/^belgium$/','/^belize$/','/^benin$/','/^bhutane$/','/^bih$/','/^bolivia$/','/^botswana$/','/^brazil$/','/^bulgaria$/','/^burkinafaso$/','/^burundi$/','/^cambodia$/','/^cameroon$/','/^canada$/','/^capeverde$/','/^caymanislands$/','/^chad$/','/^chile$/','/^china$/','/^colombia$/','/^comoros$/','/^congo$/','/^costarica$/','/^croatia$/','/^cuba$/','/^cyprus$/','/^czech$/','/^djibouti$/','/^dominica$/','/^dominicana$/','/^drcongo$/','/^easttimor$/','/^ecuador$/','/^egypt$/','/^england$/','/^equatorialguinea$/','/^eritrea$/','/^estonia$/','/^ethiopia$/','/^finland$/','/^france$/','/^frenchguiana$/','/^gabon$/','/^gambia$/','/^georgia$/','/^germany$/','/^ghana$/','/^greece$/','/^grenada$/','/^guadeloupe$/','/^guatemala$/','/^guinea$/','/^guineabissau$/','/^guyana$/','/^haiti$/','/^honduras$/','/^hungary$/','/^india$/','/^indonesia$/','/^iran$/','/^iraq$/','/^ireland$/','/^israel$/','/^italy$/','/^ivorycoast$/','/^jamaica$/','/^japan$/','/^jordan$/','/^kazakhstan$/','/^kenya$/','/^kuwait$/','/^laos$/','/^latvia$/','/^lesotho$/','/^liberia$/','/^libya$/','/^lithuania$/','/^luxembourg$/','/^macau$/','/^madagascar$/','/^malawi$/','/^malaysia$/','/^maldives$/','/^mali$/','/^mauritania$/','/^mauritius$/','/^mexico$/','/^moldova$/','/^mongolia$/','/^montenegro$/','/^montserrat$/','/^morocco$/','/^mozambique$/','/^myanmar$/','/^namibia$/','/^nepal$/','/^netherlands$/','/^newcaledonia$/','/^newzealand$/','/^nicaragua$/','/^niger$/','/^nigeria$/','/^northmacedonia$/','/^norway$/','/^oman$/','/^pakistan$/','/^panama$/','/^papuanewguinea$/','/^paraguay$/','/^peru$/','/^philippines$/','/^poland$/','/^portugal$/','/^puertorico$/','/^qatar$/','/^reunion$/','/^romania$/','/^russia$/','/^rwanda$/','/^saintkittsandnevis$/','/^saintlucia$/','/^saintvincentandgrenadines$/','/^salvador$/','/^samoa$/','/^saotomeandprincipe$/','/^saudiarabia$/','/^senegal$/','/^serbia$/','/^seychelles$/','/^sierraleone$/','/^singapore$/','/^slovakia$/','/^slovenia$/','/^solomonislands$/','/^somalia$/','/^southafrica$/','/^southsudan$/','/^spain$/','/^srilanka$/','/^sudan$/','/^suriname$/','/^swaziland$/','/^sweden$/','/^switzerland$/','/^syria$/','/^taiwan$/','/^tajikistan$/','/^tanzania$/','/^thailand$/','/^tit$/','/^togo$/','/^tonga$/','/^tunisia$/','/^turkey$/','/^turkmenistan$/','/^turksandcaicos$/','/^uae$/','/^uganda$/','/^ukraine$/','/^uruguay$/','/^usa$/','/^uzbekistan$/','/^venezuela$/','/^vietnam$/','/^virginislands$/','/^yemen$/','/^zambia$/','/^zimbabwe$/'];
$flags = [ "افغانستان 🇦🇫", "آلبانی 🇦🇱", "الجزایر 🇩🇿", "آنگولا 🇦🇴", "آنگویلا 🇦🇮", "آنتیگوا و باربودا 🇦🇬", "آرژانتین 🇦🇷", "ارمنستان 🇦🇲", "آروبا 🇦🇼", "استرالیا 🇦🇺", "اتریش 🇦🇹", "آذربایجان 🇦🇿", "باهاما 🇧🇸", "بحرین 🇧🇭", "بنگلادش 🇧🇩", "باربادوس 🇧🇧", "بلاروس 🇧🇾", "بلژیک 🇧🇪", "بلیز 🇧🇿", "بنین 🇧🇯", "بوتان 🇧🇹", "بوسنی 🇧🇦", "بولیوی 🇧🇴", "بوتسوانا 🇧🇼", "برزیل 🇧🇷", "بلغارستان 🇧🇬", "بورکینافاسو 🇧🇫", "بوروندی 🇧🇮", "کامبوج 🇰🇭", "کامرون 🇨🇲", "کانادا 🇨🇦", "کیپ ورد 🇨🇻", "جزایر کیمن 🇰🇾", "چاد 🇹🇩", "شیلی 🇨🇱", "چین 🇨🇳", "کلمبیا 🇨🇴", "مجمع‌الجزایر قمر 🇰🇲", "کنگو 🇨🇬", "کاستاریکا 🇨🇷", "کرواسی 🇭🇷", "کوبا 🇨🇺", "قبرس 🇨🇾", "جمهوری چک 🇨🇿", "جیبوتی 🇩🇯", "دومینیکا 🇩🇲", "دومینیکن 🇩🇴", "کنگو 🇨🇩", "تیمور شرقی 🇹🇱", "اکوادور 🇪🇨", "مصر 🇪🇬", "انگلستان 🏴󠁧󠁢󠁥󠁮󠁧󠁿", "گینه استوایی 🇬🇶", "اریتره 🇪🇷", "استونی 🇪🇪", "اتیوپی 🇪🇹", "فنلاند 🇫🇮", "فرانسه 🇫🇷", "گویان فرانسه 🇫🇷", "گابون 🇬🇦", "گامبیا 🇬🇲", "گرجستان 🇬🇪", "آلمان 🇩🇪", "غنا 🇬🇭", "یونان 🇬🇷", "گرنادا 🇬🇩", "گوادلوپ 🇬🇵", "گواتمالا 🇬🇹", "گینه 🇬🇳", "گینه بیسائو 🇬🇼", "گویان 🇬🇾", "هائیتی 🇭🇹", "هندوراس 🇭🇳", "مجارستان 🇭🇺", "هند 🇮🇳", "اندونزی 🇮🇩", "ایران 🇮🇷", "عراق 🇮🇶", "ایرلند 🇮🇪", "اسرائیل 🇮🇱", "ایتالیا 🇮🇹", "ساحل عاج 🇨🇮", "جامائیکا 🇯🇲", "ژاپن 🇯🇵", "اردن 🇯🇴", "قزاقستان 🇰🇿", "کنیا 🇰🇪", "کویت 🇰🇼", "لائوس 🇱🇦", "لتونی 🇱🇻", "لسوتو 🇱🇸", "لیبریا 🇱🇷", "لیبی 🇱??", "لیتوانی 🇱🇹", "لوکزامبورگ 🇱🇺", "ماکائو 🇲🇴", "ماداگاسکار 🇲🇬", "مالاوی 🇲🇼", "مالزی 🇲🇾", "مالدیو 🇲🇻", "مالی 🇲🇱", "موریتانی 🇲🇷", "موریس 🇲🇺", "مکزیک 🇲🇽", "مولداوی 🇲🇩", "مغولستان 🇲🇳", "مونته‌نگرو 🇲🇪", "مونتسرات 🇲🇸", "مراکش 🇲🇦", "موزامبیک 🇲🇿", "میانمار 🇲🇲", "نامیبیا 🇳🇦", "نپال🇳🇵", "هلند 🇳🇱", "کالدونیای جدید 🇳🇨", "نیوزیلند 🇳🇿", "نیکاراگوئه 🇳🇮", "نیجر 🇳🇪", "نیجریه 🇳🇬", "مقدونیه 🇲🇰", "نروژ 🇳🇴", "عمان 🇴🇲", "پاکستان 🇵🇰", "پاناما 🇵🇦", "پاپوآ گینهٔ نو 🇵🇬", "پاراگوئه 🇵🇾", "پرو 🇵🇪", "فیلیپین 🇵🇭", "لهستان 🇵🇱", "پرتغال 🇵🇹", "پورتوریکو 🇵🇷", "قطر 🇶🇦", "رئونیون 🇷🇪", "رومانی 🇷🇴", "روسیه 🇷🇺", "رواندا 🇷🇼", "سنت کیتس و نویس🇰🇳", "سنت لوسیا 🇱🇨", "سنت وینسنت و گرنادین‌ها 🇻🇨", "السالوادور 🇸🇻", "ساموآ 🇼🇸", "سائوتومه و پرینسیپ 🇸🇹", "عربستان 🇸🇦", "سنگال 🇸🇳", "صربستان 🇷🇸", "سیشل 🇸🇨", "سیرالئون 🇸🇱", "سنگاپور 🇸🇬", "اسلواکی 🇸🇰", "اسلوونی 🇸🇮", "جزایر سلیمان 🇸🇧", "سومالی 🇸🇴", "آفریقای جنوبی 🇿🇦", "سودان جنوبی 🇸🇸", "اسپانیا 🇪🇸", "سری‌لانکا 🇱🇰", "سودان 🇸🇩", "سورینام 🇸🇷", "سوازیلند 🇸🇿", "سوئد 🇸🇪", "سوئیس🇨🇭", "سوریه 🇸🇾", "تایوان 🇹🇼", "تاجیکستان 🇹🇯", "تانزانیا 🇹🇿", "تایلند 🇹🇭", "ترینیداد و توباگو 🇹🇹", "توگو 🇹🇬", "تونگا 🇹🇴", "تونس 🇹🇳", "ترکیه 🇹🇷", "ترکمنستان 🇹🇲", "جزایر تورکس و کایکوس 🇹🇨", "امارات 🇦🇪", "اوگاندا 🇺🇬", "اوکراین 🇺🇦", "اروگوئه 🇺🇾", "آمریکا 🇺🇸", "ازبکستان 🇺🇿", "ونزوئلا 🇻🇪", "ویتنام 🇻🇳", "جزایر ویرجین 🇻🇮", "یمن 🇾🇪", "زامبیا 🇿🇲", "زیمبابوه 🇿🇼"];

for($i=0; $i <= count($moba) ; $i++){
    $test = json_decode(file_get_contents("https://5sim.net/v1/guest/products/".$moba[$i]."/virtual18"),true);
    $viber = $test["viber"]["Price"]*$mosod;
    $amazon = $test["amazon"]["Price"]*$mosod;
    $tiktok = $test["tiktok"]["Price"]*$mosod;
    $facebook = $test["facebook"]["Price"]*$mosod;
    $microsoft = $test["microsoft"]["Price"]*$mosod;
    $instagram = $test["instagram"]["Price"]*$mosod;
    $yahoo = $test["yahoo"]["Price"]*$mosod;
    $google = $test["google"]["Price"]*$mosod;
    $telegram = $test["telegram"]["Price"]*$mosod;
    $whatsapp = $test["whatsapp"]["Price"]*$mosod;
    $line = $test["line"]["Price"]*$mosod;
    $twitter = $test["twitter"]["Price"]*$mosod;
    $paypal = $test["paypal"]["Price"]*$mosod;
    $viber = ceil($viber);
    $amazon=ceil($amazon);
    $tiktok =ceil($tiktok);
    $facebook = ceil($facebook);
    $microsoft = ceil($microsoft);
    $instagram = ceil($instagram);
    $yahoo = ceil($yahoo);
    $google = ceil($google);
    $telegram = ceil($telegram);
    $whatsapp = ceil($whatsapp);
    $line = ceil($line);
    $twitter = ceil($twitter);
    $paypal = ceil($paypal);
    $ddds = preg_replace($patern, $flags, $moba[$i]);
    mysqli_multi_query($connect,"INSERT INTO `silver` (`name`, `viber`, `amazon`, `tiktok`, `facebook`,`microsoft`, `instagram`, `yahoo`, `google`, `telegram`, `whatsapp`, `line`, `twitter`, `paypal`) VALUES ('$ddds', '$viber', '$amazon', '$tiktok', '$facebook', '$microsoft', '$instagram', '$yahoo', '$google', '$telegram', '$whatsapp', '$line', '$twitter', '$paypal')");
}

$arrrr = ["74","155","58","76","181","39","148","179","175","50","35","122","145","60","118","51","82","belize","120","bhutane","bih","92","123","73","83","119","24","41","36","186","caymanislands","42","chile","3","33","133","150","93","45","113","77","63","djibouti","126","dominicana","drcongo","easttimor","105","21","16","167","176","34","ethiopia","finland","france","frenchguiana","154","28","128","43","38","129","127","guadeloupe","94","68","130","131","26","88","84","22","6","57","47","23","13","86","ivorycoast","103","japan","116","2","8","100","11","25","49","136","135","102","44","luxembourg","macau","17","137","7","159","69","114","157","54","85","72","montenegro","180","37","80","5","138","81","48","185","67","90","139","19","183","174","107","66","112","papuanewguinea","87","65","4","15","117","97","111","146","32","0","140","134","saintlucia","101","178","53","61","29","184","115","141","149","31","177","56","64","98","142","106","46","110","55","143","9","52","99","89","62","161","turksandcaicos","95","75","1","187","187","40","70","10","30","147","96"];

for($i=0; $i <= count($arrrr) ; $i++){
    $test = json_decode(file_get_contents("https://sms-activate.ru/stubs/handler_api.php?api_key=$apikey2&action=getPrices&&country=$arrrr[$i]"),true);
    $viber = $test["$arrrr[$i]"]["vi"]["cost"]*$mosod1;
    $amazon = $test["$arrrr[$i]"]["am"]["cost"]*$mosod1;
    $tiktok = $test["$arrrr[$i]"]["lf"]["cost"]*$mosod1;
    $facebook = $test["$arrrr[$i]"]["fb"]["cost"]*$mosod1;
    $microsoft = $test["$arrrr[$i]"]["mm"]["cost"]*$mosod1;
    $instagram = $test["$arrrr[$i]"]["ig"]["cost"]*$mosod1;
    $yahoo = $test["$arrrr[$i]"]["mb"]["cost"]*$mosod1;
    $google = $test["$arrrr[$i]"]["go"]["cost"]*$mosod1;
    $telegram = $test["$arrrr[$i]"]["tg"]["cost"]*$mosod1;
    $whatsapp = $test["$arrrr[$i]"]["wa"]["cost"]*$mosod1;
    $line = $test["$arrrr[$i]"]["me"]["cost"]*$mosod1;
    $twitter = $test["$arrrr[$i]"]["tw"]["cost"]*$mosod1;
    $paypal = $test["$arrrr[$i]"]["ts"]["cost"]*$mosod1;
    $viber = ceil($viber);
    $amazon=ceil($amazon);
    $tiktok =ceil($tiktok);
    $facebook = ceil($facebook);
    $microsoft = ceil($microsoft);
    $instagram = ceil($instagram);
    $yahoo = ceil($yahoo);
    $google = ceil($google);
    $telegram = ceil($telegram);
    $whatsapp = ceil($whatsapp);
    $line = ceil($line);
    $twitter = ceil($twitter);
    $paypal = ceil($paypal);
    $viber_count = $test["$arrrr[$i]"]["vi"]["count"] ?? 0;
    $amazon_count = $test["$arrrr[$i]"]["am"]["count"] ?? 0;
    $tiktok_count = $test["$arrrr[$i]"]["lf"]["count"] ?? 0;
    $facebook_count = $test["$arrrr[$i]"]["fb"]["count"] ?? 0;
    $microsoft_count = $test["$arrrr[$i]"]["mm"]["count"] ?? 0;
    $instagram_count = $test["$arrrr[$i]"]["ig"]["count"] ?? 0;
    $yahoo_count = $test["$arrrr[$i]"]["mb"]["count"] ?? 0;
    $google_count = $test["$arrrr[$i]"]["go"]["count"] ?? 0;
    $telegram_count = $test["$arrrr[$i]"]["tg"]["count"] ?? 0;
    $whatsapp_count = $test["$arrrr[$i]"]["wa"]["count"] ?? 0;
    $line_count = $test["$arrrr[$i]"]["me"]["count"] ?? 0;
    $twitter_count = $test["$arrrr[$i]"]["tw"]["count"] ?? 0;
    $paypal_count = $test["$arrrr[$i]"]["ts"]["count"] ?? 0;

   $patern = ["/^74$/","/^155$/","/^58$/","/^76$/","/^181$/","/^39$/","/^148$/","/^179$/","/^175$/","/^50$/","/^35$/","/^122$/","/^145$/","/^60$/","/^118$/","/^51$/","/^82$/","/^120$/","/^92$/","/^123$/","/^73$/","/^83$/","/^119$/","/^24$/","/^41$/","/^36$/","/^186$/","/^42$/","/^3$/","/^33$/","/^133$/","/^150$/","/^93$/","/^45$/","/^113$/","/^77$/","/^63$/","/^126$/","/^easttimor$/","/^105$/","/^21$/","/^16$/","/^167$/","/^176$/","/^34$/","/^ethiopia$/","/^finland$/","/^france$/","/^frenchguiana$/","/^154$/","/^28$/","/^128$/","/^43$/","/^38$/","/^129$/","/^127$/","/^guadeloupe$/","/^94$/","/^68$/","/^130$/","/^131$/","/^26$/","/^88$/","/^84$/","/^22$/","/^6$/","/^57$/","/^47$/","/^23$/","/^13$/","/^86$/","/^ivorycoast$/","/^103$/","/^japan$/","/^116$/","/^2$/","/^8$/","/^100$/","/^11$/","/^25$/","/^49$/","/^136$/","/^135$/","/^102$/","/^44$/","/^luxembourg$/","/^macau$/","/^17$/","/^137$/","/^7$/","/^159$/","/^69$/","/^114$/","/^157$/","/^54$/","/^85$/","/^72$/","/^montenegro$/","/^180$/","/^37$/","/^80$/","/^5$/","/^138$/","/^81$/","/^48$/","/^185$/","/^67$/","/^90$/","/^139$/","/^19$/","/^183$/","/^174$/","/^107$/","/^66$/","/^112$/","/^papuanewguinea$/","/^87$/","/^65$/","/^4$/","/^15$/","/^117$/","/^97$/","/^111$/","/^146$/","/^32$/","/^0$/","/^140$/","/^134$/","/^saintlucia$/","/^101$/","/^178$/","/^53$/","/^61$/","/^29$/","/^184$/","/^115$/","/^141$/","/^149$/","/^31$/","/^177$/","/^56$/","/^64$/","/^98$/","/^142$/","/^106$/","/^46$/","/^110$/","/^55$/","/^143$/","/^9$/","/^52$/","/^99$/","/^89$/","/^62$/","/^161$/","/^turksandcaicos$/","/^95$/","/^75$/","/^1$/","/^187$/","/^187$/","/^40$/","/^70$/","/^10$/","/^30$/","/^147$/","/^96$/"];    $flags = ["🇦🇫افغانستان🇦🇫","🇦🇱آلبانی🇦🇱","🇩🇿الجزایر🇩🇿","🇦🇴آنگولا🇦🇴","🇦🇮آنگویلا🇦🇮","🇦🇷آرژانتین🇦🇷","🇦🇲ارمنستان🇦🇲","🇦🇼آروبا🇦🇼","🇦??استرالیا🇦🇺","🇦🇹اتریش🇦🇹","🇦🇿آذربایجان🇦🇿","🇧🇸باهاما🇧🇸","🇧🇭بحرین🇧🇭","🇧🇩بنگلادش🇧🇩","🇧🇧باربادوس🇧🇧","🇧🇾بلاروس🇧🇾","🇧🇪بلژیک🇧🇪","🇧🇯بنین🇧🇯","🇧🇴بولیوی🇧🇴","🇧🇼بوتسوانا🇧🇼","🇧🇷برزیل🇧🇷","🇧🇬بلغارستان🇧🇬","🇧🇮بوروندی🇧🇮","🇰🇭کامبوج🇰🇭","🇨🇲کامرون🇨🇲","🇨🇦کانادا🇨🇦","🇨🇻کیپ ورد🇨🇻","🇹🇩چاد🇹🇩","🇨🇳چین🇨🇳","🇨🇴کلمبیا🇨🇴","🇰🇲کومور🇰🇲","🇨🇬کنگو🇨🇬","🇨🇷کاستاریکا🇨🇷","🇭🇷کرواسی🇭🇷","🇨🇺کوبا🇨🇺","🇨🇾قبرس🇨🇾","🇨🇿چک🇨🇿","🇩🇲دومینیکا🇩🇲","🇹🇱تیمور شرقی🇹🇱","🇪🇨اکوادور🇪🇨","🇪🇬مصر🇪🇬","🏴󠁧󠁢󠁥󠁮󠁧󠁿انگلستان🏴󠁧󠁢󠁥󠁮󠁧󠁿","🇬🇶گینه استوایی🇬🇶","🇪🇷اریتره🇪🇷","🇪🇪استونی🇪🇪","🇪🇹اتیوپی🇪🇹","🇫🇮فنلاند🇫🇮","🇫🇷فرانسه🇫🇷","🇬🇫گویان فرانسه🇬🇫","🇬🇦گابن🇬🇦","🇬🇲گامبیا🇬🇲","🇬🇪گرجستان🇬🇪","🇩🇪آلمان🇩🇪","🇬🇭غنا🇬🇭","🇬🇷یونان🇬🇷","🇬🇩گرنادا🇬🇩","🇬🇵گوادلوپ🇬🇵","🇬🇹گواتمالا🇬🇹","🇬🇳گینه🇬🇳","🇬🇼گینه بیسائو🇬🇼","🇬🇾گویان🇬🇾","🇭🇹هائیتی🇭🇹","🇭🇳هندوراس🇭🇳","🇭🇺مجارستان🇭🇺","🇮🇳هند🇮🇳","🇮🇩اندونزی🇮🇩","🇮🇷ایران🇮🇷","🇮🇶عراق🇮🇶","🇮🇪ایرلند🇮🇪","🇮🇱اسرائیل🇮🇱","🇮🇹ایتالیا🇮🇹","🇨🇮ساحل عاج🇨🇮","🇯🇲جامائیکا🇯🇲","🇯🇵ژاپن🇯🇵","🇯🇴اردن🇯🇴","🇰🇿قزاقستان🇰🇿","🇰🇪کنیا🇰🇪","🇰🇼کویت🇰🇼","🇰🇬قرقیزستان🇰🇬","🇱🇦لائوس🇱🇦","🇱🇻لتونی🇱🇻","🇱🇸لسوتو🇱🇸","🇱🇷لیبریا🇱🇷","🇱🇾لیبی🇱🇾","🇱🇹لیتوانی🇱🇹","🇱🇺لوکزامبورگ🇱🇺","🇲🇴ماکائو🇲🇴","🇲🇬ماداگاسکار🇲🇬","🇲🇼مالاوی🇲🇼","🇲🇾مالزی🇲🇾","🇲🇻مالدیو🇲🇻","🇲🇱مالی🇲🇱","🇲🇷موریتانی🇲🇷","🇲🇺موریس🇲🇺","🇲🇽مکزیک🇲🇽","🇲🇩مولداوی🇲🇩","🇲🇳مغولستان🇲🇳","🇲🇪مونته نگرو🇲🇪","🇲🇸مونتسرات🇲🇸","🇲🇦مراکش🇲🇦","🇲🇿موزامبیک🇲🇿","🇲🇲میانمار🇲🇲","🇳🇦نامیبیا🇳🇦","🇳🇵نپال🇳🇵","🇳🇱هلند🇳🇱","🇳🇨کالدونیای جدید🇳🇨","🇳🇿نیوزیلند🇳🇿","🇳🇮نیکاراگوئه🇳🇮",🇳🇪نیجر🇳🇪,"🇳🇬نیجریه🇳🇬","🇲🇰مقدونیه شمالی🇲🇰","🇳🇴نروژ🇳🇴","🇴🇲عمان🇴🇲","🇵🇰پاکستان🇵🇰","🇵🇦پاناما🇵🇦","🇵🇬پاپوآ گینه نو🇵🇬","🇵🇾پاراگوئه🇵🇾","🇵🇪پرو🇵🇪","🇵🇭فیلیپین🇵🇭","🇵🇱لهستان🇵🇱","🇵🇹پرتغال🇵🇹","🇵🇷پورتوریکو🇵🇷","🇶🇦قطر🇶🇦","🇷🇪رئونیون🇷🇪","🇷🇴رومانی🇷🇴","🇷🇺روسیه🇷🇺","🇷🇼رواندا🇷🇼","🇰🇳سنت کیتس و نویس🇰🇳","🇱🇨سنت لوسیا🇱🇨","🇸🇻سالوادور🇸🇻","🇸🇹سائوتومه و پرینسیپ🇸🇹","🇸🇦عربستان سعودی🇸🇦","🇸🇳سنگال🇸🇳","🇷🇸صربستان🇷🇸","🇸🇨جمهوری سیشل🇸🇨","🇸🇱سیرالئون🇸🇱","🇸🇰اسلواکی🇸🇰","🇸🇴سومالی🇸🇴","🇿🇦آفریقای جنوبی🇿🇦","🇸🇸سودان جنوبی🇸🇸","🇪🇸اسپانیا🇪🇸","🇱🇰سریلانکا🇱🇰","🇸🇩سودان🇸🇩","🇸🇷سورینام🇸🇷","🇸🇿سوازیلند🇸🇿","🇸🇪سوئد🇸🇪","🇸🇾سوریه🇸🇾","🇹🇼تایوان🇹🇼","🇹🇯تاجیکستان🇹🇯","🇹🇿تانزانیا🇹🇿","🇹🇭تایلند🇹🇭","🇹🇬توگو🇹🇬","🇹🇳تونس🇹🇳","🇹🇷ترکیه🇹🇷","🇹🇲ترکمنستان🇹🇲","🇹🇨جزیره تورکس و کایکوس🇹🇨","🇦🇪امارات متحده عربی🇦🇪","🇺🇬اوگاندا🇺🇬","🇺🇦اوکراین🇺🇦","🇺🇸ایالات متحده آمریکا🇺🇸","🇺🇿ازبکستان🇺🇿","🇻🇪ونزوئلا🇻🇪","🇻🇳ویتنام🇻🇳","🇾🇪یمن🇾🇪","🇿🇲زامبیا🇿🇲","🇿🇼زیمبابوه🇿🇼"];
    $ddds = preg_replace($patern, $flags, $arrrr[$i]);
    mysqli_multi_query($connect,"INSERT INTO `gold` (`name`, `viber`, `amazon`, `tiktok`, `facebook`,`microsoft`, `instagram`, `yahoo`, `google`, `telegram`, `whatsapp`, `line`, `twitter`, `paypal`, `viberCount`, `amazonCount`, `tiktokCount`, `facebookCount`,`microsoftCount`, `instagramCount`, `yahooCount`, `googleCount`, `telegramCount`, `whatsappCount`, `lineCount`, `twitterCount`, `paypalCount`) VALUES ('$ddds', '$viber', '$amazon', '$tiktok', '$facebook', '$microsoft', '$instagram', '$yahoo', '$google', '$telegram', '$whatsapp', '$line', '$twitter', '$paypal','$viber_count', '$amazon_count', '$tiktok_count', '$facebook_count', '$microsoft_count', '$instagram_count', '$yahoo_count', '$google_count', '$telegram_count', '$whatsapp_count', '$line_count', '$twitter_count', '$paypal_count')");

}
$connect->query("DELETE FROM gold WHERE name = ''");
//$connect->query("DELETE FROM gold WHERE instagramCount = ''");
$connect->query("DELETE FROM silver WHERE name = ''");
?>

