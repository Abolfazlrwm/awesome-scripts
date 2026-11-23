<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
?>
<!DOCTYPE HTML>

<?php 
include "../bot.php";
$user = $_GET['id'];
$getname = jijibot('getChatMember',['chat_id'=>"$user",'user_id'=>"$user"]);
$name = $getname->result->user->first_name;
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
						<p>حساب خود را ویژه کن و از امکانات کامل ربات استفاده کن !</p>
					</header>


				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
									<div class="content">
										<header class="major">
											<h2>خرید حساب ویژه برای شناسه <?php echo "'$user'";?></h2>
										</header>
										<h3><?php echo "$name عزیز";?></h3>
										<p>برای ویژه کردن حساب خود و استفاده کامل از امکانات ربات چت گرام کافیست با روی دکمه زیر ضربه بزنید سپس به صحفه مطمئن پرداخت انتقال داده خواهید شد بعد  از خرید حساب شما خودکار ویژه خواهد شد</p>
										<ul class="actions">
											<li><a href="<?php echo "$web/pay/payzarin/pay.php?amount=3000&callback=$web/pay/payzarin/back-3000.php?user=$user";?>" class="button">خرید حساب ویژه | 3000 تومان</a></li>
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