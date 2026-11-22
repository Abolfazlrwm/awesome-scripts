<?php
error_reporting(0);
//-----------------------------------------------------------------------------------------------
$API_KC = ""; // توکن رو قرار بدید
$admins = array(,,);
$Dev = ""; 
$Channel = ""; 
//-----------------------------------------------------------------------------------------------
$dbname = ""; //  Name db  
$username = ""; // Username db 
$password = ''; // Pass username db 
//-----------------------------------------------------------------------------------------------
$connect = mysqli_connect("localhost", $username, $password, $dbname);
mysqli_query($connect,"SET SESSION collation_connection = 'utf8_persian_ci'");
//-----------------------------------------------------------------------------------------------
$API_TEL = json_decode(file_get_contents('https://api.telegram.org/bot'.$API_KC.'/getme'));
$botid = $API_TEL->result->id;
$bottag = $API_TEL->result->username;
