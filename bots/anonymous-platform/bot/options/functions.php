<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

/// @Sourrce_kade
function send_reply($method,$post_params = []){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.telegram.org/bot{$GLOBALS['settings']['token']}/".$method);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_params);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function getMe(){
    return send_reply("getMe")['result'];
}
function mainMenu($just_keyboard=false,$just_text=false){
    $keyboard = [
        [['text' => '🔗 به یه ناشناس وصلم کن!️']],
        [['text' => '🔍 جستجوی کاربران 🔎'],['text' => '📍افراد نزدیک']],
        [['text' => '💰سکه'],['text' => '👤پروفایل'],['text' => '🤔راهنما']],
        [['text' => '🚸 معرفی به دوستان (سکه رایگان)']],
        [['text' => ($GLOBALS['user_id'] == $GLOBALS['settings']['admin_id'] || $GLOBALS['row_users']['status'] == 'admin')?'👤 پنل ادمین 👤':'']]
    ];
    $GLOBALS['conn']->query("UPDATE ".users." SET step='start',prev_step='start' WHERE user_id='{$GLOBALS['user_id']}'");
    if($just_keyboard)
        return $keyboard;
    $text = "خب ، حالا چه کاری برات انجام بدم؟\n\n".
        "<code>از منوی پایین👇 انتخاب کن</code>";
    if($just_text)
        return $text;
    send_reply("sendMessage", [
        'chat_id' => $GLOBALS['user_id'],
        'text' => $text,
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $GLOBALS['message_id'],
        'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $keyboard])
    ]);
}
function panels($type){
    $user_id = $GLOBALS['user_id'];
    if($type == "admin"){
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "پنل ادمین:\n\n".
                "⭕️ جزئیات کاربر:\n".
                "/d_{$user_id}",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [
                [['text' => '⚙️ تنظیمات'],['text' => '👥 کاربران'],['text' => '📊 آمار']],
                [['text' => '💳 تراکنش ها'],['text' => '💰 تعرفه ها']],
                [['text' => $GLOBALS['main']]]
            ]])
        ]);
        return;
    }
    if($type == 'settings'){
        $row_admin = $GLOBALS['conn']->query("SELECT * FROM ".admin)->fetch();
        $msgs = [];
        $msgs['channel_1'] = $row_admin['channel_1'] == null?"ثبت نشده":"@{$row_admin['channel_1']}\nحذف کانال ثبت شده = /dlchj_1";
        $msgs['channel_2'] = $row_admin['channel_2'] == null?"ثبت نشده":"@{$row_admin['channel_2']}\nحذف کانال ثبت شده = /dlchj_2";
        $msgs['ch_cache_id'] = $row_admin['ch_cache_id'] == null?"ثبت نشده":"{$row_admin['ch_cache_id']}";
        $msgs['support'] = $row_admin['support'] == null?"ثبت نشده":"@{$row_admin['support']}";
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "پنل ادمین » تنظیمات:\n\n".
                "کانال جوین اجباری 1: {$msgs['channel_1']}\n\n".
                "کانال جوین اجباری 2: {$msgs['channel_2']}\n\n".
                "کانال کش: {$msgs['ch_cache_id']}\n".
                "پشتیبانی: {$msgs['support']}",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => [
                [['text' => '⚙️ اجباری 2'],['text' => '⚙️ اجباری 1']],
                [['text' => '📭 پشتیبانی'],['text' => '🗑 کانال کش']],
                [['text' => $GLOBALS['back']],['text' => $GLOBALS['panel']]]
            ]])
        ]);
        return;
    }
    if($type == "tariff"){
        $result = $GLOBALS['conn']->query("SELECT * FROM ".amounts." ORDER BY amount ASC")->fetchAll();
        $reply_markup = [];
        $i = $j = 0;
        foreach($result as $row){
            if($j %2 == 0 && $j > 0)
                $i++;
            $reply_markup[$i][] = ['text' => $row['coin']];
            $j++;
        }
        $reply_markup[][] = "افزودن تعرفه ➕";
        $reply_markup[][] = $GLOBALS['panel'];
        $GLOBALS['conn']->query("UPDATE ".users." SET step='set_vip;check' WHERE user_id='$user_id'");
        send_reply("sendMessage", [
            'chat_id' => $user_id,
            'text' => "پنل ادمین » تعرفه ها:\n\n".
                "❗️ با کلیک بر روی هر کدام آن ها را حذف کنید یا تعرفه جدید اضافه کنید.",
            'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => $reply_markup])
        ]);
        return;
    }
}
function keboards($type){
    if($type == 'search'){
        return [
            [['text' => '👥 هم سنی ها','callback_data' => 'search;sage;none'],['text' => '🎌 هم استانی ها','callback_data' => 'search;sstate;none']],
            [['text' => '🔍 جستجوی پیشرفته 🔎','callback_data' => 'searchadv;none']],
            [['text' => '🚶‍♂️بدون چت ها🚶‍♀️','callback_data' => 'search;wchat;none'],['text' => '🙋‍♂️ کاربران جدید 🙋‍♀️','callback_data' => 'search;nuser;none']],
        ];
    }
    if($type == 'coin'){
        $result = $GLOBALS['conn']->query("SELECT * FROM ".amounts." ORDER BY amount ASC")->fetchAll();
        $reply_markup = [[['text' => "🚸 معرفی به دوستان (سکه رایگان)",'callback_data' => 'invite']]];
        $i = $j = 0;
        foreach($result as $key => $row){
            if($j %2 == 0 && $j > 0)
                $i++;
            $reply_markup[] = [['text' => "{$row['coin']} سکه ".number_format($row['amount'])." تومان",'callback_data' => "buy_coin;{$row['id']}"]];
            $j++;
        }
        return $reply_markup;
    }
}
function states_select_adv($array,$location){
    $output = "";
    if($location)
        $output = "📍افراد نزدیک من، ";
    foreach($array as $value){
        $output .= urldecode($value)."، ";
    }
    $output = mb_substr($output,0,-2);
    return $output;
}
function userInfoList($conn,$result){
    $i = $GLOBALS['i'];
    $row_users = $GLOBALS['row_users'];
    $txts = "";
    foreach($result as $row){
        $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        $where = (!$row_user_select['latitude'] or !$row_users['latitude'])?"":"(SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.")";
        $row_user_select = $conn->query("SELECT * FROM $where ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        
        $status_chat = (substr($row_user_select['step'],0,9) == "chatting;")?" (در حال چت)":"";
        $d = $row_users['latitude']?" (🏁 ".number_format($row_user_select['distance'],1)."km)":"";
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $row_user_select['name'] = $row_user_select['name'] == null?"❓":$row_user_select['name'];
        
        $txts .= "‏$i. {$GLOBALS['status_gender_emoji'][$row_user_select['gender']]} {$row_user_select['name']} /user_{$row_user_select['uniq_id']}\n";
        $txts .= "<code>{$row_user_select['age']} {$row_user_select['state']}$city</code>$d\n";
        $txts .= "<code>".LastActivity($row_user_select['last_activity'])."$status_chat</code>\n";
        $txts .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️\n";
        $i++;
    }
    return $txts;
}
function userInfoInlineList($conn,$result){
    $link = $GLOBALS['link'];
    $row_users = $GLOBALS['row_users'];
    $results = [];
    $txts = "";
    foreach($result as $row){
        $txts = "";
        $row_user_select = $conn->query("SELECT * FROM ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        $where = (!$row_user_select['latitude'] or !$row_users['latitude'])?"":"(SELECT *, (((acos(sin(( {$row_users['latitude']} * pi() / 180)) * sin(( latitude * pi() / 180)) + cos(( {$row_users['latitude']} * pi() /180 )) *cos(( latitude * pi() / 180)) * cos((( {$row_users['longitude']} - longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM ".users.")";
        $row_user_select = $conn->query("SELECT * FROM $where ".users." WHERE user_id='{$row['user_id']}'")->fetch();
        
        $status_chat = (substr($row_user_select['step'],0,9) == "chatting;")?" (در حال چت)":"";
        $d = (!$row_users['latitude'] or !$row_user_select['latitude'])?"":" (🏁 ".number_format($row_user_select['distance'],1)."km)";
        $city = $row_user_select['city'] == null?"":" ({$row_user_select['city']})";
        $row_user_select['name'] = $row_user_select['name'] == null?"❓":$row_user_select['name'];

        $txts .= "{$row_user_select['age']} {$row_user_select['state']}$city{$d}\n";
        $txts .= "".LastActivity($row_user_select['last_activity'])."$status_chat\n";
        $results[] = [
            'type' => 'article',
            'id' => $row['id'],
            'title' => "‏{$GLOBALS['status_gender_emoji'][$row_user_select['gender']]} {$row_user_select['name']}",
            'thumb_url' => ($row_user_select['image'] == null?"$link/files/noimage-{$row_user_select['gender']}.jpg":"$link/images/{$row_user_select['uniq_id']}.jpg"),
            'description' => $txts,
            'parse_mode' => 'HTML',
            'input_message_content' => [
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                'message_text' => "/user_{$row_user_select['uniq_id']}"
            ],
        ];
    }
    return $results;
}
function to_persian($str){
    return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], $str);
}
function to_english($str){
    return str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $str);
}
function FileDownload($file_id,$path_save = false){
    $info_msg = send_reply("getFile",['file_id' => $file_id]);
    if(!$info_msg['ok'])
        return false;
    $url = "https://api.telegram.org/file/bot{$GLOBALS['settings']['token']}/{$info_msg['result']['file_path']}";
    if(!$path_save)
        return $url;
    $fp = fopen($path_save,'w+');
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_FILE,$fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return true;
}
function infoUser($value){
    return $GLOBALS['conn']->query("SELECT * FROM ".users." WHERE user_id='$value' OR uniq_id='$value'")->fetch();
}
function generateInlineButtons($uniq_id){
    $user_id = $GLOBALS['user_id'];
    $conn = $GLOBALS['conn'];
    $row = infoUser($uniq_id);
    if($conn->query("SELECT id FROM ".blocked." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'")->fetch())
        $block = "🔐 آنبلاک کردن کاربر";
    else $block = "🔒 بلاک کردن کاربر";
    if(!$conn->query("SELECT id FROM ".friends." WHERE user_id='$user_id' AND target_id='{$row['user_id']}'")->fetch())
        $contact = "➕افزودن به مخاطبین";
    else $contact = "❌حذف از مخاطبین";
    
    $reply_markup = [];
    if($row['is_likes']){
        $count = $conn->query("SELECT id FROM ".likes." WHERE target_id='{$row['user_id']}'")->rowCount();
        $reply_markup[] = [['text' => "Like ❤️ $count",'callback_data' => "gib;likes;$uniq_id;none"]];
    }
    $reply_markup[] = [
        ['text' => "📨 پیام دایرکت",'callback_data' => "gib;directMessage;$uniq_id;none"],
        ['text' => "💬 درخواست چت",'callback_data' => "gib;requestChat;$uniq_id;none"]
    ];
    $reply_markup[] = [
        ['text' => $block,'callback_data' => "gib;block;$uniq_id;none"],
        ['text' => $contact,'callback_data' => "gib;friend;$uniq_id;none"]
    ];
    $reply_markup[] = [['text' => "🚫 گزارش کاربر",'callback_data' => "gib;report;$uniq_id;none"]];
    
    if($conn->query("SELECT * FROM ".chats." WHERE (user_id_1='{$row['user_id']}' OR user_id_2='{$row['user_id']}') AND status='chatting'")->fetch())
        $reply_markup[] = [['text' => "🔔 به محض اتمام چت اطلاع بده",'callback_data' => "gib;endchatnotif;$uniq_id;none"]];
    else if(($GLOBALS['time'] - $row['last_activity']) > 900)
        $reply_markup[] = [['text' => "🔔 به محض آنلاین شدن اطلاع بده",'callback_data' => "gib;onlinenotif;$uniq_id;none"]];
        
    return $reply_markup;
}
function webhook($token,$method,$post_params=[]){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.telegram.org/bot$token/".$method);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_params);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function randomNumber($a,$table){
    $t = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    $t = preg_split('//u', $t);
    unset($t[0]);
    unset($t[count($t)]);
    $x = '';
    for($i = count($t) - $a; $i < count($t); $i++){
        $z = array_rand($t);
        $x .= $t[$z];
    }
    if(!$GLOBALS['conn']->query("SELECT id FROM $table WHERE uniq_id='$x'")->fetch())return $x;
    else randomNumber($a,$table);
}
function IsMember($channel,$user_id){
    return send_reply("getChatMember", ['chat_id' => $channel, 'user_id' => $user_id])["result"]["status"];
}
function answerCallbackQuery($text){
    send_reply("answerCallbackQuery", ['callback_query_id' => $GLOBALS['cq_id'],'text' => $text,'show_alert' => true]);
}
function checkEntities($action){
    if(!$GLOBALS['entities'])
        return false;
    foreach($GLOBALS['entities'] as $entity){
        if($action == "link" and ($entity['type'] == 'url' or $entity['type'] == 'text_link' or $entity['type'] == 'email'))
            return true;
        else if($action == "username" and ($entity['type'] == 'mention' or $entity['type'] == 'text_mention'))
            return true;
        else if($action == "hashtag" and $entity['type'] == 'hashtag')
            return true;
        else if($action == "commands" and $entity['type'] == 'bot_command')
            return true;
        else if($action == "contact" and $entity['type'] == 'phone_number')
            return true;
    }
    return false;
}
function checkInout($value){
    if(!$value or $value == null)
        return "❓";
    else return $value;
}
function checkProfileCoin($user_id){
    $row_users = $GLOBALS['conn']->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
    if(!$row_users['is_coin_comprof']){
        $row_admin = $GLOBALS['row_admin'];
        if($row_users['name'] != null and $row_users['city'] != null and $row_users['image'] != null and $row_users['latitude']){
            $row_users['balance'] += $row_admin['coin_comprof'];
            $GLOBALS['conn']->query("UPDATE ".users." SET balance={$row_users['balance']},is_coin_comprof=1 WHERE user_id='$user_id'");
            send_reply("sendMessage",[
                'chat_id' => $user_id,
                'text' =>
                    "🔔 تبریک !\n\n".
                    "شما {$row_admin['coin_comprof']} سکه بابت تکمیل کردن پروفایل دریافت کردید !\n\n".
                    "💰سکه فعلی شما : {$row_users['balance']}",
                'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
            ]);
            if($row_users['referral'] != null){
                $GLOBALS['conn']->query("UPDATE ".users." SET balance=balance+{$row_admin['coin_per_invite_profile']} WHERE user_id='{$row_users['referral']}'");
                $row_user_select = $GLOBALS['conn']->query("SELECT * FROM ".users." WHERE user_id='{$row_users['referral']}'")->fetch();
                send_reply("sendMessage",[
                    'chat_id' => $row_user_select['user_id'],
                    'text' =>
                        "🔔 تبریک ! شما {$row_admin['coin_per_invite_profile']} سکه بابت تکمیل شدن پروفایل کاربری که توسط شما معرفی شده بود دریافت کردید.\n\n".
                        "💰سکه فعلی شما : {$row_user_select['balance']}",
                    'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "👥 معرفی افراد بیشتر",'callback_data' => 'invite']]]])
                ]);
            }
            exit;
        }
    }
}
function completeProfile($row_users){
    $count = 0;
    $output = "";
    if($row_users['name'] == null){
        $output .= "نام , ";
        $inline_keyboard = [[['text' => '👤 تکمیل پروفایـــل','callback_data' => 'profile;name;complete']]];
        $count++;
    }
    if($row_users['city'] == null){
        if($output == "")
            $inline_keyboard = [[['text' => '👤 تکمیل پروفایـــل','callback_data' => 'profile;city;complete']]];
        $output .= "شهر , ";
        $count++;
    }
    if($row_users['image'] == null){
        if($output == "")
            $inline_keyboard = [[['text' => '👤 تکمیل پروفایـــل','callback_data' => 'profile;image;complete']]];
        $output .= "عکس پروفایل , ";
        $count++;
    }
    if(!$row_users['latitude']){
        if($output == "")
            $inline_keyboard = [[['text' => '👤 تکمیل پروفایـــل','callback_data' => 'profile;gps;complete']]];
        $output .= "موقعیت مکانی , ";
        $count++;
    }
    if($output != "")
        return ['count' => $count,'inline_keyboard' => $inline_keyboard,'info' => substr($output,0,-3)];
    return false;
}
function LastActivity($time_activity){
    $time = $GLOBALS['time'];
    if(($time_activity + 60) > $time)
        return "هم اکنون 👀 آنلایـــن";
    else if($time_activity < ($time - 60) && $time_activity > ($time - 3600))
        return "⏳ ".ceil(($time - $time_activity) / 60)." دقیقه قبل آنلاین بوده";
    else if($time_activity < ($time - 3600) && $time_activity > ($time - 86400))
        return "⏳ ".ceil(($time - $time_activity) / 3600)." ساعت قبل آنلاین بوده";
    else if($time_activity < ($time - 86400))
        return "⏳ ".ceil(($time - $time_activity) / 86400)." روز قبل آنلاین بوده";
}
function sendMessage($user_id,$text,$message_id = false,$text_btn = false){
    
    $array = ['chat_id' => $user_id,'text' => $text,'parse_mode' => 'html','disable_web_page_preview' => true,'reply_to_message_id' => $message_id];
    if($text_btn)
        $array['reply_markup'] = json_encode(['resize_keyboard' => true,'keyboard' => [[$text_btn]]]);
    return send_reply("sendMessage", $array);
}
function ListShow($txts,$markup,$keyboard_first = false,$keyboard_end = false){
    $num = $GLOBALS['num'];
    $step_page = $GLOBALS['step_page'];
    $num_page = $GLOBALS['num_page'];
    $prev_page = $num_page - 1;
    $next_page = $num_page + 1;

    $inline_keyboard = [];
    if($keyboard_first){
        $inline_keyboard[] = $keyboard_first;
    }
    if($GLOBALS['data_current'] == 'none' or !isset($GLOBALS['up']['callback_query']['data'])){
        $inline_keyboard[] = [['text' => "➡️ مشاهده ادامه لیست", 'callback_data' => "$markup;2"]];
        if($keyboard_end)
            $inline_keyboard[] = $keyboard_end;
        return send_reply("sendMessage", [
            'chat_id' => $GLOBALS['user_id'],
            //'text' => "num= $num"." \n step_page= $step_page"."\n num_page= $num_page \n". $txts . "\n <pre>" . json_encode($inline_keyboard,JSON_UNESCAPED_UNICODE ) . "</pre>",
            'text' => $txts,
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard]),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ]);
    }
    else{
        if($step_page >= $num){
            return answerCallbackQuery("⚠️ صفحه دیگری وجود ندارد.");
        }
        if($num_page == 1){
            $inline_keyboard[] = [['text' => "➡️ مشاهده ادامه لیست", 'callback_data' => "$markup;$next_page"]];
        }
        else if($num > ($num_page * $step_page)){
            $inline_keyboard[] = [['text' => "لیست قبلی ⬅️", 'callback_data' => "$markup;$prev_page"],['text' => "➡️ لیست بعدی", 'callback_data' => "$markup;$next_page"]];
        }
        else{
            $inline_keyboard[] = [['text' => "لیست قبلی ⬅️", 'callback_data' => "$markup;$prev_page"]];
        }
        if($keyboard_end){
            $inline_keyboard[] = $keyboard_end;
        }
    }
    // این موقع تعویض صفحه پیامو ادیت میکنه
    return send_reply("editMessageText", [
        'chat_id' => $GLOBALS['user_id'],
        'text' => $txts,
        //'text' => "num= $num"." \n step_page= $step_page"."\n num_page= $num_page \n". $txts . "\n <pre>" . json_encode($inline_keyboard,JSON_UNESCAPED_UNICODE ) . "</pre>",
        'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard]),
        'message_id' => $GLOBALS['message_id'],
        'parse_mode' => 'html',
        'disable_web_page_preview' => true
    ]);
}
function AddChange($table,$column,$info,$is_unique = false){
    $GLOBALS['conn']->query("ALTER TABLE $table ADD $column $info");
    $GLOBALS['conn']->query("ALTER TABLE $table CHANGE $column $column $info");
    if($is_unique)
        $GLOBALS['conn']->query("ALTER TABLE $table ADD UNIQUE KEY $column ($column)");
}
function CreateTable($table,$type = "INT"){
    $GLOBALS['conn']->query("CREATE TABLE IF NOT EXISTS $table (id $type NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    
    AddChange($table,"id","$type NOT NULL FIRST");
    $GLOBALS['conn']->query("ALTER TABLE $table ADD PRIMARY KEY (id)");
    $GLOBALS['conn']->query("ALTER TABLE $table MODIFY id $type NOT NULL AUTO_INCREMENT");
}

?>