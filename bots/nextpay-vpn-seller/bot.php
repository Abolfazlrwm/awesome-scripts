<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

ini_set('max_execution_time','250');
set_time_limit(250);
ob_start('ob_gzhandler');
$pid = pcntl_fork();
if($pid == '0' or $pid == null) exit;

$telegram_ip_ranges = [['lower' => '149.154.160.0','upper' => '149.154.175.255'],['lower' => '91.108.4.0','upper' => '91.108.7.255']];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
foreach($telegram_ip_ranges as $telegram_ip_range){
    if($ok){
        $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
        $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
        if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec){
            $ok = true;
        }else{
            die;
        }
    }    
}

$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $message = $update->message;
    $text = $update->message->text ?? NULL;
    $message_id = $update->message->message_id;
    $from_id = $update->message->from->id;
}if(isset($update->callback_query)){
    $data = $update->callback_query->data;
    $from_id = $update->callback_query->from->id;
    $message_id  = $update->callback_query->message->message_id;
}

define('ROBOT',' TOKEN ');

function bot($method, $data = []){
    $url  = 'https://api.telegram.org/bot'. ROBOT. '/'.$method;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    return json_decode(curl_exec($curl));
}
function send($text,$keyboard = null){
    global $from_id;
    bot('sendMessage',[
        'chat_id'=>$from_id,
        'text'=>$text,
        'reply_markup'=>$keyboard
    ]);
}
function text($text1) {
    return ($GLOBALS['text'] === $text1);
}
function del(){
    global $from_id; global $message_id;
    bot('deletemessage', [
        'chat_id'    => $from_id,
        'message_id' => $message_id
    ]);
}
function info($plan){
    switch($plan){
        case 'cx11':
            return '2-1-20-3.85';
        case 'cpx11':
            return '2-2-40-4.35';
        case 'cx21':
            return '4-2-40-5.35';
        case 'cpx21':
            return '4-3-80-7.55';
        case 'cx31':
            return '8-2-80-9.70';
        case 'cpx31':
            return '8-4-160-13.60';
        case 'cx41':
            return '16-4-240-17.40';
        case 'cpx41':
            return '16-8-240-25.20';
        default:
            return false;
    }
}
function location($loc){
    switch($loc){
        case '1':
            return '🇩🇪 falkentein';
        case '2':
            return '🇩🇪 nuremberg';
        case '3':
            return '🇫🇮 helsinki';
        default:
            return false;
    }
}
include 'database.php';
$home = json_encode(['keyboard'=>[
    [['text'=>'🛒 سرور هتزنر'],['text'=>'🛒 سرور لینود']],
    [['text'=>'💳 حساب من']],
    [['text'=>'✅ افزایش موجودی'],['text'=>'👨🏻‍💻 ارتباط با پشتیبانی']],
    [['text'=>'📚 قوانین ربات']],
    ],
    'resize_keyboard'=>true
]);
$buyserver = json_encode(['keyboard'=>[
    [['text'=>'🛒 خرید سرور'],['text'=>'📡 لیست سرور ها']],
    [['text'=>'↩️ بازگشت']],
    ],
    'resize_keyboard'=>true
]);
$back = json_encode(['keyboard'=>[
    [['text'=>'↩️ بازگشت']],
    ],
    'resize_keyboard'=>true
]);

$user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `user_id` = $from_id"));
if (!$user){
    insert('users', ['user_id' => $from_id, 'step' => 'none']);
}
$coin = $user['coin'];
$step = $user['step'];

