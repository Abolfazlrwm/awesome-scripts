<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if($text == "/start" || $text == $main || $data == "start" || ($text == $back && ($row_users['prev_step'] == 'start' || $row_users['prev_step'] == 'complete_profile' || $ex_step[0] == 'connect' || ($ex_step[0] == 'profile' && $ex_step[1] == 'none') || $ex_step[1] == 'selectGender'))){
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
if($user_id == $settings['admin_id'] || $row_users['status'] == "admin"){
    require_once 'admin.php';
}
?>