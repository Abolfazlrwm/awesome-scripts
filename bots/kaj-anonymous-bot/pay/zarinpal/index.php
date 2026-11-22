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
include '../../config.php';
include('../../lib/jdf.php');

$Amount = $_GET['amount'];
$from_id = $_GET['user_id'];

if (isset($_GET['amount']) && isset($_GET['user_id'])) {

  if ($Amount >= $hadaddfuns && $Amount <= $maxaddfuns) {

    $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
    $result = $client->PaymentRequest([
      'MerchantID' => $MerchantID,
      'Amount' => $Amount,
      'Description' => "ربات $botname | قیمت $Amount تومان | کاربر $from_id",
      'Email' => 'mail',
      'Mobile' => 'mob',
      'CallbackURL' => "$web/pay/zarinpal/back.php/?id=" . $from_id . "&amount=" . $Amount
    ]);

    if ($result->Status == 100) {
      header("Location: https://www.zarinpal.com/pg/StartPay/" . $result->Authority . "/ZarinGate");
    } else {
      header("Location: $web/pay/error.php");
    }
  } else {
    echo ('مبلغ وارد شده نامعتبر است.');
    exit;
  }
} else {
  echo ('مشخصات کافی نیست.');
  exit;
}
/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/