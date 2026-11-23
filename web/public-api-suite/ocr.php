<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(false);
header('Content-type: application/json');
$getUrl = $_GET['url'];
$getLanguage = $_GET['lang'];
$ppersian = ['Persian','Arabic','Bulgarian','ChineseSimplified','ChineseTraditional','Croatian','Czech','Danish','Dutch','English','Finnish','French','German','Greek','Hungarian','Italian', 'Japanese', 'Korean', 'Polish', 'Portuguese', 'Russian', 'Slovenian', 'Spanish', 'Swedish', 'Turkish'];
function setlang($string) {
$persian = ['Persian','Arabic','Bulgarian','ChineseSimplified','ChineseTraditional','Croatian','Czech','Danish','Dutch','English','Finnish','French','German','Greek','Hungarian','Italian', 'Japanese', 'Korean', 'Polish', 'Portuguese', 'Russian', 'Slovenian', 'Spanish', 'Swedish', 'Turkish'];
$english = ['ara','ara','bul','chs','cht','hrv','cze','dan','dut','eng','fin','fre','ger','gre','hun','ita', 'jpn', 'kor', 'pol', 'por', 'rus', 'slv', 'spa', 'swe', 'tur'];
$output= str_replace($persian, $english, $string);
return $output;
}
if (!isset($getUrl) or !isset($getLanguage)) {
//========================================================= 
echo json_encode(['ok' => true, 'channel' => '@SlowTM','writer' => '@TKPHP',  'Results' =>"The link or language parameter is empty !" , 'languages'=>$ppersian], 448);
//========================================================= 
}else{
//=========================================================
$data['url']="$getUrl";
$data['language'] = setlang($getLanguage);
$data['apikey'] = '5a64d478-9c89-43d8-88e3-c65de9999580';
$data['detectOrientation'] = 'true';
$data['scale'] = 'true';
$data['isTable'] = 'true';
$data['OCREngine'] = '1';
$data['IsCreateSearchablePDF'] = 'true';
//=========================================================
$url= "https://api8.ocr.space/parse/image";
//=========================================================
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch,CURLOPT_NOBODY,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_AUTOREFERER,1);
curl_setopt($ch,CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$kobs = curl_exec($ch);
curl_close($ch);
$json =json_decode($kobs,true);
$resu = $json['ParsedResults'][0]['ParsedText'];  
//========================================================= 
echo json_encode(['ok' => true, 'channel' => '@SlowTM','writer' => '@TKPHP',  'Results' =>$resu , 'languages'=>$ppersian], 448);
//========================================================= 
}