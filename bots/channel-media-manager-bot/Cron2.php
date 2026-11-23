<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
/*
Pv : @Cod_Arshiya

Channel : @Cod_Arshiya0
*/
define('XMSDN','tok');//توکن
function Tel($method,$Bot=[]){
$url = "https://api.telegram.org/bot".XMSDN."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$Bot);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
$GetFor = file_get_contents("For.txt");
if($GetFor == true){
$exp = explode("-",$GetFor);
$chat_id = $exp[0];
$message_id = $exp[1];
$cou = $exp[2];
$Alll = explode("\n",file_get_contents("PaefkSedV/PaefkSedVhU.txt"));
if(count($Alll) < $cou){
unlink("For.txt");
exit;
}
for($i=$cou;$i<= $cou + 100;$i++){
$user = $Alll[$i];
Tel('ForwardMessage',[
'chat_id'=>$user,
'from_chat_id'=>$chat_id,
'message_id'=>$message_id
]);
}
$cou = $cou + 100;
file_put_contents("For.txt","$chat_id-$message_id-$cou");
}
$GetFor = file_get_contents("Send.txt");
if($GetFor == true){
$exp = explode("-",$GetFor);
$chat_id = $exp[0];
$message_id = $exp[1];
$cou = $exp[2];
$Alll = explode("\n",file_get_contents("PaefkSedV/PaefkSedVhU.txt"));
if(count($Alll) < $cou){
unlink("Send.txt");
exit;
}
/*
Pv : @Cod_Arshiya

Channel : @Cod_Arshiya0
*/
for($i=$cou;$i<= $cou + 100;$i++){
$user = $Alll[$i];
Tel('sendMessage',[
'chat_id'=>$user,
'text'=>"$message_id",
]);
}
$cou = $cou + 100;
file_put_contents("Send.txt","$chat_id-$message_id-$cou");
}
/*
Pv : @Cod_Arshiya

Channel : @Cod_Arshiya0
*/