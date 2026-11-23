<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
developer : t.me/Dev_Amiri 
Channel : t.me/CristalTeam 
*/

$domain = ''; // دامین شما

if (!file_exists("pic")) {
    mkdir("pic", 0777, true); 
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'خطا در برقراری ارتباط!' . json_last_error_msg()]);
    exit;
}

$result = [];
if (isset($data['results'][0])) { 
    $person = $data['results'][0];
    $profilePictureUrl = $person['picture']['large'];
    $filename = basename($profilePictureUrl);
    $localFilePath = "pic/" . $filename;
    $imageContent = file_get_contents($profilePictureUrl);
    if ($imageContent !== false) {
        file_put_contents($localFilePath, $imageContent);
    } else {
        $localFilePath = null;  
    }

    $result = [
        'name'            => $person['name']['first'] . ' ' . $person['name']['last'],
        'gender'          => $person['gender'],
        'race'            => 'Unknown', 
        'birthday'        => $person['dob']['date'], 
        'address'         => [
            'street' => $person['location']['street']['number'] . ' ' . $person['location']['street']['name'],
            'city'   => $person['location']['city'],
            'state'  => $person['location']['state'],
            'zip'    => $person['location']['postcode'],
        ],
        'telephone'       => $person['phone'],
        'mobile'          => $person['cell'],
        'profile_picture' => $domain . $localFilePath  
    ];
}
header('Content-Type: application/json');
echo json_encode(['developer' => 't.me/Dev_Amiri', 'result' => $result]);
/*
developer : t.me/Dev_Amiri 
Channel : t.me/CristalTeam 
*/
?>
