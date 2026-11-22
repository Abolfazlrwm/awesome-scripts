<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */


date_default_timezone_set('Asia/Tehran');

ob_start();

error_reporting(0);

include_once('functions.php');

/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/

$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;
}
if(!$ok){
exit(header("location: https://t.me/DevMrErfi"));
}

$token = ''; /// توکن ربات

$admin = '7097965821'; /// آیدی عددی ادمین
$userbot = "";  ///// آیدی ربات بدون @
define('API_KEY', $token);
define('GROUPS', 'groups.json');
define('ADMIN_STATE_FILE', 'admin_state.json');
define('API', 'https://abarmizban.com/ProjecT/API/');


/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/

#-----------------------------#


$update = json_decode(file_get_contents("php://input"));
if(isset($update->message)){
    $message = "message";
$username = $update->message->from->username;
$message = $update->message;
    $from_id    = $update->message->from->id;
    $chat_id    = $update->message->chat->id;
    $tc         = $update->message->chat->type;
    $text       = $update->message->text;
    $first_name = $update->message->from->first_name;
    $message_id = $update->message->message_id;
}elseif(isset($update->callback_query)){
    $chat_id    = $update->callback_query->message->chat->id;
    $data       = $update->callback_query->data;
    $message_id = $update->callback_query->message->message_id;
    $from_id    = $update->callback_query->from->id;
}




if(!is_dir('data')){
    mkdir('data');
}
if(!is_dir("data/$from_id/")){
    mkdir("data/$from_id/");
}




if (isset($update->my_chat_member)) {
    $chat_id = $update->my_chat_member->chat->id;
    $new_status = $update->my_chat_member->new_chat_member->status;
    $old_status = $update->my_chat_member->old_chat_member->status;

    if (in_array($old_status, ['left', 'kicked', 'member'])) {
        if ($new_status == 'member') {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "💎 ربات چکر در این گروه اضافه شد!\n\n🔹 برای استفاده از قابلیت های بیشتر از منو داخل گروهت ادمین کن\n🔎 @$userbot",
            ]);
            savegroup($chat_id);
        } if ($new_status == 'administrator') {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "💎 ربات چکر در این گروه ادمین شد!\n\n🔹 برای استعلام هش تراکنش کافیه لینک هش رو بفرستی 📡\n\nتازه میدونستی میتونی اطلاعات ولت رو هم بگیری؟\nبرای گرفتن اطلاعات ولت کافیه:\n\n`wallet ولت`\n\nرو تایپ کنی و یه فاصله بزاری و بعدش ولت طرف رو بزاری تا من استعلام ولت شو در بیارم و بهت بفرستم.\n\nمثال:\n`wallet TJeTjGKBghYYftM9gvwvwMJQLWMBNK8V`",
        'parse_mode' => "markdown",
    ]);
    savegroup($chat_id);
}
    } elseif ($new_status == 'left' || $new_status == 'kicked') {
        hazfgroup($chat_id);
    }
}

/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/


if($text=="/start" and $tc=="private"){
    bot('sendMessage',[
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'reply_to_message_id' => $message_id,

        'text' =>"*به ربات چکر خوش آمدید \n\n برای استعلام هش تراکنش کافیه لینک هش رو بفرستی 📡 \n\n تازه میدونستی میتونی اطلاعات ولت رو هم بگیری؟
برای گرفتن اطلاعات ولت کافیه 
wallet 
رو تایپ کنی و یه فاصله بزاری و بعدش ولت طرف رو بزاری تا من استعلام ولت شو در بیارم و بهت بفرستم / نمونه :*
`wallet TJeTjGKBghYYftM9gvwvwMJQLWMBNK8V`

*و اینکه منو میتونی به گروه ات هم اضافه کنی*",
        'parse_mode' => "markdown",
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> "عضویت در گروه️", 'url' =>"https://t.me/$userbot?startgroup=new"]]
    ]
    
])
        
        ]);
}




if (preg_match('/^https:\/\/tronscan.org\/#\/transaction\/(.*)/', $text, $match)) {
    $url = API . 'tronscan/index.php?hash=' . $match[1];

    $response = file_get_contents($url);

    if (!$response) {
        sendMessage($chat_id, "❌ خطا در دریافت اطلاعات تراکنش.");
        return;
    }

    $info_decode = json_decode($response, true);

    if (isset($info_decode['result'])) {
        $result = $info_decode['result'];

        $vaziat = $result['contractRet'] ?? 'نامشخص';
        $amount = $result['amount'] ?? 'نامشخص';
        $from = $result['from'] ?? 'نامشخص';
        $to = $result['to'] ?? 'نامشخص';
        $date = $result['date'] ?? 'نامشخص';
        $time = $result['time'] ?? 'نامشخص';
        $hash = $result['hash'] ?? 'نامشخص';

bot('sendMessage', [
    'chat_id' => $chat_id,
    'text' => "*✅ وضعیت تراکنش : $vaziat\n\n🔍 هش تراکنش :*`$hash`\n\n*📆 تاریخ تراکنش : $date\n\n⏰ ساعت تراکنش : $time\n\n💰 مقدار ترون تراکنش : $amount\n\n📤 ولت مبدا :*`$from`\n\n*📥 ولت مقصد :*`$to`",
    'parse_mode' => 'markdown',
    'reply_to_message_id' => $message_id,
]);

    }
}

