<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

class NextPay
{
	public function PayNextPay($MerchantID,$Amount,$CallbackURL,$order_id){
    $client = new SoapClient('https://api.nextpay.org/gateway/token.wsdl', ['encoding' => 'UTF-8']);
    $result = $client->TokenGenerator([
    'api_key' => $MerchantID,
    'order_id' => $order_id,
    'amount' => $Amount,
    'callback_uri' => $CallbackURL,
    ]);
    return $result->TokenGeneratorResult;
	
	}
	
	
	public function NextPayVerification($MerchantID,$Amount,$trans_id,$order_id){
    $client = new SoapClient('https://api.nextpay.org/gateway/verify.wsdl', ['encoding' => 'UTF-8']);
    $result = $client->PaymentVerification([
    'api_key' => $MerchantID,
    'order_id' => $order_id,
    'amount' => $Amount,
    'trans_id' => $trans_id,
    ]);
    return $result->PaymentVerificationResult;
	
	}
	

}

?>
