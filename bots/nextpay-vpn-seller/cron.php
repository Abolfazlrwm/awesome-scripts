<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
include 'database.php';
function info($plan){
    switch($plan){
        case 'cx11':
            return '2-1-20-3.85';
        case 'cpx11':
            return '2-2-40-4.35';
        case 'cx21':
            return '4-2-40-5.35';
        case 'cpx21':
            return '4-3-80-7.55';
        case 'cx31':
            return '8-2-80-9.70';
        case 'cpx31':
            return '8-4-160-13.60';
        case 'cx41':
            return '16-4-240-17.40';
        case 'cpx41':
            return '16-8-240-25.20';
        default:
            return false;
    }
}

# hetzner 
$request = file_get_contents("https://bot.sinzavpn.fun/sell/api.php?list_all&api_token=$api_key");
$response = json_decode($request, true);
if(isset($response->error->message)){
    die;
}
preg_match_all('/({[^}]+})/', $request, $matches);
foreach ($matches[0] as $match) {
    $decoded = json_decode($match, true);
    $from_id = explode("-",$decoded['name'])[0];
    
    $get_info = info($decoded['model']);
    $payment = explode("-",$get_info)[3] * $euro;
    $payhour = intval($payment/30/24);
    $user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `user_id` = $from_id"));
    $id_server = $decoded['id'];
    if($user['coin'] >= $payhour){
        if(file_exists("time_server/hetzner/$id_server")){
            unlink("time_server/hetzner/$id_server");
            file_get_contents("https://bot.sinzavpn.fun/sell/api.php?poweron&api_token=$api_key&id=$id_server");
        }
        update('users', ['coin' => $user['coin'] - $payhour], ['user_id' => $from_id]);
    }else{
        
        file_get_contents("https://bot.sinzavpn.fun/sell/api.php?poweroff&api_token=$api_key&id=$id_server");
        if(file_exists("time_server/hetzner/$id_server")){
            $plus = file_get_contents("time_server/hetzner/$id_server") + 1;
            file_put_contents("time_server/hetzner/$id_server",$plus);
        }else{
            file_put_contents("time_server/hetzner/$id_server",'1');
        }
        if(file_get_contents("time_server/hetzner/$id_server") >= '24'){
            file_get_contents("https://bot.sinzavpn.fun/sell/api.php?delete&api_token=$api_key&id=$id_server");
            unlink("time_server/hetzner/$id_server");
        }
    }
}
