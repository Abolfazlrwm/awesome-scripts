<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
try{
    if(isset($_GET["id"])){
        $id = htmlspecialchars($_GET["id"]);

        require_once '../include/settings.php';
        require_once '../include/config.php';
        require_once '../bot/options/functions.php';

        $row_payment = $conn->query("SELECT * FROM ".payments." WHERE id='$id' AND (status='first_level' OR status='move_gateway')")->fetch();
        if(!$row_payment){
            exit;
        }
        if($status_payment_gateway == "zarinpanl"){
            $data = json_encode([
                "merchant_id" => $settings['merchant_id'],
                "amount" => $row_payment['amount'] * 10,
                "callback_url" => str_replace("/request.php","/verify.php","https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"),
                "description" => "توضیحات سفارش"
            ]);
            $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Content-Length: '.strlen($data)]);
            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result,true);
            
            if($result['data']['code'] == 100){
                $conn->query("UPDATE ".payments." SET authority='{$result['data']['authority']}',status='move_gateway',updated_at='$time' WHERE id='$id'");
                header("Location: https://www.zarinpal.com/pg/StartPay/{$result['data']['authority']}");
            }
            else echo "خطایی رخ داد !";
        }
        else if($status_payment_gateway == "idpay"){
            $params = [
                'order_id' => $row_payment['uniq_id'],
                'amount' => $row_payment['amount'] * 10,
                'name' => 'no name',
                'phone' => '09121234567',
                'mail' => 'email@gmail.com',
                'desc' => 'توضیحات سفارش',
                'callback' => str_replace("/request.php","/verify.php","https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"),
                'reseller' => null,
              ];
              
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_URL,'https://api.idpay.ir/v1.1/payment');
              curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($params));
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
              curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json','X-API-KEY:'.$settings['merchant_id']]);
              $result = curl_exec($ch);
              curl_close($ch);
              $result = json_decode($result,true);
              
              if(isset($result['id']) && isset($result['link'])){
                  $conn->query("UPDATE ".payments." SET authority='{$result['id']}',status='move_gateway',updated_at=$time WHERE id={$row_payment['id']}");
                  header("location: {$result['link']}");
              }
              else echo "خطایی رخ داد !";
        }
    }
}
catch(Exception $e){
    echo "خطای ناشناخته ای رخ داد !";
}

