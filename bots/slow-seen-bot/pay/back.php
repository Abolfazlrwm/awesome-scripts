<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

include '../index.php';
//==============================================================
$user = $_GET['id'];
$amount = $_GET['amount'];
$Authority = $_GET['Authority'];
//==============================================================
$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
$result = $client->PaymentVerification([
'MerchantID' => $MerchantID,
'Authority' => $Authority,
'Amount' => $amount,
]);
//==============================================================
if ($_GET['Status'] == 'OK' and $result->Status == 100){
$userdata = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$user' LIMIT 1"));
$pluscoin = $userdata['coin']  +  $amount;
$nowtimer = "در تاریخ $date و در ساعت $time";
bot('sendmessage',[
	'chat_id'=>$user,
	'text'=>"✅ #پرداخت_موفق 
⬆️ با تشکر از خرید شما , موجودی حساب شما افزایش یافت

💰 موجودی جدید شما : $pluscoin تومان
☑️ مبلغ پرداخت شده : $amount تومان
⏱ زمان : $nowtimer",
	'reply_markup'=>$home
            ]);
$connect->query("UPDATE `user` SET `coin` = '$pluscoin' WHERE `id` = '$user' LIMIT 1");	
$connect->query("INSERT INTO `buy` (`id` , `amount` , `time`) VALUES ('$user' , '$amount' , '$nowtimer')");
if($userdata['inviter'] != '0'){
$inviter = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '{$userdata['inviter']}' LIMIT 1"));
$porsant = round(($amount * $_porsant) / 100);
$pluscoin = $inviter['coin']  +  $porsant;
bot('sendmessage',[
	'chat_id'=>$userdata['inviter'],
	'text'=>"✅ تبریک ! زیر مجموعه شما از ربات خرید کرده و 5 درصد از موجودی خریداری شده به عنوان هدیه به شما تعلق گرفت
	
💰 موجودی جدید شما : $pluscoin تومان
🛍 موجودی خریداری شده : $amount تومان
🎁 مقدار هدیه : $porsant تومان
👤 خرید توسط : [$user](tg://user?id=$user)",
'parse_mode'=>'Markdown',
            ]);
$connect->query("UPDATE `user` SET `coin` = '$pluscoin' WHERE `id` = '{$userdata['inviter']}' LIMIT 1");
}
bot('sendmessage',[
	'chat_id'=>$admin[0],
	'text'=>"✅ #پرداخت موفق
	
💳 مبلغ خرید : $amount تومان
👤 کاربر : [$user](tg://user?id=$user)",
'parse_mode'=>'Markdown',
            ]);
?>
<html>
	<head>
		<title>افزایش موجودی | <?php echo $botname;?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
			<meta name="description" content="صفحه افزایش موجودیِ حساب">
      <meta name="keywords" content="افزایش بازدید و لایک پست ها در تلگرام">
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/rtl.css" />
		<link rel="icon" type="image/png" href="images/fav.png">
	</head>
	<body>
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Header -->
					<header id="header" class="alt">
						<h1>ربات <?php echo $botname;?></h1>
						<p>صفحه افزایش موجودیِ حساب</p>
					</header>
				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
							        <div class="content">
										<header class="major">
											<h2>پرداخت شما با موفقیت انجام شد و موجودی خریداری شده به حساب شما افزوده شد , برای ادامه به ربات مراجعه کنید .</h2>
										</header>
										<ul class="actions">
											<li><a href="<?php echo "https://t.me/$usernamebot";?>" class="button">برگشت به ربات</a></li>
										</ul>
									</div>
								</div>
							</section>
					</div>		
			</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
	</body>
</html>
<?php 
} else {
?>
<html>
	<head>
		<title>افزایش موجودی | <?php echo $botname;?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
			<meta name="description" content="صفحه افزایش موجودیِ حساب">
      <meta name="keywords" content="افزایش بازدید و لایک پست ها در تلگرام">
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/rtl.css" />
		<link rel="icon" type="image/png" href="images/fav.png">
	</head>
	<body>
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Header -->
					<header id="header" class="alt">
						<h1>ربات <?php echo $botname;?></h1>
						<p>صفحه افزایش موجودیِ حساب</p>
					</header>
				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
							        <div class="content">
										<header class="major">
											<h2>پرداخت شما با موفقیت انجام نشد ! به ربات برگشته و مجدد اقدام به خرید کنید</h2>
										</header>
										<ul class="actions">
											<li><a href="<?php echo "https://t.me/$usernamebot";?>" class="button">برگشت به ربات</a></li>
										</ul>
									</div>
								</div>
							</section>
					</div>		
			</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
	</body>
</html>
<?php 
}
?>