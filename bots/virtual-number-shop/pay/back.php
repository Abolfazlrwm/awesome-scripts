<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

include('../bot.php');
include("NextPay.php");
$armin = new NextPay;
//////////////////////////
$Amount = $_GET['amount'];
$user = $_GET['user'];
$order_id = $_GET['order_id'];
$trans_id = $_POST['trans_id'];
//////////////////////////////////////////////////////////

$a = $armin->NextPayVerification($MerchantID,$Amount,$trans_id,$order_id);
$mo = mysqli_fetch_assoc(mysqli_query($connect,"SELECT id FROM pay WHERE id = '$trans_id'"));

if($a->code == "0" and $mo["id"] != true){
$connect->query("INSERT INTO pay (id) VALUES ('$trans_id')");
?>

<html>
	<head>
		<title>ربات خرید شماره مجازی مکران کد</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="ربات خرید شماره مجازی مکران کد مکران کد | صفحه افزایش موجودی ربات خرید شماره مجازی مکران کد">
        <meta name="keywords" content="ربات خرید شماره مجازی مکران کد">
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/rtl.css" />
		<link rel="icon" href="images/aks.jpg" type="image/jpg"/>
	</head>
	<body>
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Header -->
					<header id="header" class="alt">
						<h1>ربات خرید شماره مجازی مکران کد</h1>
						<p>صفحه افزایش موجودیِ حساب</p>
					</header>
				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
									<div class="content">
										<header class="major">
											<h2>پرداخت شما با موفقیت انجام شد <?php echo "'$user'";?></h2>
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

$userget = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$pluscoin = $userget["stock"]  +  $Amount;
$a = $pluscoin-$Amount;
bot('sendmessage',[
  'chat_id'=>$user,
  'text'=>"✅ #پرداخت موفق  
💎 مقدار خرید : $Amount تومان
💰 موجودی جدید شما : $pluscoin تومان
❄️ موجودی قبلی : $a تومان

❤️ از خرید شما متشکریم . در صورت وجود هر گونه مشکل با پشتیبانی در تماس باشید
🌟 موجودی شما با موفقیت افزایش یافت !",
            ]);
$connect->query("UPDATE user SET  stock = '$pluscoin' WHERE id = '$user' LIMIT 1");  
bot('sendmessage',[
  'chat_id'=>$admin[0],
  'text'=>"✅ #پرداخت موفق
  
💎 مقدار خرید : $Amount تومان
👤 کاربر : [$user](tg://user?id=$user)",
'parse_mode'=>'Markdown',
           ]);
if($userget["inviter"] == true){
$userinviter = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '{$userget["inviter"]}' LIMIT 1"));
$porsant = ($Amount * 5) / 100 ;
$coinplusinviter = $userinviter["stock"] + $porsant;
$a = $coinplusinviter-$porsant;
bot('sendmessage',[
  'chat_id'=>$userget["inviter"],
  'text'=>"✅ تبریک ! زیر مجموعه شما از ربات خرید کرد و 5 درصد از موجودی خریداری شده به عنوان هدیه به شما تعلق گرفت
  
🛍 موجودی خریداری شده : $Amount تومان
💰 موجودی جدید شما : $coinplusinviter تومان
❄️ موجودی قبلی : $a تومان
🎁 مقدار هدیه : $porsant تومان
👤 خرید توسط : [$user](tg://user?id=$user)",
'parse_mode'=>'Markdown',
            ]);
$connect->query("UPDATE user SET stock = '$coinplusinviter' WHERE id = '{$userget["inviter"]}' LIMIT 1");
}
} else {
?>
<html>
	<head>
		<title>ربات خرید شماره مجازی مکران کد</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="ربات خرید شماره مجازی مکران کد مکران کد | صفحه افزایش موجودی ربات خرید شماره مجازی مکران کد">
        <meta name="keywords" content="ربات خرید شماره مجازی مکران کد">
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/rtl.css" />
		<link rel="icon" href="images/aks.jpg" type="image/jpg"/>
	</head>
	<body>
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Header -->
					<header id="header" class="alt">
						<h1>ربات خرید شماره مجازی مکران کد</h1>
						<p>صفحه افزایش موجودیِ حساب</p>
					</header>
				<!-- Main -->
					<div id="main">
					<!-- First Section -->
										<section id="intro" class="main">
								<div class="spotlight">
									<div class="content">
										<header class="major">
											<h2>    خطا مشکلی پیش آمده <?php echo "'$user'";?></h2>
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
