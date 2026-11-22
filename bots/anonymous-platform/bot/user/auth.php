<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

$row_admin = $conn->query("SELECT * FROM ".admin)->fetch();
$row_users = $conn->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
$ex_step = explode(";",$row_users['step']);
if(!$row_users){
    if($ex_text[0] == "/start r"){
        $row = infoUser($ex_text[1]);
        if(!$row_users){
            $conn->query("INSERT INTO ".users." (user_id,referral,created_at,uniq_id) VALUES ('$user_id','{$row['user_id']}','$time','".randomNumber(10,users)."')");
            $conn->query("UPDATE ".users." SET balance=balance+{$row_admin['coin_per_invite']} WHERE user_id='{$row['user_id']}'");
            $row_users = $conn->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
            
            $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['user_id']}'")->fetch();
            send_reply("sendMessage",[
                'chat_id' => $row['user_id'],
                'text' =>
                    "💥تبریک!\n\n".
                    "هم اکنون یک نفر با لینک مخصوص شما در ربات عضو شد و به شما {$row_admin['coin_per_invite']} سکه بابت این معرفی تعلق گرفت.\n\n".
                    "💰سکه فعلی شما : {$row_user_select['balance']}\n".
                    "به محض تکمیل پروفایل کاربر {$row_admin['coin_per_invite_profile']} سکه و به محض معرفی کردن به دیگران {$row_admin['coin_per_invite_invite']} سکه به شما تعلق خواهد گرفت😎",
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "👥 معرفی افراد بیشتر",'callback_data' => 'invite']]]])
            ]);
            if($row_user_select['referral'] != null){
                $conn->query("UPDATE ".users." SET balance=balance+{$row_admin['coin_per_invite_invite']} WHERE user_id='{$row_user_select['referral']}'");
                $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row_user_select['referral']}'")->fetch();
                send_reply("sendMessage",[
                    'chat_id' => $row_user_select['user_id'],
                    'text' =>
                        "🔔 تبریک ! شما {$row_admin['coin_per_invite_invite']} سکه بابت معرفی به دیگرانِ کاربری که توسط شما معرفی شده بود دریافت کردید.\n\n".
                        "💰سکه فعلی شما : {$row_user_select['balance']}",
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "👥 معرفی افراد بیشتر",'callback_data' => 'invite']]]])
                ]);
            }
        }
    }
    if(!$row_users){
        $conn->query("INSERT INTO ".users." (user_id,last_activity,created_at,uniq_id) VALUES ('$user_id','$time','$time','".randomNumber(10,users)."')");
        $row_users = $conn->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
    }
    send_reply("sendMessage", [
        'chat_id' => $user_id,
        'text' => 
            "سلام $first_name عزیز ✋️\n\n".
            "به 《{$settings['bot_name']} 🤖》 خوش اومدی ، توی این ربات می تونی افراد #نزدیک ات رو پیدا کنی و باهاشون آشنا شی و یا به یه نفر بصورت #ناشناس وصل شی و باهاش #چت کنی ❗️\n\n".
            "- استفاده از این ربات رایگانه و اطلاعات تلگرام شما مثل اسم،عکس پروفایل یا موقعیت GPS کاملا محرمانه هست😎\n".
            "<code>برای شروع جنسیتت رو انتخاب کن</code> 👇",
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'من🙍‍♂️پسرم','callback_data' => 'set_gender;boy'],['text' => 'من🙎‍♀️دخترم','callback_data' => 'set_gender;girl']]]])
    ]);
    exit;
}
if($user_id != $settings['admin_id'] and ($row_users['status'] == "block"))
    exit;
