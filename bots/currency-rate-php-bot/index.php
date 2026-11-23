<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

function post($url) : array {
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_TIMEOUT,5);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
$res = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);
return $err ? ['error'=>$err] : json_decode($res,true);
}
$json = post('https://api.nobitex.ir/market/global-stats');
if(isset($json['status']) === false or $json['status'] !== 'ok') die('Error in receiving prices !');
$bonbast = file_get_contents('https://bonbast.com/graph/dollar');
if(preg_match_all('#<td class="price">(.*?)</td>#i',$bonbast,$result)){
$Sell = preg_replace('![^\d]*!',(string) null,$result[0][0]);
$Buy = preg_replace('![^\d]*!',(string) null,$result[0][1]);
$DollarPrice = round(($Sell + $Buy) / 2);
}else{
$DollarPrice = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Currency prices</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<link rel="stylesheet" type="text/css" href="css/util.css">
<link rel="stylesheet" type="text/css" href="css/main.css">
<meta name="robots" content="noindex, follow">
<meta http-equiv="refresh" content="60">
</head>
<body>
<div class="limiter">
<div class="container-table100">
<div class="wrap-table100">
<div class="table100">
<table>
<thead>
<tr class="table100-head">
<th class="column1">Currency</th>
<th class="column2">Prices</th>
</tr>
</thead>
<tbody>
<?php ksort($json['markets']['binance']); ?>
<?php foreach($json['markets']['binance'] as $key => $price){ ?>
<tr>
<td class="column1"><?= $key ?></td>
<td class="column2"><?= $price.' Dollar'.' | '.($price * $DollarPrice).' Rial' ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script src="js/main.js"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){
		dataLayer.push(arguments);
	}
	gtag('js',new Date());
	gtag('config','UA-23581568-13');
</script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194" integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw==" data-cf-beacon='{"rayId":"7193193e9bd901fc","token":"PUT-YOUR-KEY-HERE","version":"2021.12.0","si":100}' crossorigin="anonymous"></script>
</body>
</html>