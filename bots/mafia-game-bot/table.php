<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
@$server = 'localhost';
@$username = 'snowytea_mf';
@$password = 'PUT-YOUR-KEY-HERE';
@$db = 'snowytea_mf';
$connect = mysqli_connect($server,$username,$password,$db);
//========================== // table creator // ==============================
mysqli_multi_query($connect,"CREATE TABLE `user` (
  `id` bigint PRIMARY KEY,
  `step` TEXT DEFAULT NULL,
  `name` TEXT DEFAULT NULL,
  `coin` bigint NOT NULL,
  `tas` bigint DEFAULT 0,
  `score` bigint DEFAULT 0,
  `game` bigint DEFAULT 0,
  `win` bigint DEFAULT 0,
  `warn` TEXT DEFAULT NULL,
  `data` TEXT DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `vip` varchar(70) DEFAULT NULL,
  `server` TEXT DEFAULT NULL,
  `like` bigint DEFAULT 0,
  `follower` bigint DEFAULT 0,
  `following` bigint DEFAULT 0
    ) default charset = utf8mb4;
    CREATE TABLE `like` (
    `id` bigint NOT NULL,
    `who` bigint NOT NULL
    ) default charset = utf8mb4; 
    CREATE TABLE `follower` (
    `id` bigint NOT NULL,
    `who` bigint NOT NULL
    ) default charset = utf8mb4; 
    CREATE TABLE `following` (
    `id` bigint NOT NULL,
    `who` bigint NOT NULL
    ) default charset = utf8mb4; 
    ");
//========================== // Check connection // ==============================
if ($connect->connect_error) {
   die("خطا در ارتصال به خاطره :" . $connect->connect_error);
}
  echo "دیتابیس متصل و نصب شد ."
?>