if($ex_text[0] == "/start r" or $text == $main or $text == $back or $data == "start"){
    if($data == "start"){
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        if($row_users['step'] == 'start'){
            send_reply("sendMessage", [
                'chat_id' => $user_id,
                'text' =>
                    "متوجه نشدم :/\n\n".
                    "<code>چه کاری برات انجام بدم؟ از منوی پایین انتخاب کن 👇</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            exit;
        }
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "خب ، حالا چه کاری برات انجام بدم؟\n\n".
                "<code>از منوی پایین👇 انتخاب کن</code>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
        ]);
        exit;
    }
    mainMenu();
    exit;
}
if($row_users['gender'] == null or !$row_users['age'] or $row_users['state'] == null){
    $keboard_ages = json_encode(['resize_keyboard' => true,'keyboard' => [
        [['text' => '9'],['text' => '10'],['text' => '11'],['text' => '12'],['text' => '13'],['text' => '14'],['text' => '15']],
        [['text' => '16'],['text' => '17'],['text' => '18'],['text' => '19'],['text' => '20'],['text' => '21'],['text' => '22']],
        [['text' => '23'],['text' => '24'],['text' => '25'],['text' => '26'],['text' => '27'],['text' => '28'],['text' => '29']],
        [['text' => '30'],['text' => '31'],['text' => '32'],['text' => '33'],['text' => '34'],['text' => '35'],['text' => '36']],
        [['text' => '37'],['text' => '38'],['text' => '39'],['text' => '40'],['text' => '41'],['text' => '42'],['text' => '43']],
        [['text' => '44'],['text' => '45'],['text' => '46'],['text' => '47'],['text' => '48'],['text' => '49'],['text' => '50']],
        [['text' => '51'],['text' => '52'],['text' => '53'],['text' => '54'],['text' => '55'],['text' => '56'],['text' => '57']],
        [['text' => '58'],['text' => '59'],['text' => '60'],['text' => '61'],['text' => '62'],['text' => '63'],['text' => '64']],
        [['text' => '65'],['text' => '66'],['text' => '67'],['text' => '68'],['text' => '69'],['text' => '70'],['text' => '71']],
        [['text' => '79'],['text' => '80'],['text' => '81'],['text' => '82'],['text' => '83'],['text' => '84'],['text' => '85']],
        [['text' => '86'],['text' => '87'],['text' => '88'],['text' => '89'],['text' => '90'],['text' => '91'],['text' => '92']],
        [['text' => '93'],['text' => '94'],['text' => '95'],['text' => '96'],['text' => '97'],['text' => '98'],['text' => '99']],
    ]]);
    if($row_users['gender'] == null){
        if($ex_data[0] == 'set_gender'){
            $conn->query("UPDATE ".users." SET gender='{$ex_data[1]}' WHERE user_id='$user_id'");
            send_reply("sendMessage", [
                'chat_id' => $user_id,
                'text' => "خب حالا سنت رو بهم بگو ؟:\n\n".
                    "<code>• سنت رو از لیست پایین 👇انتخاب کن یا خودت تایپ کن</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => $keboard_ages
            ]);
        }
        else{
            send_reply("sendMessage", [
                'chat_id' => $user_id,
                'text' => "⚠️ خطا: فقط یکی از گزینه های زیر را انتخاب کنید 👇",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'من🙍‍♂️پسرم','callback_data' => 'set_gender;boy'],['text' => 'من🙎‍♀️دخترم','callback_data' => 'set_gender;girl']]]])
            ]);
        }
        exit;
    }

    $result = $conn->query("SELECT * FROM ".states." WHERE parent=0 ORDER BY id ASC")->fetchAll();
    $keboard_states = [];
    $i = 0;
    $j = 1;
    foreach($result as $key => $row){
        $keboard_states[$i][] = ['text' => $row['state']];
        if($j % 2 == 0)
            $i++;
        $j++;
    }
    if(!$row_users['age']){
        if(is_numeric($text) and $text >= 9 and $text <= 99){
            $conn->query("UPDATE ".users." SET age='$text' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "خب حالا فقط کافیه استانت رو انتخاب کنی تا وارد ربات شیم\n\n".
                    "<code>• استانت رو از لیست پایین 👇انتخاب کن</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $keboard_states])
            ]);
        }
        else{
            send_reply("sendMessage", [
                'chat_id' => $user_id,
                'text' => "⚠️ خطا: فقط یکی از گزینه های زیر را انتخاب کنید 👇",
                'reply_to_message_id' => $message_id,
                'reply_markup' => $keboard_ages
            ]);
        }
        exit;
    }
    if($row_users['state'] == null){
        if($conn->query("SELECT id FROM ".states." WHERE parent=0 AND state='$text'")->fetch()){
            $conn->query("UPDATE ".users." SET state='$text' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "✅اطلاعات شما ثبت شد.\n\n".
                    "به خانواده بزرگ #{$settings['bot_name']} خوش اومدی بهت توصیه میکنم اول از همه با لمس کردن 《🤔 راهنما》 با ربات آشنا شی\n\n".
                    "<code>از منوی پایین👇 انتخاب کن</code>",
                'parse_mode' => 'HTML',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            $keboard = keboards('search');
            $keboard[] = [['text' => '🗣 به یه ناشناس وصلم کن','callback_data' => 'anon']];
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "همین الان برای شروع چت 《🗣 به یه ناشناس وصلم کن》 رو بزن و شانستو #رایگان و بدون نیاز به 💰سکه امتحان کن 😎👇\n\n".
                    "چه کسایی رو نشونت بدم؟ <code>انتخاب کن👇</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => $keboard])
            ]);
        }
        else{
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "استانت رو از لیست پایین انتخاب کن👇",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $keboard_states])
            ]);
        }
        exit;
    }
}
if($data == "checkJoin"){
    $output = "";
    if($row_admin['channel_1'] != null and IsMember("@{$row_admin['channel_1']}", $user_id) == "left"){
        $output = "{$row_admin['channel_1_name']}";
    }
    if($row_admin['channel_2'] != null and IsMember("@{$row_admin['channel_2']}", $user_id) == "left"){
        if($output == "")
            $output = "{$row_admin['channel_2_name']}";
        else $output = "$output و {$row_admin['channel_2_name']}";
    }
    if($row_admin['channel_3'] != null and IsMember("@{$row_admin['channel_3']}", $user_id) == "left"){
        if($output == "")
            $output = "{$row_admin['channel_3_name']}";
        else $output = "$output و {$row_admin['channel_3_name']}";
    }
    if($output != ""){
        answerCallbackQuery("⚠️ شما هنوز عضو کانال های « $output » نشده اید، برای عضو شدن در کانال ها JOIN/عضو شدن را در کانال بزنید.");
        exit;
    }
    send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => 
            "✅ عضویت شما تایید شد ! شما هم اکنون می توانید از امکانات ویژه ربات استفاده کنید !\n\n".
            "<code>یکی از گزینه های زیر را لمس کنید 👇</code>",
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
    ]);
    $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
    exit;
}
if(($ex_step[0] == "no_join_channels" or ($row_users['last_check_join_at'] + 120) < $time) and ($row_admin['channel_1'] != null or $row_admin['channel_2'] != null or $row_admin['channel_3'] != null)){
    $output = "";
    $reply_markup = [];
    if($row_admin['channel_1'] != null and IsMember("@{$row_admin['channel_1']}", $user_id) == "left"){
        $reply_markup[] = [['text' => "کانال اول ({$row_admin['channel_1_name']})",'url' => "https://t.me/{$row_admin['channel_1']}"]];
        $output = "{$row_admin['channel_1_name']}";
        $channel_1 = "👉 @{$row_admin['channel_1']}\n";
    }
    if($row_admin['channel_2'] != null and IsMember("@{$row_admin['channel_2']}", $user_id) == "left"){
        $reply_markup[] = [['text' => "کانال دوم ({$row_admin['channel_2_name']})",'url' => "https://t.me/{$row_admin['channel_2']}"]];
        if($output == "")
            $output = "{$row_admin['channel_2_name']}";
        else $output = "$output و {$row_admin['channel_2_name']}";
        $channel_2 = "👉 @{$row_admin['channel_2']}\n";
    }
    if($row_admin['channel_3'] != null and IsMember("@{$row_admin['channel_3']}", $user_id) == "left"){
        $reply_markup[] = [['text' => "کانال سوم ({$row_admin['channel_3_name']})",'url' => "https://t.me/{$row_admin['channel_3']}"]];
        if($output == "")
            $output = "{$row_admin['channel_3_name']}";
        else $output = "$output و {$row_admin['channel_3_name']}";
        $channel_3 = "👉 @{$row_admin['channel_3']}\n";
    }
    if($output != ""){
        $conn->query("UPDATE ".users." SET step='no_join_channels;none',last_check_join_at='$time' WHERE user_id='$user_id'");
        $reply_markup[] = [['text' => '♻️ بررسی عضویت و فعالسازی ♻️','callback_data' => 'checkJoin']];
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => 
                "سلام $first_name عزیز\n".
                "برای استفاده از ربات  ابتدا باید در کانال های زیر عضو بشی 👇\n\n".
                $channel_1.
                "$channel_2\n\n".
                "<code>⚠️ توجه: درصورتی که در عضویت کانال با خطا مواجه میشوید از نسخه اصلی تلگرام استفاده کنید.</code>\n\n".
                "بعد از عضـــویت « بررسی عضویت و فعال سازی » را لمس کنید تا ربات برای شما فعال شود. 👇\n",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        if(!$conn->query("SELECT * FROM ".notif." WHERE user_id='$user_id' AND reason='first_msg_force_join' AND status='end'")->fetch()){
            $conn->query("INSERT INTO ".notif." (user_id,reason,status,date) VALUES ('$user_id','first_msg_force_join','end','$time')");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => 
                    "اسپانسر این ربات کانال های 《 $output 》 هستند.\n\n".
                    "- بعد از عضو شدن توی کانال ها می تونی از ربات استفاده کنی 😍",
            ]);
        }
        exit;
    }
    $conn->query("UPDATE ".users." SET last_check_join_at='$time' WHERE user_id='$user_id'");
}
if($ex_text[0] == "/user" and $ex_text[1] == $row_users['uniq_id']){
    $text = '👤پروفایل';
    $ex_text = "";
}
if($text == $show_profile){
    $row_chats = $conn->query("SELECT * FROM ".chats." WHERE (user_id_1='$user_id' OR user_id_2='$user_id') AND status='chatting'")->fetch();
    if($row_chats){
        $user_id_select = $row_chats['user_id_1'] == $user_id?$row_chats['user_id_2']:$row_chats['user_id_1'];
        $row = infoUser($user_id_select);
        send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "🤖 پیام سیستم 👇\n\n".
                "مخاطب شما 《 پروفایلِ {$settings['bot_name']} 》  شما را مشاهده کرد.\n\n".
                "<code>⚠️ توجه: پروفایل {$settings['bot_name']} اطلاعاتی است که در بخش پروفایل ربات ثبت کرده اید!</code>",
            'parse_mode' => 'HTML'
        ]);
        $ex_text[0] = "/user";
        $ex_text[1] = $row['uniq_id'];
    }
}
if($ex_text[0] == "/user" && $uniq_id = $ex_text[1]){
    $row_users['latitude'] == null?$where = "":$where = "(SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.")";
    $row = $conn->query("SELECT * FROM $where ".users." WHERE uniq_id='$uniq_id'")->fetch();
    if(!$row){
        $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "⚠️ خطا: کاربر یافت نشد",
        ]);
        exit;
    }
    $row['image'] == null?$file_id = new CURLFile("../files/noimage-{$row['gender']}.jpg"):$file_id = $row['image'];
    $status_chat = "";
    if(substr($row['step'],0,9) == "chatting;")
        $status_chat = " (در حال چت)";
    if(!$row_users['latitude'])
        $d = "موقعیت شما ثبت نشده!";
    else if(!$row['latitude'])
        $d = "موقعیت کاربر ثبت نشده!";
    else $d = number_format($row['distance'],1)."km";
    $array = [
        'chat_id' => $user_id,
        'photo' => $file_id,
        'caption' =>
            "• نام: ".checkInout($row['name'])."\n".
            "• جنسیت: {$txts_array['type_text_user']['with_emoji'][$row['gender']]}\n".
            "• استان: {$row['state']}\n".
            "• شهر: ".checkInout($row['city'])."\n".
            "• سن: {$row['age']}\n\n".
            "".LastActivity($row['last_activity'])."$status_chat\n\n".
            "‏🆔 آیدی : /user_{$row['uniq_id']}\n\n\n".
            "🏁 فاصله از شما: $d",
        'reply_markup' => json_encode(['inline_keyboard' => generateInlineButtons($row['uniq_id'])])
    ];
    if(!send_reply("sendPhoto",$array)['ok']){
        
        $array['photo'] = new CURLFile("../files/noimage-{$row['gender']}.jpg");
        send_reply("sendPhoto", $array);
    }
    exit;
}

?>