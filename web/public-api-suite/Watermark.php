<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

if(isset($_GET['1'])){
$top_file = 'image1.png';
$bottom_file = 'image2.png';

$top = imagecreatefrompng($top_file);
$bottom = imagecreatefrompng($bottom_file);

// show images
echo "<img src=\"".$top_file."\">";
echo "<br /><br />";
echo "<img src=\"".$bottom_file."\">";

// get current width/height
list($top_width, $top_height) = getimagesize($top_file);
list($bottom_width, $bottom_height) = getimagesize($bottom_file);

// compute new width/height
$new_width = ($top_width > $bottom_width) ? $top_width : $bottom_width;
$new_height = $top_height + $bottom_height;

// create new image and merge
$new = imagecreatetruecolor($new_width, $new_height);
imagecopy($new, $top, 0, 0, 0, 0, $top_width, $top_height);
imagecopy($new, $bottom, 0, $top_height+1, 0, 0, $bottom_width, $bottom_height);

// save to file
$new_name='merged_image.png';
imagepng($new, $new_name);

// show image
echo "<br /><br />";
echo "<img src=\"".$new_name."\">";

// free the memory
imagedestroy($top);
imagedestroy($bottom);
imagedestroy($new);
}
if(isset($_GET['2'])){
$image_name='image.jpeg';
$logo_name='logo.png';
$im_1 = imagecreatefromjpeg($image_name);
$im_2 = imagecreatefrompng($logo_name);
echo "<img src=\"".$image_name."\">";

$marge_right = 10;
$marge_bottom = 10;
$sx = imagesx($im_2);
$sy = imagesy($im_2);

imagecopy($im_1, $im_2, imagesx($im_1) - $sx - $marge_right, imagesy($im_1) - $sy - $marge_bottom, 0, 0, imagesx($im_2), imagesy($im_2));

$second_image_name='image_with_logo.png';
imagepng($im_1, $second_image_name);
echo "<br /><br />";
echo "<img src=\"".$second_image_name."\">";
imagedestroy($im_1);
imagedestroy($im_2);
}
if(isset($_GET['3'])){
$image_name='image.jpeg';
$im = imagecreatefromjpeg($image_name);
$im_x_length=imagesx($im);
$im_y_length=imagesy($im);
imagedestroy($im);
echo "image X length : ".$im_x_length." pixel";
echo "<br />";
echo "image Y length : ".$im_y_length." pixel";
}
if(isset($_GET['4'])){
// Load the stamp and the photo to apply the watermark to
$image_name='image.jpeg';
$second_image_name='image_stamp.png';
$im = imagecreatefromjpeg($image_name);
echo "<img src=\"".$image_name."\">";

// First we create our stamp image manually from GD
$stamp = imagecreatetruecolor(180, 70);
imagefilledrectangle($stamp, 0, 0, 179, 69, 0x0000FF);
imagefilledrectangle($stamp, 9, 9, 170, 60, 0xFFFFFF);
$im = imagecreatefromjpeg($image_name);
imagestring($stamp, 5, 20, 20, 'kelidestan.com', 0x0000FF);
imagestring($stamp, 3, 20, 40, 'keys', 0x0000FF);

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 100;
$marge_bottom = 100;
$sx = imagesx($stamp);
$sy = imagesy($stamp);

// Merge the stamp onto our photo with an opacity of 50%
imagecopymerge($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp), 50);

// Save the image to file and free memory
imagepng($im, $second_image_name);
echo "<br /><br />";
echo "<img src=\"".$second_image_name."\">";
imagedestroy($im);
}
?>