if (preg_match('/^wallet (.*)/', $text, $match)) {
    $response = file_get_contents(API . 'Tron/tron.php?address=' . $match[1]);

    if (!$response) {
        sendMessage($chat_id, "❌ خطا در دریافت اطلاعات ولت.");
        return;
    }

    $info_decode = json_decode($response, true);
    $balance = $info_decode['balance'] ?? 'خالی است';

bot('sendMessage', [
        'chat_id' => $chat_id,
        'reply_to_message_id' => $message_id,
        'text' => "💎 Mowjudi : $balance",
    ]);
    
}


if($chat_id == $admin){

if($text=="/panel"){
    hazfadminstate($user_id);
    bot('sendMessage',[
        'chat_id' => $chat_id,
        'text' => "سلام ادمین عزیز \n به پنل مدیریتی خوش آمدید",
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'آمار 📊', 'callback_data' =>'amar']],
[['text'=>'📥 پیام همگانی','callback_data'=>"hmg"],['text'=>"📩 فوروارد همگانی",'callback_data'=>"fwd"]],
[['text'=>"ارسال به گروه هایی که عضو است",'callback_data'=>"hmggroups"]],
    ]
    
])
        ]);
}

/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/

if($data=="back"){
hazfadminstate($user_id);
bot('editMessageText',[
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "سلام ادمین عزیز \n به پنل مدیریتی خوش آمدید",
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'آمار 📊', 'callback_data' =>'amar']],
[['text'=>'📥 پیام همگانی','callback_data'=>"hmg"],['text'=>"📩 فوروارد همگانی",'callback_data'=>"fwd"]],
[['text'=>"ارسال به گروه هایی که عضو است",'callback_data'=>"hmggroups"]],

    ]
    
])
        ]);
}

if ($data == "hmggroups") {
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "سلام ادمین عزیز / لطفا پیامی که میخواهید به گپ ها ارسال کنم رو بفرستید",
            'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
        ]);
        saveadminstate($user_id, 'awaiting_message');
    } else {
        $admin_state = getAdminState($user_id);
        if ($admin_state == 'awaiting_message') {
            if (file_exists(GROUPS)) {
                $groups = json_decode(file_get_contents(GROUPS), true);
                if (!empty($groups)) {
                    foreach ($groups as $group_id) {
                        bot('forwardMessage', [
                            'chat_id' => $group_id,
                            'from_chat_id' => $chat_id,
                            'message_id' => $message_id,
                        ]);
                    }
                    bot('sendMessage', [
                        'chat_id' => $chat_id,
                        'text' => "پیام شما به تمام گروه‌ها ارسال شد.",
                    ]);
                } else {
                    bot('sendMessage', [
                        'chat_id' => $chat_id,
                        'text' => "هیچ گروهی ذخیره نشده است.",
                    ]);
                }
            } else {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "هیچ گروهی ذخیره نشده است.",
                ]);
                        hazfadminstate($user_id);
                    }
                }
                
            }


/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/



if($data=="amar"){
    $scan = scandir('data');
    $amar = count($scan) - 2;
    bot('editMessageText',[
        'chat_id' => $chat_id,
        'message_id' => $message_id ,
        'text'=> "آمار ربات در حال حاضر $amar",
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
        ]);
}



if($data == "hmg"){
        bot('editMessageText',[
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        'text'=> "پیام مورد نظر خود را ارسال کنید...", 
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
        ]);
        saveadminstate($user_id, 'send-to-all');

    }
    
    elseif($data == "fwd"){
        bot('editMessageText',[
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        'text'=> "پیام خود را جهت فروارد به تمامی کاربران، به من فروارد کنید...",
        'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
        
        ]);
        saveadminstate($user_id, 'for-to-all');
        
    }

    
    elseif($admin_state == "send-to-all" and $data!="back"){
        
        if($text){
            
            $users_array = scandir('data');
            unset($users_array[0]);
            unset($users_array[1]);
            
            foreach($users_array as $id_to_send){
                
                sendmessage($id_to_send, $text);
                
            }
            
            hazfadminstate($user_id);
            bot('sendMessage',[
                'chat_id' => $chat_id,
            'text' => "عملیات ارسال همگانی با موفقیت انجام شد!",
            'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
            ]);
        }else{
            
            sendmessage($from_id, "صزفا میتوانید متن را برای ارسال به کاربران، برای من ارسال کنید.\n\nلطفا مجددا ارسال کنید:");
            
        }
        
    }
    
    elseif($admin_state == "for-to-all" and $data!="back"){
            
        $users_array = scandir('data');
        unset($users_array[0]);
        unset($users_array[1]);
        
        foreach($users_array as $id_to_send){
            
            bot('forwardMessage', [
                'from_chat_id' => $from_id,
                'message_id' => $message_id,
                'chat_id' => $id_to_send
            ]);
            
        }
        
       hazfadminstate($user_id);
        bot('sendMessage',[
            'chat_id' =>$chat_id,
            'text' =>" فروارد همگانی با موفقیت انجام شد...",
            'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=> 'برگشت 🔙', 'callback_data' =>'back']],

    ]
    
])
            ]);

    }
    

}

/*
Source kade | سورس کده 

کانال سورس تلگرامی با بیش از سه سال سابقه دارای سورس های کاملا رایگان برای عضو شدن از لینک زیر استفاده کنید!

https://t.me/Sourrce_kade

Coded by Erfan RasolPour. @DevMrErfi
*/

?>