if(in_array($text,["/start","↩️ بازگشت"])){
    send('•• به ربات هاستینگ ( سرور ساعتی هتزنر ) خوش آمدید .',$home);
    die;
}elseif(text('🛒 سرور هتزنر')){
    send('no text',$buyserver);
    update('users', ['step' => 'hetzner'], ['user_id' => $from_id]);
}elseif(text('📚 قوانین ربات')){
    send('no text',$home);
    update('users', ['step' => 'none'], ['user_id' => $from_id]);
}elseif(text('💳 حساب من')){
    send("▫️اطلاعات حساب کاربری شما:

•• اعتبار فعلی شما: $coin تومان",$home);
    update('users', ['step' => 'none'], ['user_id' => $from_id]);
}elseif(text('👨🏻‍💻 ارتباط با پشتیبانی')){
    send("support id is : ",$home);
    update('users', ['step' => 'none'], ['user_id' => $from_id]);
}elseif(text('✅ افزایش موجودی')){
    send('👈🏻 مبلغ مورد خود را به تومان وارد کنید',$back);
    update('users', ['step' => 'payment'], ['user_id' => $from_id]);
}elseif($step == 'payment'){
    if ($text== intval($text) && preg_match('/^\d+$/', $text) and $text >= '30000') {
        $SefChargee = rand(11111111111,999999999999999);
        file_put_contents("ListCode/$SefChargee.txt","$from_id");
        $inline = json_encode(['inline_keyboard'=>[
            [['text'=>"🌐 واریز مبلغ 🌐",'url'=>"https://bot.sinzavpn.fun/sell/payment/nextpay.php?get&order_id=$SefChargee&amount=$text"]],
        ]]);
        send('⬇️ لینک پرداخت :',$inline);
        update('users', ['step' => 'none'], ['user_id' => $from_id]);
        send('•• به ربات هاستینگ ( سرور ساعتی هتزنر ) خوش آمدید .',$home);
    }else{
        send('مبلغ وارد شده کمتر از 30,000 تومان است لطفا مجدد تلاش کنید',$back);
    }
}

# api start 
if(text('🛒 خرید سرور')){
    if($step == 'hetzner'){
        $inline = json_encode(['inline_keyboard'=>[
            [['text'=>"cx11",'callback_data'=>"crs-cx11"],['text'=>"cpx11",'callback_data'=>"crs-cpx11"]],
            [['text'=>"cx21",'callback_data'=>"crs-cx21"],['text'=>"cpx21",'callback_data'=>"crs-cpx21"]],
            [['text'=>"cx31",'callback_data'=>"crs-cx31"],['text'=>"cpx31",'callback_data'=>"crs-cpx31"]],
            [['text'=>"cx41",'callback_data'=>"crs-cx41"],['text'=>"cpx41",'callback_data'=>"crs-cpx41"]],
        ]]);
        send('🛍 به قسمت خرید سرور خوش آمدید ؛  ',$inline);
        update('users', ['step' => 'hetzner'], ['user_id' => $from_id]);
    }
}
if(strpos($data,'crs-') !== false){
    $systems = explode("-",$data)[1];
    $get_info = info($systems);
    $inline = json_encode(['inline_keyboard'=>[
        [['text'=>"🇩🇪 falkentein",'callback_data'=>"location-$systems-1"],['text'=>"🇩🇪 nuremberg",'callback_data'=>"location-$systems-2"]],
        [['text'=>"🇫🇮 helsinki",'callback_data'=>"location-$systems-3"]],
    ]]);
    $ram = explode("-",$get_info)[0];
    $cpu = explode("-",$get_info)[1];
    del();
    send("•• لطفا لوکیشن سرور خود را انتخاب کنید :\n❏ $systems : RAM = $ram | CPU = $cpu Core",$inline);
}if(strpos($data,'location-') !== false){
    $systems = explode("-",$data)[1];
    $get_info = info($systems);
    $location = explode("-",$data)[2];
    $inline = json_encode(['inline_keyboard'=>[
        [['text'=>"ubuntu 20",'callback_data'=>"os-$systems-$location|ubuntu-20.04"],['text'=>"ubuntu 18",'callback_data'=>"os-$systems-$location|ubuntu-18.04"]],
        [['text'=>"ubuntu 22",'callback_data'=>"os-$systems-$location|ubuntu-22.04"],['text'=>"centos 7",'callback_data'=>"os-$systems-$location|centos-7"]],
    ]]);
    
    $payment = explode("-",$get_info)[3] * $euro;
    $payhour = intval($payment/30/24);
    $pays = number_format($payment, 0, '.', ',');
    $loc = location($location);
    del();
    send("🖥 سرور کلود مدل $systems

◂◂ لوکیشن انتخاب شده :$loc
💶 هزینه ماهانه : $pays
💶 هزینه ساعتی : $payhour",$inline);
}if(strpos($data,'os-') !== false){
    del();
    $systems = explode("-",$data)[1];
    $location = explode("|",explode("-",$data)[2])[0];
    $os = explode("|",$data)[1];
    if($coin < '30000'){
        send("➕ موجودی شما کمتر از 30,000 تومان است لطفا اول شارژ حساب انجام دهید سپس وارد بخش خرید شوید",$home);
        return;
    }
    $randoms = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTU"),0,6);
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?create&server_type=$systems&loc=$location&api_token=$api_key&name=$from_id-$randoms&os=$os");
    $response = json_decode($request, true);
    if(isset($response->error->message)){
        send('❌ سرور ساخته نشد متاسفانه',$home);
        die;
    }if(isset($response['server']['public_net']['ipv4']['ip'])){
        $get_info = info($systems);
        $payment = explode("-",$get_info)[3] * $euro;
        $payhour = intval($payment/30/24) + 1000;
        update('users', ['coin' => $coin - $payhour], ['user_id' => $from_id]);
        send("☑️ سرور شما با موفقیت ساخته شد :

🌐 سیستم عامل : $os
🌐 ipv4 : {$response['server']['public_net']['ipv4']['ip']}
🌐 ipv6 : {$response['server']['public_net']['ipv6']['ip']}
🌐 root password : {$response['root_password']}

✖️ مبلغ کسر شده + 1000 تومان هزینه ساخت : $payhour

            ",$home);
    }
}if(text('📡 لیست سرور ها')){
    if($step == 'hetzner'){
        $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?list_all&api_token=$api_key");
        $response = json_decode($request, true);
        if(isset($response->error->message)){
            update('users', ['step' => 'none'], ['user_id' => $from_id]);
            send('❌ خطا دریافت شده است',$home);
            die;
        }
        preg_match_all('/({[^}]+})/', $request, $matches);
        $t = [];
        foreach ($matches[0] as $match) {
            $decoded = json_decode($match, true);
            if(strpos($decoded['name'],"$from_id") !== false){
                $t[] = [['text' => $decoded['ipv4'], 'callback_data' => "edits|{$decoded['id']}"]];
            }
        }if($t == null){
            update('users', ['step' => 'none'], ['user_id' => $from_id]);
            send('❌ شما سروری ندارید در اکانتتان',$home);
            die;
        }
        send("🕹 کدام سرور را میخواهید تنظیماتی بر رویش اعمال کنید :",json_encode(['inline_keyboard' => $t]));
        update('users', ['step' => 'hetzner'], ['user_id' => $from_id]);
    }
}

if(strpos($data,'edits|') !== false){
    del();
    if($coin < '20000'){
        send("❌ موجودی شما کمتر از 20,000 تومان است و قادر به مدیریت سرویس های خود نیستید.",$home);
        exit;
    }
    $systems = explode("|",$data)[1];
    $inline = json_encode(['inline_keyboard'=>[
        [['text'=>"⚙️ ریبیلد سرور",'callback_data'=>"rebuild|$systems"],['text'=>'🗑 حذف سرور','callback_data'=>"deleteserver|$systems"]],
        [['text'=>"❌ خاموش",'callback_data'=>"poweroff|$systems"],['text'=>"✅ روشن",'callback_data'=>"poweron|$systems"]],
        [['text'=>"⚙️ اطلاعات سرور",'callback_data'=>"datserver|$systems"],['text'=>"👨‍💻 بازیابی رمز عبور",'callback_data'=>"resetpassword|$systems"]],
    ]]);
    send('⁉️ چه کاری میخواهید انجام دهید :',$inline);
}if(strpos($data,'resetpassword|') !== false){
    del();
    $systems = explode("|",$data)[1];
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?reset&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if($response['root_password'] != null){
        bot('sendMessage',[
                'chat_id'=>$from_id,
                'text'=>"☑️ پسورد جدید : `{$response['root_password']}`",
                'parse_mode' => "MarkDown",
                'reply_markup'=>$home
            ]);
    }else{
        send('❌ ریست پسورد ناموفق بود',$home);
    }
}if(strpos($data,'deleteserver|') !== false){
    del();
    $systems = explode("|",$data)[1];
     $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?delete&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if($response['action']['command'] == 'delete_server'){
        send("☑️ سرور با موفقیت حذف شد",$home);
    }else{
        send('❌ سرور از قبل حذف شده یا امکان ندارد الان این سرور را حذف نمایید',$home);
    }
}
if(strpos($data,'rebuild|') !== false){
    del();
    $systems = explode("|",$data)[1];
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?rebuild&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if($response['root_password'] != null){
        bot('sendMessage',[
                'chat_id'=>$from_id,
                'text'=>"✅ ریبیلد با موفقیت انجام شد و پسورد جدید سرورتون : `{$response['root_password']}`",
                'parse_mode' => "MarkDown",
                'reply_markup'=>$home
            ]);
    }else{
        send('🔴 ریبیلد کردن سرور ناموفق بود',$home);
    }
}if(strpos($data,'poweroff|') !== false){
    del();
    $systems = explode("|",$data)[1];
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?poweroff&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if($response['action']['command'] == 'stop_server'){
        send('🙁 سرور با موفقیت خاموش شد',$home);
    }else{
        send('🔰 سرور یا از قبل خاموش بود یا مشکلی پیش امده است',$home);
    }
}if(strpos($data,'poweron|') !== false){
    del();
    $systems = explode("|",$data)[1];
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?poweron&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if($response['action']['command'] == 'start_server'){
        send('😊 سرور با موفقیت روشن شد',$home);
    }else{
        send('🔰 سرور یا از قبل روشن بوده یا مشکلی پیش امده است',$home);
    }
}if(strpos($data,'datserver|') !== false){
    del();
    $systems = explode("|",$data)[1];
    $request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?data&api_token=$api_key&id=$systems");
    $response = json_decode($request, true);
    if(isset($response['ipv4'])){
        if($response['status'] == 'running'){
            $v = '✅';
        }else{
            $v = '❌';
        }
        $qq = $response['outgoing_traffic'] ?: '0';
        send("⚠️ وضعیت سرور : $v

☑️ ram : {$response['ram']}
☑️ cpu : {$response['cpu']}
☑️ disk : {$response['disk']}
☑️ ipv4 : {$response['ipv4']}
☑️ ipv6 : {$response['ipv6']}
☑️ traffic upload use : $qq
",$home);
    }else{
        send('❌ مشکلی در دریافت اطلاعات پیش امده است',$home);
    }
}