<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */


function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function sendmessage($chat_id,$text,$keyboard = null) {
    bot('sendMessage',[
        'chat_id' => $chat_id,
        'reply_to_message_id' => $message_id,
        'text' => $text,
        
        'parse_mode' => "HTML",
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard,
                'parse_mode' => 'markdown',

    ]);
}

function editMessage($chat_id, $message_id, $text, $reply_markup = null) {
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => 'markdown',
        'reply_markup' => $reply_markup,
    ]);
}

function savegroup($chat_id) {
    $groups = [];
    if (file_exists(GROUPS)) {
        $groups = json_decode(file_get_contents(GROUPS), true);
    }

    if (!in_array($chat_id, $groups)) {
        $groups[] = $chat_id;
        file_put_contents(GROUPS, json_encode($groups));
    }
}

// تابع حذف آیدی گروه از فایل
function hazfgroup($chat_id) {
    if (file_exists(GROUPS)) {
        $groups = json_decode(file_get_contents(GROUPS), true);
        $index = array_search($chat_id, $groups);
        if ($index !== false) {
            unset($groups[$index]);
            file_put_contents(GROUPS, json_encode(array_values($groups)));
        }
    }
}


function saveadminstate($user_id, $state) {
    $admin_state = [];
    if (file_exists(ADMIN_STATE_FILE)) {
        $admin_state = json_decode(file_get_contents(ADMIN_STATE_FILE), true);
    }

    $admin_state[$user_id] = $state;
    file_put_contents(ADMIN_STATE_FILE, json_encode($admin_state));
}

function getAdminState($user_id) {
    if (file_exists(ADMIN_STATE_FILE)) {
        $admin_state = json_decode(file_get_contents(ADMIN_STATE_FILE), true);
        if (isset($admin_state[$user_id])) {
            return $admin_state[$user_id];
        }
    }
    return null;
}
function hazfadminstate($user_id) {
    if (file_exists(ADMIN_STATE_FILE)) {
        $admin_state = json_decode(file_get_contents(ADMIN_STATE_FILE), true);
        if (isset($admin_state[$user_id])) {
            unset($admin_state[$user_id]);
            file_put_contents(ADMIN_STATE_FILE, json_encode($admin_state));
        }
    }
}

?>