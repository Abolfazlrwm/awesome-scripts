<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
function Sstr($card){
$zx = str_split("$card");
return "$zx[0]$zx[1]$zx[2]$zx[3]$zx[4]$zx[5]$zx[6]$zx[7]$zx[8]$zx[9]$zx[10]$zx[11]$zx[12]$zx[13]$zx[14]$zx[15]";
}
function Create($x,$lmm,$c1,$c2,$n){
include('Font/FarsiGD.php');
include('jdf.php');
header('Content-type: image/jpeg');
$m = number_format($lmm);
$name = $n;
$FaGD = new FarsiGD();
$a = $FaGD->persianText($name,'en','normal');
if($x == 1){
$jpg_image = imagecreatefromjpeg('Photo/IMG_1.jpg');
$black = imagecolorallocate($jpg_image, 5, 204, 171);
$blajck = imagecolorallocate($jpg_image, 0, 0, 0);
$blacpk = imagecolorallocate($jpg_image, 255, 255, 255);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d H:i:s");
$rand= rand(00000000000,99999999999);
imagettftext($jpg_image, 15, 0, 60, 286, $blacpk, $font_path, $mm);
imagettftext($jpg_image, 15, 0, 60, 352, $black, $font_path, $m);
imagettftext($jpg_image, 15, 0, 65, 420, $blajck, $font_path, $c1);
imagettftext($jpg_image, 15, 0, 65, 470, $blajck, $font_path, $c2);
imagettftext($jpg_image, 15, 0, 65, 530, $blajck, $font_path, $a);
imagettftext($jpg_image, 15, 0, 65, 590, $blajck, $font_path, $rand);
imagejpeg($jpg_image);
imagedestroy($jpg_image);
}
if($x == 2){
$jpg_image = imagecreatefrompng('Photo/IMG_2.png');
$black = imagecolorallocate($jpg_image, 87, 89, 86);
$blajck = imagecolorallocate($jpg_image, 87, 89, 86);
$blacpk = imagecolorallocate($jpg_image, 87, 89, 86);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(00000000000,99999999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 15, 0, 20, 513, $blacpk, $font_path, $mm);
imagettftext($jpg_image, 15, 0, 20, 548, $blacpk, $font_path, $gmm);
imagettftext($jpg_image, 15, 0, 20, 575, $blacpk, $font_path, $ag);
imagettftext($jpg_image, 15, 0, 20, 475, $black, $font_path,"$rial$m");
imagettftext($jpg_image, 15, 0, 20, 375, $blajck, $font_path, $c1);
imagettftext($jpg_image, 15, 0, 20, 410, $blajck, $font_path, $c2);
imagettftext($jpg_image, 15, 0, 20, 445, $blajck, $font_path, $a);
imagettftext($jpg_image, 15, 0, 20, 610, $blajck, $font_path, $rand);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 3){
$jpg_image = imagecreatefrompng('Photo/IMG_3.png');
$black = imagecolorallocate($jpg_image, 87, 89, 86);
$blajck = imagecolorallocate($jpg_image, 87, 89, 86);
$blacpk = imagecolorallocate($jpg_image, 19, 174, 57);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(00000000000,99999999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 25, 0, 30, 363, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 27, 0, 95, 430, $blacpk, $font_path,$m);
imagettftext($jpg_image, 25, 0, 30, 495, $blajck, $font_path, $c1);
imagettftext($jpg_image, 25, 0, 30, 553, $blajck, $font_path, $c2);
imagettftext($jpg_image, 25, 0, 30, 612, $blajck, $font_path, $a);
imagettftext($jpg_image, 30, 0, 245, 815, $blajck, $font_path, $rand);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 4){
$jpg_image = imagecreatefromjpeg('Photo/IMG_4.jpg');
$black = imagecolorallocate($jpg_image, 97, 86 ,116);
$blajck = imagecolorallocate($jpg_image,97, 86 ,116);
$blacpk = imagecolorallocate($jpg_image,97, 86, 116);
$font_path = "Font/Far_Bank.ttf";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(00000000000,99999999999);
$randf= rand(0000000000000,9999999999999);
$rial = 'ریال';
$ag = $FaGD->persianText($n,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
$ppp1 = explode("****-****-****-",$c1)[1];
imagettftext($jpg_image, 30, 0, 120, 130, $blajck, $font_path, "$gmm                     $mm");
imagettftext($jpg_image, 27, 0, 60, 308, $blacpk, $font_path,"$rial $m");
imagettftext($jpg_image, 25, 0, 60, 412, $blajck, $font_path, "****-****-****-");
imagettftext($jpg_image, 27, 0, 455, 412, $blajck, $font_path, $ppp1);
imagettftext($jpg_image, 27, 0, 60, 620, $blajck, $font_path, $c2);
imagettftext($jpg_image, 27, 0, 60, 720, $blajck, $font_path, $ag);
imagettftext($jpg_image, 27, 0, 60, 213, $blajck, $font_path, $rand);
imagettftext($jpg_image, 27, 0, 60, 520, $blajck, $font_path, $randf);
imagejpeg($jpg_image);
imagedestroy($jpg_image);
}
if($x == 5){
$jpg_image = imagecreatefrompng('Photo/IMG_5.png');
$black = imagecolorallocate($jpg_image, 39, 68, 107);
$blajck = imagecolorallocate($jpg_image, 39, 68, 107);
$blacpk = imagecolorallocate($jpg_image, 39, 68, 107);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(00000000000,99999999999);
$rial = 'ریال';
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 24, 0, 190, 500, $black, $font_path, $mm);
imagettftext($jpg_image, 24, 0, 390, 500, $black, $font_path, $gmm);
imagettftext($jpg_image, 25, 0, 133, 550, $black, $font_path,"$rial$m");
imagettftext($jpg_image, 25, 0, 133, 600, $blajck, $font_path, $c1);
imagettftext($jpg_image, 25, 0, 133, 653, $blajck, $font_path, $c2);
imagettftext($jpg_image, 25, 0, 133, 700, $blajck, $font_path, $a);
imagettftext($jpg_image, 25, 0, 180, 870, $blajck, $font_path, $rand);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 6){
$jpg_image = imagecreatefromjpeg("Photo/IMG_6.jpg");
$black = imagecolorallocate($jpg_image, 87, 89, 86);
$blajck = imagecolorallocate($jpg_image, 31, 37, 69);
$blacpk = imagecolorallocate($jpg_image, 21, 23, 40);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(000000,999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 26, 0, 100, 190, $blacpk, $font_path,"$m");
imagettftext($jpg_image, 26, 0, 50, 250, $blacpk, $font_path,"$a");
imagettftext($jpg_image, 26, 0, 50, 310, $blacpk, $font_path,"$c2");
imagettftext($jpg_image, 16, 0, 60, 770, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 16, 0, 60, 570, $blajck, $font_path,"$rial $m");
imagettftext($jpg_image, 16, 0, 60, 610, $blajck, $font_path, $c1);
imagettftext($jpg_image, 16, 0, 60, 650, $blajck, $font_path, $c2);
imagettftext($jpg_image, 16, 0, 60, 690, $blajck, $font_path, $a);
imagettftext($jpg_image, 16, 0, 60, 730, $blajck, $font_path, $rand);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 7){
$jpg_image = imagecreatefrompng('Photo/IMG_7.png');
$black = imagecolorallocate($jpg_image, 90, 90, 90);
$blajck = imagecolorallocate($jpg_image, 90, 90, 90);
$blacpk = imagecolorallocate($jpg_image, 90, 90, 90);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(000000,999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 18, 0, 120, 900, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 18, 0, 120, 850, $blajck, $font_path,"$rial $m");
imagettftext($jpg_image, 18, 0, 120, 685, $blajck, $font_path, $c1);
imagettftext($jpg_image, 18, 0, 120, 730, $blajck, $font_path, $c2);
imagettftext($jpg_image, 18, 0, 120, 770, $blajck, $font_path, $a);
imagettftext($jpg_image, 18, 0, 120, 645, $blajck, $font_path, $rand);
imagettftext($jpg_image, 18, 0, 120, 605, $blajck, $font_path, 10);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 8){
$jpg_image = imagecreatefromjpeg("Photo/IMG_8.png");
$black = imagecolorallocate($jpg_image, 91, 91, 91);
$blajck = imagecolorallocate($jpg_image, 91, 91, 91);
$blacpk = imagecolorallocate($jpg_image, 91, 91, 91);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(000000,999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 26, 0, 250, 250, $blacpk, $font_path,"$a");
imagettftext($jpg_image, 18, 0, 44, 475, $blacpk, $font_path,"$c2");
imagettftext($jpg_image, 18, 0, 44, 550, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 30, 0, 280, 300, $blajck, $font_path,"$m");
imagettftext($jpg_image, 18, 0, 44, 700, $blajck, $font_path, $c1);
imagettftext($jpg_image, 18, 0, 100, 400, $blajck, $font_path, 5000);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 9){
$jpg_image = imagecreatefromjpeg("Photo/IMG_9.jpg");
$black = imagecolorallocate($jpg_image, 251, 183, 20);
$blajck = imagecolorallocate($jpg_image, 251, 183, 20);
$blacpk = imagecolorallocate($jpg_image, 251, 183, 20);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(000000,999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 15, 0, 28, 500, $blacpk, $font_path,"$a");
imagettftext($jpg_image, 15, 0, 28, 455, $blacpk, $font_path,"$c2");
imagettftext($jpg_image, 15, 0, 28, 600, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 15, 0, 28, 365, $blajck, $font_path,"$m");
imagettftext($jpg_image, 15, 0, 28, 555, $blajck, $font_path, $rand);
imagettftext($jpg_image, 15, 0, 28, 410, $blajck, $font_path, $c1);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
if($x == 12){
$jpg_image = imagecreatefromjpeg("Photo/IMG_12.jpg");
$black = imagecolorallocate($jpg_image, 182, 184, 183);
$blajck = imagecolorallocate($jpg_image, 182, 184, 183);
$blacpk = imagecolorallocate($jpg_image, 182, 184, 183);
$font_path = "Font/Yekan.TTF";
$mm= jdate("Y/m/d");
$gmm= jdate("H:i:s");
$rand= rand(000000000,999999999);
$rand2= rand(000000,999999);
$ppp= 'تراکنش موفق کسر از موجودی';
$rial = 'ریال';
$ag = $FaGD->persianText($ppp,'en','normal');
$rial = $FaGD->persianText($rial,'en','normal');
imagettftext($jpg_image, 22, 0, 55, 785, $blacpk, $font_path,"$a");
imagettftext($jpg_image, 22, 0, 55, 835, $blacpk, $font_path,"$c2");
imagettftext($jpg_image, 22, 0, 55, 935, $blajck, $font_path, "$mm $gmm");
imagettftext($jpg_image, 22, 0, 55, 735, $blajck, $font_path,"$m");
imagettftext($jpg_image, 22, 0, 55, 985, $blajck, $font_path, $rand);
imagettftext($jpg_image, 22, 0, 55, 1035, $blajck, $font_path, $rand2);
imagettftext($jpg_image, 22, 0, 55, 885, $blajck, $font_path, $c1);
imagepng($jpg_image);
imagedestroy($jpg_image);
}
}
return Create($_GET['x'],$_GET['m'],$_GET['c1'],Sstr($_GET['c2']),$_GET['n']);