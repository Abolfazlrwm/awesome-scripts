<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

require_once 'config.php';


class installdata {
    private $conn;

    public function __construct($localhost, $user, $password, $name) {
        $this->conn = new mysqli($localhost, $user, $password, $name);

        if ($this->conn->connect_error) {
            die($this->conn->connect_error);
    }
        }

    public function creat() {
$this->conn->multi_query("CREATE TABLE `user` (`id` bigint PRIMARY KEY,`step` varchar(150) DEFAULT NULL,`step2` varchar(150) DEFAULT NULL,`step3` varchar(150) DEFAULT NULL,`time` varchar(150) DEFAULT NULL,`coin` float DEFAULT '0',`trc20_wallet` varchar(34) DEFAULT NULL,`trc20_privatekey` varchar(64) DEFAULT NULL,`hex` varchar(64) DEFAULT NULL,) default charset = utf8mb4;");
$this->conn->multi_query("CREATE TABLE `block` (`id` bigint NOT NULL) default charset = utf8mb4;");
$this->conn->multi_query("CREATE TABLE `trx_payments` (`txid` varchar(64) NOT NULL) default charset = utf8mb4;");
$this->conn->multi_query("CREATE TABLE `settingb` ( `idadmin` bigint DEFAULT NULL,) default charset = utf8mb4;");
$this->conn->multi_query("CREATE TABLE `channels` (`id` text NOT NULL,`admin` bigint NOT NULL,`tarikh` bigint NOT NULL) default charset = utf8mb4;");
$this->conn->multi_query("CREATE TABLE `sendpm` (`step` varchar(20) DEFAULT NULL,`text` text DEFAULT NULL,`chat` varchar(100) DEFAULT NULL,`user` bigint DEFAULT '0',`pan` bigint DEFAULT '0') default charset = utf8mb4;");
$this->conn->multi_query("INSERT INTO `sendpm` (step, text, chat, user, pan) VALUES ('', '', '', 0, 0)");
$this->conn->multi_query("INSERT INTO `trx_payments` (wallet) VALUES ('none')");
    }
    public function closeConnection() {
        $this->conn->close();
    }
    }
$installer = new installdata($localhost, $user, $password, $name);
$installer->creat();
$installer->closeConnection();

echo "<b>The database is installed</b>";


