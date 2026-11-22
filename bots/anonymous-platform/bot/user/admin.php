<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if($text == "/panel" || $text == "👤 پنل ادمین 👤" || $text == $panel){
    $conn->query("UPDATE ".users." SET step='panel' WHERE user_id='$user_id'");
    panels('admin');
    exit;
}
if(($ex_text[0] == "/d" && $uid = $ex_text[1]) || ($ex_data[0] == 'd' && $uid = $ex_data[1])){
    $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='$uid' OR uniq_id='$uid'")->fetch();
    if(!$row_user_select){
        sendMessage($user_id,"❌ یافت نشد!");
        exit;
    }
    if($ex_text[0] == "/d")
        $type = "sendMessage";
    else{
        $type = "editMessageText";
        if($ex_data[2] == "sendmsg"){
            $conn->query("UPDATE ".users." SET step='sendmsg;{$row_user_select['user_id']}' WHERE user_id='$user_id'");
            sendMessage($user_id,"📌 پیام خود را وارد کنید:\nکنسل: /panel");
            exit;
        }
        if($ex_data[2] == "block" || $ex_data[2] == "admin" || $ex_data[2] == "user"){
            if($user_id != $settings['admin_id']){
                if($ex_data[2] == "block" && $row_user_select['status'] == "admin"){
                    answerCallbackQuery("❌ فقط ادمین اصلی میتونه یک ادمین رو بلاک بکنه!");
                    exit;
                }
                if($ex_data[2] == "admin" || ($ex_data[2] == "user" && $row_user_select['status'] == "admin")){
                    answerCallbackQuery("❌ فقط ادمین اصلی میتونه یک ادمین منصوب/عزل بکنه!");
                    exit;
                }
            }
            $conn->query("UPDATE ".users." SET status='{$ex_data[2]}' WHERE user_id='{$row_user_select['user_id']}'");
            $row_user_select['status'] = $ex_data[2];
        }
        else if($ex_data[2] == "balance"){
            $row_user_select['balance'] += $ex_data[3];
            if($row_user_select['balance'] < 0){
                answerCallbackQuery("باید بیشتر از 0 باشد.");
                exit;
            }
            $conn->query("UPDATE ".users." SET balance={$row_user_select['balance']} WHERE user_id='{$row_user_select['user_id']}'");
        }
    }
    if($row_user_select['status'] == "block"){
        $block = 'بلاک ✅';
        $block_status = "user";
    }
    else{
        $block = 'بلاک ❌';
        $block_status = "block";
    }
    if($row_user_select['status'] == "admin"){
        $admin = 'ادمین ✅';
        $admin_status = "user";
    }
    else{
        $admin = 'ادمین ❌';
        $admin_status = "admin";
    }
    $date_today = date("Y-m-d");
    $time_today = strtotime($date_today);
    $time_yesterday = $time_today - 86400;

    $count_block_today = $conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row_user_select['user_id']}' AND created_at>=$time_today")->rowCount();
    $count_block_yesterday = $conn->query("SELECT id FROM ".blocked." WHERE user_id='{$row_user_select['user_id']}' AND  created_at>=$time_yesterday AND created_at<$time_today")->rowCount();
    $count_blocked_today = $conn->query("SELECT id FROM ".blocked." WHERE target_id='{$row_user_select['user_id']}' AND created_at>=$time_today")->rowCount();
    $count_blocked_yesterday = $conn->query("SELECT id FROM ".blocked." WHERE target_id='{$row_user_select['user_id']}' AND  created_at>=$time_yesterday AND created_at<$time_today")->rowCount();
    send_reply($type,[
        'chat_id' => $user_id,
        'text' => 
            "کاربر <a href='tg://user?id={$row_user_select['user_id']}'>{$row_user_select['user_id']}</a> (/d_{$row_user_select['user_id']})\n\n".
            "آیدی: /user_{$row_user_select['uniq_id']}\n".
            "بلاک کرده امروز: $count_block_today\n".
            "بلاک کرده دیروز: $count_block_yesterday\n".
            "بلاک شده امروز: $count_blocked_today\n".
            "بلاک شده دیروز: $count_blocked_yesterday",
        'disable_web_page_preview' => true,
        'parse_mode' => 'html',
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => "ارسال پیام",'callback_data' => "d;{$row_user_select['user_id']};sendmsg"]],
            [
                ['text' => $block,'callback_data' => "d;{$row_user_select['user_id']};$block_status"],
                ['text' => $admin,'callback_data' => "d;{$row_user_select['user_id']};$admin_status"]
            ],
            [['text' => "سکه: ".number_format($row_user_select['balance']),'callback_data' => "test"]],
            [
                ['text' => '-10','callback_data' => "d;{$row_user_select['user_id']};balance;-10"],
                ['text' => '-5','callback_data' => "d;{$row_user_select['user_id']};balance;-5"],
                ['text' => '-1','callback_data' => "d;{$row_user_select['user_id']};balance;-1"],
                ['text' => '+1','callback_data' => "d;{$row_user_select['user_id']};balance;+1"],
                ['text' => '+5','callback_data' => "d;{$row_user_select['user_id']};balance;+5"],
                ['text' => '+10','callback_data' => "d;{$row_user_select['user_id']};balance;+10"],
            ]
        ]])
    ]);
    exit;
}
if($text == "📊 آمار"){
    $date_today = date("Y-m-d 00:00:00");
    $time_today = strtotime($date_today);
    $time_yesterday = $time_today - 86400;
    send_reply("sendMessage",[
        'chat_id' => $user_id,
        'text' =>
            "اطلاعات کاربران:\n".
                "👤 تعداد کل: ".$conn->query("SELECT id FROM ".users)->rowCount()."\n".
                "👤 امروز عضو شدند: ".$conn->query("SELECT id FROM ".users." WHERE created_at>=$time_today")->rowCount()."\n".
                "👤 دیروز عضو شدند: ".$conn->query("SELECT id FROM ".users." WHERE created_at>=$time_yesterday AND created_at<$time_today")->rowCount()."\n".
                "👤 بدون چت: ".$conn->query("SELECT id FROM ".users." WHERE num_chats=0")->rowCount()."\n".
                "👤 بدون موجودی: ".$conn->query("SELECT id FROM ".users." WHERE balance=0")->rowCount()."\n".
                "👤 دعوت شده: ".$conn->query("SELECT id FROM ".users." WHERE referral IS NOT NULL")->rowCount()."\n".
                "🚫 بلاک: ".$conn->query("SELECT id FROM ".users." WHERE status='block'")->rowCount()."\n".
                "👑 ادمین: ".$conn->query("SELECT id FROM ".users." WHERE status='admin'")->rowCount()."",
    ]);
    exit;
}
if($text == "💳 تراکنش ها" or $ex_data[0] == "paysListAdmin"){
    $step_page = 30;
    $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT * FROM ".payments." ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            sendMessage($user_id,"⚠️ تراکنشی یافت نشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT id FROM ".payments)->rowCount();
    foreach($result as $row){
        $txts .= "$i. شناسه: /p_{$row['id']} ({$status_pay[$row['status']]})\n";
        $i++;
    }
    ListShow("🔰 لیست ادمین\n\n$txts",'paysListAdmin');
    exit;
}
if($text == "👥 کاربران"){
    $conn->query("UPDATE ".users." SET step='users;none',prev_step='panel' WHERE user_id='$user_id'");
    send_reply("sendMessage", [
        'chat_id' => $user_id,
        'text' => "پنل ادمین » کاربران:",
        'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [
                [['text' => '👥 ادمین'],['text' => '👥 بلاک'],['text' => '👥 همه']],
                [['text' => '👥 پیام همگانی'],['text' => '👥 فوروارد همگانی']],
                [['text' => '👥 سکه همگانی']],
                [['text' => $panel]]
            ]
        ])
    ]);
    exit;
}
if($ex_step[0] == "users"){
    if($ex_step[1] == "none"){
        if($text == "👥 ادمین" or $ex_data[0] == "adminListAdmin"){
            $step_page = 30;
            $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
            $num_page = $data_current == 'none'?1:$data_current;
            $selected_pages = ($num_page - 1) * $step_page;
            $i = $selected_pages + 1;

            $result = $conn->query("SELECT user_id FROM ".users." WHERE status='admin' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            if(!$result){
                if($data_current == 'none')
                    sendMessage($user_id,"⚠️ کاربری یافت نشد.");
                else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
                exit;
            }
            $num = $conn->query("SELECT user_id FROM ".users." WHERE status='admin'")->rowCount();
            foreach($result as $row){
                $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/delAdmin_{$row['user_id']})";
                $i++;
            }
            ListShow("🔰 لیست ادمین\n\n$txts",'adminListAdmin');
            exit;
        }
        if($text == "👥 بلاک" or $ex_data[0] == "blockListAdmin"){
            $step_page = 30;
            $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
            $num_page = $data_current == 'none'?1:$data_current;
            $selected_pages = ($num_page - 1) * $step_page;
            $i = $selected_pages + 1;

            $result = $conn->query("SELECT user_id FROM ".users." WHERE status='block' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            if(!$result){
                if($data_current == 'none')
                    sendMessage($user_id,"⚠️ کاربری یافت نشد.");
                else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
                exit;
            }
            $num = $conn->query("SELECT id FROM ".users." WHERE status='block'")->rowCount();
            foreach($result as $row){
                $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/unblock{$row['user_id']})";
                $i++;
            }
            ListShow("🔰 لیست کاربران:\n$txts\n‌",'blockListAdmin');
            exit;
        }
        if($text == "👥 همه" or $ex_data[0] == "usersListAdmin"){
            $step_page = 30;
            $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
            $num_page = $data_current == 'none'?1:$data_current;
            $selected_pages = ($num_page - 1) * $step_page;
            $i = $selected_pages + 1;

            $result = $conn->query("SELECT user_id FROM ".users." ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
            if(!$result){
                if($data_current == 'none')
                    sendMessage($user_id,"⚠️ کاربری یافت نشد.");
                else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
                exit;
            }
            $num = $conn->query("SELECT id FROM ".users)->rowCount();
            foreach($result as $row){
                $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/d_{$row['user_id']})";
                $i++;
            }
            ListShow("🔰 لیست کاربران:\n$txts\n‌",'usersListAdmin');
            exit;
        }
        if($text == "👥 پیام همگانی"){
            $conn->query("UPDATE ".users." SET step='users;m_all',prev_step='panel;users;none' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "پیام خود را ارسال کنید:",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
            ]);
            exit;
        }
        if($text == "👥 فوروارد همگانی"){
            $conn->query("UPDATE ".users." SET step='users;f_all',prev_step='panel;users;none' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "پیام خود را فوروارد کنید:",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
            ]);
            exit;
        }
        if($text == "👥 سکه همگانی"){
            $conn->query("UPDATE ".users." SET step='users;coin_all',prev_step='panel;users;none' WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "تعداد سکه را وارد کنید:",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
            ]);
            exit;
        }
    }
    else if($ex_step[1] == "m_all"){
        if(!isset($message['text'])){
            send_reply("sendMessage",['chat_id' => $chat_id,'text' => "❌ فقط یک متن ارسال کنید"]);
        }
        else{
            $conn->query("UPDATE ".users." SET step='users;none' WHERE user_id='$user_id'");
            $max_send_id = $conn->query("SELECT * FROM ".users." ORDER BY id DESC LIMIT 1")->fetch()['id'];
            $count_members = $conn->query("SELECT * FROM ".users."")->rowcount();
            $count_members = $conn->query("SELECT COUNT(*) as count FROM ".users."")->fetch()['count'];
            $edit_message_id = send_reply("sendMessage",['chat_id' => $chat_id,'text' => "🔰 وضعیت: در حال آماده سازی..."])['result']['message_id'];
            $conn->query("INSERT INTO ".cron." (user_id,chat_id,type,file_type,text,max_send_id,message_id_edit,count_members) VALUES ('$user_id','$user_id','bot','message','$text','$max_send_id','$edit_message_id','$count_members')");
        }
    }
    else if($ex_step[1] == "f_all"){
        $conn->query("UPDATE ".users." SET step='users;none' WHERE user_id='$user_id'");
        $max_send_id = $conn->query("SELECT * FROM ".users." ORDER BY id DESC LIMIT 1")->fetch()['id'];
        $count_members = $conn->query("SELECT COUNT(*) as count FROM ".users."")->fetch()['count'];
        $edit_message_id = send_reply("sendMessage",['chat_id' => $chat_id,'text' => "🔰 وضعیت: در حال آماده سازی..."])['result']['message_id'];
        $conn->query("INSERT INTO ".cron." (user_id,chat_id,type,file_type,message_id,max_send_id,message_id_edit,count_members) VALUES ('$user_id','$user_id','bot','forward','$message_id','$max_send_id','$edit_message_id','$count_members')");
    }
    if($ex_step[1] == "coin_all"){
        if(!is_numeric($text) or $text < 1){
            sendMessage($user_id,"❌ مشکلی پیش آمده، دوباره تلاش کنید.");
            exit;
        }
        $conn->query("UPDATE ".users." SET step='users;none' WHERE user_id='$user_id'");
        $conn->query("UPDATE ".users." SET balance=balance+$text");
        $max_send_id = $conn->query("SELECT * FROM ".users." ORDER BY id DESC")->fetch()['id'];
        $content = "🔥 کاربر گرامی $text سکه به شما اهدا شد";
        $conn->query("INSERT INTO ".cron." (chat_id,file_type,text,max_send_id) VALUES ('$user_id','message','$content','$max_send_id')");
        sendMessage($user_id,"✅ انجام شد");
        exit;
    }
    exit;
}
if($text == "💰 تعرفه ها"){
    panels("tariff");
    exit;
}
if($ex_step[0] == "set_vip"){
    if($text == "افزودن تعرفه ➕"){
        $conn->query("UPDATE ".users." SET step='set_vip;day_vip' WHERE user_id='$user_id'");
        sendMessage($user_id,"تعداد سکه ها را وارد کنید.\n\n".
            "‼️ یک عدد اط 1 تا 10000:",null,$panel);
        exit;
    }
    if($ex_step[1] == "check"){
        if(!$conn->query("SELECT * FROM ".amounts." WHERE coin='$text'")->fetch()){
            sendMessage($user_id,"❌ خطایی رخ داد.");
            exit;
        }
        $conn->query("DELETE FROM ".amounts." WHERE coin='$text'");
        sendMessage($user_id,"✅ تعرفه موردنظر با موفقیت حذف شد.");
        panels("tariff");
        exit;
    }
    if($ex_step[1] == "day_vip"){
        if($conn->query("SELECT * FROM ".amounts." WHERE coin='$text'")->fetch()){
            sendMessage($user_id,"❌ تعرفه مربوطه قبلا تعریف شده");
            exit;
        }
        else if(!is_numeric($text) || $text < 1 || $text > 10000){
            sendMessage($user_id,"تعداد سکه ها را یک عدد از 1 تا 10000 وارد کنید:",null,$panel);
            exit;
        }
        $conn->query("UPDATE ".users." SET step='set_vip;amount_vip;$text' WHERE user_id='$user_id'");
        sendMessage($user_id,"قیمت $text سکه را یک عدد از 1000 تا 10000000 وارد کنید:",null,$panel);
        exit;
    }
    if($ex_step[1] == "amount_vip"){
        
        $row_amounts = $conn->query("SELECT * FROM ".amounts." WHERE coin='$ex_step[2]'")->fetch();
        if(!is_numeric($text) || $text < 1000 || $text > 10000000){
            sendMessage($user_id,"قیمت $ex_step[2] سکه را یک عدد بین 1000 تا 1000000 وارد کنید:",null,$panel);
            exit;
        }
        $conn->query("INSERT INTO ".amounts." (amount,coin) VALUES ($text,$ex_step[2])");
        $conn->query("UPDATE ".users." SET step='panel;none' WHERE user_id='$user_id'");
        sendMessage($user_id,"✅ تعرفه جدید با موفقیت ثبت شد.");
        panels("tariff");
        exit;
    }
}
if($ex_text[0] == "/p"){
    $row_payments = $conn->query("SELECT * FROM ".payments." WHERE id='{$ex_text[1]}'")->fetch();
    if(!$row_payments){
        sendMessage($user_id,"❌ تراکنش موردنظر یافت نشد.");
        exit;
    }
    send_reply("sendMessage", [
        'chat_id' => $user_id,
        'text' => "شناسه: <code>{$row_payments['id']}</code>\n".
            "پیگیری زرین پال: <code>{$row_payments['ref_id']}</code>\n".
            "کاربر: <a href='tg://user?id={$row_payments['user_id']}'>{$row_payments['user_id']}</a> (/d_{$row_payments['user_id']})\n".
            "وضعیت: {$status_pay[$row_payments['status']]}\n".
            "مبلغ: ".to_persian(number_format($row_payments['amount']))." تومان\n".
            "ایجاد: ".jdate("Y/m/d ساعت H:i:s",$row_payments['created_at'])."\n".
            "بروزرسانی: ".jdate("Y/m/d ساعت H:i:s",$row_payments['updated_at']),
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $message_id,
    ]);
    exit;
}
if($text == "⚙️ تنظیمات"){
    $conn->query("UPDATE ".users." SET step='settings;none',prev_step='panel' WHERE user_id='$user_id'");
    panels('settings');
    exit;
}
if($ex_step[0] == "settings"){
    if($text == "🗑 کانال کش"){
        $conn->query("UPDATE ".users." SET step='settings;ch_cache_id',prev_step='settings' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "شناسه عددی کانال کش را بفرستید:\n\n".
                "از این ربات برای بدست اوردن شناسه کانال استفاده کنید: @info_tel_bot",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
        ]);
        exit;
    }
    if($ex_step[1] == "ch_cache_id"){
        if(IsMember($text,getMe()['id']) != "administrator"){
            sendMessage($user_id,"❌ کانال مورد نظر شناسایی نشد!");
            exit;
        }
        $conn->query("UPDATE ".admin." SET {$ex_step[1]}='$text'");
        $conn->query("UPDATE ".users." SET step='settings;none' WHERE user_id='$user_id'");
        sendMessage($user_id,"✅ انجام شد.");
        panels('settings');
        exit;
    }
    if($text == "⚙️ اجباری 1" || $text == "⚙️ اجباری 2" || $text == "⚙️ اجباری 3"){
        $channel = mb_substr($text,10);
        $conn->query("UPDATE ".users." SET step='settings;force_join;channel_$channel',prev_step='settings' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "یوزرنیم و نام کانال مانند نمونه در دو خط ارسال کنید:\n\n".
                "نام کانال\n".
                "@username",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
        ]);
        exit;
    }
    if($ex_step[1] == "force_join"){
        $ex = explode("\n",$text);
        $text = str_replace(['https://','t.me/','@'],'',$ex[1]);
        if(count($ex) != 2 or IsMember("@$text",getMe()['id']) != "administrator"){
            sendMessage($user_id,"❌ کانال مورد نظر شناسایی نشد!",$message_id);
            exit;
        }
        $conn->query("UPDATE ".admin." SET {$ex_step[2]}='$text',{$ex_step[2]}_name='$ex[0]'");
        $conn->query("UPDATE ".users." SET step='settings;none' WHERE user_id='$user_id'");
        sendMessage($user_id,"✅ انجام شد.");
        panels('settings');
        exit;
    }
    if($text == "📭 پشتیبانی"){
        $conn->query("UPDATE ".users." SET step='settings;support',prev_step='settings' WHERE user_id='$user_id'");
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "یوزرنیم پشتیبان را وارد کنید:",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $panel]]]])
        ]);
        exit;
    }
    if($ex_step[1] == "support"){
        $text = str_replace(['https://','t.me/','@'],'',$text);
        $conn->query("UPDATE ".admin." SET {$ex_step[1]}='$text'");
        $conn->query("UPDATE ".users." SET step='settings;none' WHERE user_id='$user_id'");
        sendMessage($user_id,"✅ انجام شد.");
        panels('settings');
        exit;
    }
}
if($text == "/dlchj_1" || $text == "/dlchj_2" || $text == "/dlchj_3"){
    $channel = "channel_".$ex_text[1];
    $conn->query("UPDATE ".admin." SET $channel=null");
    sendMessage($user_id,"✅ انجام شد.");
    panels('settings');
    exit;
}
if($ex_step[0] == "sendmsg"){
    $row_user = infoUser($ex_step[1]);
    if(!$row_user){
        sendMessage($user_id,"❌ کاربر دریافت کننده پیام یافت نشد!",$message_id,$main_page);
        exit;
    }
    if(isset($up["message"]["text"])){
        $infoMsg = send_reply("sendMessage",[
            'chat_id' => $row_user['user_id'],
            'text' => "📧️ پیام جدید از طرف پشتیبانی\n——————————————————\n$text",
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ]);
    }
    else if(isset($up["message"]["photo"])){
        $infoMsg = send_reply("sendPhoto",[
            'chat_id' => $row_user['user_id'],
            'photo' => $up["message"]["photo"][0]["file_id"],
            'caption' => "📧️ پیام جدید از طرف پشتیبانی\n——————————————————\n{$up['message']['caption']}",
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ]);
    }
    else if((isset($up["message"]["video"]) && $msg = "video") || (isset($up["message"]["audio"]) && $msg = "audio") || (isset($up["message"]["voice"]) && $msg = "voice") || (isset($up["message"]["document"]) && $msg = "document")){
        $infoMsg = send_reply("send$msg",[
            'chat_id' => $row_user['user_id'],
            $msg => $up["message"][$msg]['file_id'],
            'caption' => "📧️ پیام جدید از طرف پشتیبانی\n——————————————————\n{$up['message']['caption']}",
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ]);
    }
    else{
        sendMessage($user_id,"❌ شما می توانید تصویر، ویدیو، موزیک، ویس، فایل ارسال کنید");
        exit;
    }
    if($infoMsg['ok'])
        sendMessage($user_id,"✅ پیام شما ارسال شد.");
    else sendMessage($user_id,"❌ مشکلی در ارسال پیام رخ داد!");
    
    $conn->query("UPDATE ".users." SET step='start' WHERE user_id='$user_id'");
    exit;
}
if($ex_text[0] == "/block"){
    $row_user = infoUser($ex_text[1]);
    if(!$row_user)
        sendMessage($user_id,"❌ یافت نشد!");
    else if($row_user['status'] == 'block')
        sendMessage($user_id,"❌ کاربر هم اکنون در لیست بلاک است!");
    else{
        $conn->query("UPDATE ".users." SET status='block' WHERE user_id='{$row_user['user_id']}'");
        sendMessage($user_id,"انجام شد ✅");
    }
    exit;
}
if($ex_text[0] == "/unblock"){
    $row_user = infoUser($ex_text[1]);
    if(!$row_user)
        sendMessage($user_id,"❌ یافت نشد!");
    else if($row_user['status'] != 'block')
        sendMessage($user_id,"❌ کاربر هم اکنون آزاد است!");
    else{
        $conn->query("UPDATE ".users." SET status='user' WHERE user_id='{$row_user['user_id']}'");
        sendMessage($user_id,"انجام شد ✅");
    }
    exit;
}
if($ex_text[0] == "/addAdmin"){
    $row_user = infoUser($ex_text[1]);
    if(!$row_user)
        sendMessage($user_id,"❌ یافت نشد!");
    else if($row_user['status'] == 'admin')
        sendMessage($user_id,"❌ کاربر هم اکنون در لیست ادمین است!");
    else{
        $conn->query("UPDATE ".users." SET status='admin' WHERE user_id='{$row_user['user_id']}'");
        sendMessage($user_id,"انجام شد ✅");
    }
    exit;
}
if($ex_text[0] == "/delAdmin"){
    
    $row_user = infoUser($ex_text[1]);
    if(!$row_user)
        sendMessage($user_id,"❌ یافت نشد!");
    else if($row_user['status'] != 'admin')
        sendMessage($user_id,"❌ کاربر هم اکنون آزاد است!");
    else{
        $conn->query("UPDATE ".users." SET status='user' WHERE user_id='{$row_user['user_id']}'");
        sendMessage($user_id,"انجام شد ✅");
    }
    exit;
}
if($text == "👥 ادمین" or $ex_data[0] == "adminListAdmin"){
    $step_page = 30;
    $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;

    $result = $conn->query("SELECT user_id FROM ".users." WHERE status='admin' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            sendMessage($user_id,"⚠️ کاربری یافت نشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT user_id FROM ".users." WHERE status='admin'")->rowCount();
    foreach($result as $row){
        $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/delAdmin_{$row['user_id']})";
        $i++;
    }
    ListShow("🔰 لیست ادمین\n\n$txts",'adminListAdmin');
    exit;
}
if($text == "👥 همه" or $ex_data[0] == "usersListAdmin"){
    $step_page = 30;
    $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT user_id FROM ".users." ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            sendMessage($user_id,"⚠️ کاربری یافت نشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT id FROM ".users)->rowCount();
    foreach($result as $row){
        $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/d_{$row['user_id']})";
        $i++;
    }
    ListShow("🔰 لیست کاربران:\n$txts\n‌",'usersListAdmin');
    exit;
}
if($text == "👥 بلاک" or $ex_data[0] == "blockListAdmin"){
    $step_page = 30;
    $data_current = isset($up["message"]["text"])?'none':$ex_data[1];
    $num_page = $data_current == 'none'?1:$data_current;
    $selected_pages = ($num_page - 1) * $step_page;
    $i = $selected_pages + 1;
    
    $result = $conn->query("SELECT user_id FROM ".users." WHERE status='block' ORDER BY id DESC LIMIT $selected_pages,$step_page")->fetchAll();
    if(!$result){
        if($data_current == 'none')
            sendMessage($user_id,"⚠️ کاربری یافت نشد.");
        else answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        exit;
    }
    $num = $conn->query("SELECT id FROM ".users." WHERE status='block'")->rowCount();
    foreach($result as $row){
        $txts .= "\n$i. <a href='tg://user?id={$row['user_id']}'>{$row['user_id']}</a> (/unblock{$row['user_id']})";
        $i++;
    }
    ListShow("🔰 لیست کاربران:\n$txts\n‌",'blockListAdmin');
    exit;
}

?>