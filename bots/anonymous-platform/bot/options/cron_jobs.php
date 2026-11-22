<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
if(!isset($_SERVER['XDG_SESSION_ID']))exit;

$dir = __DIR__;
require_once "$dir/../../include/settings.php";
require_once "$dir/../../include/config.php";
require_once "$dir/functions.php";

$row_admin = $conn->query("SELECT * FROM ".admin)->fetch();
$conn->query("DELETE FROM ".search." WHERE (created_at+86400)<$time");
$conn->query("DELETE FROM ".chatmsgs." WHERE (created_at+604800)<$time");
$conn->query("DELETE FROM ".chats." WHERE status='end'");
$conn->query("DELETE FROM ".notif." WHERE status='end'");

$result = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND content='normal' AND (date+120)<$time AND status='doing'")->fetchAll();
if($result){
    foreach($result as $row){
        $same_age = $row['age']?"<code>⚠️ فعال بودن جستجوی همسن باعث افزوده شدن فیلتر سن در جستجو می شود و می تواند باعث دیر پیدا شدن (و یا گاهی پیدا نشدن) مخاطب شما شود. </code>\n\n":"";
        send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "😔 متاسفانه کسی رو پیدا نکردم\n\n".$same_age.
                "میتونی دوباره جستجو کنی 👇\n\n".
                "<code>    - در قسمت «🔍 جستجوی پیشرفته 🔎»  می تونی افراد هم استانی و نزدیک بدون چت رو پیدا کنی و بهشون درخواست چت بدی !</code>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "♻️ جستجو دوباره ♻️",'callback_data' => "anon"]]]])
        ]);
        $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row['id']}'");
    }
}
$result = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND content='gps' AND (date+600)<$time AND status='doing'")->fetchAll();
if($result){
    foreach($result as $row){
        $same_age = $row['age']?"<code>⚠️ فعال بودن جستجوی همسن باعث افزوده شدن فیلتر سن در جستجو می شود و می تواند باعث دیر پیدا شدن (و یا گاهی پیدا نشدن) مخاطب شما شود. </code>\n\n":"";
        send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "😔 متاسفانه کسی رو پیدا نکردم\n\n".$same_age.
                "میتونی دوباره جستجو کنی 👇\n\n".
                "<code>    - در قسمت «🔍 جستجوی پیشرفته 🔎»  می تونی افراد هم استانی و نزدیک بدون چت رو پیدا کنی و بهشون درخواست چت بدی !</code>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "♻️ جستجو دوباره ♻️",'callback_data' => "anon;gps;none"]]]])
        ]);
        $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row['id']}'");
    }
}
$result = $conn->query("SELECT * FROM ".users." WHERE ($time-last_activity)>259200")->fetchAll();
if($result){
    foreach($result as $row){
        try {
            $count_girl = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
                ".users." WHERE user_id NOT IN ('{$row['user_id']}') AND latitude>0 AND gender='girl' AND distance<100 AND ($time-last_activity)<259200")->rowCount();
            $count_boy = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
                ".users." WHERE user_id NOT IN ('{$row['user_id']}') AND latitude>0 AND gender='boy' AND distance<100 AND ($time-last_activity)<259200")->rowCount();
        }
        catch(\Throwable $th){}
        $row['balance'] < 5?$row['balance'] += 5:0;
            send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "سلام 🙂✋ \n\n".
                "!یه مدتیه به ربات سر نمیزنی؟ نگرانت شدم\n\n".
                ($row['balance'] < 5?"نگرانت شدم، اگه سکه نیاز داری بهت 5 تا سکه اضافه کردم.\n\n":"").
                "<code>از وقتی رفتی (تو این 3 روز)، $count_boy تا 🙎‍♂️ پسر و $count_girl تا 🙍‍♀️دختر که #نزدیک تو بودن تو ربات فعالیت کردن !</code>\n\n".
                "- راستی دقت کرده بودی میتونی با معرفی هر نفر به ربات 20 تا سکه بگیری؟😍\n\n".
                "منتظر چی هستی همین الان یکی از گزینه هارو انتخاب کن و به جمع خانواده 10 میلیون نفری {$settings['bot_name']} برگرد👇",
            'parse_mode' => 'HTML',
        ]);
        $conn->query("UPDATE ".users." SET last_activity='$time',balance='{$row['balance']}' WHERE user_id='{$row['user_id']}'");
    }
}
$query = $conn->query("SELECT id,user_id FROM ".payments." WHERE (updated_at+900)<$time AND (status='first_level' OR status='move_gateway') LIMIT 50");
while($row = $query->fetch()){
    send_reply("sendMessage",[
        'chat_id' => $row['user_id'],
        'text' =>
            "🔻 بنظر میرسد شما دقایقی پیش اقدام  به خرید سکه کرده اید.\n\n".
            "💠 درصورتی که خرید شما نا موفق بوده است می توانید دوباره خرید خود را انجام دهید، دقت کنید حتما گزینه \"تکمیل فرایند خرید\" را پس از پرداخت بزنید.\n\n".
            "💠 درصورت خرید ناموفق مبلغی از حساب شما کسر شده است ، تا 24 ساعت آینده توسط بانک بطور خودکاربه حسابتان بازخواهد گشت.\n\n".
            "💠 برای ارتباط با بخش پشتیبانی ربات می توانید با ایدی  @{$row_admin['support']} در ارتباط باشید.\n\n".
            "💠 درصورتی که هنگام خرید با خطای \"مشتری گرامی دسترسی شما به این صفحه غیر مجاز می باشد\" و یا \"خطا، شما به درستی هدایت نشده اید.\" مواجه شدید ، از صفحه خارج شده و دوباره از طریق لینک های خرید در ربات، اقدام به خرید کنید.  این خطا به دلیل دوبار وارد شدن پشت سر هم شما به درگاه پرداخت است.",
    ]);
    $conn->query("UPDATE ".payments." SET status='expired' WHERE id='{$row['id']}'");
}
