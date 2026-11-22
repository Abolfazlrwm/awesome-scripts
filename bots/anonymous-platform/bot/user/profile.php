<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if($text == "/id"){
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' => "🆔 ایدی شما در ربات: /user_{$row_users['uniq_id']}",
    ]);
    exit;
}
if($text == "👤پروفایل" or $text == "/profile"){
    $row_users['image'] == null?$file_id = new CURLFile("../files/noimage-{$row_users['gender']}.jpg"):$file_id = $row_users['image'];
    $count = $GLOBALS['conn']->query("SELECT id FROM ".likes." WHERE target_id='{$row_users['user_id']}'")->rowCount();
    $array = [
        'chat_id' => $user_id,
        'photo' => $file_id,
        'caption' =>
            "• نام: ".checkInout($row_users['name'])."\n".
            "• جنسیت: {$txts_array['type_text_user']['with_emoji'][$row_users['gender']]}\n".
            "• استان: {$row_users['state']}\n".
            "• شهر: ".checkInout($row_users['city'])."\n".
            "• سن: {$row_users['age']}\n\n".
            "♥️ لایک ها : $count\n\n".
            "".LastActivity($row_users['last_activity'])."$status_chat\n\n".
            "‏🆔 آیدی : /user_{$row_users['uniq_id']}\n‏",
        'reply_to_message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => '📍مشاهده موقعیت GPS ثبت شده من','callback_data' => 'profile;gps_show']],
            [
                ['text' => '🙍‍♂️🙎‍♀️ مخاطبین','callback_data' => 'contacts;none'],
                ['text' => '❤️ لایک های من','callback_data' => 'likes;none']
            ],
            [
                ['text' => '🚫 بلاک شده ها','callback_data' => 'blockeds;none'],
                ['text' => '🔕 سایلنت','callback_data' => 'profile;silent;none']
            ],
            [['text' => '📝 ویرایش اطلاعات پروفایل','callback_data' => 'profile;edit']]
        ]])
    ];
    if(!send_reply("sendPhoto", $array)['ok']){
        $array['photo'] = new CURLFile("../files/noimage-{$row_users['gender']}.jpg");
        send_reply("sendPhoto", $array);
    }
    $output = completeProfile($row_users);
    if($output){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "🔔 فقط {$output['count']} قدم تا تکمیل پروفایل !\n\n".
                "اطلاعات تکمیل نشده ی شما :  {$output['info']}\n\n".
                "<code>پروفایل خود را تکمیل کنید👇 و 5 سکه 💰 دریافت کنید.</code>",
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => $output['inline_keyboard']])
        ]);
        exit;
    }
    exit;
}
if($ex_data[0] == 'likes'){
    if($ex_data[1] == 'unactive'){
        $conn->query("UPDATE ".users." SET is_likes=0 WHERE user_id='$user_id'");
        answerCallbackQuery("📴 بخش لایک پروفایل شما غیرفعال شد.\n\n".
            "دیگر برای کاربران قابل مشاهده نیست!"
        );
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        exit;
    }
    if($ex_data[1] == 'active'){
        $conn->query("UPDATE ".users." SET is_likes=1 WHERE user_id='$user_id'");
        answerCallbackQuery("✅ بخش لایک پروفایل شما فعال شد.");
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        exit;
    }
    if(!$row_users['is_likes']){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "📴 بخش لایک پروفایل شما غیر فعال است!\n\n".
                "<code>    - برای کاربران قابل مشاهده نیست.</code>\n\n\n".
                "برای فعال سازی گزینه (✅ فعال سازی) را لمس کنید 👇",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "✅ فعال سازی",'callback_data' => "likes;active"]]]])
        ]);
        exit;
    }
    $data_current = $ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT * FROM ".likes." WHERE target_id='$user_id' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            answerCallbackQuery("⚠️ توجه: تاکنون کاربری پروفایل شما را لایک نکرده است.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT id FROM ".likes." WHERE target_id='$user_id'")->rowCount();
    $txts = userInfoList($conn,$result);
    ListShow("👥 لیست کاربرانی که پروفایل شما را لایکـ❤️ کرده اند در زیر آمده است.\n\n$txts\n\n".
        "➖ برای حذف دکمه لایک از پروفایلتان میتوانید این بخش را را کلیک روی دکمه غیر فعال سازی بخش لایک غیر فعال کنید. 👇",
        'likes',
        [['text' => "📴 غیر فعال سازی بخش  لایکـ❤️", 'callback_data' => "likes;unactive"]],
        [['text' => '⚡️ مشاهده بصورت کشویی','switch_inline_query_current_chat' => "لیست لایک کننده های پروفایل من"]]
    );
    exit;
}
if($ex_data[0] == 'contacts'){
    $data_current = $ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT * FROM ".friends." WHERE user_id='$user_id' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            answerCallbackQuery("⚠️ توجه: لیست مخاطبین شما خالی میباشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT * FROM ".friends." WHERE user_id='$user_id'")->rowCount();
    foreach($result as $row){
        $row_user_select = $conn->query("SELECT * FROM (SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.") 
            ".users." WHERE user_id='{$row['target_id']}'")->fetch();
        
        $status_chat = (substr($row_user_select['step'],0,9) == "chatting;")?" (در حال چت)":"";
        $d = ($row_user_select['latitude'] and $row_users['latitude'])?" (🏁 ".number_format($row_user_select['distance'],1)."km)":"";
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $row_user_select['name'] = $row_user_select['name'] == null?"❓":$row_user_select['name'];
        
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $txts .= "‏$i. {$status_gender_emoji[$row_user_select['gender']]} {$row_user_select['name']} ({$row['name']}) /user_{$row_user_select['uniq_id']}\n";
        $txts .= "<code>{$row_user_select['age']} {$row_user_select['state']}$city</code>$d\n";
        $txts .= "<code>".LastActivity($row_user_select['last_activity'])."$status_chat</code>\n";
        $txts .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️\n";
        $i++;
    }
    ListShow("🙎‍♂️🙎‍♀️ لیست مخاطبین شما\n\n$txts\n".
        "➖ برای حذف کاربر روی پروفایل کاربر گزینه «حذف از مخاطبین» را بزنید.\n\n".
        "🗑 حذف همه مخاطبین : /deleteAllContacts",
        'contacts'
    );
    exit;
}
if($ex_data[0] == 'blockeds'){
    $data_current = $ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT * FROM ".blocked." WHERE user_id='$user_id' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            answerCallbackQuery("⚠️ توجه: لیست کاربران مسدود شده شما خالی میباشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT * FROM ".blocked." WHERE user_id='$user_id'")->rowCount();
    foreach($result as $row){
        $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['target_id']}'")->fetch();
        $where = (!$row_user_select['latitude'] or !$row_users['latitude'])?"":"(SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.")";
        $row_user_select = $conn->query("SELECT * FROM $where ".users." WHERE user_id='{$row['target_id']}'")->fetch();
        
        $status_chat = (substr($row_user_select['step'],0,9) == "chatting;")?" (در حال چت)":"";
        $d = $row_users['latitude']?" (🏁 ".number_format($row_user_select['distance'],1)."km)":"";
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $row_user_select['name'] = $row_user_select['name'] == null?"❓":$row_user_select['name'];
        
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $txts .= "‏$i. {$status_gender_emoji[$row_user_select['gender']]} {$row_user_select['name']} /user_{$row_user_select['uniq_id']}\n";
        $txts .= "<code>{$row_user_select['age']} {$row_user_select['state']}$city</code>$d\n";
        $txts .= "<code>".LastActivity($row_user_select['last_activity'])."$status_chat</code>\n";
        $txts .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️\n";
        $i++;
    }
    ListShow("👥 لیست کاربران بلاک شده\n\n$txts\n".
        "➖ برای حذف کاربر روی پروفایل کاربر گزینه «آنبلاک کردن کاربر» را بزنید.\n\n".
        "🗑 حذف همه بلاک شده ها : /deleteAllBlocks",
        'blockeds'
    );
    exit;
}
if($ex_data[0] == 'profile' and isset($ex_data[2]) and $ex_data[2] == 'complete'){
    $conn->query("UPDATE ".users." SET step='start',prev_step='complete_profile' WHERE user_id='$user_id'");
}
if($ex_step[0] == "profile" and $data != "start"){
    if($ex_step[1] == "edit"){
        if($ex_step[2] == "set_name"){
            if(mb_strlen($text) > 100 || !preg_match("/^[ آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهیئ\s]+$/", $text)){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا: لطفا فقط از حروف فارسی استفاده کنید.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر نام','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET name='$text',step='start' WHERE user_id='$user_id'");
            if($row_users['prev_step'] != 'complete_profile'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "✏️ تغییر نام با موفقیت انجام شد ✅\n\n".
                        mainMenu(false,true),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
                ]);
                checkProfileCoin($user_id);
                exit;
            }
            checkProfileCoin($user_id);
        }
        else if($ex_step[2] == "set_age"){
            $text = to_english($text);
            if(!is_numeric($text) || $text < 9 || $text > 99){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا : ورودی باید بصورت اعداد باشد.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر سن','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET age=$text,step='start' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "✏️ تغییر سن با موفقیت انجام شد ✅\n\n".
                    mainMenu(false,true),
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            checkProfileCoin($user_id);
            exit;
        }
        else if($ex_step[2] == "set_gender"){
            if($ex_data[1] != 'boy' and $ex_data[1] != 'girl'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا: فقط یکی از گزینه های زیر را انتخاب کنید 👇',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [['text' => 'من🙍‍♂️پسرم','callback_data' => 'set_gender;boy'],['text' => 'من🙎‍♀️دخترم','callback_data' => 'set_gender;girl']],
                        [['text' => 'بیخیال ✏️ تغییر جنسیت','callback_data' => 'start']]
                    ]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET gender='{$ex_data[1]}',step='start' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>  "✏️ تغییر جنسیت با موفقیت انجام شد ✅\n\n".
                    mainMenu(false,true),
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            checkProfileCoin($user_id);
            exit;
        }
        else if($ex_step[2] == "set_state"){
            if(!$conn->query("SELECT id FROM ".states." WHERE parent=0 AND state='$text'")->fetch()){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا : ورودی باید بصورت اعداد باشد.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر استان','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET state='$text',step='start' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "✏️ تغییر استان با موفقیت انجام شد ✅\n\n".
                    mainMenu(false,true),
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            checkProfileCoin($user_id);
            exit;
        }
        else if($ex_step[2] == "set_city"){
            $parent_id = $conn->query("SELECT * FROM ".states." WHERE parent=0 AND state='{$row_users['state']}'")->fetch()['id'];
            if(!$conn->query("SELECT id FROM ".states." WHERE state='$text' AND parent='$parent_id'")->fetch()){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا : ورودی باید بصورت اعداد باشد.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر شهر','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET city='$text',step='start' WHERE user_id='$user_id'");
            if($row_users['prev_step'] != 'complete_profile'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "✏️ تغییر شهر با موفقیت انجام شد ✅\n\n".
                        mainMenu(false,true),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
                ]);
                checkProfileCoin($user_id);
                exit;
            }
            checkProfileCoin($user_id);
        }
        else if($ex_step[2] == "set_location"){
            if(!isset($up['message']['location'])){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا : ورودی باید بصورت لوکیشن GPS باشد.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر موقعیت','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET latitude='{$up['message']['location']['latitude']}',longitude='{$up['message']['location']['longitude']}',step='start' WHERE user_id='$user_id'");
            if($row_users['prev_step'] != 'complete_profile'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "✏️ تغییر موقعیت با موفقیت انجام شد ✅\n\n".
                        mainMenu(false,true),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
                ]);
                checkProfileCoin($user_id);
                exit;
            }
            checkProfileCoin($user_id);
        }
        else if($ex_step[2] == "set_image"){
            if(!isset($up["message"]["photo"])){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => '⚠️ خطا: ورودی باید بصورت عکس باشد.',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال ✏️ تغییر عکس','callback_data' => 'start']]]])
                ]);
                exit;
            }
            $infoMsg = send_reply('sendPhoto',['chat_id' => $row_admin['ch_cache_id'],'photo' => $up["message"]["photo"][count($up["message"]["photo"]) - 1]['file_id']]);
            $file_id = $infoMsg["result"]["photo"][count($infoMsg['result']['photo'])-1]["file_id"];
            /*if(!FileDownload($file_id,"../images/{$row_users['uniq_id']}.jpg")){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "❌ مشکلی پیش آمد، لطفا دوباره ارسال کنید",
                    'reply_to_message_id' => $message_id,
                ]);
                exit;
            }*/
            $conn->query("UPDATE ".users." SET image='$file_id',step='start' WHERE user_id='$user_id'");
            if($row_users['prev_step'] != 'complete_profile'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "✏️ تغییر عکس با موفقیت انجام شد ✅\n\n".
                        mainMenu(false,true),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
                ]);
                checkProfileCoin($user_id);
                exit;
            }
            checkProfileCoin($user_id);
        }
    }
    if($row_users['prev_step'] == 'complete_profile')
        $row_users = $conn->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
}
if(($ex_data[0] == 'profile' and $ex_data[1] == 'silent') or $text == '/silent'){
    if($ex_data[2] == 'none' or $text == '/silent'){
        $type = "sendMessage";
    }
    else{
        $type = "editMessageText";
        $row_users['silent'] = $ex_data[2] == '0'?0:$time + $ex_data[2];
        $conn->query("UPDATE ".users." SET silent='{$row_users['silent']}' WHERE user_id='$user_id'");
    }
    $reply_markup = [
        [
            ['text' => "🔕 سایلنت تا یک ساعت",'callback_data' => "profile;silent;3600"],
            ['text' => "🔕 سایلنت تا 20 دقیقه",'callback_data' => "profile;silent;1200"],
        ],
        [['text' => "🔕 همیشه سایلنت",'callback_data' => "profile;silent;126144000"]],
    ];
    if(!$row_users['silent']){
        $silent = "🔔 غیر فعال";
    }
    else{
        $silent = "🔕 فعال تا ".to_english(jdate("Y-m-d H:i",$row_users['silent']))."";
        $reply_markup[] = [['text' => "🔔 غیر فعال کردن سایلنت",'callback_data' => "profile;silent;0"]];
    }
    send_reply($type,[
        'chat_id' => $user_id,
        'text' =>
            "🔻 حالت سایلنت : $silent\n\n".
            "_____________________\n".
            "<code> 💡با فعال شدن حالت سایلنت ، درخواست چت دریافت نخواهید کرد.</code>",
        'message_id' => $message_id,
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode(['inline_keyboard' => $reply_markup])
    ]);
    exit;
}
if($ex_data[0] == 'profile' or $row_users['prev_step'] == 'complete_profile'){
    if($ex_data[1] == 'gps_show'){
        if(!$row_users['latitude']){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "⚠️ خطا : شما موقعیت مکانی خود را ثبت نکرده اید.\n\n".
                    "با زدن گزینه 📍 ثبت موقعیت GPS  ، موقعیت خود را ثبت کرده و 5 سکه 💰 دریافت کنید.👇",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "📍 ثبت مجدد موقعیت GPS",'callback_data' => "profile;gps"]]]])
            ]);
        }
        else{
            send_reply("sendlocation",[
                'chat_id' => $user_id,
                'latitude' => $row_users['latitude'],
                'longitude' => $row_users['longitude'],
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "📍 ثبت مجدد موقعیت GPS",'callback_data' => "profile;gps"]]]])
            ]);
        }
        exit;
    }
    if($ex_data[1] == 'edit'){
        send_reply("editMessageReplyMarkup",[
            'chat_id' => $user_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [
                [
                    ['text' => '✏️ تغییر جنسیت','callback_data' => 'profile;gender'],
                    ['text' => '✏️ تغییر نام','callback_data' => 'profile;name']
                ],
                [
                    ['text' => '✏️ تغییر شهر','callback_data' => 'profile;city'],
                    ['text' => '✏️ تغییر سن','callback_data' => 'profile;age']
                ],
                [
                    ['text' => '✏️ تغییر استان','callback_data' => 'profile;state'],
                    ['text' => '✏️ تغییر عکس','callback_data' => 'profile;image']
                ],
                [['text' => '✏️ تغییر موقعیت GPS','callback_data' => 'profile;gps']]
            ]])
        ]);
        exit;
    }
    if($ex_data[1] == "name" or ($row_users['prev_step'] == 'complete_profile' and $row_users['name'] == null)){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "❓ لطفا نام خود را به صورت متن ارسال کنید.",
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_name' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == "gender"){
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "❓ لطفا جنسیت خود را انتخاب کنید 👇",
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'من🙍‍♂️پسرم','callback_data' => 'set_gender;boy'],['text' => 'من🙎‍♀️دخترم','callback_data' => 'set_gender;girl']]]])
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_gender' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == "state"){
        $result = $conn->query("SELECT * FROM ".states." WHERE parent=0 ORDER BY id ASC")->fetchAll();
        $reply_markup = [];
        $i = 0;
        $j = 1;
        foreach($result as $key => $row){
            $reply_markup[$i][] = ['text' => $row['state']];
            if($j % 2 == 0)
                $i++;
            $j++;
        }
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "❓ لطفا استان خود را انتخاب کنید 👇",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $reply_markup])
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_state' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == "city" or ($row_users['prev_step'] == 'complete_profile' and $row_users['city'] == null)){
        $parent_id = $conn->query("SELECT * FROM ".states." WHERE parent=0 AND state='{$row_users['state']}'")->fetch()['id'];
        $result = $conn->query("SELECT * FROM ".states." WHERE parent='$parent_id' ORDER BY id ASC")->fetchAll();
        $reply_markup = [[['text' => $back]]];
        $i = 1;
        $j = 1;
        foreach($result as $key => $row){
            $reply_markup[$i][] = ['text' => $row['state']];
            if($j % 2 == 0)
                $i++;
            $j++;
        }
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "❓ لطفا شهر خود را انتخاب کنید 👇",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $reply_markup])
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_city' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == "age"){
        $reply_markup = json_encode(['resize_keyboard' => true,'keyboard' => [
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
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "❓ لطفا عدد سن خود را انتخاب کنید 👇",
            'reply_markup' => $reply_markup
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_age' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == "image" or ($row_users['prev_step'] == 'complete_profile' and $row_users['image'] == null)){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "❓ لطفا عکس پروفایل خود را ارسال کنید.",
            'reply_markup' => json_encode(['remove_keyboard' => true])
        ]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_image' WHERE user_id='$user_id'");
        exit;
    }
    if($ex_data[1] == 'gps' or ($row_users['prev_step'] == 'complete_profile' and !$row_users['latitude'])){
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        $conn->query("UPDATE ".users." SET step='profile;edit;set_location' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "⚠️ توجه کنید : با توجه به این که پروفایل کاربران به صورت عمومی قابل مشاهده است ، در صورت رعایت نکردن قوانین زیر حساب کاربری شما بصورت دائمی مسدود خواهد شد.\n\n".
                "1️⃣ هرگونه محتوای غیر اخلاقی یا توهین آمیز در پروفایل ( عکس یا متن )\n\n".
                "2️⃣ پخش شماره موبایل یا اطلاعات شخصی دیگران\n\n".
                "3️⃣ تبلیغات کانال ، ربات و یا سایت\n\n".
                "👇👇👇",
            'parse_mode' => 'HTML',
        ]);
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' =>
                "⚠️ هنگام ارسال موقعیت مکانی مطمعن شوید GPS موبایل شما روشن است.\n\n".
                "✅ کسی قادر به دیدن موقعیت مکانی شما در ربات نخواهد بود و فقط برای تخمین فاصله و یافتن افراد نزدیک کاربرد خواهد داشت\n\n".
                "❓موقعیت GPS خود را ارسال کنید👇",
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [
                [['text' => "📍ارسال موقعیت جی پی اس",'request_location' => true]],
                [['text' => $back]]
            ]])
        ]);
    }
    exit;
}
if($ex_data[0] == "gib"){
    $row = infoUser($ex_data[2]);
    if(!$row){
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        exit;
    }
    if($ex_data[1] == "likes"){
        if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
            answerCallbackQuery("⚠️ خطا: شما توسط این کاربر بلاک شده اید.");
            exit;
        }
        if($conn->query("SELECT id FROM ".likes." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'")->fetch()){
            $conn->query("DELETE FROM ".likes." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'");
            answerCallbackQuery("لایکـ❤️ کاربر حذف شد!");
        }
        else{
            $conn->query("INSERT INTO ".likes." (user_id,target_id,created_at) VALUES ('$user_id','{$row['user_id']}','$time')");
            answerCallbackQuery("کاربر لایکـ❤️ شد!");
            send_reply("sendMessage",[
                'chat_id' => $row['user_id'],
                'text' => "کاربر /user_{$row_users['uniq_id']} پروفایل شما رو پسندید ❤️\n\n".
                    "❗️ اگر کاربر برای شما مزاحمت ایجاد کرده می توانید کاربر را بلاک کنید",
                'parse_mode' => 'HTML',
            ]);
        }
        send_reply("editMessageReplyMarkup",[
            'chat_id' => $user_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => generateInlineButtons($row['uniq_id'])])
        ]);
        exit;
    }
    if($ex_data[1] == "friend"){
        if($conn->query("SELECT id FROM ".friends." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'")->fetch()){
            $conn->query("DELETE FROM ".friends." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        else{
            $conn->query("UPDATE ".users." SET step='$data',prev_step='start' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "👤شما در حال ذخیره کردن کاربر  در لیست مخاطبین خود هستید.\n\n".
                    "            در صورت تمایل برای اینکار عنوانی که بعدا بتوانید این کاربر را بیاد آورید ارسال کنید یا در صورت عدم تمایل از منوی پایین روی گزینه 《 بازگشت 🔙 》 کلیک کنید.",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $back]]]])
            ]);
        }
        exit;
    }
    if($ex_data[1] == "requestChat"){
        if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
            answerCallbackQuery("⚠️ خطا: شما توسط این کاربر بلاک شده اید.");
            exit;
        }
        if($conn->query("SELECT * FROM ".chats." WHERE (user_id_1='{$row['user_id']}' OR user_id_2='{$row['user_id']}') AND status='chatting'")->fetch()){
            answerCallbackQuery("⚠️ خطا: امکان ارسال درخواست چد وجود ندارد.\n\n".
                "کاربر در حال چت است."
            );
            exit;
        }
        if($conn->query("SELECT * FROM ".chats." WHERE (user_id_1='{$row_users['user_id']}' OR user_id_2='{$row_users['user_id']}') AND status='chatting'")->fetch()){
            answerCallbackQuery("⚠️ خطا: امکان ارسال درخواست چد وجود ندارد.\n\n".
                "شما هم اکنون در حال چت هستید."
            );
            exit;
        }
        if($row_users['balance'] < 2){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "⚠️ توجه : شما سکه کافی ندارید !  (2 سکه مورد نیاز)\n\n".
                    "<code>💡 برای بدست آوردن سکه میتونی رباتو به دوستات معرفی کنی و به ازای معرفی هر نفر ".($row_admin['coin_per_invite']+$row_admin['coin_per_invite_profile']+$row_admin['coin_per_invite_invite'])." .</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '🚸 معرفی به دوستان (سکه رایگان)','callback_data' => 'invite']]]])
            ]);
            if(!$row_users['is_coin_comprof']){
                $output = completeProfile($row_users);
                if($output){
                    send_reply("sendMessage",[
                        'chat_id' => $user_id,
                        'text' =>
                            "🔔 سکه نداری؟ 😕 میخوای سکه رایگان بهت بدم؟ 😎\n\n".
                            "    فقط کافیه پروفایلتو تکمیل کنی! تا سکه هدیه بگیری! 😍👇",
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode(['inline_keyboard' => $output['inline_keyboard']])
                    ]);
                    exit;
                }
            }
            exit;
        }
        if(($row['last_activity'] + 900) < $time){
            answerCallbackQuery("⚠️ خطا: فقط امکان ارسال درخواست چت به کاربرانی که در 15 دقیقه اخیر آنلاین بوده اند وجود دارد.\n\n".
                "در حال حاضر میتوانید برای ین کاربر 📨پیام دایرکت ارسال نمایید."
            );
            exit;
        }
        if($row['silent'] > $time){
            answerCallbackQuery("⚠️ خطا: حالت سایلنت برای این کاربر فعال است.\n".
                "امکان ارسال درخواست چت تا ".to_english(jdate("Y:m:d H:i",$row['silent']))." وجود ندارد\n\n".
                "💡 شما می توانید برای این کاربر پیام دایرکت ارسال کنید."
            );
            exit;
        }
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id='{$row_users['user_id']}' AND user_id_2='{$row['user_id']}' AND reason='request_chat' AND status='doing' AND ($time - date) < 120")->fetch();
        if($row_notif){
            $deffence = 120 - ($time - $row_notif['date']);
            if($deffence == 60)
                $output = "1 دقیقه";
            else if($deffence > 60)
                $output = floor($deffence / 60)." دقیقه و ".($deffence % 60)." ثانیه";
            else{
                $output = ($deffence % 60)." ثانیه";
            }
            answerCallbackQuery("⚠️ خطا: شما در 2 دقیقه اخیر یک درخواست چت به این کاربر ارسال کرده اید.\n\n".
                "⏳ لطفا $output دیگر صبر کنید."
            );
            exit;
        }
        $conn->query("INSERT INTO ".notif." (user_id,balance,user_id_2,reason,status,date) VALUES ('{$row_users['user_id']}','2','{$row['user_id']}','request_chat','doing','$time')");
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id='{$row_users['user_id']}' AND user_id_2='{$row['user_id']}' AND reason='request_chat' AND status='doing'")->fetch();
        
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "✅ درخواست چت شما برای /user_{$row['uniq_id']} ارسال شد.\n\n".
                "🚶منتظر باش و اگه تا دو دقیقه تایید نکرد درخواست چت لغو میشه...",
        ]);
        send_reply("sendMessage", [
            'chat_id' => $row['user_id'],
            'text' => "🔔درخواست چت از طرف /user_{$row_users['uniq_id']} را میپذیرید؟\n\n".
                "<code>- شما تا دو دقیقه پس از ارسال این پیام میتوانید درخواست چت را تایید کنید.</code>\n\n".
                "<u>💡با فعال کردن حالت سایلنت ، کسی امکان درخواست چت به شما را نخواهد داشت 👈 /silent</u>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [
                    [
                        ["text" => "✅ قبول درخواست","callback_data" => "gib;accept;{$row_users['uniq_id']};{$row_notif['id']}"],
                        ["text" => "👎 رد درخواست","callback_data" => "gib;reject;{$row_users['uniq_id']};{$row_notif['id']}"]
                    ],
                ]
            ])
        ]);
        exit;
    }
    if($ex_data[1] == "accept"){
        if($conn->query("SELECT * FROM ".chats." WHERE (user_id_1='{$row['user_id']}' OR user_id_2='{$row['user_id']}') AND status='chatting'")->fetch()){
            answerCallbackQuery("⚠️ خطا: امکان ارسال درخواست چد وجود ندارد.\n\n".
                "کاربر در حال چت است."
            );
            exit;
        }
        if($conn->query("SELECT * FROM ".chats." WHERE (user_id_1='{$row_users['user_id']}' OR user_id_2='{$row_users['user_id']}') AND status='chatting'")->fetch()){
            answerCallbackQuery("⚠️ خطا: امکان ارسال درخواست چد وجود ندارد.\n\n".
                "شما هم اکنون در حال چت هستید."
            );
            exit;
        }
        if($row['silent'] > $time){
            answerCallbackQuery("⚠️ خطا: حالت سایلنت برای این کاربر فعال است.\n".
                "امکان ارسال درخواست چت تا ".to_english(jdate("Y:m:d H:i",$row['silent']))." وجود ندارد\n\n".
                "💡 شما می توانید برای این کاربر پیام دایرکت ارسال کنید."
            );
            exit;
        }
        
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE id='{$ex_data[3]}'")->fetch();
        if(!$row_notif){
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        if(($time - $row_notif['date']) > 120){
            $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row_notif['id']}'");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        
        $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row_notif['id']}'");
        $conn->query("INSERT INTO ".chats." (user_id_1,user_id_2,status,created_at) VALUES ('{$row_users['user_id']}','{$row['user_id']}','chatting','$time')");
        $conn->query("UPDATE ".users." SET num_chats=num_chats+1 WHERE user_id='$user_id'");
        $conn->query("UPDATE ".users." SET balance=balance-{$row_notif['balance']},num_chats=num_chats+1 WHERE user_id='{$row['user_id']}'");
        
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "👀 درخواست چت وصل شد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "👀 درخواست چت وصل شد\n\n".
                "به مخاطبت سلام کن 🗣",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        exit;
    }
    if($ex_data[1] == "reject"){
        $row_notif = $conn->query("SELECT * FROM ".notif." WHERE id='{$ex_data[3]}'")->fetch();
        if(!$row_notif){
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        if(($time - $row_notif['date']) > 120){
            $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row_notif['id']}'");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            exit;
        }
        $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row_notif['id']}'");
        
        answerCallbackQuery("✅ درخواست چت رد شد.");
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => "🔔 درخواست چت شما به /user_{$row_users['uniq_id']} رد شد.",
        ]);
        exit;
    }
    if($ex_data[1] == "directMessage"){
        if($ex_data[3] == 'none'){
            if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
                answerCallbackQuery("⚠️ خطا: شما توسط این کاربر بلاک شده اید.");
                exit;
            }
            if($row_users['balance'] < 1){
                answerCallbackQuery("⚠️ خطا: شما سکه ای برای ارسال پیام دایرکت ندارید!\n\n".
                    "برای ارسال پیام دایرکت 1 سکه نیاز است."
                );
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "سکه نداری ؟ ولی میخوای برای /user_{$row['uniq_id']} پیام دایرکت بفرستی؟ الان بهت یه راهکار نشون میدم\n\n".
                        "با قابلیت 《 ارسال 📨 پیام دایرکت و کسر سکه از گیرنده 》  از گیرنده درخواست میشه تا با دادن 💰1 سکه بتونه پیامت رو مشاهده کنه و بجای تو از اون کسر بشه !\n\n".
                        "<code>برای استفاده از این قابلیت و ارسال پیام دایرکت دکمه زیر رو لمس کن 👇</code>",
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'ارسال 📨 پیام دایرکت و کسر 💰1 سکه از گیرنده','callback_data' => "gib;directMessage;{$row['uniq_id']};wc"]]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET step='$data' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "📬 متن پیام دایرکت خود را ارسال کنید (حداکثر 200 حرف)\n\n".
                    "<code>    برای لغو ارسال پیام دایرکت 《بیخیال》 را لمس کنید👇</code>",
                'parse_mode' => 'HTML',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};cancel"]]]])
            ]);
            exit;
        }
        if($ex_data[3] == 'wc'){
            if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
                answerCallbackQuery("⚠️ خطا: شما توسط این کاربر بلاک شده اید.");
                exit;
            }
            $conn->query("UPDATE ".users." SET step='$data' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "📬 متن پیام دایرکت خود را ارسال کنید (حداکثر 200 حرف)\n\n".
                    "<code>    برای لغو ارسال پیام دایرکت 《بیخیال》 را لمس کنید👇</code>",
                'parse_mode' => 'HTML',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};cancel"]]]])
            ]);
            exit;
        }
        if($ex_data[3] == 'cancel'){
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "✅ ارسال پیام دایرکت لغو شد.",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            exit;
        }
        if($ex_data[3] == 'send'){
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            $row_notif = $conn->query("SELECT * FROM ".notif." WHERE id='{$ex_data[5]}'")->fetch();
            if(!$row_notif)
                exit;
            $conn->query("UPDATE ".notif." SET status='end' WHERE id='{$row_notif['id']}'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "✅ پیام #دایرکت شما به /user_{$row['uniq_id']} در ".to_english(jdate("Y:m:d H:i",$row_notif['date']))." ارسال شد.\n".
                    "ــــــــــــــــــــــــــــــــــــــــ\n".
                    $row_notif['content'],
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            if($ex_data[4] == 'none'){
                $conn->query("UPDATE ".users." SET step='start',balance=balance-1 WHERE user_id='$user_id'");
                send_reply("sendMessage",[
                    'chat_id' => $row['user_id'],
                    'text' => "🔔  پیام #دایرکت جدید از طرف /user_{$row_users['uniq_id']} در ".to_english(jdate("Y:m:d H:i",$row_notif['date']))." ارسال شد.\n".
                        "ــــــــــــــــــــــــــــــــــــــــ\n".
                        $row_notif['content'],
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '📨 ارسال پاسخ','callback_data' => "gib;directMessage;{$row_users['uniq_id']};none"]]]])
                ]);
            }
            else if($ex_data[4] == 'wc'){
                send_reply("sendMessage",[
                    'chat_id' => $row['user_id'],
                    'text' => "🔔  پیام #دایرکت جدید از طرف /user_{$row_users['uniq_id']} ، در 1400:10:12 16:47\n".
                        "ــــــــــــــــــــــــــــــــــــــــ\n".
                        "کاربر /user_{$row['uniq_id']} بهت پیام دایرکت فرستاده و بدلیل نداشتن 💰سکه ازت خواسته سکه پیام دایرکتش از تو کسر بشه.\n\n".
                        "درصورتی که موافق هستی گزینه زیر رو بزن تا پیامی که بهت فرستاده رو ببینی 👇",
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'ارسال 📨 پیام دایرکت و کسر 💰1 سکه از گیرنده','callback_data' => "gib;directMessage;{$row_users['uniq_id']};get;{$row_notif['id']}"]]]])
                ]);
            }
            exit;
        }
        if($ex_data[3] == 'get'){
            $row_notif = $conn->query("SELECT * FROM ".notif." WHERE id='{$ex_data[4]}'")->fetch();
            if(!$row_notif){
                send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
                exit;
            }
            if($row_users['balance'] < 1){
                answerCallbackQuery("⚠️ خطا: شما سکه ای برای ارسال پیام دایرکت ندارید!\n\n".
                    "برای ارسال پیام دایرکت 1 سکه نیاز است."
                );
                exit;
            }
            $conn->query("UPDATE ".users." SET step='start',balance=balance-1 WHERE user_id='$user_id'");
            send_reply("editMessageText",[
                'chat_id' => $user_id,
                'text' => "🔔  پیام #دایرکت جدید از طرف /user_{$row['uniq_id']} در ".to_english(jdate("Y:m:d H:i",$row_notif['date']))." ارسال شد.\n".
                    "ــــــــــــــــــــــــــــــــــــــــ\n".
                    $row_notif['content'],
                'message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '📨 ارسال پاسخ','callback_data' => "gib;directMessage;{$row['uniq_id']};none"]]]])
            ]);
        }
        exit;
    }
    if($ex_data[1] == "block"){
        if($conn->query("SELECT id FROM ".blocked." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'")->fetch()){
            $conn->query("DELETE FROM ".blocked." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'");
            answerCallbackQuery("✅ کاربر آنبلاک شده.\n\n".
                "این کاربر امکان ارسال درخواست چت و پیام دایرکت به شما را خواهد داشت."
            );
        }
        else{
            $conn->query("INSERT INTO ".blocked." (user_id,target_id,created_at) VALUES ('$user_id','{$row['user_id']}','$time')");
            answerCallbackQuery("✅ کاربر بلاک شده.\n\n".
                "این کاربر امکان ارسال درخواست چت و پیام دایرکت به شما را نخواهد داشت."
            );
        }
        
        send_reply("editMessageReplyMarkup",[
            'chat_id' => $user_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => generateInlineButtons($row['uniq_id'])])
        ]);
        exit;
    }
    if($ex_data[1] == "report"){
        $row_chats = $conn->query("SELECT * FROM ".chats." WHERE (user_id_1='$user_id' OR user_id_2='$user_id') AND status='chatting'")->fetch();
        if($row_chats){
            answerCallbackQuery("⚠️ درصورتی که مخاطب شما محتوای نامناسبی برایتان ارسال کرده چت را خاتمه دهید و روی دکمه « 🚫 گزارش کاربر » که در پروفایل مخاطب قرار دارد کلیک کنید تا حساب کاربری او مسدود شود.");
            exit;
        }
        else{
            if($ex_data[3] == "repchat"){
                send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
                exit;
            }
        }
        if($ex_data[3] == 'none'){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "⚠️ فرم ارسال گزارش عدم رعایت قوانین\n\n".
                    "چرا میخوای /user_{$row['uniq_id']} رو گزارش کنی؟\n\n".
                    "- توجه : تمامی گزارشات بررسی خواهند شد و 🔴 ارسال گزارشات اشتباه موجب مسدود شدن شما خواهد شد.\n\n".
                    "انتخاب کنید 👇",
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [['text' => $report_options['ads'],'callback_data' => "gib;report;{$row['uniq_id']};ads"]],
                    [['text' => $report_options['immoral'],'callback_data' => "gib;report;{$row['uniq_id']};immoral"]],
                    [['text' => $report_options['disturb'],'callback_data' => "gib;report;{$row['uniq_id']};disturb"]],
                    [['text' => $report_options['dissemination'],'callback_data' => "gib;report;{$row['uniq_id']};dissemination"]],
                    [['text' => $report_options['immoralprofile'],'callback_data' => "gib;report;{$row['uniq_id']};immoralprofile"]],
                    [['text' => $report_options['wronggender'],'callback_data' => "gib;report;{$row['uniq_id']};wronggender"]],
                    [['text' => $report_options['other'],'callback_data' => "gib;report;{$row['uniq_id']};other"]]
                ]])
            ]);
        }
        else{
            $conn->query("UPDATE ".users." SET step='$data',prev_step='start' WHERE user_id='$user_id'");
            send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "⚠️ فرم ارسال گزارش عدم رعایت قوانین به دلیل {$report_options[$ex_data[3]]}.\n\n".
                    "<code>خب حالا کافیه یه توضیح کوتاه و 《کامل》 درباره گزارشت بفرستی تا ثبتش کنم.</code>\n\n".
                    "<code>- مثلا : داره تبلیغات فلان کانال رو میکنه.</code>\n\n".
                    "برای لغو گزارش 《 بازگشت 🔙 》 را انتخاب کنید 👇",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $back]]]])
            ]);
        }
        exit;
    }
    if($ex_data[1] == "onlinenotif"){
        if($conn->query("SELECT * FROM ".notif." WHERE user_id='$user_id' AND user_id_2='{$row['user_id']}' AND reason='onlinenotif' AND status='doing'")->fetch()){
            answerCallbackQuery("⚠️ خطا : این قابلیت قبلا توسط شما برای این کاربر فعال شده است.");
            exit;
        }
        if($ex_data[3] == 'none'){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "🔔 به محض آنلاین شدن کاربر /user_{$row['uniq_id']} به شما اطلاع داده خواهد شد.\n".
                    "(راهنما : /help_onw)\n\n".
                    "⚠️ توجه : فعال کردن این قابلیت 1 💰 سکه از شما کم خواهد کرد.\n\n\n".
                    "<code>فعال سازی 👇</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "کسر 1💰 سکه و فعال سازی",'callback_data' => "gib;onlinenotif;{$row['uniq_id']};active"]]]])
            ]);
        }
        else if($ex_data[3] == 'active'){
            if($row_users['balance'] < 1){
                answerCallbackQuery("⚠️ تعداد سکه شما کافی نیست");
                exit;
            }
            $conn->query("INSERT INTO ".notif." (user_id,user_id_2,reason,date) VALUES ('$user_id','{$row['user_id']}','onlinenotif','$time')");
            $conn->query("UPDATE ".users." SET balance=balance-1 WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "✅ با موفقیت ثبت شد. (راهنما : /help_onw)\n\n".
                    "🔔 به محض آنلاین شدن کاربر /user_{$row['uniq_id']} به شما اطلاع داده خواهد شد.",
            ]);
        }
        exit;
    }
    if($ex_data[1] == "endchatnotif"){
        if($conn->query("SELECT * FROM ".notif." WHERE user_id='$user_id' AND user_id_2='{$row['user_id']}' AND reason='endchatnotif' AND status='doing'")->fetch()){
            answerCallbackQuery("⚠️ خطا : این قابلیت قبلا توسط شما برای این کاربر فعال شده است.");
            exit;
        }
        if($ex_data[3] == 'none'){
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "🔔 به محض اتمام چت کاربر /user_{$row['uniq_id']} به شما اطلاع داده خواهد شد.\n".
                    "(راهنما : /help_onw)\n\n".
                    "⚠️ توجه : فعال کردن این قابلیت 1 💰 سکه از شما کم خواهد کرد.\n\n\n".
                    "<code>فعال سازی 👇</code>",
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "کسر 1💰 سکه و فعال سازی",'callback_data' => "gib;endchatnotif;{$row['uniq_id']};active"]]]])
            ]);
        }
        else if($ex_data[3] == 'active'){
            if($row_users['balance'] < 1){
                answerCallbackQuery("⚠️ تعداد سکه شما کافی نیست");
                exit;
            }
            $conn->query("INSERT INTO ".notif." (user_id,user_id_2,reason,date) VALUES ('$user_id','{$row['user_id']}','endchatnotif','$time')");
            $conn->query("UPDATE ".users." SET balance=balance-1 WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "✅ با موفقیت ثبت شد. (راهنما : /help_onw)\n\n".
                    "🔔 به محض اتمام چت کاربر /user_{$row['uniq_id']} به شما اطلاع داده خواهد شد.",
            ]);
        }
        exit;
    }
    exit;
}
if($ex_step[0] == "gib"){
    $row = infoUser($ex_step[2]);
    if(!$row){
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        exit;
    }
    if($ex_step[1] == "directMessage"){
        if($ex_step[3] == 'none'){
            if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
                $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "⚠️ خطا: شما توسط این کاربر بلاک شده اید.",
                    'reply_to_message_id' => $message_id,
                ]);
                exit;
            }
            if($row_users['balance'] < 1){
                answerCallbackQuery("⚠️ خطا: شما سکه ای برای ارسال پیام دایرکت ندارید!\n\n".
                    "برای ارسال پیام دایرکت 1 سکه نیاز است."
                );
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "سکه نداری ؟ ولی میخوای برای /user_{$row['uniq_id']} پیام دایرکت بفرستی؟ الان بهت یه راهکار نشون میدم\n\n".
                        "با قابلیت 《 ارسال 📨 پیام دایرکت و کسر سکه از گیرنده 》  از گیرنده درخواست میشه تا با دادن 💰1 سکه بتونه پیامت رو مشاهده کنه و بجای تو از اون کسر بشه !\n\n".
                        "<code>برای استفاده از این قابلیت و ارسال پیام دایرکت دکمه زیر رو لمس کن 👇</code>",
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '🚸 معرفی به دوستان (سکه رایگان)','callback_data' => 'invite']]]])
                ]);
                exit;
            }
            if(isset($up["message"]["text"]) and strlen($text) < 200){
                $conn->query("INSERT INTO ".notif." (user_id,user_id_2,reason,content,date) VALUES ('$user_id','{$row['user_id']}','direct_message','$text','$time')");
                $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id='$user_id' AND user_id_2='{$row['user_id']}' AND reason='direct_message' AND status='doing' ORDER BY id DESC")->fetch();
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "📜 پیش نمایش پیام دایرکت شما به /user_{$row['uniq_id']}\n\n".
                        "ــــــــــــــــــــــــــــــــــــــــ\n".
                        "$text",
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [
                            ['text' => '📨 ارسال','callback_data' => "gib;directMessage;{$row['uniq_id']};send;none;{$row_notif['id']}"],
                            ['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};cancel"]
                        ]
                    ]])
                ]);
            }
            else{
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "⚠️خطا : پیام دایرکت فقط باید بصورت متن باشد (حداکثر 200 حرف)\n\n".
                        "- برای لغو ارسال پیام دایرکت به /user_{$row['uniq_id']} 《بیخیال》 را لمس کنید",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};other"]]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
            exit;
        }
        if($ex_step[3] == 'wc'){
            if($conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row['user_id']}' AND target_id='$user_id'")->fetch()){
                $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "⚠️ خطا: شما توسط این کاربر بلاک شده اید.",
                    'reply_to_message_id' => $message_id,
                ]);
                exit;
            }
            if(isset($up["message"]["text"]) and strlen($text) < 200){
                $conn->query("INSERT INTO ".notif." (user_id,user_id_2,reason,content,date) VALUES ('$user_id','{$row['user_id']}','direct_message','$text','$time')");
                $row_notif = $conn->query("SELECT * FROM ".notif." WHERE user_id='$user_id' AND user_id_2='{$row['user_id']}' AND reason='direct_message' AND status='doing' ORDER BY id DESC")->fetch();
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "📜 پیش نمایش پیام دایرکت شما به /user_{$row['uniq_id']}\n\n".
                        "ــــــــــــــــــــــــــــــــــــــــ\n".
                        "$text",
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [
                            ['text' => '📨 ارسال','callback_data' => "gib;directMessage;{$row['uniq_id']};send;wc;{$row_notif['id']}"],
                            ['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};cancel"]
                        ]
                    ]])
                ]);
            }
            else{
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' =>
                        "⚠️خطا : پیام دایرکت فقط باید بصورت متن باشد (حداکثر 200 حرف)\n\n".
                        "- برای لغو ارسال پیام دایرکت به /user_{$row['uniq_id']} 《بیخیال》 را لمس کنید",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'بیخیال','callback_data' => "gib;directMessage;{$row['uniq_id']};other"]]]])
                ]);
                exit;
            }
            $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
            exit;
        }
        exit;
    }
    if($ex_step[1] == "friend"){
        $conn->query("INSERT INTO ".friends." (name,user_id,target_id,created_at) VALUES ('$text','$user_id','{$row['user_id']}','$time')");
        $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "👥 مخاطب ذخیره شد ✅\n\n".
                "توجه: مخاطبین خود را می توانید از قسمت مخاطبین که در بخش پروفایل قرار دارد مشاهده کنید.\n\n".
                mainMenu(false,true),
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
        ]);
        exit;
    }
    if($ex_step[1] == "report"){
        $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
        $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        if(isset($up["message"]["text"])){
            send_reply("sendMessage", [
                'chat_id' => $settings['admin_id'],
                'text' =>
                    "🚫 ثبت گزارش جدید!\n\n".
                    "گزارش کننده: <a href='tg://user?id=$user_id'>$user_id</a> (/user_{$row_users['uniq_id']})\n".
                    "گزارش شده: <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/user_{$row_user_select['uniq_id']})\n\n".
                    "دلیل:\n{$report_options[$ex_step[3]]}\n\n".
                    "متن:\n".mb_substr($text,0,1500),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ]);
        }
        else if(isset($up["message"]["photo"])){
            send_reply("sendphoto",[
                'chat_id' => $settings['admin_id'],
                'photo' => $up["message"]["photo"][count($up["message"]["photo"]) - 1]["file_id"],
                'caption' => 
                    "🚫 ثبت گزارش جدید!\n\n".
                    "گزارش کننده: <a href='tg://user?id=$user_id'>$user_id</a> (/user_{$row_users['uniq_id']})\n".
                    "گزارش شده: <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/user_{$row_user_select['uniq_id']})\n\n".
                    "دلیل:\n{$report_options[$ex_step[3]]}\n\n".
                    "متن:\n".mb_substr($up["message"]["caption"],0,500),
                'parse_mode' => 'HTML',
            ]);
        }
        else{
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "❌ لطفا فقط متن یا تصویر ارسال کنید",
            ]);
            exit;
        }
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "✅ با تشکر از همکاری شما، گزارش شما با موفقیت ثبت شد و بزودی بررسی خواهد شد 🌹",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
        ]);
        exit;
    }
}
?>