<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

include("config.php");
/*
نوشته شده توسط @Dev_Amiri و اوپن شده در کانال @CristalTeam 
 ذکر منبع اجباری!
 */
$sql = "CREATE TABLE `user` (
    `id` bigint(30) PRIMARY KEY,
	`step` varchar(150) DEFAULT NULL,
	) default charset = utf8mb4;
";
if ($connect->multi_query($sql) === TRUE) {
  echo "دیتابیس متصل و نصب شد";
} else {
  echo "Error creating table: " . $connect->error;
}
?>