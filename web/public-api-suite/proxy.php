<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
header('Content-type: application/json');
$get = file_get_contents('https://free-proxy-list.net/');
preg_match_all("#<tr><td>(.*?)</td><td>(.*?)</td><td>(.*?)</td><td class='hm'>(.*?)</td><td>(.*?)</td><td class='hm'>(.*?)</td><td class='hx'>(.*?)</td><td class='hm'>(.*?)</td></tr>#", $get, $proxy);
for($i = 0; $i < 20; $i++){
$proxies[] = ['IP Address'=>$proxy[1][$i],'Port'=>$proxy[2][$i],'Code'=>$proxy[3][$i],'Country'=>$proxy[4][$i],'Anonymity'=>$proxy[5][$i],'Google'=>$proxy[6][$i],'Https'=>$proxy[7][$i],'Last Checked'=>$proxy[8][$i]];
}
echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "20 HTTPS proxies found for you !", 'list'=> $proxies], 448);