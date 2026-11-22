<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if(substr($text,0,2) == "/s"){
    $ex_data[0] = 'anon';
    $two = substr($text,2);
    if($two == "r")
        $ex_data[1] = 'all';
    else if($two == "g")
        $ex_data[1] = 'girl';
    else if($two == "b")
        $ex_data[1] = 'boy';
}
if($text == "/sr"){
    $ex_data[0] == 'anon';
}
if($text == "🔗 به یه ناشناس وصلم کن!️" or $data == "anon"){
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => "به کی وصلت کنم؟ <code>انتخاب کن👇</code>",
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => '🎲 جستجو شانسی 🎲','callback_data' => "anon;all;none"]],
            [
                ['text' => 'جستجو پسر 🙋‍♂️','callback_data' => "anon;boy;none"],
                ['text' => 'جستجو دختر 🙋‍♀️','callback_data' => "anon;girl;none"]
            ],
            [['text' => '🛰 جستجوی اطراف','callback_data' => "anon;gps;none"]]
        ]])
    ]);
    exit;
}
if($ex_step[0] == 'anon'){
    if($ex_step[1] == 'gps'){
        if(!isset($up['message']['location'])){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => '⚠️ اطلاعاتی که میفرستی درست نیست !',
            ]);
            exit;
        }
        $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
        $location = $up['message']['location']['latitude'];
        $longitude = $up['message']['location']['longitude'];
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "چه کسی رو از افراد نزدیکت پیدا کنم؟ <code>انتخاب کن👇</code>",
            'parse_mode' => 'HTML',
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [
                [
                    ['text' => '🙎‍♀️ دختر باشه (4💰)','callback_data' => "anon;gps;girl;$location;$longitude"],
                    ['text' => '🙍‍♂️ پسر باشه (4💰)','callback_data' => "anon;gps;boy;$location;$longitude"]
                ],
                [['text' => 'فرقی نمیکنه (رایگان)','callback_data' => "anon;gps;all;$location;$longitude"]]
            ]])
        ]);
        checkProfileCoin($user_id);
        exit;
    }
}
if($ex_data[0] == 'anon'){
    if($row_users['same_age']){
        $status_same_age = "✅ فعال\n- غیر فعال : /off";
        $same_age = $row_users['age'];
        $same_age_where = "AND age='$same_age'";
    }
    else{
        $status_same_age = "📴 غیر فعال\n- فعال کردن : /on";
        $same_age = 0;
        $same_age_where = "AND (age='$same_age' OR age='{$row_users['age']}')";
    }
    if($ex_data[1] == 'gps'){
        if($ex_data[2] == 'none'){
            send_reply("editMessageText",[
                'chat_id' => $user_id,
                'text' => "چه کسی رو از افراد نزدیکت پیدا کنم؟ <code>انتخاب کن👇</code>",
                'parse_mode' => 'HTML',
                'message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [['text' => '📍افراد نزدیک من','callback_data' => "anon;gps;near;none"]],
                    [
                        ['text' => '🙎‍♀️ دختر باشه (4💰)','callback_data' => "anon;gps;girl;none"],
                        ['text' => '🙍‍♂️ پسر باشه (4💰)','callback_data' => "anon;gps;boy;none"]
                    ],
                    [['text' => 'فرقی نمیکنه (رایگان)','callback_data' => "anon;gps;all;none"]]
                ]])
            ]);
            exit;
        }
        if($ex_data[2] == 'near'){
            $conn->query("UPDATE ".users." SET step='anon;gps' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "⚠️ هنگام ارسال موقعیت مکانی مطمعن شوید GPS موبایل شما روشن است.\n\n".
                    "برای جستجوی افراد نزدیکت روی دکمه «📍ارسال موقعیت جی پی اس » کلیک کن! 👇",
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [
                    [['text' => "📍ارسال موقعیت جی پی اس",'request_location' => true]],
                    [['text' => $back]]
                ]])
            ]);
            exit;
        }
        if($ex_data[3] != 'none'){
            $row_users['latitude'] = $ex_data[3];
            $row_users['longitude'] = $ex_data[4];
        }
        if($ex_data[2] == 'all'){
            $row_notif = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".notif.") 
                ".notif." WHERE user_id NOT IN ('$user_id') AND distance<50 $same_age_where AND (request_gender='{$row_users['gender']}' OR reason='connectToUserGps') AND content='gps' AND status='doing'")->fetch();
            if(!$row_notif){
                $row_notif = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND user_id='$user_id' AND request_gender IS NULL AND content='gps' AND status='doing'")->fetch();
                if($row_notif){
                    if($row_notif['content'] == 'normal')
                        $deffence = 120 - ($time - $row_notif['date']);
                    if($row_notif['content'] == 'gps')
                        $deffence = 600 - ($time - $row_notif['date']);

                    if($deffence == 60)
                        $output = "1 دقیقه";
                    else if($deffence > 60)
                        $output = floor($deffence / 60)." دقیقه و ".($deffence % 60)." ثانیه";
                    else $output = ($deffence % 60)." ثانیه";
                    answerCallbackQuery("⚠️ خطا: چندبار میزنی؟ دارم جستجو میکنم.\n\n".
                        "⏳ لطفا $output دیگر صبر کنید."
                    );
                    exit;
                }
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "🔎 درحال جستجوی مخاطب ناشناس شما\n".
                        "<code>- 🛰 جستجوی اطراف (🎲شانسی)</code>\n\n".
                        "⏳ حداکثر تا 10 دقیقه صبر کنید.\n\n".
                        "⚙️ جستجوی همسن : $status_same_age",
                    'parse_mode' => 'HTML',
                ]);
                $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND user_id='$user_id'");
                $conn->query("INSERT INTO ".notif." (type,user_id,balance,gender,age,latitude,longitude,reason,content,status,date) VALUES ('search','$user_id','0','{$row_users['gender']}','$same_age','{$row_users['latitude']}','{$row_users['longitude']}','connectToUserGps','gps','doing','$time')");
                exit;
            }
            $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND (user_id='$user_id' OR user_id='{$row_notif['user_id']}')");
            $conn->query("INSERT INTO ".chats." (user_id_1,user_id_2,status,created_at) VALUES ('$user_id','{$row_notif['user_id']}','chatting','$time')");
            $conn->query("UPDATE ".users." SET num_chats=num_chats+1 WHERE user_id='$user_id'");
            $conn->query("UPDATE ".users." SET balance=balance-{$row_notif['balance']},num_chats=num_chats+1 WHERE user_id='{$row_notif['user_id']}'");
            
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                    "به مخاطبت سلام کن 🗣",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
            ]);
            send_reply("sendMessage",[
                'chat_id' => $row_notif['user_id'],
                'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                    "به مخاطبت سلام کن 🗣",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
            ]);
            exit;
        }
        if($ex_data[2] == 'boy' or $ex_data[2] == 'girl'){
            if($row_users['balance'] < 4){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "⚠️ توجه: شما سکه کافی ندارید !  (4 سکه مورد نیاز)\n\n".
                        "<code>💡 برای بدست آوردن سکه میتونی رباتو به دوستات معرفی کنی و به ازای معرفی هر نفر ".($row_admin['coin_per_invite']+$row_admin['coin_per_invite_profile']+$row_admin['coin_per_invite_invite'])." .</code>",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['inline_keyboard' => keboards('coin')])
                ]);
                exit;
            }
            $row_notif = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".notif.") 
                ".notif." WHERE user_id NOT IN ('$user_id') AND latitude>0 AND gender='{$ex_data[2]}' $same_age_where AND distance<50 AND (request_gender='{$row_users['gender']}' OR reason='connectToUserGps') AND content='gps' AND status='doing'")->fetch();
            if(!$row_notif){
                $row_notif = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND user_id='$user_id' AND request_gender='{$ex_data[1]}' AND content='normal' AND status='doing'")->fetch();
                if($row_notif){
                    if($row_notif['content'] == 'normal')
                        $deffence = 120 - ($time - $row_notif['date']);
                    if($row_notif['content'] == 'gps')
                        $deffence = 600 - ($time - $row_notif['date']);

                    if($deffence == 60)
                        $output = "1 دقیقه";
                    else if($deffence > 60)
                        $output = floor($deffence / 60)." دقیقه و ".($deffence % 60)." ثانیه";
                    else{
                        $output = ($deffence % 60)." ثانیه";
                    }
                    answerCallbackQuery("⚠️ خطا: چندبار میزنی؟ دارم جستجو میکنم.\n\n".
                        "⏳ لطفا $output دیگر صبر کنید."
                    );
                    exit;
                }
                $title = $ex_data[2] == 'boy'?"- جستجوی اطراف (جستجو پسر 🙋‍♂️)":"- جستجوی اطراف (جستجو دختر 🙋‍♀️)";
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "🔎 درحال جستجوی مخاطب ناشناس شما\n".
                        "<code>$title</code>\n\n".
                        "⏳ حداکثر تا 10 دقیقه صبر کنید.\n\n".
                        "⚙️ جستجوی همسن : $status_same_age",
                    'parse_mode' => 'HTML',
                ]);
                $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND user_id='$user_id'");
                $conn->query("INSERT INTO ".notif." (type,user_id,balance,gender,request_gender,age,latitude,longitude,content,status,date) VALUES ('search','$user_id','4','{$row_users['gender']}','{$ex_data[2]}','$same_age','{$row_users['latitude']}','{$row_users['longitude']}','gps','doing','$time')");
                exit;
            }
            $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND (user_id='$user_id' OR user_id='{$row_notif['user_id']}')");
            $conn->query("INSERT INTO ".chats." (user_id_1,user_id_2,status,created_at) VALUES ('$user_id','{$row_notif['user_id']}','chatting','$time')");
            $conn->query("UPDATE ".users." SET balance=balance-4,num_chats=num_chats+1 WHERE user_id='$user_id'");
            $conn->query("UPDATE ".users." SET balance=balance-{$row_notif['balance']},num_chats=num_chats+1 WHERE user_id='{$row_notif['user_id']}'");
            
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                    "به مخاطبت سلام کن 🗣",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
            ]);
            send_reply("sendMessage",[
                'chat_id' => $row_notif['user_id'],
                'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                    "به مخاطبت سلام کن 🗣",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
            ]);
            exit;
        }
        exit;
    }
    if($ex_data[1] == 'all'){
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id NOT IN ('$user_id') AND type='search' $same_age_where AND (request_gender='{$row_users['gender']}' OR reason='connectToUser') AND content='normal' AND status='doing'")->fetch();
        if(!$row_notif){
            $row_notif = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND user_id='$user_id' AND request_gender IS NULL AND content='normal' AND status='doing'")->fetch();
            if($row_notif){
                if($row_notif['content'] == 'normal')
                    $deffence = 120 - ($time - $row_notif['date']);
                if($row_notif['content'] == 'gps')
                    $deffence = 600 - ($time - $row_notif['date']);

                if($deffence == 60)
                    $output = "1 دقیقه";
                else if($deffence > 60)
                    $output = floor($deffence / 60)." دقیقه و ".($deffence % 60)." ثانیه";
                else{
                    $output = ($deffence % 60)." ثانیه";
                }
                answerCallbackQuery("⚠️ خطا: چندبار میزنی؟ دارم جستجو میکنم.\n\n".
                    "⏳ لطفا $output دیگر صبر کنید."
                );
                exit;
            }
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "🔎 درحال جستجوی مخاطب ناشناس شما\n".
                    "<code>- 🎲 جستجو شانسی 🎲</code>\n\n".
                    "⏳ حداکثر تا 2 دقیقه صبر کنید.\n\n".
                    "⚙️ جستجوی همسن : $status_same_age",
                'parse_mode' => 'HTML',
            ]);
            $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND user_id='$user_id'");
            $conn->query("INSERT INTO ".notif." (type,user_id,balance,gender,age,reason,content,status,date) VALUES ('search','$user_id','0','{$row_users['gender']}','$same_age','connectToUser','normal','doing','$time')");
            exit;
        }
        $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND (user_id='$user_id' OR user_id='{$row_notif['user_id']}')");
        $conn->query("INSERT INTO ".chats." (user_id_1,user_id_2,status,created_at) VALUES ('$user_id','{$row_notif['user_id']}','chatting','$time')");
        $conn->query("UPDATE ".users." SET num_chats=num_chats+1 WHERE user_id='$user_id'");
        $conn->query("UPDATE ".users." SET balance=balance-{$row_notif['balance']},num_chats=num_chats+1 WHERE user_id='{$row_notif['user_id']}'");
        
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        send_reply("sendMessage",[
            'chat_id' => $row_notif['user_id'],
            'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        exit;
    }
    if($ex_data[1] == 'boy' or $ex_data[1] == 'girl'){
        if($row_users['balance'] < 2){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "⚠️ توجه: شما سکه کافی ندارید !  (2 سکه مورد نیاز)\n\n".
                    "<code>💡 برای بدست آوردن سکه میتونی رباتو به دوستات معرفی کنی و به ازای معرفی هر نفر ".($row_admin['coin_per_invite']+$row_admin['coin_per_invite_profile']+$row_admin['coin_per_invite_invite'])." .</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => keboards('coin')])
            ]);
            exit;
        }
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id NOT IN ('$user_id') AND type='search' AND gender='{$ex_data[1]}' $same_age_where AND (request_gender='{$row_users['gender']}' OR reason='connectToUser') AND content='normal' AND status='doing'")->fetch();
        $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND user_id='$user_id'");
        if(!$row_notif){
            $row_notif = $conn->query("SELECT * FROM ".notif." WHERE type='search' AND user_id='$user_id' AND request_gender='{$ex_data[1]}' AND content='normal' AND status='doing'")->fetch();
            if($row_notif){
                if($row_notif['content'] == 'normal')
                    $deffence = 120 - ($time - $row_notif['date']);
                if($row_notif['content'] == 'gps')
                    $deffence = 600 - ($time - $row_notif['date']);

                if($deffence == 60)
                    $output = "1 دقیقه";
                else if($deffence > 60)
                    $output = floor($deffence / 60)." دقیقه و ".($deffence % 60)." ثانیه";
                else{
                    $output = ($deffence % 60)." ثانیه";
                }
                answerCallbackQuery("⚠️ خطا: چندبار میزنی؟ دارم جستجو میکنم.\n\n".
                    "⏳ لطفا $output دیگر صبر کنید."
                );
                exit;
            }
            $title = $ex_data[1] == 'boy'?"- جستجو پسر 🙋‍♂️":"- جستجو دختر 🙋‍♀️";
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "🔎 درحال جستجوی مخاطب ناشناس شما\n".
                    "<code>$title</code>\n\n".
                    "⏳ حداکثر تا 2 دقیقه صبر کنید.\n\n".
                    "⚙️ جستجوی همسن : $status_same_age",
                'parse_mode' => 'HTML',
            ]);
            $conn->query("INSERT INTO ".notif." (type,user_id,balance,gender,request_gender,age,content,status,date) VALUES ('search','$user_id','2','{$row_users['gender']}','{$ex_data[1]}','$same_age','normal','doing','$time')");
            exit;
        }
        $conn->query("UPDATE ".notif." SET status='end' WHERE type='search' AND (user_id='$user_id' OR user_id='{$row_notif['user_id']}')");
        $conn->query("INSERT INTO ".chats." (user_id_1,user_id_2,status,created_at) VALUES ('$user_id','{$row_notif['user_id']}','chatting','$time')");
        $conn->query("UPDATE ".users." SET balance=balance-2,num_chats=num_chats+1 WHERE user_id='$user_id'");
        $conn->query("UPDATE ".users." SET balance=balance-{$row_notif['balance']},num_chats=num_chats+1 WHERE user_id='{$row_notif['user_id']}'");
        
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        send_reply("sendMessage",[
            'chat_id' => $row_notif['user_id'],
            'text' => "👀 پیدا کردم وصلتون کرد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        exit;
    }
    exit;
}
if($text == "📍افراد نزدیک"){
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => "🛰 چه کسایی رو نشونت بدم؟ <code>انتخاب کن👇</code>",
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [
                ['text' => "فقط 🙍‍♀️ دختر ها",'callback_data' => "findgps;girl;none"],
                ['text' => "فقط 🙎‍♂️ پسر ها",'callback_data' => "findgps;boy;none"]
            ],
            [['text' => "همه رو نشون بده",'callback_data' => "findgps;all;none"]]
        ]])
    ]);
    exit;
}
if($ex_data[0] == "findgps"){

    if(!$row_users['latitude']){
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' =>
                "انتظار نداری که بدون دونستن موقعیتت بتونم افراد نزدیکتو پیدا کنم؟\n\n".
                "⚠️ خطا: شما موقعیت مکانی خود را ثبت نکرده اید.\n\n".
                "با زدن گزینه 📍 ثبت موقعیت GPS  ، موقعیت خود را ثبت کنید 👇",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "📍 ثبت موقعیت GPS",'callback_data' => "profile;gps"]]]])
        ]);
        exit;
    }
    $step_page = 20;
    $data_current = $ex_data[2];
    $num_page = $data_current == 'none' ? 1 : $ex_data[2];
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    if($ex_data[1] == "girl"){
        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender='girl' AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender='girl' AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        $switch_inline_query_current_chat = "کاربران نزدیک خانم";
    }
    if($ex_data[1] == "boy"){
        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender='boy' AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender='boy' AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        $switch_inline_query_current_chat = "کاربران نزدیک آقا";
    }
    if($ex_data[1] == "all"){
        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        $switch_inline_query_current_chat = "کاربران نزدیک";
    }
    if(!$result){
        if($data_current == 'none')
            answerCallbackQuery("⚠️ در 3 روز اخیر کاربری در اطراف شما آنلاین نبوده است.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    foreach($result as $row){
        $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        $where = (!$row_user_select['latitude'] or !$row_users['latitude'])?"":"(SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.")";
        $row_user_select = $conn->query("SELECT * FROM $where ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        
        $status_chat = (substr($row_user_select['step'],0,9) == "chatting;")?" (در حال چت)":"";
        $d = (!$row_users['latitude'] or !$row_user_select['latitude'])?"":" (🏁 ".number_format($row_user_select['distance'],1)."km)";
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $txts .= "‏$i. {$status_gender_emoji[$row_user_select['gender']]} {$row_user_select['name']} /user_{$row_user_select['uniq_id']}\n";
        $txts .= "<code>{$row_user_select['age']} {$row_user_select['state']}$city</code>$d\n";
        $txts .= "<code>".LastActivity($row_user_select['last_activity'])."$status_chat</code>\n";
        $txts .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️\n";
        $i++;
    }
    ListShow("🛰 لیست افراد نزدیک به شما\n\n$txts\n".
        "جستجو شده در ".to_english(jdate("Y-m-d H:i"))."",
        "{$ex_data[0]};{$ex_data[1]}",
        false,
        [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => $switch_inline_query_current_chat]]
    );

    exit;
}
if(isset($up['inline_query'])){
    if(mb_substr($inline_query,0,6) == 'جستجو '){
        $id_search = mb_substr($inline_query,6);
        if(!is_numeric($id_search))
            exit;
        $row_search = $conn->query("SELECT * FROM ".search." WHERE id='$id_search'")->fetch();
        if(!$row_search){
            answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد");
            exit;
        }
        $search = json_decode($row_search['search'],true);
        $sort = $whcat = $state = "";
        if($search['sort'] == 'last_activity')
            $sort = " ORDER BY last_activity DESC";
        else if($search['sort'] == 'near')
            $sort = "AND latitude NOT IN ('0') ORDER BY distance ASC";
        else if($search['sort'] == 'wchat')
            $whcat = "AND num_chats=0";
        else if($search['sort'] == 'min_age')
            $sort = " ORDER BY age ASC";
        else if($search['sort'] == 'age')
            $sort = "AND age<({$row_users['age']}+5) AND age>({$row_users['age']}-5)";
        else if($search['sort'] == 'max_age')
            $sort = " ORDER BY age DESC";
        
        $onlinetime = $search['onlinetime'] == "all"?"":"AND ($time - last_activity)<{$search['onlinetime']}";
        $gender = $search['gender'] == "all"?"":"AND gender='{$search['gender']}'";
        $location = !$search['location']?"":"AND distance<=50";
        if(count($search['state']) > 0){
            foreach($search['state'] as $value){
                $value = urldecode($value);
                $state .= "state='$value' OR ";
            }
            $state = "AND (".substr($state,0,-4).")";
        }
        $selected_pages = is_numeric($offset)?$offset:0;
        $step_page = 50;

        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort")->fetchAll();
        $results = userInfoInlineList($conn,$result);
        $selected_pages += $step_page;
        if($selected_pages >= $num)
            $selected_pages = null;
        send_reply('answerInlineQuery',[
            'inline_query_id' => $inline_query_id,
            'cache_time' => 0,
            'results' => json_encode($results),
            'next_offset' => $selected_pages
        ]);
        exit;
    }
    if(mb_substr($inline_query,-4) == "خانم"){
        $type_users = "girl";
        $inline_query_edit = mb_substr($inline_query,0,-5);
    }
    else if(mb_substr($inline_query,-3) == "آقا"){
        $type_users = "boy";
        $inline_query_edit = mb_substr($inline_query,0,-4);
    }
    else{
        $type_users = "all";
        $inline_query_edit = $inline_query;
    }
    $where = $type_users == "all"?"":"AND gender='$type_users'";
    if(in_array($inline_query_edit,$status_gender)){
        $type = array_search($inline_query_edit,$status_gender);
        if($type == 'sage'){
            $where = "AND age='{$row_users['age']}' $where AND (last_activity+259200)>$time ORDER BY last_activity DESC";
        }
        else if($type == 'sstate'){
            $where = "AND state='{$row_users['state']}' $where AND (last_activity+259200)>$time ORDER BY last_activity DESC";
        }
        else if($type == 'wchat'){
            $where = "AND num_chats=0 $where ORDER BY last_activity DESC";
        }
        else if($type == 'nuser'){
            $where = "AND (last_activity+60)>$time $where ORDER BY created_at DESC";
        }
        
        $selected_pages = is_numeric($offset)?$offset:0;
        $step_page = 50;
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') $where LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT id FROM ".users." WHERE user_id NOT IN ('$user_id') $where")->rowCount();
        $results = userInfoInlineList($conn,$result);
        $selected_pages += $step_page;
        if($selected_pages >= $num)
            $selected_pages = null;
        send_reply('answerInlineQuery',[
            'inline_query_id' => $inline_query_id,
            'cache_time' => 0,
            'results' => json_encode($results),
            'next_offset' => $selected_pages
        ]);
        exit;
    }
    if($inline_query_edit == 'کاربران نزدیک'){
        $selected_pages = is_numeric($offset)?$offset:0;
        $step_page = 50;
        if($type_users == "girl"){
            $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND gender='girl' AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND gender='girl' AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        }
        if($type_users == "boy"){
            $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND gender='boy' AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND gender='boy' AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        }
        if($type_users == "all"){
            $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND distance<20 AND (last_activity+259200)>$time ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            $num = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id NOT IN ('$user_id') AND distance<20 AND (last_activity+259200)>$time")->rowCount();
        }
        if(!$result)
            exit;
        $results = userInfoInlineList($conn,$result);
        $selected_pages += $step_page;
        if($selected_pages >= $num)
            $selected_pages = null;
        send_reply('answerInlineQuery',[
            'inline_query_id' => $inline_query_id,
            'cache_time' => 0,
            'results' => json_encode($results),
            'next_offset' => $selected_pages
        ]);
        exit;
    }
    if($inline_query == 'لیست لایک کننده های پروفایل من'){
        $selected_pages = is_numeric($offset)?$offset:0;
        $step_page = 50;
        $i = $selected_pages + 1;
        $result = $conn->query("SELECT * FROM ".likes." WHERE target_id='$user_id' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
        $num = $conn->query("SELECT id FROM ".likes." WHERE target_id='$user_id'")->rowCount();
        $results = $results = userInfoInlineList($conn,$result);
        $selected_pages += $step_page;
        if($selected_pages >= $num)
            $selected_pages = null;
        send_reply('answerInlineQuery',[
            'inline_query_id' => $inline_query_id,
            'cache_time' => 0,
            'results' => json_encode($results),
            'next_offset' => $selected_pages
        ]);
        exit;
    }
    exit;
}
if($text == "🔍 جستجوی کاربران 🔎"){
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => "چه کسایی رو نشونت بدم؟ <code>انتخاب کن👇</code>",
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => keboards('search')])
    ]);
    exit;
}
if($ex_data[0] == "search"){

    /*$debug= json_encode($ex_data,JSON_UNESCAPED_UNICODE ) ;
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => $debug,
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $message_id,
    ]);*/
    if($ex_data[2] == "none"){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "چه کسایی رو از بین {$status_gender_search[$ex_data[1]]} نشونت بدم؟ <code>انتخاب کن👇</code>",
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [
                [
                    ['text' => 'فقط 🙍‍♀️ دختر ها','callback_data' => "search;{$ex_data[1]};girl;none"],
                    ['text' => 'فقط 🙍‍♂️ پسر ها','callback_data' => "search;{$ex_data[1]};boy;none"]
                ],
                [['text' => 'همه رو نشون بده','callback_data' => "search;{$ex_data[1]};all;none"]],
            ]])
        ]);
        exit;
    }
    if($ex_data[2] == "boy"){
        $switch_inline_query_current_chat = "{$status_gender[$ex_data[1]]} آقا";
    }
    else if($ex_data[2] == "girl"){
        $switch_inline_query_current_chat = "{$status_gender[$ex_data[1]]} خانم";
    }
    else {
        $switch_inline_query_current_chat = $status_gender[$ex_data[1]];
    }
    $where = $ex_data[2] == "all" ? "" : "AND gender='{$ex_data[2]}'";
    $where .= " AND state IS NOT NULL";


    $step_page = 10; // تعداد در یک صفحه
    $data_current = $ex_data[3];
    $num_page = $data_current == 'none' ? 1 : $ex_data[3];
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    if($ex_data[1] == "sage"){
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND age='{$row_users['age']}' $where AND (last_activity+259200)>$time ORDER BY last_activity DESC LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            if($data_current == 'none')
                answerCallbackQuery("⚠️ کاربری یافت نشد.");
            else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
            exit;
        }
        $num = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND age='{$row_users['age']}' $where AND (last_activity+259200)>$time")->rowCount();
    }
    else if($ex_data[1] == "sstate"){
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND state='{$row_users['state']}' $where AND (last_activity+259200)>$time ORDER BY last_activity DESC LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            if($data_current == 'none')
                answerCallbackQuery("⚠️ کاربری یافت نشد.");
            else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
            exit;
        }
        $num = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND state='{$row_users['state']}' $where AND (last_activity+259200)>$time")->rowCount();
    }
    else if($ex_data[1] == "wchat"){
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND num_chats=0 $where ORDER BY last_activity DESC LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            if($data_current == 'none')
                answerCallbackQuery("⚠️ کاربری یافت نشد.");
            else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
            exit;
        }
        $num = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND num_chats=0 $where")->rowCount();
    }
    else if($ex_data[1] == "nuser"){
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') $where AND (last_activity+60)>$time ORDER BY created_at DESC LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            if($data_current == 'none')
                answerCallbackQuery("⚠️ کاربری یافت نشد.");
            else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
            exit;
        }
        $num = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') $where AND (last_activity+60)>$time")->rowCount();
    }

    $txts = userInfoList($conn,$result); // دریافت لیست کاربران
    // فانکشن ارسال پیام لیست کاربرها
    // دکمه های ادامه لیست و مشاهده کشویی
    ListShow("{$title_gender_search[$ex_data[1]]}\n\n$txts\n".
        "جستجو شده در ".to_english(jdate("Y-m-d H:i"))."",
        "{$ex_data[0]};{$ex_data[1]};{$ex_data[2]}",
        false,
        [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => $switch_inline_query_current_chat]]
    );
    exit;
}
if($ex_data[0] == "searchadv"){
    if($ex_data[1] == "none"){
        if($ex_data[1] == "none"){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "🔍 جستجوی پیشرفته🔎\n\n".
                    "🎌 چه کسایی رو نشونت بدم؟  <code>جنسیت رو انتخاب کن👇</code>",
                'parse_mode' => 'HTML',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [
                        ['text' => 'فقط 🙍‍♀️ دختر ها','callback_data' => "searchadv;no;gender;girl;none"],
                        ['text' => 'فقط 🙍‍♂️ پسر ها','callback_data' => "searchadv;no;gender;boy;none"]
                    ],
                    [['text' => 'همه رو نشون بده','callback_data' => "searchadv;no;gender;all;none"]],
                ]])
            ]);
            exit;
        }
        if($ex_data[1] == "boy")
            $switch_inline_query_current_chat = "{$status_gender[$ex_data[1]]} آقا";
        else if($ex_data[1] == "girl")
            $switch_inline_query_current_chat = "{$status_gender[$ex_data[1]]} خانم";
        else if($ex_data[1] == "all")
            $switch_inline_query_current_chat = $status_gender[$ex_data[1]];
        else exit;
        $where = $ex_data[1] == "all"?"":"AND gender='{$ex_data[1]}'";
        
        $step_page = 10;
        $data_current = $ex_data[2];
        $num_page = $data_current == 'none'?1:$data_current;
        $selected_pages = ($num_page - 1) * $step_page;
        $i = $selected_pages + 1;
        
        $result = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND age='{$row_users['age']}' $where AND (last_activity+259200)>$time ORDER BY last_activity DESC LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            if($data_current == 'none')
                answerCallbackQuery("⚠️ کاربری یافت نشد.");
            else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
            exit;
        }
        $num = $conn->query("SELECT * FROM ".users." WHERE user_id NOT IN ('$user_id') AND age='{$row_users['age']}' $where AND (last_activity+259200)>$time")->rowCount();
        $txts = userInfoList($conn,$result);
        ListShow("{$title_gender_search[$ex_data[1]]}\n\n$txts\n".
            "جستجو شده در ".to_english(jdate("Y-m-d H:i"))."",
            "$ex_data[0];$ex_data[1]",
            false,
            [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => $switch_inline_query_current_chat]]
        );
        exit;
    }
    if($ex_data[2] == "gender"){
        $search = ['gender' => $ex_data[3],'onlinetime' => 0,'age' => ['start' => 0,'end' => 0],'state' => [],'location' => 0,'newest' => 0,'noChat' => 0];
        $search['gender'] == $ex_data[3];
        $conn->query("INSERT INTO ".search." (user_id,search,created_at) VALUES ('$user_id','".json_encode($search)."','$time')");
        $id_search = $conn->query("SELECT * FROM ".search." WHERE user_id='$user_id' ORDER BY id DESC")->fetch()['id'];

        $reply_markup = [
            [['text' => "➡️ مرحله بعدی",'callback_data' => "searchadv;$id_search;age;none"]],
            [
                ['text' => "✅ انتخاب همه",'callback_data' => "searchadv;$id_search;state;all"],
                ['text' => "📍افراد نزدیک من",'callback_data' => "searchadv;$id_search;state;location"]
            ]
        ];
        $result = $conn->query("SELECT * FROM ".states." WHERE parent=0 ORDER BY id ASC")->fetchAll();
        $i = 2;
        $j = 1;
        foreach($result as $key => $row){
            if(in_array(urlencode($row['state']),$search['state']))
                $reply_markup[$i][] = ['text' => "{$row['state']}✔️",'callback_data' => "searchadv;$id_search;state;{$row['id']}"];
            else
                $reply_markup[$i][] = ['text' => $row['state'],'callback_data' => "searchadv;$id_search;state;{$row['id']}"];
            if($j % 3 == 0)
                $i++;
            $j++;
        }
        $reply_markup[] = [['text' => "➡️ مرحله بعدی",'callback_data' => "searchadv;$id_search;age;none"]];
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : []\n\n".
                "<code>استان های مورد نظرتو انتخاب کن و در آخر گزینه «➡️ مرحله بعدی » رو بزن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    $id_search = $ex_data[1];
    $row_search = $conn->query("SELECT * FROM ".search." WHERE id='$id_search'")->fetch();
    if(!$row_search){
        answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد");
        exit;
    }
    $search = json_decode($row_search['search'],true);
    if($ex_data[2] == "state"){
        if($ex_data[3] == 'location')
            $search['location'] = 1 - $search['location'];
        else{
            if($ex_data[3] == 'all'){
                $array = [];
                if(count($search['state']) == 31){
                    $search['state'] = $array;
                    $search['location'] = 0;
                }
                else{
                    $result = $conn->query("SELECT * FROM ".states." WHERE parent=0 ORDER BY id ASC")->fetchAll();
                    foreach($result as $row){
                        $array[] = urlencode($row['state']);
                    }
                    $search['state'] = $array;
                    $search['location'] = 1;
                }
            }
            else{
                $state = urlencode($conn->query("SELECT * FROM ".states." WHERE parent=0 AND id='{$ex_data[3]}'")->fetch()['state']);
                if(in_array($state,$search['state']))
                    unset($search['state'][array_search($state,$search['state'])]);
                else $search['state'][] = $state;
            }
        }
        $conn->query("UPDATE ".search." SET search='".json_encode($search)."' WHERE id='$id_search'");
        
        $location = $search['location']?"📍افراد نزدیک من✔️":"📍افراد نزدیک من";
        $reply_markup = [
            [['text' => "➡️ مرحله بعدی",'callback_data' => "searchadv;$id_search;age;none"]],
            [
                ['text' => "✅ انتخاب همه",'callback_data' => "searchadv;$id_search;state;all"],
                ['text' => $location,'callback_data' => "searchadv;$id_search;state;location"]
            ]
        ];
        $result = $conn->query("SELECT * FROM ".states." WHERE parent=0 ORDER BY id ASC")->fetchAll();
        $i = 2;
        $j = 1;
        foreach($result as $key => $row){
            if(in_array(urlencode($row['state']),$search['state']))
                $reply_markup[$i][] = ['text' => "{$row['state']}✔️",'callback_data' => "searchadv;$id_search;state;{$row['id']}"];
            else
                $reply_markup[$i][] = ['text' => $row['state'],'callback_data' => "searchadv;$id_search;state;{$row['id']}"];
            if($j % 3 == 0)
                $i++;
            $j++;
        }
        $reply_markup[] = [['text' => "➡️ مرحله بعدی",'callback_data' => "searchadv;$id_search;age;none"]];
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n\n".
                "<code>استان های مورد نظرتو انتخاب کن و در آخر گزینه «➡️ مرحله بعدی » رو بزن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    if($ex_data[2] == "age"){
        if(count($search['state']) == 0 AND !$search['location'])
            exit;
        $reply_markup = [];
        $i = 0;
        $j = 0;
        for($num = 9;$num <= 99;$num++){
            if($j %7 == 0 && $j > 0)
                $i++;
            $reply_markup[$i][] = ['text' => $num,'callback_data' => "searchadv;$id_search;end;$num"];
            $j++;
        }
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n\n".
                "👥 بازه سنی : [❓ - ❓]\n\n".
                "<code>حداقل سن بازه رو انتخاب کن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    if($ex_data[2] == "end"){
        $search['age']['start'] = $ex_data[3];
        $conn->query("UPDATE ".search." SET search='".json_encode($search)."' WHERE id='$id_search'");
        $reply_markup = [];
        $i = 0;
        $j = 0;
        for($num = 9;$num <= 99;$num++){
            if($j %7 == 0 && $j > 0)
                $i++;
            $reply_markup[$i][] = ['text' => $num,'callback_data' => "searchadv;$id_search;onlinetime;$num"];
            $j++;
        }
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n\n".
                "👥 بازه سنی : [{$search['age']['start']} - ❓]\n\n".
                "<code>حداکثر سن بازه رو انتخاب کن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    if($ex_data[2] == "onlinetime"){
        $search['age']['end'] = $ex_data[3];
        $conn->query("UPDATE ".search." SET search='".json_encode($search)."' WHERE id='$id_search'");
        $reply_markup = [
            [
                ['text' => "تا 6 ساعت قبل",'callback_data' => "searchadv;$id_search;sort;21600"],
                ['text' => "تا یک ساعت قبل",'callback_data' => "searchadv;$id_search;sort;3600"]
            ],
            [
                ['text' => "تا دو روز قبل",'callback_data' => "searchadv;$id_search;sort;172800"],
                ['text' => "تا یک روز قبل",'callback_data' => "searchadv;$id_search;sort;86400"]
            ],
            [
                ['text' => "تا یک هفته قبل",'callback_data' => "searchadv;$id_search;sort;604800"],
                ['text' => "تا سه روز قبل",'callback_data' => "searchadv;$id_search;sort;259200"]
            ],
            [['text' => "همه",'callback_data' => "searchadv;$id_search;sort;all"]]
        ];
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n\n".
                "👥 بازه سنی : [{$search['age']['start']} - {$search['age']['end']}]\n\n".
                "👀 آخرین حضور : []\n\n".
                "<code>آخرین زمان حضور کاربر رو انتخاب کن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    if($ex_data[2] == "sort"){
        $search['onlinetime'] = $ex_data[3];
        $conn->query("UPDATE ".search." SET search='".json_encode($search)."' WHERE id='$id_search'");
        $reply_markup = [
            [
                ['text' => "تاریخ آنلاین",'callback_data' => "searchadv;$id_search;show;last_activity"],
                ['text' => "فاصله نزدیک",'callback_data' => "searchadv;$id_search;show;near"]
            ],
            [
                ['text' => "کمترین سن",'callback_data' => "searchadv;$id_search;show;min_age"],
                ['text' => "سن نزدیک",'callback_data' => "searchadv;$id_search;show;age"],
                ['text' => "بیشترین سن",'callback_data' => "searchadv;$id_search;show;max_age"]
            ],
            [['text' => "فقط نمایش بدون چت های آنلاین",'callback_data' => "searchadv;$id_search;show;wchat"]]
        ];
        send_reply("editMessageText",[
            'chat_id' => $user_id,
            'text' => "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n\n".
                "👥 بازه سنی : [{$search['age']['start']} - {$search['age']['end']}]\n\n".
                "👀 آخرین حضور : [{$txts_array['onlinetime'][$search['onlinetime']]}]\n\n".
                "<code>اولویت ترتیب نمایش لیست کاربران رو انتخاب کن 👇</code>",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
        ]);
        exit;
    }
    if($ex_data[2] == "show"){
        $search['sort'] = $ex_data[3];
        $conn->query("UPDATE ".search." SET search='".json_encode($search)."' WHERE id='$id_search'");

        $sort = $whcat = $state = "";
        if($search['sort'] == 'last_activity')
            $sort = " ORDER BY last_activity DESC";
        else if($search['sort'] == 'near')
            $sort = "AND latitude NOT IN ('0') ORDER BY distance ASC";
        else if($search['sort'] == 'wchat')
            $whcat = "AND num_chats=0";
        else if($search['sort'] == 'min_age')
            $sort = " ORDER BY age ASC";
        else if($search['sort'] == 'age')
            $sort = "AND age<({$row_users['age']}+5) AND age>({$row_users['age']}-5)";
        else if($search['sort'] == 'max_age')
            $sort = " ORDER BY age DESC";
        
        $onlinetime = $search['onlinetime'] == "all"?"":"AND ($time - last_activity)<{$search['onlinetime']}";
        $gender = $search['gender'] == "all"?"":"AND gender='{$search['gender']}'";
        $location = !$search['location']?"":"AND distance<=50";
        if(count($search['state']) > 0){
            foreach($search['state'] as $value){
                $value = urldecode($value);
                $state .= "state='$value' OR ";
            }
            $state = "AND (".substr($state,0,-4).")";
        }
        
        $data_current = 'none';
        $num_page = $data_current == 'none'?1:$data_current;
        $selected_pages = ($num_page - 1) * $step_page;
        $i = $selected_pages + 1;
        $next_page = $num_page + 1;
        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            answerCallbackQuery("⚠️ کاربری با تنظیمات مربوطه یافت نشد.");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        $num = $query = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort")->rowCount();
        $txts = userInfoList($conn,$result);
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        $info_msg = send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "♻️ در حال جستجو ی پیشرفته\n\n".
                "👫 جنسیت : [{$type_status_gender[$search['gender']]}]\n".
                "🎌 استان های انتخاب شده  : [".states_select_adv($search['state'],$search['location'])."]\n".
                "👥 بازه سنی : [{$search['age']['start']} - {$search['age']['end']}]\n".
                "👀 آخرین حضور : [{$txts_array['onlinetime'][$search['onlinetime']]}]\n".
                "🔻 ترتیب نمایش : [{$txts_array['sort'][$search['sort']]}]\n\n".
                "👇👇👇👇👇",
            'message_id' => $message_id,
            'parse_mode' => 'HTML',
        ]);
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "نتایج 🔍 جستجوی پیشرفته🔎\n\n$txts\n".
                "جستجو شده در ".to_english(jdate("Y-m-d H:i"))."",
            'reply_to_message_id' => $info_msg['result']['message_id'],
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [
                [['text' => "➡️ مشاهده ادامه لیست", 'callback_data' => "{$ex_data[0]};{$ex_data[1]};result;$next_page"]],
                [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => "جستجو $id_search"]]
            ]])
        ]);
        exit;
    }
    if($ex_data[2] == "result"){
        $sort = $whcat = $state = "";
        if($search['sort'] == 'last_activity')
            $sort = " ORDER BY last_activity DESC";
        else if($search['sort'] == 'near')
            $sort = "AND latitude NOT IN ('0') ORDER BY distance ASC";
        else if($search['sort'] == 'wchat')
            $whcat = "AND num_chats=0";
        else if($search['sort'] == 'min_age')
            $sort = " ORDER BY age ASC";
        else if($search['sort'] == 'age')
            $sort = "AND age<({$row_users['age']}+5) AND age>({$row_users['age']}-5)";
        else if($search['sort'] == 'max_age')
            $sort = " ORDER BY age DESC";
        
        $onlinetime = $search['onlinetime'] == "all"?"":"AND ($time - last_activity)<{$search['onlinetime']}";
        $gender = $search['gender'] == "all"?"":"AND gender='{$search['gender']}'";
        $location = !$search['location']?"":"AND distance<=50";
        if(count($search['state']) > 0){
            foreach($search['state'] as $value){
                $value = urldecode($value);
                $state .= "state='$value' OR ";
            }
            $state = "AND (".substr($state,0,-4).")";
        }
        
        $data_current = $ex_data[3];
        $num_page = $data_current == 'none'?1:$data_current;
        $selected_pages = ($num_page - 1) * $step_page;
        $i = $selected_pages + 1;

        $result = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort LIMIT $selected_pages,$step_page")->fetchAll();
        if(!$result){
            answerCallbackQuery("⚠️ کاربری با تنظیمات مربوطه یافت نشد.");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        $num = $query = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(({$row_users['latitude']} * pi() / 180)) *sin(( latitude * pi() / 180)) + cos(({$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((({$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
        ".users." WHERE user_id NOT IN ('$user_id') AND gender IS NOT NULL AND (age>={$search['age']['start']} OR age<={$search['age']['end']}) $gender $whcat $state $onlinetime $location $sort")->rowCount();
        $txts = userInfoList($conn,$result);
        ListShow("نتایج 🔍 جستجوی پیشرفته🔎\n\n$txts\n".
            "جستجو شده در ".to_english(jdate("Y-m-d H:i"))."",
            "{$ex_data[0]};$id_search;result",
            false,
            [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => "جستجو $id_search"]]
        );
        exit;
    }
    exit;
}

?>