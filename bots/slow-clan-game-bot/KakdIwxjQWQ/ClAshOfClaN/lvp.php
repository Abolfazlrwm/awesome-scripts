<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

function farm($level,$mode){
 
if($level >= 10){
if($mode == "speed"){
return "10";
}
if($mode == "update"){
return "max";
}
if($mode == "max"){
return "100000";
}
}elseif($level >= 9){
if($mode == "speed"){
return "9";
}

if($mode == "update"){
return "90000";
}
if($mode == "max"){
return "90000";
}
}elseif($level >= 8){
if($mode == "speed"){
return "8";
}

if($mode == "update"){
return "80000";
}
if($mode == "max"){
return "80000";
}
}elseif($level >= 7){
if($mode == "speed"){
return "7";
}
if($mode == "update"){
return "70000";
}
if($mode == "max"){
return "70000";
}
}elseif($level >= 6){
if($mode == "speed"){
return "6";
}
 
if($mode == "update"){
return "60000";
}
if($mode == "max"){
return "60000";
}
}elseif($level >= 5){
if($mode == "speed"){
return "5";
}
if($mode == "update"){
return "50000";
}
if($mode == "max"){
return "50000";
}
}elseif($level >= 4){
if($mode == "speed"){
return "4";
}
if($mode == "update"){
return "40000";
}
if($mode == "max"){
return "40000";
}
}elseif($level >= 3){
if($mode == "speed"){
return "3";
}
if($mode == "update"){
return "30000";
}
if($mode == "max"){
return "30000";
}
}elseif($level >= 2){
if($mode == "speed"){
return "2";
}
if($mode == "update"){
return "20000";
}
if($mode == "max"){
return "20000";
}
}elseif($level == 1){
if($mode == "speed"){
return "1";
}
if($mode == "update"){
return "10000";
}
 
if($mode == "max"){
return "10000";
}
}

}
 
////////////hmdemon///
?>