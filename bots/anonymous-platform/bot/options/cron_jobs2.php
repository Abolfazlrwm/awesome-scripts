<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
if(!isset($_SERVER['XDG_SESSION_ID'])){
    exit;
}
$dir = __DIR__;
require_once "$dir/../../include/settings.php";
require_once "$dir/../../include/config.php";
require_once "$dir/functions.php";

$row_cron = $conn->query("SELECT * FROM ".cron."")->fetch();
if($row_cron){
    if($row_cron['send_id'] < $row_cron['max_send_id']){
        if($row_cron['file_type'] == 'message'){
            $result = $conn->query("SELECT * FROM ".users." WHERE id>{$row_cron['send_id']} AND id<={$row_cron['max_send_id']} LIMIT 100")->fetchAll();
            if(!$result){
                $conn->query("DELETE FROM ".cron." WHERE id='{$row_cron['id']}'");
            }
            else{
                foreach($result as $row){
                    if(send_reply("sendMessage",[
                        'chat_id' => $row['user_id'],
                        'text' => $row_cron['text'],
                        'parse_mode' => 'HTML'
                    ])['ok']){
                        $row_cron['send_correct'] += 1;
                    }
                    else{
                        $row_cron['send_failed'] += 1;
                    }
                }
                $conn->query("UPDATE ".cron." SET send_id='{$row['id']}' WHERE id='{$row_cron['id']}'");
            }
        }
        else if($row_cron['file_type'] == 'forward'){
            $result = $conn->query("SELECT * FROM ".users." WHERE id>{$row_cron['send_id']} AND id<={$row_cron['max_send_id']} LIMIT 100")->fetchAll();
            if(!$result){
                $conn->query("DELETE FROM ".cron." WHERE id='{$row_cron['id']}'");
            }
            else{
                foreach($result as $row){
                    if(send_reply("forwardMessage", [
                        'chat_id' => $row["user_id"],
                        'from_chat_id' => $row_cron["chat_id"],
                        'message_id' => $row_cron["message_id"]
                    ])['ok']){
                        $row_cron['send_correct'] += 1;
                    }
                    else{
                        $row_cron['send_failed'] += 1;
                    }
                }
                $conn->query("UPDATE ".cron." SET send_id='{$row['id']}' WHERE id='{$row_cron['id']}'");
            }
        }
    }
    else{
        $conn->query("DELETE FROM ".cron." WHERE id='{$row_cron['id']}'");
        send_reply("editMessageText", [
            'chat_id' => $row_cron['user_id'],
            'text' => 
                "🔰 وضعیت: اتمام عملیات\n\n".
                "✅ موفق: {$row_cron['send_correct']}\n".
                "❌ ناموفق: {$row_cron['send_failed']}\n".
                "🔅 کل : [{$row_cron['count_members']}/{$row_cron['count_members']}]\n\n".
                "❗️️ در ارسال ناموفق کاربر ربات را بلاک یا دیلیت اکانت زده.",
            'message_id' => $row_cron['message_id_edit']
        ]);
        exit;
    }
    $count_check = $row_cron['send_correct'] + $row_cron['send_failed'];
    $conn->query("UPDATE ".cron." SET count_members='{$row_cron['count_members']}',send_correct={$row_cron['send_correct']},send_failed='{$row_cron['send_failed']}' WHERE id='{$row_cron['id']}'");
    send_reply("editMessageText", [
        'chat_id' => $row_cron['user_id'],
        'text' => 
            "🔰 وضعیت: در حال ارسال ...\n\n".
            "✅ موفق: {$row_cron['send_correct']}\n".
            "❌ ناموفق: {$row_cron['send_failed']}\n".
            "🔅 کل : [$count_check/{$row_cron['count_members']}]\n\n".
            "❗️️ در ارسال ناموفق کاربر ربات را بلاک یا دیلیت اکانت زده.\n".
            "❕ هر دقیقه به 100 چت ارسال می شود.",
        'message_id' => $row_cron['message_id_edit']
    ]);
}
