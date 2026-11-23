<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

include '../database.php';

function redirect($url)
{
    if (!headers_sent()){
        header("Location: $url");
    }else{
        echo "<script type='text/javascript'>window.location.href='$url'</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'/></noscript>";
    }
    exit;
}

define("Domin","sinzavpn.fun"); 
define("nextpay", "31c652c5-e0e5-4350-90e3-a00b260c0de3"); 

 $order_id = $_GET["order_id"];
 if(!file_exists("../ListCode/$order_id.txt")){
echo "لینک درگاه باطل شده است !";
exit();
}

$from_id = file_get_contents("../ListCode/$order_id.txt");
$desc = 'پرداخت جهت خرید هاست و هرگونه فعالیت غیر مجاز مثل فیشینگ و کلاهبرداری عواقب ان به عهده بنده یعنی خریدار است';
$amount = $_GET["amount"];
if(!preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$amount)){
echo "مبلغ نامعتبر است !";
exit;
}
    
if (isset($_GET["order_id"]) && isset($_GET["amount"]) && isset($_GET["get"])) {
 $order_id = $_GET["order_id"];
 $amount = $_GET["amount"];
 $url = "https://nextpay.org/nx/gateway/token";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "api_key=" . nextpay . "&amount=" . $amount . "&payer_desc=" . $desc . "&currency=IRT&order_id=" . $order_id . "&callback_uri=https://" . Domin . "/payment/nextpay.php?back",
    ));
    $result = curl_exec($curl);
    $result = json_decode($result);
    curl_close($curl);

    $trans_id = $result->trans_id;
 if ($result->code !== null){
  if ($result->code == "-1") {
            redirect("https://nextpay.org/nx/gateway/payment/$trans_id"); 
  } else {
   echo "مشکلی به وجود اومده ! \n";
  }
 } else {
  echo "<h1 style=\"text-align: center;margin-top:30px\">درخواست نامعتبر است</h1>";
 }
} else {
 if (isset($_GET["back"])) {
        $order_id = $_GET["order_id"];
        $trans_id = $_GET["trans_id"];
        $amount = $_GET["amount"];
        $status = $_GET["np_status"];

        if ($status == "OK") {
            $url = "https://nextpay.org/nx/gateway/verify";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'api_key=' . nextpay . '&amount=' . $amount . '&trans_id=' . $trans_id,
            ));
            $result = curl_exec($curl);
            $result = json_decode($result);
           $order_id1 = $result->order_id;
            curl_close($curl);
            if ($result->code == '0') {

if($order_id1 != $order_id){
    echo 'no bug';
    exit;
}

update('users', ['coin' => $coin + $amount], ['user_id' => $from_id]);


                         echo "<h1 style=\"text-align: center\">🔥 پرداخت شما با موفقیت تایید شد به ربات برگردید 🔥</h1>";
                unlink("../ListCode/$order_id1.txt");
            }
                else {
                echo "<h1 style=\"text-align: center\">❌ از فیشینگ شما استفاده کردید کارتی که احراز شده با کارت پرداختی یکی نیست ❌</h1>";
            }
        } else {
            echo "<h1 style=\"text-align: center\">❌ تراکنش توسط شما لغو شد ❌ </h1>";
            unlink("../ListCode/$order_id.txt");
        }
    } else {
        echo "<h1 style=\"text-align: center\">درخواست نامعتبر است</h1>";
    }
}