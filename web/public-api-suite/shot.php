<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$siteURL = $_GET['url'];
if(!isset($siteURL)){
//========================================================= 
echo json_encode(['ok' => false, 'channel' => '@SlowTM','writer' => '@TKPHP',  'Results' =>"The link parameter is empty !"], 448);
}else{
$googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$siteURL&screenshot=true");
$json = json_decode($googlePagespeedData, true);
$screenshot = $json["lighthouseResult"]["audits"]["full-page-screenshot"]["details"];
$screenshot = $screenshot['screenshot']['data'];
echo "<img src= " . $screenshot . " />";
}