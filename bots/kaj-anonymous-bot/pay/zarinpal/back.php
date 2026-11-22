<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/
ob_start();
error_reporting(0);
include('../../config.php');
include('../../lib/jdf.php');
$token = API_KEY;
date_default_timezone_set('Asia/Tehran');
$time = date('H:i');
$date = gregorian_to_jalali(date('Y'), date('m'), date('d'), '/');
define('API_KEY', $token);
//========================================================
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
//========================================================
function sendmessage($chat_id, $text)
{
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => "MarkDown",
    ]);
}
//========================================================
$MerchantID = $MerchantID;
$Amount = $_GET['amount'];
$Authority = $_GET['Authority'];
$user = $_GET['id'];
//============================
if ($_GET['Status'] == 'OK') {
    $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
    $result = $client->PaymentVerification(['MerchantID' => $MerchantID, 'Authority' => $Authority, 'Amount' => $Amount,]);
    if ($result->Status == 100) {
        $userdata = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$user' LIMIT 1"));
        $plusamount = $userdata["amount"] + $Amount;
        echo 'خرید شما با موفقیت انجام شد کد پیگیری » ' . $result->RefID;
        bot('sendmessage', [
            'chat_id' => $user,
            'text' => "✅ تراکنش شما با موفقیت انجام شد و موجودی حساب شما افزایش یافت.

☑️ مبلغ پرداخت شده : $Amount تومان
💰 موجودی جدید شما : $plusamount تومان
⏱ زمان پرداخت : $date | $time

💐 با تشکر از خرید شما",
            'parse_mode' => 'Markdown',
        ]);
        $connect->query("UPDATE `user` SET `amount` = '$plusamount' WHERE `id` = '$user' LIMIT 1");
        $connect->query("INSERT INTO `buy` (`id` , `amount` , `time` , `date`) VALUES ('$user' , '$Amount' , '$time' , '$date')");
        bot('sendmessage', [
            'chat_id' => $channellog,
            'text' => "✅ یک پرداخت با مشخصات زیر هم اکنون صورت گرفت.

☑️ مبلغ پرداخت شده : $Amount تومان
💰 موجودی جدید کاربر : $plusamount تومان
⏱ زمان پرداخت : $date | $time

👤 [جهت مشاهده کاربر کلیک نمایید.](tg://user?id=$user)
$user
",
            'parse_mode' => 'Markdown',
        ]);
    } else {
        echo 'پرداخت شما قبلا ثبت شده است » ' . $result->RefID;
    }
} else {
    echo 'پرداخت انجام نشد » ' . $result->RefID;
    bot('sendmessage', [
        'chat_id' => $user,
        'text' => "❌ پرداخت شما انجام نشد.

⚠️ جهت مجدد خرید باید دوباره اقدام نمایید و با لینک جدید وارد درگاه پرداخت شوید.",
        'parse_mode' => 'html',
    ]);
}
/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/