<?php
// Telegram: @Abolfazl_rwm
error_reporting(0);
//----------------------------------------//
// تنظیمات — بهتر است توکن‌ها را به‌صورت متغیر محیطی ست کنید
define('API_KEY', getenv('BOT_TOKEN') ?: 'توکن ربات');
define('PARTNER_BOT_TOKEN', getenv('PARTNER_BOT_TOKEN') ?: 'توکن ربات دوم');
$admins = getenv('ADMIN_USERNAME') ?: 'admin';
$boter_id = getenv('BOT_USERNAME') ?: 'mybot';
$id_adady = (string) (getenv('ADMIN_ID') ?: '0');
//----------------------------------------//
?>
