<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
* Title: سورس ربات تلگرامی دانلود از پینترسته
* Developer: Mr Kigga
* Telegram: t.me/Mr_Kigga
* Channel: t.me/pulseXcode
*
* توجه: این سورس حق نشر دارد و بدون ذکر تگ سازنده غیرقانونی است.
*/


date_default_timezone_set('Asia/Tehran');

function bot($method, $datas = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.API_KEY.'/'.$method);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
	$curl_exec = curl_exec($ch);
	return json_decode($curl_exec, true);
}

function check_join($id , $message_id = null) {
	global $Channels;
	$in_ch = [];
	foreach ($Channels as $ch_id) {
		$type = bot("getChatMember", ["chat_id" => "@$ch_id", "user_id" => $id]);
		$type = (is_object($type)) ? $type->result->status : $type['result']['status'];
		if ($type == 'creator' || $type == 'administrator' || $type == 'member') {
			$in_ch[$ch_id] = $type;
			if (!is_null($message_id)) {
				bot('deleteMessage', [
				'chat_id' => $id,
				'message_id' => $message_id
				]);
			}
		} else
		{
			return false;
			break;
		}
	}
	return true;
}

function is_join($id) {
	global $Channels;
	$in_ch = [];
	foreach ($Channels as $ch_id) {
		$type = bot("getChatMember", ["chat_id" => "@$ch_id", "user_id" => $id]);
		$type = (is_object($type)) ? $type->result->status : $type['result']['status'];
		if ($type == 'creator' || $type == 'administrator' || $type == 'member') {
			$in_ch[$ch_id] = $type;
		} else
		{
			$keyboard = [];
			foreach ($Channels as $ch_id) {
				$ch_info = bot("getChat", ["chat_id" => "@$ch_id"]); //get channel
				$ch_name = (is_object($ch_info)) ? $ch_info->result->title : $ch_info['result']['title']; //channel name
				$keyboard[] = [['text' => $ch_name,
					'url' => "https://t.me/$ch_id"]];
			}
			$keyboard[] = [['text' => '✅ تایید عضویت',
				'callback_data' => 'join']];
			$text = 'لطفا در کانال های زیر عضو شده و سپس دکمه تایید عضویت را انتخاب کنید';

			bot('sendmessage', [
				'chat_id' => $id,
				'text' => "$text", 'parse_mode' => 'MarkDown', 'reply_markup' =>
				json_encode(['inline_keyboard' => $keyboard])]);
			break;
		}
	}
	return true;
}

function send($id , $text , $message_id = null, $parse_mode = null , $reply_markup = null)
{
	$result = bot('sendMessage',[
		'chat_id' => $id,
		'text' => $text,
		'reply_to_message_id' => $message_id,
		'parse_mode' => $parse_mode,
		'reply_markup' => $reply_markup
	]);
	return $result;
}

function answercallbackquery($data_id , $text)
{
	$result = bot('answercallbackquery', [
			'callback_query_id' => $data_id,
			'text' => $text,
			'show_alert' => true
	]);
	return $result;
}

function userExists($pdo, $chat_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE id = ?");
    $stmt->execute([$chat_id]);
    return $stmt->fetchColumn() > 0;
}

function saveUser($pdo, $data) {
    if (!userExists($pdo, $data['id'])) {
        $stmt = $pdo->prepare("INSERT INTO `users` (id, step, username, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['id'] ?? null,
            $data['step'] ?? null,
            $data['username'] ?? null,
            $data['first_name'] ?? null,
            $data['last_name'] ?? null
        ]);
    }
}

function update(PDO $pdo, int $userId, string $field, $value): bool {

    $sql = "UPDATE `users` SET `$field` = :value WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':value' => $value,
        ':id' => $userId
    ]);
}

function sendChatAction($id,$type)
{
    return bot('sendChatAction', [
        'chat_id' => $id,
        'action' => $type
    ]);
}

