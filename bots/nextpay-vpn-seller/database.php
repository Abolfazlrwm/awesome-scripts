<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$database = [

    'servername' => 'localhost',
    'username'   => '',
    'passwoed'   => '',
    'dbname'     => '',

];
$db = new mysqli($database['servername'], $database['username'], $database['passwoed'], $database['dbname']);
if ($db->query("CREATE TABLE IF NOT EXISTS `users` (
      `user_id` BIGINT PRIMARY KEY,
      `step` VARCHAR(50) DEFAULT NULL,
      `coin` VARCHAR(25) DEFAULT '0'
    )CHARSET = utf8mb4 COLLATE utf8mb4_general_ci;") == false) {
    echo "Error creating table - users: " . $db->error;
}

$euro = '75000';
$api_key = ''; // api hetzner

function update($table, $data, $where){
    global $db;

    $set = array();
    foreach ($data as $key => $value) {
        $set[] = "$key = '" . mysqli_real_escape_string($db, $value) . "'";
    }
    $set = implode(', ', $set);

    $where_clause = '';
    if (is_array($where)) {
        $where_clause = ' WHERE ';
        $where_parts = array();
        foreach ($where as $key => $value) {
            $where_parts[] = "$key = '" . mysqli_real_escape_string($db, $value) . "'";
        }
        $where_clause .= implode(' AND ', $where_parts);
    } elseif (!empty($where)) {
        $where_clause = ' WHERE ' . mysqli_real_escape_string($db, $where);
    }

    $stmt = mysqli_prepare($db, "UPDATE $table SET $set$where_clause");
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return mysqli_affected_rows($db);
}

function insert($table, $data) {
    global $db;
    $insert_keys = array();
    $insert_values = array();

    foreach ($data as $key => $value) {
        $insert_keys[] = mysqli_real_escape_string($db, $key);
        $insert_values[] = mysqli_real_escape_string($db, $value);
    }

    $stmt = mysqli_prepare($db, "INSERT INTO $table (".implode(',', $insert_keys).") VALUES (" . implode(',', array_fill(0, count($data), '?')) . ")");
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($insert_values)), ...$insert_values);

    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($db);
    } else {
        return false;
    }
}