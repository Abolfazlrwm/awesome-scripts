<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
header('Content-type: application/json');

function bankCardInfo($cards){
	$cards = (integer)$cards;
	if($cards == 603799 or $cards == 170019 or $cards == 589905) {
	  $bankname = "ملی";  
	  $bankinfo = "بانک ملی
● Site : bale.apk
● USSD : *717#  
● TBank : 09622
";
	}elseif($cards == 589210) {
		$bankname = "سپه";
		$bankinfo = "بانک سپه
● Site : https://ib.ebanksepah.ir/desktop/sepahPages/shetabCard.sepah
● USSD : #
● TBank : 021 64058
";
	}elseif($cards == 627648 or $cards == 207177) {
		$bankname = "توسعه صادرات";
		$bankinfo = "بانک توسعه صادرات
● Site : #
● USSD : #
● TBank : 021 2722
";
	}elseif($cards == 627961) {
		$bankname = "صنعت و معدن";
		$bankinfo = "بانک صنعت و معدن
● Site : #
● USSD : *719#
● TBank : 021 75024
";
	}elseif($cards == 603770 or $cards == 639217) {
		$bankname = "کشاورزی";
		$bankinfo = "بانک کشاورزی
● Site : https://ib.bki.ir/pid46.lmx
● USSD : *730#
● TBank : 09603
";
	}elseif($cards == 628023) {
		$bankname = "مسکن";
		$bankinfo = "بانک مسکن
● Site : #
● USSD : *714#, *737#
● TBank : 021 64096
";
	}elseif($cards == 627760) {
		$bankname = "پست بانک ایران";
		$bankinfo = "پست بانک ایران
● Site : #
● USSD : *747#
● TBank : 021 84284
";
	}elseif($cards == 502908) {
		$bankname = "توسعه تعاون";
		$bankinfo = "بانک توسعه تعاون
● Site : https://epayment.ttbank.ir
● USSD : #
● TBank : #
";
	}elseif($cards == 627412) {
		$bankname = "اقتصاد نوین";
		$bankinfo = "بانک اقتصاد نوین
● Site : https://modern.enbank.net/CustomerManager/viewLogin.html
● USSD : #
● TBank : 021 85292
";
	}elseif($cards == 622106 or $cards == 639194 or $cards == 627884) {
		$bankname = "پارسیان";
		$bankinfo = "بانک پارسیان
● Site : #
● USSD : *708#
● TBank : 021 89111
";
	}elseif($cards == 502229 or $cards == 639347) {
		$bankname = "پاسارگاد";
		$bankinfo = "بانک پاسارگاد
● Site : https://epay.bpi.ir/balanceinquiry.aspx
● USSD : *720#
● TBank : 021 828991111
";
	}elseif($cards == 627488 or $cards == 502910) {
		$bankname = "کار آفرین";
		$bankinfo = "بانک کار افرین
● Site : #
● USSD : #
● TBank : 021 23640
";
	}elseif($cards == 621986) {
		$bankname = "سامان";
		$bankinfo = "بانک سامان
● Site : #
● USSD : *724#
● TBank : 021 6422
";
	}elseif($cards == 639346) {
		$bankname = "سینا";
		$bankinfo = "بانک سینا
● Site : https://sina24h.com/CustomerService2/viewLogin.html
● USSD : *727#
● TBank : 021 82487
";
	}elseif($cards == 639607) {
		$bankname = "سرمایه";
		$bankinfo = "بانک سرمایه
● Site : https://pg.sbank.ir/balanceRequest.do
● USSD : #
● TBank : 021 8254
";
	}elseif($cards == 636214) {
		$bankname = "آینده";
		$bankinfo = "بانک آینده
● Site : #
● USSD : *745#
● TBank : 021 2957
";
	}elseif($cards == 502806 or $cards == 504706) {
		$bankname = "شهر";
		$bankinfo = "بانک شهر
● Site : https://ebank.city-bank.net/customermanager/viewLogin.html
● USSD : *787#
● TBank : 021 87611
";
	}elseif($cards == 502938) {
		$bankname = "دی";
		$bankinfo = "بانک دی
● Site : #
● USSD : #
● TBank : 021 2726
";
	}elseif($cards == 603769) {
		$bankname = "صادرات";
		$bankinfo = "بانک صادرات
● Site : #
● USSD : *719#
● TBank : 09602
";
	}elseif($cards == 610433 or $cards == 991975) {
		$bankname = "ملت";
		$bankinfo = "بانک ملت
● Site : #
● USSD : *720#
● TBank : 021 8132
";
	}elseif($cards == 585983) {
		$bankname = "تجارت";
		$bankinfo = "بانک تجارت
● Site : https://pg.tejaratbank.ir/paymentGateway/getBalance
● USSD : #
● TBank : 021 81277
";
	}elseif($cards == 589463) {
		$bankname = "رفاه";
		$bankinfo = "بانک رفاه
● Site : #
● USSD : *729#
● TBank : 021 84043000
";
	}elseif($cards == 627381) {
		$bankname = "انصار";
		$bankinfo = "بانک انصار
● Site : https://ebank.ansarbank.com/customermanager/viewLogin.html
● USSD : *763#
● TBank : 021 48049
";
	}elseif($cards == 505785) {
		$bankname = "ایران زمین";
		$bankinfo = "بانک ایران زمین
● Site : #
● USSD : #
● TBank : 021 24760
";
	}elseif($cards == 636795) {
		$bankname = "بانک مرکزی";
		$bankinfo = "بانک مرکزی
● Site : #
● USSD : #
● TBank : #
";
	}elseif($cards == 636949) {
		$bankname = "حکمت";
		$bankinfo = "بانک حکمت
● Site : #
● USSD : #
● TBank : 021 89555
";
	}elseif($cards == 505416) {
		$bankname = "گردشگری";
		$bankinfo = "بانک گردشگری
● Site : https://epayment.tourism-bank.com/BalanceInquiry.aspx
● USSD : *764#
● TBank : 021 22630345
";
	}elseif($cards == 606373) {
		$bankname = "قرضالحسنه ایران";
		$bankinfo = "بانک قرضالحسنه ایران
● Site : https://epayment.rqb.ir/BalanceInquiry.aspx
● USSD : #
● TBank : 021 8528
";
	}elseif($cards == 628157) {
		$bankname = "موسسه اعتباری توسعه";
		$bankinfo = "موسسه اعتباری توسعه
● Site : #
● USSD : #
● TBank : 021 81461
";
	}elseif($cards == 505801) {
		$bankname = "کوثر";
		$bankinfo = "بانک کوثر
● Site : https://modernkosarfci.net/simiaweb/login.htm
● USSD : *744#
● TBank : 021 86777
";
	}elseif($cards == 639370) {
		$bankname = "موسسه مهر";
		$bankinfo = "موسسه مهر
● Site : https://modern.qmbi24.com/customermngr/viewLogin.html
● USSD : #
● TBank : 021 8989
";
	}elseif($cards == 639599) { 
		$bankname = "بانک قوامین";
		$bankinfo = "بانک قوامین
● Site : www.ghbi.ir
● USSD : #
● TBank : 021 84270
";
	}else{
		$bankname = "بانک نامعلوم";
		$bankinfo = "کارت فیکه!";
	}
	return array($bankinfo,$bankname);
}

