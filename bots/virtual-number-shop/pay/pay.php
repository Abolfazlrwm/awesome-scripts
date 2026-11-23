
<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

//////////////////////////
include('../bot.php');
include("NextPay.php");
$armin = new NextPay;
//////////////////////////

$user = $_GET['id'];
$name = bot('getChatMember',['chat_id'=>"$user",'user_id'=>"$user"])->result->user->first_name;
$MerchantID = "bac54844-97c0-4438-89cc-15ba92926b94";
$Amount = $_GET['amount'];
$order_id = time();

$callback_uri = "https://topnum.ir/num/pay/back.php?amount=$Amount&user=$user&order_id=$order_id";


$a = $armin->PayNextPay($MerchantID,$Amount,$callback_uri,$order_id);
if($a->code == -1){
    $pay = "http://api.nextpay.org/gateway/payment/{$a->trans_id}";
}

?>


<html>
	<head>
		<title>ربات خرید شماره مجازی فادر وب</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="ربات خرید شماره مجازی   | صفحه افزایش موجودی ربات خرید شماره مجازی ">
        <meta name="keywords" content="ربات خرید شماره مجازی ">
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/rtl.css" />
		<link rel="icon" href="images/aks.jpg" type="image/jpg"/>
	</head>
	<body>
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Header -->
					<header id="header" class="alt">
						<h1>ربات خرید شماره مجازی </h1>
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
										<p>برای افزایش موجودی حساب خود  به مبلغ <?php echo $Amount;?> تومان , کافیست از دکمه زیر استفاده کنید تا به صفحه پرداخت مطمئن منتقل شوید و پس از خرید موجودی حساب شما به صورت خودکار افزایش پیدا خواهد کرد</p>
										<ul class="actions">
											<li><a href="<?php echo $pay;?>" class="button">پرداخت <?php echo $Amount;?> تومان</a></li>
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


