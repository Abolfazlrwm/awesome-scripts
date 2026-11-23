<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
* عنوان: سورس ربات ارسال پیام انبوه تلگرام
* توسعه‌دهنده: Mr Kigga
* یوزرنیم: t.me/Mr_Kigga
* چنل: t.me/pulseXcode
*
* نکته مهم: این فایل باید توسط کرونجاب هر 1 دقیقه یکبار اجرا شود
* دستور کرونجاب نمونه:
* * * * * /usr/bin/php /path/to/this/file.php
*
* هشدار: هرگونه استفاده یا انتشار بدون ذکر منبع ممنوع است.
*/


include 'config.php';
include 'functions.php';


$admins = admins($pdo);

$stmt = $pdo->query("SELECT * FROM `send` ORDER BY id DESC LIMIT 1");
$send = $stmt->fetch(PDO::FETCH_ASSOC);

if ($send['target'] == 'users') {
    if ($send['type_send'] == 'simple') {
        $stmt = $pdo->query("SELECT COUNT(id) as total FROM `users`");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stmt = $pdo->prepare("SELECT id FROM `users` LIMIT 100 OFFSET :offset");
        $stmt->bindValue(':offset', $send['send_count'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        foreach ($users as $user) {
            if (is_null($send['media'])) {

                bot('sendmessage', [
                    'chat_id' => $user['id'],
                    'text' => $send['message']
                ]);
            } else {
                bot('sendphoto', [
                    'chat_id' => $user['id'],
                    'photo' => $send['media'],
                    'caption' => $send['message']
                ]);
            }
        }
        
        $new_offset = $send['send_count'] + 100;
        $stmt = $pdo->prepare("UPDATE `send` SET `send_count` = :new_offset WHERE id = :id");
        $stmt->execute([
            ':new_offset' => $new_offset,
            ':id' => $send['id']
        ]);
        
        if ($new_offset >= $total_users) {
            bot('sendmessage', [
                'chat_id' => $admins[0],
                'text' => "✅ پیام با موفقیت برای همه کاربران ارسال شد"
            ]);
            
            $stmt = $pdo->prepare("UPDATE `send` SET 
                `target` = 'none',
                `type_send` = 'none',
                `message` = '',
                `media` = NULL,    
                `send_count` = 0
                WHERE id = :id");
            $stmt->execute([':id' => $send['id']]);
        }
    } 
    
    if ($send['type_send'] == 'forward') {
    	$stmt = $pdo->query("SELECT COUNT(id) as total FROM `users`");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->prepare("SELECT id FROM `users` LIMIT 100 OFFSET :offset");
        $stmt->bindValue(':offset', $send['send_count'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            bot('ForwardMessage',[
            	'chat_id'=>$user['id'],   
            	'from_chat_id'=>$send['chat_id'],
            	'message_id'=>$send['message'],
            ]);	
        }
        
        $new_offset = $send['send_count'] + 100;
        $stmt = $pdo->prepare("UPDATE `send` SET `send_count` = :new_offset WHERE id = :id");
        $stmt->execute([
            ':new_offset' => $new_offset,
            ':id' => $send['id']
        ]);
        
        if ($new_offset >= $total_users) {
            bot('sendmessage', [
                'chat_id' => $admins[0],
                'text' => "✅ پیام با موفقیت برای همه کاربران ارسال شد"
            ]);
            
            $stmt = $pdo->prepare("UPDATE `send` SET 
                `target` = 'none',
                `type_send` = 'none',
                `message` = '',
                `media` = NULL,    
                `send_count` = 0
                WHERE id = :id");
            $stmt->execute([':id' => $send['id']]);
        }
    }
}

if ($send['target'] == 'groups') {
    if ($send['type_send'] == 'simple') {
        $stmt = $pdo->query("SELECT COUNT(id) as total FROM `groups`");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $pdo->prepare("SELECT id FROM `groups` LIMIT 100 OFFSET :offset");
        $stmt->bindValue(':offset', $send['send_count'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        foreach ($users as $user) {
            if (is_null($send['media'])) {

                bot('sendmessage', [
                    'chat_id' => $user['id'],
                    'text' => $send['message']
                ]);
            } else {
                bot('sendphoto', [
                    'chat_id' => $user['id'],
                    'photo' => $send['media'],
                    'caption' => $send['message']
                ]);
            }
        }
        
        $new_offset = $send['send_count'] + 100;
        $stmt = $pdo->prepare("UPDATE `send` SET `send_count` = :new_offset WHERE id = :id");
        $stmt->execute([
            ':new_offset' => $new_offset,
            ':id' => $send['id']
        ]);
        
        if ($new_offset >= $total_users) {
            bot('sendmessage', [
                'chat_id' => $admins[0],
                'text' => "✅ پیام با موفقیت برای همه گروه ها ارسال شد"
            ]);
            
            $stmt = $pdo->prepare("UPDATE `send` SET 
                `target` = 'none',
                `type_send` = 'none',
                `message` = '',
                `media` = NULL,    
                `send_count` = 0
                WHERE id = :id");
            $stmt->execute([':id' => $send['id']]);
        }
    } 
    
    if ($send['type_send'] == 'forward') {
    	$stmt = $pdo->query("SELECT COUNT(id) as total FROM `groups`");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->prepare("SELECT id FROM `groups` LIMIT 100 OFFSET :offset");
        $stmt->bindValue(':offset', $send['send_count'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            bot('ForwardMessage',[
            	'chat_id'=>$user['id'],   
            	'from_chat_id'=>$send['chat_id'],
            	'message_id'=>$send['message'],
            ]);	
        }
        
        $new_offset = $send['send_count'] + 100;
        $stmt = $pdo->prepare("UPDATE `send` SET `send_count` = :new_offset WHERE id = :id");
        $stmt->execute([
            ':new_offset' => $new_offset,
            ':id' => $send['id']
        ]);
        
        if ($new_offset >= $total_users) {
            bot('sendmessage', [
                'chat_id' => $admins[0],
                'text' => "✅ پیام با موفقیت برای همه گروه ها ارسال شد"
            ]);
            
            $stmt = $pdo->prepare("UPDATE `send` SET 
                `target` = 'none',
                `type_send` = 'none',
                `message` = '',
                `media` = NULL,    
                `send_count` = 0
                WHERE id = :id");
            $stmt->execute([':id' => $send['id']]);
        }
    }
}

/*
* پایان سورس ربات تلگرامی دانلود از پینترسته
* توسعه‌دهنده: Mr Kigga
* تلگرام: t.me/Mr_Kigga
* چنل: t.me/pulseXcode
*
* هشدار: هرگونه کپی‌برداری یا انتشار بدون ذکر منبع پیگرد قانونی دارد.
* این سورس متعلق به سازنده اصلی است و هیچ‌کس حق ندارد آن را به نام خود منتشر کند.
*/
?>