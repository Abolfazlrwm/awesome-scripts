<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

$row_chats = $conn->query("SELECT * FROM ".chats." WHERE (user_id_1='$user_id' OR user_id_2='$user_id') AND status='chatting'")->fetch();
if($row_chats){
    $reply_to_message_id = null;
    $user_id_select = $row_chats['user_id_1'] == $user_id?$row_chats['user_id_2']:$row_chats['user_id_1'];
    $row = infoUser($user_id_select);
    if($text == $end_chat){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "🤖 پیام سیستم 👇\n\n".
                "مطمئنی میخوای چت رو قطع کنی؟",
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [
                [
                    ['text' => "❌ اتمام چت",'callback_data' => "endchat;{$row['uniq_id']};end"],
                    ['text' => "🗣 ادامه چت",'callback_data' => "endchat;{$row['uniq_id']};continue"]
                ]
            ]])
        ]);
        exit;
    }
    if($ex_data[0] == 'endchat'){
        if($ex_data[2] == 'end'){
            $conn->query("UPDATE ".chats." SET status='end' WHERE id='{$row_chats['id']}'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' => "🎌 چت شما با /user_{$row['uniq_id']} توسط شما قطع شد.\n\n".
                    "برای گزارش عدم رعایت قوانین (/ghavanin) می توانید با لمس 《 🚫 گزارش کاربر 》 در پروفایل، کاربر را گزارش کنید.",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            send_reply("sendMessage",[
                'chat_id' => $row['user_id'],
                'text' => "🎌 چت شما با /user_{$row_users['uniq_id']} توسط مخاطب شما قطع شد.\n\n".
                    "برای گزارش عدم رعایت قوانین (/ghavanin) می توانید با لمس 《 🚫 گزارش کاربر 》 در پروفایل، کاربر را گزارش کنید.",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            $result = $conn->query("SELECT * FROM ".notif." WHERE user_id_2='{$row['user_id']}' AND reason='endchatnotif'")->fetchAll();
            foreach($result as $row){
                send_reply("sendMessage",['chat_id' => $row['user_id'],'text' => "🔔 هم اکنون چت کاربر /user_{$row['uniq_id']} به پایان رسید."]);
                $conn->query("DELETE FROM ".notif." WHERE id='{$row['id']}'");
            }
            $result = $conn->query("SELECT * FROM ".notif." WHERE user_id_2='$user_id' AND reason='endchatnotif'")->fetchAll();
            foreach($result as $row){
                send_reply("sendMessage",['chat_id' => $row['user_id'],'text' => "🔔 هم اکنون چت کاربر /user_{$row_users['uniq_id']} به پایان رسید.",]);
                $conn->query("DELETE FROM ".notif." WHERE id='{$row['id']}'");
            }
        }
        send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $message_id]);
        exit;
    }
    if($text == "/start"){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "🤖 پیام سیستم 👇\n\n".
                "<code>    - ❗️اول باید مکالمه جاری رو قطع کنی بعدا 《استارت》 بزنی 👇</code>",
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        exit;
    }
    if(in_array($text,$btns_bot) or isset($up["callback_query"]["data"])){
        send_reply("sendMessage",[
            'chat_id' => $user_id,
            'text' => "⚠️ خطا: هم اکنون شما در حال چت با /user_{$row['uniq_id']} هستید !\n\n".
                "<code>برای استفاده از ربات ابتدا باید مکالمه رو قطع کنی 👇</code>",
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [[['text' => $show_profile]],[['text' => $end_chat]]]])
        ]);
        exit;
    }
    if($GLOBALS['entities']){
        foreach($GLOBALS['entities'] as $entity){
            if($entity['type'] == 'url' or $entity['type'] == 'text_link' or $entity['type'] == 'email' or $entity['type'] == 'mention' or $entity['type'] == 'text_mention'){
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "🤖 پیام سیستم 👇\n\n".
                        "⚠️ خطا: امکان ارسال لینک وجود ندارد.\n\n".
                        "<code>- برای ارسال لینک یا آیدی تلگرام از پیام دایرکت استفاده کنید.</code>",
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => $message_id
                ]);
                exit;
            }
        }
    }
    if(isset($up["message"]["reply_to_message"])){
        $r_message_id = $up["message"]["reply_to_message"]["message_id"];
        $row_chatmsg = $conn->query("SELECT * FROM ".chatmsgs." WHERE user_id_1='$user_id' AND message_id_1='{$r_message_id}'")->fetch();
        if($row_chatmsg){
            if($text == "حذف"){
                send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $r_message_id]);
                send_reply("deleteMessage",['chat_id' => $row_chatmsg['user_id_2'],'message_id' => $row_chatmsg['message_id_2']]);
                send_reply("sendMessage",[
                    'chat_id' => $user_id,
                    'text' => "✔️ با موفقیت حذف شد.",
                    'reply_to_message_id' => $message_id
                ]);
                exit;
            }
            $row['user_id'] = $row_chatmsg['user_id_2'];
            $reply_to_message_id = $row_chatmsg['message_id_2'];
        }
        else{
            $row_chatmsg = $conn->query("SELECT * FROM ".chatmsgs." WHERE user_id_2='$user_id' AND message_id_2='{$r_message_id}'")->fetch();
            if($row_chatmsg){
                if($text == "حذف"){
                    send_reply("deleteMessage",['chat_id' => $user_id,'message_id' => $r_message_id]);
                    send_reply("deleteMessage",['chat_id' => $row_chatmsg['user_id_1'],'message_id' => $row_chatmsg['message_id_1']]);
                    send_reply("sendMessage",[
                        'chat_id' => $user_id,
                        'text' => "✔️ با موفقیت حذف شد.",
                        'reply_to_message_id' => $message_id
                    ]);
                    exit;
                }
                $row['user_id'] = $row_chatmsg['user_id_1'];
                $reply_to_message_id = $row_chatmsg['message_id_1'];
            }
        }
    }
    if(isset($up["edited_message"])){
        $row_chatmsg = $conn->query("SELECT * FROM ".chatmsgs." WHERE user_id_1='$user_id' AND message_id_1='{$message_id}'")->fetch();
        if(isset($up["edited_message"]['text'])){
            send_reply("editMessageText",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'text' => "$text\n\n".
                    "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")",
                'message_id' => $row_chatmsg['message_id_2']
            ]);
            exit;
        }
        else if(isset($up["edited_message"]['photo'])){
            send_reply("editMessageMedia",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'media' => json_encode([
                    'type' => 'photo',
                    'media' => $up["edited_message"]["photo"][count($up["edited_message"]["photo"]) - 1]["file_id"],
                    'caption' =>  $up["edited_message"]["caption"]."\n\n".
                        "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")"
                ]),
                'message_id' => $row_chatmsg['message_id_2'],
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
            ]);
            exit;
        }
        else if(isset($up["edited_message"]['audio'])){
            send_reply("editMessageMedia",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'media' => json_encode([
                    'type' => 'audio',
                    'media' => $up["edited_message"]["audio"]["file_id"],
                    'caption' =>  $up["edited_message"]["caption"]."\n\n".
                        "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")"
                ]),
                'message_id' => $row_chatmsg['message_id_2'],
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
            ]);
            exit;
        }
        else if(isset($up["edited_message"]['video'])){
            send_reply("editMessageMedia",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'media' => json_encode([
                    'type' => 'video',
                    'media' => $up["edited_message"]["video"]["file_id"],
                    'caption' =>  $up["edited_message"]["caption"]."\n\n".
                        "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")"
                ]),
                'message_id' => $row_chatmsg['message_id_2'],
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
            ]);
            exit;
        }
        else if(isset($up["edited_message"]['animation'])){
            send_reply("editMessageMedia",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'media' => json_encode([
                    'type' => 'animation',
                    'media' => $up["edited_message"]["animation"]["file_id"],
                    'caption' =>  $up["edited_message"]["caption"]."\n\n".
                        "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")"
                ]),
                'message_id' => $row_chatmsg['message_id_2'],
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
            ]);
            exit;
        }
        else if(isset($up["edited_message"]['document'])){
            send_reply("editMessageMedia",[
                'chat_id' => $row_chatmsg['user_id_2'],
                'media' => json_encode([
                    'type' => 'document',
                    'media' => $up["edited_message"]["document"]["file_id"],
                    'caption' =>  $up["edited_message"]["caption"]."\n\n".
                        "📝 ویرایش شده در (".to_english(jdate("Y-m-d H:i")).")"
                ]),
                'message_id' => $row_chatmsg['message_id_2'],
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
            ]);
            exit;
        }
        exit;
    }
    if(isset($up["message"]["text"])){
        $info_msg = send_reply("sendMessage",[
            'chat_id' => $row['user_id'],
            'text' => $text,
            'reply_to_message_id' => $reply_to_message_id
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["photo"])){
        $info_msg = send_reply("sendphoto",[
            'chat_id' => $row['user_id'],
            'photo' => $up["message"]["photo"][count($up["message"]["photo"]) - 1]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["audio"])){
        $info_msg = send_reply("sendaudio",[
            'chat_id' => $row['user_id'],
            'audio' => $up["message"]["audio"]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["animation"])){
        $info_msg = send_reply("sendAnimation",[
            'chat_id' => $row['user_id'],
            'animation' => $up["message"]["animation"]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["document"])){
        $info_msg = send_reply("senddocument",[
            'chat_id' => $row['user_id'],
            'document' => $up["message"]["document"]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["voice"])){
        $info_msg = send_reply("sendVoice",[
            'chat_id' => $row['user_id'],
            'voice' => $up["message"]["voice"]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["contact"])){
        $info_msg = send_reply("sendcontact",[
            'chat_id' => $row['user_id'],
            'phone_number' => $up["message"]["contact"]["phone_number"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["location"])){
        $info_msg = send_reply("sendlocation",[
            'chat_id' => $row['user_id'],
            'latitude' => $up["message"]["location"]["latitude"],
            'longitude' => $up["message"]["location"]["longitude"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    if(isset($up["message"]["sticker"])){
        $info_msg = send_reply("sendsticker",[
            'chat_id' => $row['user_id'],
            'sticker' => $up["message"]["sticker"]["file_id"],
            'caption' => $up["message"]["caption"],
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⚠️ گزارش",'callback_data' => "gib;report;{$row['uniq_id']};repchat"]]]])
        ]);
        $conn->query("INSERT INTO ".chatmsgs." (user_id_1,message_id_1,user_id_2,message_id_2,created_at) VALUES ('$user_id','$message_id','{$row['user_id']}','{$info_msg['result']['message_id']}','$time')");
        exit;
    }
    exit;
}
?>