function sendReaction($id, $message_id, $emoji = '❤️')
{
    return bot('setMessageReaction', [
        'chat_id' => $id,
        'message_id' => $message_id,
        'reaction' => json_encode([['type' => 'emoji', 'emoji' => $emoji]])
    ]);
}

function deleteMessage($chat_id,$message_id)
{
	return bot('deleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id['result']['message_id']
    ]);
}


function getPinterestMedia($url, $return_url = true) {
 $api_url = "https://haji.s2025h.space/pin/?url=" . urlencode($url).'&client_key=keyvip';
    $response = file_get_contents($api_url);
    return json_decode($response, true)['download_url'];
 }

 return 'error';
}

function formatTime($seconds)
{
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf("%02d:%02d", $minutes, $seconds);
}

function checkTimeLimit($pdo, $userId)
{

    $stmt = $pdo->query("SELECT setting_value FROM bot_settings WHERE setting_name = 'request_cooldown'");
    $cooldown = (int)$stmt->fetchColumn();
    $cooldown = $cooldown > 0 ? $cooldown : 

    $stmt = $pdo->prepare("SELECT UNIX_TIMESTAMP(last_request_at) FROM `users` WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $lastRequestTimestamp = $stmt->fetchColumn();

    if ($lastRequestTimestamp === false || $lastRequestTimestamp === null) {
        return [true, 0]; 
    }

    $currentTimestamp = time();
    $diff = $currentTimestamp - $lastRequestTimestamp;

    if ($diff >= $cooldown) {
        return [true, 0];
    } else {
        $remaining = $cooldown - $diff;
        return [false, $remaining];
    }
}

function updateLastRequest($pdo, $userId)
{
    $stmt = $pdo->prepare("UPDATE `users` SET last_request_at = CURRENT_TIMESTAMP WHERE id = :id");
    $stmt->execute([':id' => $userId]);
}

function me(){
	return bot('getMe', []);
}


function is_bot_admin($chat_id, $bot_id) {

    $response = bot('getChatMember', [
        'chat_id' => $chat_id,
        'user_id' => $bot_id
    ]);


    if ($response && isset($response['result']['status'])) {
        $status = strtolower($response['result']['status']);
        
        return in_array($status, ['administrator', 'creator']);
    }

    return false;
}

function createInlineKeyboardButton($text, $callback_data) {
	return ['text' => $text,
		'callback_data' => $callback_data];
}


function getChatMember($chat_id, $user_id) {
    try {
        $response = bot('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id
        ]);
        
        return $response ?? null;
    } catch (Exception $e) {
        error_log("Error in getChatMember: " . $e->getMessage());
        return null;
    }
}


function saveGroup($pdo, $userId, $title, $type)
{
    if ($type == 'group' || $type == 'supergroup') {
    	$stmt = $pdo->prepare("INSERT IGNORE INTO `groups` (id, title, type) VALUES (:id, :title, :type)");
    $stmt->execute([
    	':id' => $userId,
    	':title' => $title,
    	':type' => $type
    	]);
    }
}

function user($pdo,$id) {
    

        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        return $group;
}

function isAdmin()
{
	global $from_id;
	global $admins;
	if (in_array($from_id,$admins)) {
		return true;
	} else {
		return false;
	}
}

function gregorian_to_jalali($gy, $gm, $gd){
    $g_d_m = array(0,31,59,90,120,151,181,212,243,273,304,334);
    $jy = ($gy <= 1600) ? 0 : 979;
    $gy -= ($gy <= 1600) ? 621 : 1600;
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 365 * $gy + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * ((int)($days / 12053));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    $jy += (int)(($days - 1) / 365);
    if($days > 365)
        $days = ($days - 1) % 365;
    $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return $jy.'/'.$jm.'/'.$jd;
}

function get_Date(){
	return gregorian_to_jalali(date('Y'), date('m'), date('d'));
}

function get_Time(){
	return date('H:i:s');
}

