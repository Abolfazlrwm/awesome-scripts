<?php
// IR Creator
// مسیر فایل‌ها
$style1 = __DIR__ . '/1.jpg';
$style2 = __DIR__ . '/2.jpg';
$style3 = __DIR__ . '/3.jpg'; // فایل سوم
$font = __DIR__ . '/font.ttf';

// پارامترهای ورودی
$captcha = isset($_GET['cap']) ? $_GET['cap'] : '12345';
$style = isset($_GET['style']) ? $_GET['style'] : 1;

// انتخاب تصویر زمینه
if ($style == 3 && file_exists($style3)) {
    $background = $style3;
} elseif ($style == 2 && file_exists($style2)) {
    $background = $style2;
} else {
    $background = $style1;
}

// بررسی وجود فایل و فرمت تصویر
$imageInfo = getimagesize($background);
if (!$imageInfo) {
    die('فرمت تصویر پشتیبانی نمی‌شود: ' . $background);
}

switch ($imageInfo['mime']) {
    case 'image/jpeg':
        $image = imagecreatefromjpeg($background);
        break;
    case 'image/png':
        $image = imagecreatefrompng($background);
        break;
    default:
        die('فرمت تصویر پشتیبانی نمی‌شود: ' . $imageInfo['mime']);
}

// تعریف رنگ‌ها
$lightBlue = imagecolorallocate($image, 173, 216, 230); // آبی کمرنگ
$softerBlue = imagecolorallocate($image, 100, 149, 237); // آبی کمی کمرنگ‌تر
$purple = imagecolorallocate($image, 128, 0, 128);       // بنفش
$shadowColor = imagecolorallocate($image, 0, 0, 0);      // سایه سیاه

// مختصات متن
$imageWidth = imagesx($image);
$imageHeight = imagesy($image);
$fontSize = 95; // اندازه فونت
$angle = 0; // زاویه متن
$textBox = imagettfbbox($fontSize, $angle, $font, $captcha);
$textWidth = $textBox[4] - $textBox[6];
$textHeight = $textBox[1] - $textBox[7];
$x = ($imageWidth - $textWidth) / 2;
$y = ($imageHeight + $textHeight) / 2;

// رسم متن با توجه به استایل
if ($style == 3) {
    // استایل 3: سمت چپ آبی کمرنگ، سمت راست بنفش
    $midPoint = floor(strlen($captcha) / 2);
    $textLeft = substr($captcha, 0, $midPoint); // نیمه اول
    $textRight = substr($captcha, $midPoint);  // نیمه دوم

    // افزودن سایه
    imagettftext($image, $fontSize, $angle, $x + 2, $y + 2, $shadowColor, $font, $captcha);

    // افزودن متن نیمه اول (آبی کمرنگ)
    imagettftext($image, $fontSize, $angle, $x, $y, $lightBlue, $font, $textLeft);

    // اندازه‌گیری عرض نیمه اول برای تعیین مکان شروع نیمه دوم
    $textLeftBox = imagettfbbox($fontSize, $angle, $font, $textLeft);
    $textLeftWidth = $textLeftBox[4] - $textLeftBox[6];
    $xRight = $x + $textLeftWidth;

    // افزودن متن نیمه دوم (بنفش)
    imagettftext($image, $fontSize, $angle, $xRight, $y, $purple, $font, $textRight);

} elseif ($style == 2) {
    // استایل 2: آبی کمرنگ برای کل متن
    imagettftext($image, $fontSize, $angle, $x + 2, $y + 2, $shadowColor, $font, $captcha);
    imagettftext($image, $fontSize, $angle, $x, $y, $lightBlue, $font, $captcha);
} else {
    // استایل 1: آبی کمی کمرنگ‌تر برای کل متن
    imagettftext($image, $fontSize, $angle, $x + 2, $y + 2, $shadowColor, $font, $captcha);
    imagettftext($image, $fontSize, $angle, $x, $y, $softerBlue, $font, $captcha);
}

// تنظیم هدر و خروجی تصویر
header('Content-Type: image/jpeg');

imagejpeg($image);
imagedestroy($image);
?>
