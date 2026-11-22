<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
try{
    require_once '../include/settings.php';
    require_once '../include/config.php';
    require_once '../bot/options/functions.php';
    
    if($status_payment_gateway == "zarinpanl" and isset($_GET['Authority']) and isset($_GET['Status']) and htmlspecialchars($_GET["Status"]) == "OK"){
        $authority = htmlspecialchars($_GET['Authority']);
        $row_payments = $conn->query("SELECT * FROM ".payments." WHERE authority='$authority' AND status='move_gateway'")->fetch();
        if(!$row_payments){
            echo "خطایی رخ داد !\ncode = 131";
            exit;
        }
        
        $data = json_encode(["merchant_id" => $settings['merchant_id'], "authority" => $authority, "amount" => $row_payments['amount'] * 10]);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Content-Length: ' . strlen($data)]);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);

        if($result['data']['code'] == 100 || $result['data']['code'] == 101){
            $conn->query("UPDATE ".payments." SET ref_id='{$result['data']['ref_id']}',status='success',updated_at=$time WHERE id={$row_payments['id']}");
            $conn->query("UPDATE ".users." SET balance=balance+{$row_payments['coins']} WHERE user_id='{$row_payments['user_id']}'");
            
            send_reply("sendMessage", [
                'chat_id' => $row_payments['user_id'],
                'text' => "✅ تراکنش با شناسه <code>{$row_payments['id']}</code> با موفقیت پرداخت شد.\n\n".
                    "🌟 تعداد {$row_payments['coins']} سکه به حساب شما افزوده شد.\n\n".
                    "از پرداخت شما متشکریم 🌹\n‌",
                'parse_mode' => "html",
                'disable_web_page_preview' => true,
            ]);
            echo "تراکنش با موفقیت پرداخت شد !<br>شناسه پرداخت : {$row_payments['id']}";
        }
        else{
            echo "مشکلی در تایید تراکنش رخ داد !<br>شناسه پرداخت : {$row_payments['id']}";
        }
    }
    else if($status_payment_gateway == "idpay" and isset($_GET['id']) and isset($_GET['order_id']) and isset($_GET['status'])){
        $authority = htmlspecialchars($_GET['id']);
        $row_payments = $conn->query("SELECT * FROM ".payments." WHERE authority='$authority' AND status='move_gateway'")->fetch();
        if(!$row_payments){
            echo "خطایی رخ داد !\ncode = 131";
            exit;
        }
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://api.idpay.ir/v1.1/payment/verify');
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(['id' => $authority,'order_id' => $row_payments['uniq_id']]));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json','X-API-KEY:'.$settings['merchant_id']]);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);
        
        if($result['status'] == 100 || $result['status'] == 101){
            $conn->query("UPDATE ".payments." SET ref_id='{$result['track_id']}',status='success',updated_at=$time WHERE id={$row_payments['id']}");
            $conn->query("UPDATE ".users." SET balance=balance+{$row_payments['coins']} WHERE user_id='{$row_payments['user_id']}'");
            
            send_reply("sendMessage", [
                'chat_id' => $row_payments['user_id'],
                'text' => "✅ تراکنش با شناسه <code>{$row_payments['id']}</code> با موفقیت پرداخت شد.\n\n".
                    "🌟 تعداد {$row_payments['coins']} سکه به حساب شما افزوده شد.\n\n".
                    "از پرداخت شما متشکریم 🌹\n‌",
                'parse_mode' => "html",
                'disable_web_page_preview' => true,
            ]);
            echo "تراکنش با موفقیت پرداخت شد !<br>شناسه پرداخت : {$row_payments['id']}";
        }
        else{
            echo "مشکلی در تایید تراکنش رخ داد !<br>شناسه پرداخت : {$row_payments['id']}";
        }
    }
}
catch(Exception $e){
    echo "خطای ناشناخته ای رخ داد!";
}
