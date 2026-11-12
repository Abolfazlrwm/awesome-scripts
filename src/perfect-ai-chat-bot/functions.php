<?php
require_once __DIR__ . '/config.php';

function apiRequest($method, $params = [])
{
    if (!is_string($method) || $method === '') {
        error_log('apiRequest: نام متد نامعتبر است');
        return null;
    }

    foreach ($params as $key => $val) {
        if (!is_string($val) && !is_numeric($val)) {
            $params[$key] = json_encode($val);
        }
    }

    $url = 'https://api.telegram.org/bot' . API_KEY . '/' . $method;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $res = curl_exec($ch);

    if ($res === false) {
        error_log('خطای cURL در متد ' . $method . ': ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $decoded = json_decode($res);
    if (!$decoded || empty($decoded->ok)) {
        error_log('خطای API تلگرام در متد ' . $method . ': ' . $res);
    }
    return $decoded;
}

function sendMessage($chatId, $text, $replyMarkup = null, $parseMode = 'Markdown')
{
    $params = ['chat_id' => $chatId, 'text' => $text];
    if ($parseMode) {
        $params['parse_mode'] = $parseMode;
    }
    if ($replyMarkup !== null) {
        $params['reply_markup'] = $replyMarkup;
    }
    return apiRequest('sendMessage', $params);
}

function forwardMsg($toChatId, $fromChatId, $messageId)
{
    return apiRequest('forwardMessage', [
        'chat_id' => $toChatId,
        'from_chat_id' => $fromChatId,
        'message_id' => $messageId,
    ]);
}

function copyMsg($toChatId, $fromChatId, $messageId)
{
    return apiRequest('copyMessage', [
        'chat_id' => $toChatId,
        'from_chat_id' => $fromChatId,
        'message_id' => $messageId,
    ]);
}

function answerCallback($callbackId, $text = '', $showAlert = false)
{
    return apiRequest('answerCallbackQuery', [
        'callback_query_id' => $callbackId,
        'text' => $text,
        'show_alert' => $showAlert,
    ]);
}

function mdEscape($text)
{
    return str_replace(['_', '*', '`', '['], ['\_', '\*', '\`', '\['], (string) $text);
}

function isMemberOfRequiredChannel($userId)
{
    if (!REQUIRED_CHANNEL) {
        return true; // قفل غیرفعاله
    }
    $res = apiRequest('getChatMember', ['chat_id' => REQUIRED_CHANNEL, 'user_id' => $userId]);
    if (!$res || empty($res->ok)) {
        // اگه نشد بررسی کنیم (مثلا ربات ادمین اون کانال نیست)، برای امنیت بیشتر دسترسی داده نمیشه
        return false;
    }
    $status = $res->result->status ?? 'left';
    return in_array($status, ['creator', 'administrator', 'member'], true);
}

function atomicWriteJson($path, $data)
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $fp = @fopen($path, 'c+');
    if ($fp === false) {
        error_log('امکان باز کردن فایل برای نوشتن وجود ندارد: ' . $path);
        return false;
    }
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function readJsonFile($path)
{
    if (!file_exists($path)) {
        return null;
    }
    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return null;
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : null;
}

function ensureDataDirs()
{
    $dirs = [DATA_DIR, DATA_DIR . '/users', DATA_DIR . '/channels', DATA_DIR . '/owners'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
    $htaccess = DATA_DIR . '/.htaccess';
    if (!file_exists($htaccess)) {
        @file_put_contents($htaccess, "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Deny from all\n</IfModule>\n");
    }
}

function userFile($id)
{
    return DATA_DIR . "/users/{$id}.json";
}

function getUser($id)
{
    return readJsonFile(userFile($id));
}

function saveUser($user)
{
    return atomicWriteJson(userFile($user['id']), $user);
}

function getOrCreateUser($id, $firstName = '', $username = '')
{
    $user = getUser($id);
    if ($user === null) {
        $user = [
            'id' => $id,
            'first_name' => $firstName,
            'username' => $username,
            'state' => 'none',
            'active_channel' => null,
            'joined_at' => time(),
        ];
        saveUser($user);
        return $user;
    }
    if (($user['first_name'] ?? '') !== $firstName || ($user['username'] ?? '') !== $username) {
        $user['first_name'] = $firstName;
        $user['username'] = $username;
        saveUser($user);
    }
    return $user;
}

function getAllUserIds()
{
    $files = glob(DATA_DIR . '/users/*.json');
    if (!$files) {
        return [];
    }
    $ids = [];
    foreach ($files as $f) {
        $ids[] = (int) basename($f, '.json');
    }
    return $ids;
}

function channelFile($key)
{
    return DATA_DIR . "/channels/{$key}.json";
}

function getChannel($key)
{
    if (!$key) {
        return null;
    }
    return readJsonFile(channelFile($key));
}

function saveChannel($channel)
{
    return atomicWriteJson(channelFile($channel['key']), $channel);
}

function deleteChannel($key)
{
    $channel = getChannel($key);
    if ($channel === null) {
        return false;
    }
    removeOwnerChannelKey($channel['owner_id'], $key);
    @unlink(channelFile($key));
    return true;
}

function generateChannelKey()
{
    do {
        $key = substr(bin2hex(random_bytes(4)), 0, 8);
    } while (file_exists(channelFile($key)));
    return $key;
}

function getAllChannelKeys()
{
    $files = glob(DATA_DIR . '/channels/*.json');
    if (!$files) {
        return [];
    }
    $keys = [];
    foreach ($files as $f) {
        $keys[] = basename($f, '.json');
    }
    return $keys;
}

function findChannelByChatId($chatId)
{
    foreach (getAllChannelKeys() as $key) {
        $channel = getChannel($key);
        if ($channel && (string) $channel['chat_id'] === (string) $chatId) {
            return $key;
        }
    }
    return null;
}

function ownerIndexFile($ownerId)
{
    return DATA_DIR . "/owners/{$ownerId}.json";
}

function getOwnerChannelKeys($ownerId)
{
    $data = readJsonFile(ownerIndexFile($ownerId));
    return is_array($data) ? $data : [];
}

function addOwnerChannelKey($ownerId, $key)
{
    $keys = getOwnerChannelKeys($ownerId);
    if (!in_array($key, $keys, true)) {
        $keys[] = $key;
        atomicWriteJson(ownerIndexFile($ownerId), $keys);
    }
}

function removeOwnerChannelKey($ownerId, $key)
{
    $keys = getOwnerChannelKeys($ownerId);
    $keys = array_values(array_diff($keys, [$key]));
    atomicWriteJson(ownerIndexFile($ownerId), $keys);
}

function isAdmin($id)
{
    return in_array((int) $id, ADMIN_IDS, true);
}

function mainMenuKeyboard($user)
{
    $rows = [];
    $rows[] = [['text' => '🗨 شروع چت کردن']];
    $rows[] = [['text' => '➕ فعال‌سازی ربات در کانال من']];
    if (!empty(getOwnerChannelKeys($user['id']))) {
        $rows[] = [['text' => '📋 کانال‌های من']];
    }
    return json_encode(['keyboard' => $rows, 'resize_keyboard' => true]);
}

function backKeyboard()
{
    return json_encode(['keyboard' => [[['text' => '🔙 بازگشت']]], 'resize_keyboard' => true]);
}

function endChatKeyboard()
{
    return json_encode(['keyboard' => [[['text' => '✅ پایان چت']]], 'resize_keyboard' => true]);
}
