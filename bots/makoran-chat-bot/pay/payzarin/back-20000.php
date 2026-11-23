<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
include("../../bot.php");
$MerchantID = '';//مریچنت
$Amount = 20000;
$Authority = $_GET['Authority'];
$user = $_GET['user'];
if ($_GET['Status'] == 'OK'){
$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
$result = $client->PaymentVerification(
[
'MerchantID' => $MerchantID,
'Authority' => $Authority,
'Amount' => $Amount,
]
);
if ($result->Status == 100){
$userget = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$pluscoin = $userget["coin"]  + 300 ;
jijibot('sendmessage',[
	'chat_id'=>$user,
	'text'=>"#پرداخت موفق ✅	
💰 مقدار خرید : $Amount تومان
ℹ️ تعداد سکه های شما : $pluscoin
❤️ از خرید شما و حمایت ربات چت گرام متشکریم

🌟 `سکه ها خریداری شده توسط شما با موفقیت به حساب شما افزوده شد`",
'parse_mode'=>'Markdown',
            ]);
jijibot('sendmessage',[
	'chat_id'=>$admin[0],
	'text'=>"#پرداخت موفق ✅
	
💰 مقدار خرید : $Amount تومان
🌟 برای خرید سکه
👤 کاربر : [$user](tg://user?id=$user)",
'parse_mode'=>'Markdown',
            ]);
$connect->query("UPDATE user SET  coin = '$pluscoin' WHERE id = '$user' LIMIT 1");	
header("Location: https://t.me/$usernamebot");
die();	
} else {
echo 'پرداخت شما قبلا ثبت شده است';
}
} else {
echo 'پرداخت انجام نشد';
}
?>