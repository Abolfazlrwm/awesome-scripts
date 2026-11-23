<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
@$server = 'localhost';
@$username = 'likes';
@$password = 'PUT-YOUR-KEY-HERE';
@$db = 'likes';
$connect = mysqli_connect($server,$username,$password,$db);
//========================== // table creator // ==============================
mysqli_multi_query($connect,"CREATE TABLE `user` (
  `id` bigint PRIMARY KEY,
  `step` TEXT DEFAULT NULL
    ) default charset = utf8mb4;
    CREATE TABLE `like` (
    `hash` TEXT DEFAULT NULL,
    `type` TEXT DEFAULT NULL,
    `file` TEXT DEFAULT NULL,
    `caption` TEXT DEFAULT NULL,
    `join` TEXT DEFAULT NULL,
    `button` TEXT DEFAULT NULL,
    `admin` bigint NOT NULL,
    `likes1` bigint NOT NULL,
    `likes2` bigint NOT NULL,
    `likes3` bigint NOT NULL,
    `likes4` bigint NOT NULL,
    `likes5` bigint NOT NULL,
    `likes6` bigint NOT NULL
    ) default charset = utf8mb4;
    CREATE TABLE `set` (
    `id` bigint NOT NULL,
    `post` bigint NOT NULL
    ) default charset = utf8mb4; 
    CREATE TABLE `history` (
    `id` bigint PRIMARY KEY,
    `history` TEXT DEFAULT NULL
    ) default charset = utf8mb4;
    ");
//========================== // Check connection // ==============================
if ($connect->connect_error) {
   die("خطا در ارتصال به خاطره :" . $connect->connect_error);
}
  echo "دیتابیس متصل و نصب شد ."
?>