function updateSend($pdo, $target , $type_send , $chat_id , $message , $media , $send_count)
{
    $stmt = $pdo->prepare("UPDATE `send` SET 
                `target` = $target,
                `type_send` = $type_send,
                `chat_id` = 
                `message` = $message,
                `media` = $media,    
                `send_count` = $send_count
                WHERE id = :id");
            $stmt->execute([':id' => $send['id']]);
}

function channels($pdo) {
  
        $stmt = $pdo->query("SELECT link FROM `channels`");
        $data = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
        


}
function admins($pdo) {
    $stmt = $pdo->query("SELECT id FROM `admins`");
    $data = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    return $data ?: [];
}

function addAdmin(PDO $pdo, int $adminId): bool
{
        $stmt = $pdo->prepare("INSERT INTO `admins` (`id`) VALUES (:adminId)");
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        return $stmt->execute();
    
}


function deleteAdmin(PDO $pdo, int $adminId): bool
{
        $stmt = $pdo->prepare("DELETE FROM `admins` WHERE `id` = :adminId");
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        return $stmt->execute();
}

function addChannel(PDO $pdo, string $link): bool
{
    $stmt = $pdo->prepare("INSERT INTO `channels` (`link`) VALUES (:link)");
    $stmt->bindParam(':link', $link, PDO::PARAM_STR);
    return $stmt->execute();
}

function deleteChannel(PDO $pdo, string $link): bool
{
    $stmt = $pdo->prepare("DELETE FROM `channels` WHERE `link` = :link");
    $stmt->bindParam(':link', $link, PDO::PARAM_STR);
    return $stmt->execute();
}

function getAllAdmins(PDO $pdo): array
{
        $stmt = $pdo->query("SELECT id FROM `admins`");
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function getAllChannels(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT link FROM `channels`");
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function isAdmins(PDO $pdo, int $adminId): bool
{
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE id = :adminId");
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (bool)$stmt->fetchColumn();
}

function isChannels(PDO $pdo, string $link): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `channels` WHERE link = :link");
    $stmt->bindParam(':link', $link, PDO::PARAM_STR);
    $stmt->execute();
    
    return (bool)$stmt->fetchColumn();
}


function getCooldownTime($pdo) {
        $stmt = $pdo->query("SELECT `setting_value` FROM `bot_settings` WHERE `setting_name` = 'request_cooldown'");
        $cooldown = (int)$stmt->fetchColumn();
        return $cooldown > 0 ? $cooldown : 15;
}



function setCooldownTime($pdo, $seconds) {
        $seconds = max(5, min(3600, (int)$seconds)); 
        $stmt = $pdo->prepare("INSERT INTO `bot_settings` (setting_name, setting_value) 
                              VALUES ('request_cooldown', :value)
                              ON DUPLICATE KEY UPDATE setting_value = :value");
        $stmt->execute([':value' => $seconds]);
        return true;
}



function getDeleteMediaTime($pdo) {
    $stmt = $pdo->query("SELECT `setting_value` FROM `bot_settings` WHERE `setting_name` = 'delete_media_time'");
        $cooldown = (int)$stmt->fetchColumn();
        return $cooldown > 0 ? $cooldown : 15;
}

function setDeleteMediaTime($pdo, $seconds) {
        $seconds = max(5, min(3600, (int)$seconds)); 
        $stmt = $pdo->prepare("INSERT INTO `bot_settings` (setting_name, setting_value) 
                              VALUES ('delete_media_time', :value)
                              ON DUPLICATE KEY UPDATE setting_value = :value");
        $stmt->execute([':value' => $seconds]);
        return true;
}

/*
* پایان سورس ربات تلگرامی دانلود از پینترسته
* توسعه‌دهنده: Mr Kigga
* تلگرام: t.me/Mr_Kigga
* چنل: t.me/pulseXcode
*
* هشدار: هرگونه کپی‌برداری یا انتشار بدون ذکر منبع پیگرد قانونی دارد.
* این سورس متعلق به سازنده اصلی است و هیچ‌کس حق ندارد آن را به نام خود منتشر کند.
*/
?>