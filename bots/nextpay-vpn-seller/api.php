<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$apiToken = $_GET['api_token'];

function createServer($apiToken, $serverName, $serverType, $image, $loc) {
    $url = 'https://api.hetzner.cloud/v1/servers';
    
    $data = array(
        'name' => $serverName,
        'server_type' => $serverType,
        'image' => $image,
        'location' => $loc
    );
    
    $headers = array(
        'Authorization: Bearer '.$apiToken,
        'Content-Type: application/json',
    );
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo 'Error: '.curl_error($ch);
    }
    
    curl_close($ch);
    
    return json_decode($response);
}
function deleteServer($apiToken, $serverId) {
    $url = 'https://api.hetzner.cloud/v1/servers/'.$serverId;
    
    $headers = array(
        'Authorization: Bearer '.$apiToken,
        'Content-Type: application/json',
    );
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo 'Error: '.curl_error($ch);
    }
    
    curl_close($ch);
    
    return json_decode($response);
}

function getServersList($apiToken) {
    $url = 'https://api.hetzner.cloud/v1/servers';
    
    $headers = array(
        'Authorization: Bearer '.$apiToken,
        'Content-Type: application/json',
    );
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo 'Error: '.curl_error($ch);
    }
    
    curl_close($ch);
    
    return json_decode($response);
}
function power($apiToken, $server_id , $power) {
    $url = "https://api.hetzner.cloud/v1/servers/{$server_id}/actions/$power";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiToken}",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
}

function get_data($apiToken, $server_id) {
    $url = "https://api.hetzner.cloud/v1/servers/{$server_id}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiToken}",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    if (isset($data['server'])) {
        

        return $data;
    } else {
        return false;
    }
}

function rebuild($apiToken, $server_id) {
    $url = "https://api.hetzner.cloud/v1/servers/{$server_id}/actions/rebuild";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiToken}",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "image" => 'ubuntu-20.04'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result;
}

function resetServerPassword($apiToken, $serverId) {
    $url = "https://api.hetzner.cloud/v1/servers/{$serverId}/actions/reset_password";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer {$apiToken}",
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result;
}


if(isset($_GET['create'])){
    $createdServer = createServer($apiToken, $_GET['name'], $_GET['server_type'], 'ubuntu-20.04',$_GET['loc']);
    echo json_encode($createdServer);
    
}
if(isset($_GET['delete'])){
    $deletedServer = deleteServer($apiToken, $_GET['id']);
    echo json_encode($deletedServer);
}
if(isset($_GET['list_all'])){
    $serversList = getServersList($apiToken);
    
    foreach ($serversList->servers as $server) {
        echo json_encode(['name' => $server->name, 'ipv4' => $server->public_net->ipv4->ip,'ipv6' => $server->public_net->ipv6->ip,'id'=>$server->id,'model' => $server->server_type->name]);
    }
}
if(isset($_GET['poweroff'])){
    $deletedServer = power($apiToken, $_GET['id'] , 'poweroff');
    echo json_encode($deletedServer,true);
}
if(isset($_GET['poweron'])){
    $deletedServer = power($apiToken, $_GET['id'] , 'poweron');
    echo json_encode($deletedServer,true);
}
if(isset($_GET['data'])){
    $deletedServer = get_data($apiToken, $_GET['id']);
    $server_info = $deletedServer['server'];
    $ip = $server_info['public_net']['ipv4']['ip'];
    echo json_encode(['outgoing_traffic' => $server_info['outgoing_traffic'],'ram'=>$server_info['server_type']['memory'],'cpu'=>$server_info['server_type']['cores'],'disk'=>$server_info['server_type']['disk'], 'ipv4' => $server_info['public_net']['ipv4']['ip'],'ipv6' => $server_info['public_net']['ipv6']['ip'],'status'=>$server_info['status']]);
}
if(isset($_GET['rebuild'])){
    $deletedServer = rebuild($apiToken, $_GET['id']);
    echo json_encode($deletedServer,true);
}
if(isset($_GET['reset'])){
    $deletedServer = resetServerPassword($apiToken,$_GET['id']);
    echo json_encode(['root_password' => $deletedServer['root_password']]);
}
?>