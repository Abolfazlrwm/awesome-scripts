<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

header('Content-type: application/json');
function TextNow($url){
$data['url'] = "$url";
$data['language'] = "eng";
$data['apikey'] = '5a64d478-9c89-43d8-88e3-c65de9999580';
$data['detectOrientation'] = 'true';
$data['scale'] = 'true';
$data['isTable'] = 'true';
$data['OCREngine'] = '1';
$data['IsCreateSearchablePDF'] = 'true';
//=========================================================
$ch = curl_init("https://api8.ocr.space/parse/image");
curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.87 Mobile Safari/537.36");
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch,CURLOPT_HEADER, false);
curl_setopt($ch,CURLOPT_NOBODY,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_AUTOREFERER,1);
curl_setopt($ch,CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$kobs = curl_exec($ch);
curl_close($ch);
$json =json_decode($kobs,true);
$array = $json['ParsedResults'][0]['ParsedText'];
$text = str_replace([' ','-','(',')'],null,$array);
$random = explode("\n",$text);
foreach($random as $value){
if(preg_match('/(\d{10,11})/', $value, $txt)){
$add = ('+'.strrev(substr(strrev('+1'.$txt[0]),0,11)));
$list = $list."$add\n";
}
}
return $list;
}
if (isset($_GET['url'])) {
echo(TextNow($_GET['url']));
}else{
echo 'The url parameter is empty!';
}