function bankCardCheck($card='', $irCard=true){
	$card = (string) preg_replace('/\D/','',$card);
	$strlen = strlen($card);
	if($irCard==true and $strlen!=16)
		return false;
	if($irCard!=true and ($strlen<13 or $strlen>19))
		return false;
	if(!in_array($card[0],[2,4,5,6,9]))
		return false;
	
	for($i=0; $i<$strlen; $i++){
		$res[$i] = $card[$i];
		if(($strlen%2)==($i%2))
		{
			$res[$i] *= 2;
			if($res[$i]>9)
				$res[$i] -= 9;		
		}
	}
	return array_sum($res)%10==0?true:false;	
}
$card = $_GET['card'] ?? $_POST['card'];
if(isset($card) and !empty($card)){
	if(!is_numeric($card)){
		echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "Just Enter Digits Number !"], 448);
		exit();
	}
	if(strlen($card) != 16){
		echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The Card Number Should Only Be 16 Digits !"], 448);
		exit();
	}
	if(bankCardCheck($card)){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://ipb.parsian-bank.ir/mobileBank/1.0/getCardOwnerWithoutLogin");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: " . $_SERVER["REMOTE_ADDR"], "HTTP_X_FORWARDED_FOR: ". $_SERVER["REMOTE_ADDR"]));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"destinationPan\":\"$card\",\"pan\":\"6280231350040589\"}");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json; charset=UTF-8',
		'User-Agent: Mozilla/5.0 (Linux; Android 6.0; ALE-L21 Build/HuaweiALE-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/63.0.3239.83 Mobile Safari/537.36'
		));
		$result = curl_exec($ch);
		$json = json_decode($result, true);
		echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "This Card Number Is #Right !", 'information'=>['bank' => bankCardInfo(substr($card, 0, -10))[1], 'info' => bankCardInfo(substr($card, 0, -10))[0], 'firstName' => $json["firstName"], 'lastName'=> $json["lastName"]]], 448);
	}else{
		echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "This Card Number Is #Invalid !"], 448);
	}
}else{
	echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The Card parameter is empty !"], 448);
}