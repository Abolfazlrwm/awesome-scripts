<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
<!DOCTYPE HTML>

<?php 
include "../bot.php";
$user = $_GET['id'];
$getname = jijibot('getChatMember',['chat_id'=>"$user",'user_id'=>"$user"]);
$name = $getname->result->user->first_name;
$amount = $_GET['amount'];
$coin = $_GET['coin'];
?>
<html>
	<head>
		<title>ربات چت گرام</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<link rel="stylesheet" href="assets/css/rtl.css" />
	</head>
	<body>

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Header -->
					<header id="header" class="alt">
						<span class="logo"><img src="images/cutmypic.png" alt="" /></span>
						<h1>ربات چت گرام</h1>
						<p>سکه های خودت رو افزایش بده و از ربات لذت ببر !</p>
					</header>


				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
									<div class="content">
										<header class="major">
											<h2>افزایش سکه برای شناسه <?php echo "'$user'";?></h2>
										</header>
										<h3><?php echo "$name عزیز";?></h3>
										<p>برای افزاش که حساب خود کافیست از دکمه زیر استفاده کنید تا به صحفه پرداخت مطمئن منتقل شوید بعد از خرید سکه ها به صورت خودکار به حساب شما افزوده خواهد شد</p>
										<ul class="actions">
											<li><a href="<?php echo "$web/pay/payzarin/pay.php?amount=$amount&callback=$web/pay/payzarin/back-$amount.php?user=$user";?>" class="button">خرید <?php echo $coin;?> سکه | <?php echo $amount;?> تومان</a></li>
											<li><a href="<?php echo "https://t.me/$usernamebot";?>" class="button">ورود به ربات</a></li>
										</ul>
									</div>
								</div>
							</section>
					</div>
				<!-- Footer -->
					<footer id="footer">
						<p class="copyright">&copy; کلیه حقوق سایت برای ال4گپ محفوظ است</a>.</p>
					</footer>
			</div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="assets/js/main.js"></script>
	</body>
</html>