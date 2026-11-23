<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

//==================================================
include '../index.php';
//====================//  Get  //==============================
$user = $_GET['id'];
$amount = $_GET['amount'];
$name = bot('getChatMember',['chat_id'=>$user,'user_id'=>$user])->result->user->first_name;
//========================== // config // ==============================
$CallbackURL = "$web/pay/back.php?id=$user&amount=$amount"; // لینک برگشت خرید
$Description = "افزایش موجودی $botname";
//==================================================
$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
$result = $client->PaymentRequest([
'MerchantID' => $MerchantID,
'Amount' => $amount,
'Description' => $Description,
'Email' => $Email,
'Mobile' => $Mobile,
'CallbackURL' => $CallbackURL,
]);
//==============================================================
$pay = "https://www.zarinpal.com/pg/StartPay/$result->Authority/ZarinGate";
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
											<h2>افزایش موجودی برای شناسه <?php echo "'$user'";?></h2>
										</header>
										<h3><?php echo "$name عزیز";?></h3>
										<p>برای افزایش موجودی حساب خود  به مبلغ <?php echo $amount;?> تومان , کافیست از دکمه زیر استفاده کنید تا به صفحه پرداخت مطمئن منتقل شوید و پس از خرید موجودی حساب شما به صورت خودکار افزایش پیدا خواهد کرد</p>
										<ul class="actions">
											<li><a href="<?php echo $pay;?>" class="button">پرداخت <?php echo $amount;?> تومان</a></li>
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