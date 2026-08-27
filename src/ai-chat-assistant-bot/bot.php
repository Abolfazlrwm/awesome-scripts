<?php
// ================== تنظیمات پایه ===================
define('BOT_TOKEN', 'توکن ربات');//توکن
define('ADMIN_ID', 123456789); // آیدی عددی ادمین را اینجا وارد کنید
define('LOG_FILE', 'bot_log.txt');
define('DATA_FILE', 'bot_data.json');

// ================== کلاس دیتابیس ==================
class Database {
    private $file;
    
    public function __construct() {
        $this->file = DATA_FILE;
        if (!file_exists($this->file)) {
            $this->initData();
        }
    }
    
    private function initData() {
        $data = array(
            'users' => array(),
            'admins' => array(ADMIN_ID),
            'blocked' => array(),
            'settings' => array(
                'default_model' => 'gpt',
                'force_join' => true,
                'welcome_message' => "🚀 به ربات هوش مصنوعی خوش آمدید!\n\nبرای شروع /help را بزنید.",
                'api_keys' => array(
                    'gpt' => 'YOUR_API_KEY_HERE',
                    'deepseek' => 'YOUR_API_KEY_HERE',
                    'grok' => 'YOUR_API_KEY_HERE'
                ),
                'api_urls' => array(
                    'gpt' => 'https://api.fast-creat.ir/gpt/chat',
                    'deepseek' => 'https://api.fast-creat.ir/deepseek',
                    'grok' => 'https://api.fast-creat.ir/grokai'
                ),
                'channels' => array(),
                'style' => array(
                    'theme' => 'dark',
                    'emoji_prefix' => '🤖'
                )
            ),
            'user_states' => array(),
            'feedback' => array()
        );
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function getAll() {
        if (!file_exists($this->file)) return null;
        return json_decode(file_get_contents($this->file), true);
    }
    
    public function get($key) {
        $data = $this->getAll();
        return isset($data[$key]) ? $data[$key] : null;
    }
    
    public function set($key, $value) {
        $data = $this->getAll();
        if (!$data) $data = array();
        $data[$key] = $value;
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function getSetting($key, $default = null) {
        $settings = $this->get('settings');
        if (!is_array($settings)) return $default;
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
    
    public function setSetting($key, $value) {
        $settings = $this->get('settings');
        if (!is_array($settings)) $settings = array();
        $settings[$key] = $value;
        $this->set('settings', $settings);
    }
    
    public function addUser($user_id, $username = '', $first_name = '') {
        $users = $this->get('users');
        if (!is_array($users)) $users = array();
        if (!isset($users[$user_id])) {
            $users[$user_id] = array(
                'first_seen' => date('Y-m-d H:i:s'),
                'last_seen' => date('Y-m-d H:i:s'),
                'username' => $username,
                'first_name' => $first_name,
                'messages' => 0,
                'preferred_model' => $this->getSetting('default_model', 'gpt')
            );
            $this->set('users', $users);
        } else {
            $users[$user_id]['last_seen'] = date('Y-m-d H:i:s');
            if ($username) $users[$user_id]['username'] = $username;
            if ($first_name) $users[$user_id]['first_name'] = $first_name;
            $this->set('users', $users);
        }
        return true;
    }
    
    public function getUser($user_id) {
        $users = $this->get('users');
        return (is_array($users) && isset($users[$user_id])) ? $users[$user_id] : null;
    }
    
    public function updateUser($user_id, $data) {
        $users = $this->get('users');
        if (!is_array($users) || !isset($users[$user_id])) return false;
        foreach ($data as $key => $value) {
            $users[$user_id][$key] = $value;
        }
        $this->set('users', $users);
        return true;
    }
    
    public function incrementMessages($user_id) {
        $users = $this->get('users');
        if (!is_array($users) || !isset($users[$user_id])) return false;
        $users[$user_id]['messages']++;
        $users[$user_id]['last_seen'] = date('Y-m-d H:i:s');
        $this->set('users', $users);
        return true;
    }
    
    public function isAdmin($user_id) {
        $admins = $this->get('admins');
        if (!is_array($admins)) $admins = array(ADMIN_ID);
        return in_array($user_id, $admins);
    }
    
    public function addAdmin($user_id) {
        $admins = $this->get('admins');
        if (!is_array($admins)) $admins = array(ADMIN_ID);
        if (!in_array($user_id, $admins)) {
            $admins[] = $user_id;
            $this->set('admins', $admins);
            return true;
        }
        return false;
    }
    
    public function removeAdmin($user_id) {
        if ($user_id == ADMIN_ID) return false;
        $admins = $this->get('admins');
        if (!is_array($admins)) return false;
        $key = array_search($user_id, $admins);
        if ($key !== false) {
            unset($admins[$key]);
            $this->set('admins', array_values($admins));
            return true;
        }
        return false;
    }
    
    public function isBlocked($user_id) {
        $blocked = $this->get('blocked');
        return is_array($blocked) && in_array($user_id, $blocked);
    }
    
    public function blockUser($user_id) {
        $blocked = $this->get('blocked');
        if (!is_array($blocked)) $blocked = array();
        if (!in_array($user_id, $blocked)) {
            $blocked[] = $user_id;
            $this->set('blocked', $blocked);
            return true;
        }
        return false;
    }
    
    public function unblockUser($user_id) {
        $blocked = $this->get('blocked');
        if (!is_array($blocked)) return false;
        $key = array_search($user_id, $blocked);
        if ($key !== false) {
            unset($blocked[$key]);
            $this->set('blocked', array_values($blocked));
            return true;
        }
        return false;
    }
    
    public function addChannel($channel_id, $username = '') {
        $channels = $this->getSetting('channels', array());
        if (!is_array($channels)) $channels = array();
        foreach ($channels as $ch) {
            if ($ch['id'] == $channel_id) return false;
        }
        $channels[] = array('id' => $channel_id, 'username' => $username);
        $this->setSetting('channels', $channels);
        return true;
    }
    
    public function removeChannel($channel_id) {
        $channels = $this->getSetting('channels', array());
        if (!is_array($channels)) return false;
        foreach ($channels as $key => $ch) {
            if ($ch['id'] == $channel_id) {
                unset($channels[$key]);
                $this->setSetting('channels', array_values($channels));
                return true;
            }
        }
        return false;
    }
    
    public function setUserState($user_id, $state, $data = array()) {
        $states = $this->get('user_states');
        if (!is_array($states)) $states = array();
        $states[$user_id] = array('state' => $state, 'data' => $data, 'time' => time());
        $this->set('user_states', $states);
    }
    
    public function getUserState($user_id) {
        $states = $this->get('user_states');
        if (!is_array($states) || !isset($states[$user_id])) return null;
        if (time() - $states[$user_id]['time'] > 600) {
            unset($states[$user_id]);
            $this->set('user_states', $states);
            return null;
        }
        return $states[$user_id];
    }
    
    public function clearUserState($user_id) {
        $states = $this->get('user_states');
        if (!is_array($states)) return;
        unset($states[$user_id]);
        $this->set('user_states', $states);
    }
}

// ================== کلاس اصلی ربات ==================
class AIChatBot {
    private $db;
    private $update;
    private $chat_id;
    private $text;
    private $user_id;
    private $username;
    private $first_name;
    private $is_admin;
    private $callback_query_id;
    private $callback_data;
    private $message_id;
    private $is_callback = false;
    
    public function __construct() {
        $this->db = new Database();
        $input = file_get_contents('php://input');
        $this->update = json_decode($input, true);
        
        if (!$this->update) exit;
        
        if (isset($this->update['message'])) {
            $this->chat_id = $this->update['message']['chat']['id'] ?? null;
            $this->text = $this->update['message']['text'] ?? '';
            $this->user_id = $this->update['message']['from']['id'] ?? null;
            $this->username = $this->update['message']['from']['username'] ?? '';
            $this->first_name = $this->update['message']['from']['first_name'] ?? '';
            $this->message_id = $this->update['message']['message_id'] ?? null;
            $this->is_callback = false;
        }
        
        if (isset($this->update['callback_query'])) {
            $this->callback_query_id = $this->update['callback_query']['id'] ?? null;
            $this->callback_data = $this->update['callback_query']['data'] ?? '';
            $this->chat_id = $this->update['callback_query']['message']['chat']['id'] ?? $this->chat_id;
            $this->user_id = $this->update['callback_query']['from']['id'] ?? $this->user_id;
            $this->username = $this->update['callback_query']['from']['username'] ?? $this->username;
            $this->first_name = $this->update['callback_query']['from']['first_name'] ?? $this->first_name;
            $this->message_id = $this->update['callback_query']['message']['message_id'] ?? null;
            $this->is_callback = true;
        }
        
        $this->is_admin = $this->db->isAdmin($this->user_id);
        
        if ($this->user_id) {
            $this->db->addUser($this->user_id, $this->username, $this->first_name);
        }
    }
    
    public function run() {
        if (!$this->chat_id) return;
        
        if ($this->db->isBlocked($this->user_id) && !$this->is_admin) {
            $this->sendMessage('⛔️ شما توسط ادمین مسدود شده‌اید.');
            return;
        }
        
        if (!$this->is_admin && !$this->checkForceJoin()) {
            return;
        }
        
        if ($this->callback_query_id) {
            $this->handleCallback();
            return;
        }
        
        if ($this->text && $this->is_admin) {
            $state = $this->db->getUserState($this->user_id);
            if ($state && $this->handleAdminState($state)) {
                return;
            }
        }
        
        if ($this->text) {
            if (strpos($this->text, '/') === 0) {
                $this->handleCommand();
            } else {
                $this->handleChat();
            }
        }
    }
    
    // ================== بررسی جوین اجباری ==================
    private function checkForceJoin() {
        if (!$this->db->getSetting('force_join', true)) return true;
        
        $channels = $this->db->getSetting('channels', array());
        if (!is_array($channels) || empty($channels)) return true;
        
        $not_joined = array();
        foreach ($channels as $channel) {
            if (!$this->checkChatMember($channel['id'], $this->user_id)) {
                $not_joined[] = $channel;
            }
        }
        
        if (empty($not_joined)) return true;
        
        $msg = "🔒 <b>برای استفاده از ربات، ابتدا عضو کانال‌های زیر شوید:</b>\n\n";
        $keyboard = array('inline_keyboard' => array());
        
        foreach ($not_joined as $channel) {
            $username = $channel['username'] ? $channel['username'] : $channel['id'];
            if (strpos($username, '@') !== 0 && !is_numeric($username)) {
                $username = '@' . $username;
            }
            $msg .= "➖ " . $username . "\n";
            $link = is_numeric($username) ? "https://t.me/c/" . str_replace('-100', '', $username) : "https://t.me/" . str_replace('@', '', $username);
            $keyboard['inline_keyboard'][] = array(
                array('text' => "📢 عضویت در " . $username, 'url' => $link)
            );
        }
        
        $keyboard['inline_keyboard'][] = array(
            array('text' => '✅ بررسی مجدد', 'callback_data' => 'check_join')
        );
        
        $this->sendMessage($msg, 'HTML', $keyboard);
        return false;
    }
    
    private function checkChatMember($chat_id, $user_id) {
        $result = $this->sendRequest('getChatMember', array(
            'chat_id' => $chat_id,
            'user_id' => $user_id
        ));
        $data = json_decode($result, true);
        if (!$data || !isset($data['ok']) || !$data['ok']) return false;
        $status = $data['result']['status'] ?? '';
        return in_array($status, array('member', 'administrator', 'creator'));
    }
    
    // ================== دستورات ==================
    private function handleCommand() {
        $parts = explode(' ', $this->text, 2);
        $cmd = strtolower($parts[0]);
        
        switch ($cmd) {
            case '/start': $this->sendStartMessage(); break;
            case '/help': $this->sendHelpMessage(); break;
            case '/admin':
                if ($this->is_admin) $this->showAdminPanel();
                else $this->sendMessage('⛔️ شما دسترسی ادمین ندارید.');
                break;
            case '/model': $this->showModelSelector(); break;
            case '/profile': $this->showProfile(); break;
            case '/test':
                if ($this->is_admin) $this->testAllModels();
                else $this->sendMessage('⛔️ فقط ادمین می‌تواند تست کند.');
                break;
            case '/stats':
                if ($this->is_admin) $this->showStats();
                break;
            case '/cancel':
                $this->db->clearUserState($this->user_id);
                $this->sendMessage('✅ عملیات لغو شد.');
                break;
            default:
                $this->sendMessage('❓ دستور نامعتبر. /help را بزنید.');
        }
    }
    
    // ================== مدیریت State ادمین ==================
    private function handleAdminState($state) {
        $s = $state['state'];
        $data = $state['data'] ?? array();
        
        switch ($s) {
            case 'waiting_broadcast_text':
                $this->sendBroadcast($this->text);
                $this->db->clearUserState($this->user_id);
                return true;
                
            case 'waiting_forward_broadcast':
                if (isset($this->update['message'])) {
                    $this->sendBroadcastForward($this->update['message']);
                    $this->db->clearUserState($this->user_id);
                    return true;
                }
                return false;
                
            case 'waiting_api_key_gpt':
            case 'waiting_api_key_deepseek':
            case 'waiting_api_key_grok':
                $model = str_replace('waiting_api_key_', '', $s);
                $keys = $this->db->getSetting('api_keys', array());
                $keys[$model] = $this->text;
                $this->db->setSetting('api_keys', $keys);
                $this->db->clearUserState($this->user_id);
                $this->sendMessage("✅ کلید " . strtoupper($model) . " با موفقیت تغییر کرد.");
                $this->showApiSettings();
                return true;
                
            case 'waiting_add_admin':
                if (is_numeric($this->text)) {
                    if ($this->db->addAdmin((int)$this->text)) {
                        $this->sendMessage("✅ ادمین اضافه شد.");
                    } else {
                        $this->sendMessage("⚠️ این کاربر قبلاً ادمین بوده.");
                    }
                } else {
                    $this->sendMessage("❌ آیدی باید عدد باشد.");
                }
                $this->db->clearUserState($this->user_id);
                $this->showAdminsList();
                return true;
                
            case 'waiting_remove_admin':
                if (is_numeric($this->text)) {
                    if ($this->db->removeAdmin((int)$this->text)) {
                        $this->sendMessage("✅ ادمین حذف شد.");
                    } else {
                        $this->sendMessage("⚠️ حذف ممکن نیست.");
                    }
                } else {
                    $this->sendMessage("❌ آیدی باید عدد باشد.");
                }
                $this->db->clearUserState($this->user_id);
                $this->showAdminsList();
                return true;
                
            case 'waiting_add_channel':
                $channel = trim($this->text);
                if (strpos($channel, '@') === 0 || is_numeric($channel)) {
                    $this->db->setUserState($this->user_id, 'waiting_channel_username', array('channel_id' => $channel));
                    $this->sendMessage("📝 حالا آیدی کانال (با @) را بفرستید:\n\nمثال: @mychannel\n\nبرای لغو /cancel را بزنید.");
                    return true;
                }
                $this->sendMessage("❌ فرمت کانال نامعتبر است.");
                return true;
                
            case 'waiting_channel_username':
                $channel_id = $data['channel_id'] ?? '';
                $username = trim($this->text);
                if ($this->db->addChannel($channel_id, $username)) {
                    $this->sendMessage("✅ کانال اضافه شد.");
                } else {
                    $this->sendMessage("⚠️ این کانال قبلاً اضافه شده.");
                }
                $this->db->clearUserState($this->user_id);
                $this->showChannelsList();
                return true;
                
            case 'waiting_welcome_message':
                $this->db->setSetting('welcome_message', $this->text);
                $this->db->clearUserState($this->user_id);
                $this->sendMessage("✅ پیام خوش‌آمدگویی تغییر کرد.");
                $this->showSettingsPanel();
                return true;
                
            case 'waiting_style_emoji':
                $style = $this->db->getSetting('style', array());
                $style['emoji_prefix'] = $this->text;
                $this->db->setSetting('style', $style);
                $this->db->clearUserState($this->user_id);
                $this->sendMessage("✅ ایموجی پیش‌فرض تغییر کرد.");
                $this->showStylePanel();
                return true;
        }
        
        return false;
    }
    
    // ================== ارسال همگانی ==================
    private function sendBroadcast($text) {
        $users = $this->db->get('users');
        if (!is_array($users) || empty($users)) {
            $this->sendMessage("❌ کاربری وجود ندارد.");
            return;
        }
        
        $success = 0; $fail = 0;
        foreach ($users as $id => $data) {
            $result = $this->sendRequest('sendMessage', array(
                'chat_id' => $id, 'text' => $text, 'parse_mode' => 'HTML'
            ));
            $res = json_decode($result, true);
            if ($res && isset($res['ok']) && $res['ok']) $success++;
            else $fail++;
            usleep(50000);
        }
        
        $this->sendMessage("✅ پیام همگانی ارسال شد.\n\n📤 موفق: <code>$success</code>\n❌ ناموفق: <code>$fail</code>", 'HTML');
    }
    
    private function sendBroadcastForward($message) {
        $users = $this->db->get('users');
        if (!is_array($users) || empty($users)) {
            $this->sendMessage("❌ کاربری وجود ندارد.");
            return;
        }
        
        $success = 0; $fail = 0;
        
        if (isset($message['text'])) {
            $method = 'sendMessage';
            $params = array('text' => $message['text']);
        } elseif (isset($message['photo'])) {
            $method = 'sendPhoto';
            $photos = $message['photo'];
            $params = array('photo' => end($photos)['file_id']);
            if (isset($message['caption'])) $params['caption'] = $message['caption'];
        } elseif (isset($message['video'])) {
            $method = 'sendVideo';
            $params = array('video' => $message['video']['file_id']);
        } elseif (isset($message['document'])) {
            $method = 'sendDocument';
            $params = array('document' => $message['document']['file_id']);
        } elseif (isset($message['sticker'])) {
            $method = 'sendSticker';
            $params = array('sticker' => $message['sticker']['file_id']);
        } else {
            $this->sendMessage("❌ نوع پیام پشتیبانی نمی‌شود.");
            return;
        }
        
        foreach ($users as $id => $data) {
            $params['chat_id'] = $id;
            $result = $this->sendRequest($method, $params);
            $res = json_decode($result, true);
            if ($res && isset($res['ok']) && $res['ok']) $success++;
            else $fail++;
            usleep(50000);
        }
        
        $this->sendMessage("✅ پیام همگانی ارسال شد.\n\n📤 موفق: <code>$success</code>\n❌ ناموفق: <code>$fail</code>", 'HTML');
    }
    
    // ================== تست مدل‌ها ==================
    private function testAllModels() {
        $this->sendMessage('🔄 در حال تست مدل‌ها...');
        
        $keys = $this->db->getSetting('api_keys', array());
        $urls = $this->db->getSetting('api_urls', array());
        
        $models = array(
            'gpt' => array('url' => $urls['gpt'] ?? '', 'key' => $keys['gpt'] ?? ''),
            'deepseek' => array('url' => $urls['deepseek'] ?? '', 'key' => $keys['deepseek'] ?? ''),
            'grok' => array('url' => $urls['grok'] ?? '', 'key' => $keys['grok'] ?? '')
        );
        
        $results = array();
        foreach ($models as $name => $config) {
            if (empty($config['url']) || empty($config['key'])) {
                $results[] = array('name' => $name, 'status' => '⚠️', 'details' => 'تنظیم نشده');
                continue;
            }
            
            $url = $config['url'] . '?apikey=' . urlencode($config['key']) . '&text=' . urlencode('سلام');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $status = ($httpCode == 200 && $response && strlen($response) > 5) ? '✅' : '❌';
            $results[] = array('name' => $name, 'status' => $status, 'details' => 'HTTP ' . $httpCode);
        }
        
        $msg = "📊 <b>نتیجه تست مدل‌ها</b>\n\n";
        foreach ($results as $r) {
            $msg .= "• <b>" . strtoupper($r['name']) . "</b>: {$r['status']} {$r['details']}\n";
        }
        $this->sendMessage($msg, 'HTML');
    }
    
    // ================== انتخاب مدل ==================
    private function showModelSelector() {
        $user = $this->db->getUser($this->user_id);
        $current = $user['preferred_model'] ?? 'gpt';
        
        $models = array(
            'gpt' => '🧠 GPT',
            'deepseek' => '🔬 DeepSeek',
            'grok' => '⚡ Grok'
        );
        
        $msg = "🤖 <b>انتخاب مدل هوش مصنوعی</b>\n\n";
        $msg .= "🎯 مدل فعلی: <b>" . $models[$current] . "</b>\n\n";
        $msg .= "یکی از مدل‌های زیر را انتخاب کنید:";
        
        $keyboard = array('inline_keyboard' => array());
        foreach ($models as $key => $name) {
            $mark = ($key == $current) ? ' ✅' : '';
            $keyboard['inline_keyboard'][] = array(
                array('text' => $name . $mark, 'callback_data' => 'model_' . $key)
            );
        }
        $keyboard['inline_keyboard'][] = array(
            array('text' => '🔙 بازگشت', 'callback_data' => 'back_main')
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    // ================== دریافت پاسخ از API ==================
    private function getAIResponse($prompt, $model = 'gpt') {
        $keys = $this->db->getSetting('api_keys', array());
        $urls = $this->db->getSetting('api_urls', array());
        
        $url = isset($urls[$model]) ? $urls[$model] : $urls['gpt'];
        $key = isset($keys[$model]) ? $keys[$model] : $keys['gpt'];
        
        if (empty($url) || empty($key)) return null;
        
        $api_url = $url . '?apikey=' . urlencode($key) . '&text=' . urlencode($prompt);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode != 200 || empty($response)) return null;
        
        $data = json_decode($response, true);
        if ($data) {
            $extractors = array(
                function($d) { return isset($d['ok']) && $d['ok'] && isset($d['result']['text']) ? $d['result']['text'] : null; },
                function($d) { return isset($d['result']) && is_string($d['result']) ? $d['result'] : null; },
                function($d) { return isset($d['response']) && is_string($d['response']) ? $d['response'] : null; },
                function($d) { return isset($d['text']) && is_string($d['text']) ? $d['text'] : null; },
                function($d) { return isset($d['message']) && is_string($d['message']) ? $d['message'] : null; }
            );
            foreach ($extractors as $ext) {
                $result = $ext($data);
                if ($result) return $result;
            }
        }
        
        if (is_string($response) && strlen($response) > 10) return $response;
        return null;
    }
    
    // ================== چت ==================
    private function handleChat() {
        if (empty($this->text)) return;
        
        $this->sendChatAction('typing');
        
        $user = $this->db->getUser($this->user_id);
        $model = $user['preferred_model'] ?? 'gpt';
        
        $response = $this->getAIResponse($this->text, $model);
        
        if (!$response && $model != 'gpt') {
            $response = $this->getAIResponse($this->text, 'gpt');
            if ($response) {
                $response = "⚠️ مدل $model در دسترس نبود، از GPT استفاده شد.\n\n" . $response;
            }
        }
        
        if ($response) {
            if (strlen($response) > 4000) {
                $response = substr($response, 0, 4000) . "\n... (ادامه دارد)";
            }
            $this->sendMessage($response);
            $this->db->incrementMessages($this->user_id);
        } else {
            $this->sendMessage('❌ خطا در ارتباط با سرور.');
        }
    }
    
    // ================== پیام خوش‌آمدگویی ==================
    private function sendStartMessage() {
        $user = $this->db->getUser($this->user_id);
        $model = $user['preferred_model'] ?? 'gpt';
        $models = array('gpt' => '🧠 GPT', 'deepseek' => '🔬 DeepSeek', 'grok' => '⚡ Grok');
        
        $welcome = $this->db->getSetting('welcome_message', "🚀 به ربات هوش مصنوعی خوش آمدید!");
        
        $msg = $welcome . "\n\n";
        $msg .= "👤 <b>نام شما:</b> " . htmlspecialchars($this->first_name) . "\n";
        $msg .= "🤖 <b>مدل فعلی:</b> " . ($models[$model] ?? 'GPT') . "\n";
        $msg .= "💬 <b>تعداد پیام‌ها:</b> <code>" . ($user['messages'] ?? 0) . "</code>\n\n";
        $msg .= "📌 <b>دستورات اصلی:</b>\n";
        $msg .= "/model - 🎯 انتخاب مدل\n";
        $msg .= "/profile - 👤 پروفایل\n";
        $msg .= "/help - 📚 راهنما";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(
                    array('text' => '🤖 انتخاب مدل', 'callback_data' => 'show_models'),
                    array('text' => '👤 پروفایل من', 'callback_data' => 'show_profile')
                ),
                array(
                    array('text' => '🖼️ ادیت عکس', 'callback_data' => 'edit_photo'),
                    array('text' => '🧪 تست مدل‌ها', 'callback_data' => 'test_models')
                ),
                array(
                    array('text' => '📚 راهنما', 'callback_data' => 'show_help')
                )
            )
        );
        
        if ($this->is_admin) {
            $keyboard['inline_keyboard'][] = array(
                array('text' => '👑 پنل مدیریت', 'callback_data' => 'admin_panel')
            );
        }
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function sendHelpMessage() {
        $msg = "📚 <b>راهنمای ربات</b>\n\n";
        $msg .= "🔹 <b>دستورات کاربری:</b>\n";
        $msg .= "/start - 🚀 شروع\n";
        $msg .= "/model - 🎯 انتخاب مدل\n";
        $msg .= "/profile - 👤 پروفایل\n";
        $msg .= "/help - 📚 راهنما\n\n";
        $msg .= "🔹 <b>مدل‌های موجود:</b>\n";
        $msg .= "• 🧠 GPT - عمومی و قدرتمند\n";
        $msg .= "• 🔬 DeepSeek - تخصصی و دقیق\n";
        $msg .= "• ⚡ Grok - سریع و هوشمند\n\n";
        $msg .= "💡 برای چت با هوش مصنوعی، فقط پیام خود را بفرستید.";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'back_main'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    // ================== پروفایل ==================
    private function showProfile() {
        $user = $this->db->getUser($this->user_id);
        if (!$user) {
            $this->sendMessage('❌ پروفایل یافت نشد!');
            return;
        }
        
        $models = array('gpt' => '🧠 GPT', 'deepseek' => '🔬 DeepSeek', 'grok' => '⚡ Grok');
        
        $msg = "👤 <b>پروفایل شما</b>\n\n";
        $msg .= "🆔 <b>آیدی عددی:</b> <code>{$this->user_id}</code>\n";
        $msg .= "👤 <b>نام:</b> " . htmlspecialchars($this->first_name) . "\n";
        if ($this->username) $msg .= "🔹 <b>یوزرنیم:</b> @{$this->username}\n";
        $msg .= "📅 <b>تاریخ عضویت:</b> {$user['first_seen']}\n";
        $msg .= "💬 <b>تعداد پیام‌ها:</b> <code>{$user['messages']}</code>\n";
        $msg .= "🤖 <b>مدل فعلی:</b> " . ($models[$user['preferred_model'] ?? 'gpt'] ?? 'GPT');
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'back_main'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    // ================== پنل ادمین ==================
    private function showAdminPanel() {
        $users = $this->db->get('users');
        $admins = $this->db->get('admins');
        $channels = $this->db->getSetting('channels', array());
        $blocked = $this->db->get('blocked');
        
        $user_count = is_array($users) ? count($users) : 0;
        $admin_count = is_array($admins) ? count($admins) : 0;
        $channel_count = is_array($channels) ? count($channels) : 0;
        $blocked_count = is_array($blocked) ? count($blocked) : 0;
        
        $msg = "👑 <b>پنل مدیریت ربات</b>\n\n";
        $msg .= "📊 <b>آمار کلی:</b>\n";
        $msg .= "• 👥 کاربران: <code>$user_count</code>\n";
        $msg .= "• 👨‍💼 ادمین‌ها: <code>$admin_count</code>\n";
        $msg .= "• 📢 کانال‌ها: <code>$channel_count</code>\n";
        $msg .= "• 🚫 مسدود: <code>$blocked_count</code>\n\n";
        $msg .= "🎛️ <b>بخش‌های مدیریتی:</b>";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(
                    array('text' => '📢 پیام همگانی', 'callback_data' => 'admin_broadcast'),
                    array('text' => '🔄 فوروارد همگانی', 'callback_data' => 'admin_forward')
                ),
                array(
                    array('text' => '🔑 تنظیمات API', 'callback_data' => 'admin_api'),
                    array('text' => '⚙️ تنظیمات کلی', 'callback_data' => 'admin_settings')
                ),
                array(
                    array('text' => '👨‍💼 مدیریت ادمین‌ها', 'callback_data' => 'admin_admins'),
                    array('text' => '📢 مدیریت کانال‌ها', 'callback_data' => 'admin_channels')
                ),
                array(
                    array('text' => '🎨 استایل ربات', 'callback_data' => 'admin_style'),
                    array('text' => '📊 فعالیت کاربران', 'callback_data' => 'admin_activity')
                ),
                array(
                    array('text' => '🗑️ پاک کردن کش', 'callback_data' => 'admin_clear_cache'),
                    array('text' => '📈 آمار کامل', 'callback_data' => 'admin_stats')
                ),
                array(
                    array('text' => '🔙 بازگشت', 'callback_data' => 'back_main')
                )
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showBroadcastMenu() {
        $msg = "📢 <b>پیام همگانی</b>\n\nیکی از گزینه‌ها را انتخاب کنید:";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '✍️ ارسال متن', 'callback_data' => 'broadcast_text')),
                array(array('text' => '📎 فوروارد پیام', 'callback_data' => 'broadcast_forward')),
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showApiSettings() {
        $keys = $this->db->getSetting('api_keys', array());
        $urls = $this->db->getSetting('api_urls', array());
        
        $msg = "🔑 <b>تنظیمات API</b>\n\n";
        $msg .= "🧠 <b>GPT:</b> <code>" . $this->maskKey($keys['gpt'] ?? '') . "</code>\n";
        $msg .= "🔬 <b>DeepSeek:</b> <code>" . $this->maskKey($keys['deepseek'] ?? '') . "</code>\n";
        $msg .= "⚡ <b>Grok:</b> <code>" . $this->maskKey($keys['grok'] ?? '') . "</code>\n\n";
        $msg .= "📝 برای تغییر کلید، روی دکمه مربوطه بزنید:";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '🔑 تغییر کلید GPT', 'callback_data' => 'change_key_gpt')),
                array(array('text' => '🔑 تغییر کلید DeepSeek', 'callback_data' => 'change_key_deepseek')),
                array(array('text' => '🔑 تغییر کلید Grok', 'callback_data' => 'change_key_grok')),
                array(array('text' => '🧪 تست مدل‌ها', 'callback_data' => 'test_models')),
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function maskKey($key) {
        if (strlen($key) < 10) return $key;
        return substr($key, 0, 5) . '...' . substr($key, -5);
    }
    
    private function showSettingsPanel() {
        $force_join = $this->db->getSetting('force_join', true);
        $welcome = $this->db->getSetting('welcome_message', '');
        
        $msg = "⚙️ <b>تنظیمات کلی ربات</b>\n\n";
        $msg .= "🔒 <b>جوین اجباری:</b> " . ($force_join ? '✅ فعال' : '❌ غیرفعال') . "\n";
        $msg .= "📝 <b>پیام خوش‌آمدگویی:</b>\n<code>" . htmlspecialchars(substr($welcome, 0, 100)) . "...</code>";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => ($force_join ? '🔓 غیرفعال کردن جوین اجباری' : '🔒 فعال کردن جوین اجباری'), 'callback_data' => 'toggle_force_join')),
                array(array('text' => '📝 ویرایش پیام خوش‌آمد', 'callback_data' => 'edit_welcome')),
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showAdminsList() {
        $admins = $this->db->get('admins');
        if (!is_array($admins)) $admins = array(ADMIN_ID);
        
        $msg = "👨‍💼 <b>لیست ادمین‌ها</b>\n\n";
        foreach ($admins as $admin_id) {
            $mark = ($admin_id == ADMIN_ID) ? ' 👑' : '';
            $msg .= "• <code>$admin_id</code>$mark\n";
        }
        $msg .= "\n📝 برای افزودن یا حذف ادمین:";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '➕ افزودن ادمین', 'callback_data' => 'add_admin')),
                array(array('text' => '➖ حذف ادمین', 'callback_data' => 'remove_admin')),
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showChannelsList() {
        $channels = $this->db->getSetting('channels', array());
        
        $msg = "📢 <b>لیست کانال‌های جوین اجباری</b>\n\n";
        if (empty($channels) || !is_array($channels)) {
            $msg .= "❌ هیچ کانالی اضافه نشده.\n\n";
        } else {
            foreach ($channels as $ch) {
                $msg .= "• <code>{$ch['id']}</code>";
                if (!empty($ch['username'])) $msg .= " ({$ch['username']})";
                $msg .= "\n";
            }
            $msg .= "\n";
        }
        
        $keyboard = array('inline_keyboard' => array());
        if (!empty($channels)) {
            foreach ($channels as $ch) {
                $keyboard['inline_keyboard'][] = array(
                    array('text' => '❌ حذف ' . ($ch['username'] ?: $ch['id']), 'callback_data' => 'remove_channel_' . $ch['id'])
                );
            }
        }
        $keyboard['inline_keyboard'][] = array(array('text' => '➕ افزودن کانال', 'callback_data' => 'add_channel'));
        $keyboard['inline_keyboard'][] = array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'));
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showStylePanel() {
        $style = $this->db->getSetting('style', array());
        $theme = $style['theme'] ?? 'dark';
        $emoji = $style['emoji_prefix'] ?? '🤖';
        
        $msg = "🎨 <b>تنظیمات استایل ربات</b>\n\n";
        $msg .= "🌓 <b>تم فعلی:</b> $theme\n";
        $msg .= "✨ <b>ایموجی پیش‌فرض:</b> $emoji";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(
                    array('text' => '🌓 تم تاریک', 'callback_data' => 'style_dark'),
                    array('text' => '☀️ تم روشن', 'callback_data' => 'style_light')
                ),
                array(array('text' => '✨ تغییر ایموجی پیش‌فرض', 'callback_data' => 'change_emoji')),
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showActivityPanel() {
        $users = $this->db->get('users');
        if (!is_array($users) || empty($users)) {
            $this->sendMessage("❌ کاربری ثبت نشده.");
            return;
        }
        
        uasort($users, function($a, $b) {
            return strtotime($b['last_seen'] ?? $b['first_seen']) - strtotime($a['last_seen'] ?? $a['first_seen']);
        });
        
        $msg = "📊 <b>فعالیت کاربران (۱۰ کاربر آخر)</b>\n\n";
        $count = 0;
        foreach ($users as $id => $data) {
            if ($count >= 10) break;
            $name = !empty($data['first_name']) ? htmlspecialchars($data['first_name']) : 'کاربر';
            $last = $data['last_seen'] ?? $data['first_seen'];
            $msgs = $data['messages'] ?? 0;
            $msg .= "• <b>$name</b>\n";
            $msg .= "  🆔 <code>$id</code> | 💬 $msgs | 🕐 $last\n\n";
            $count++;
        }
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    private function showStats() {
        $users = $this->db->get('users');
        $admins = $this->db->get('admins');
        $blocked = $this->db->get('blocked');
        $channels = $this->db->getSetting('channels', array());
        
        $user_count = is_array($users) ? count($users) : 0;
        $total_messages = is_array($users) ? array_sum(array_column($users, 'messages')) : 0;
        
        $msg = "📈 <b>آمار کامل ربات</b>\n\n";
        $msg .= "👥 <b>کل کاربران:</b> <code>$user_count</code>\n";
        $msg .= "👨‍💼 <b>تعداد ادمین‌ها:</b> <code>" . (is_array($admins) ? count($admins) : 0) . "</code>\n";
        $msg .= "📢 <b>کانال‌های جوین اجباری:</b> <code>" . (is_array($channels) ? count($channels) : 0) . "</code>\n";
        $msg .= "🚫 <b>کاربران مسدود:</b> <code>" . (is_array($blocked) ? count($blocked) : 0) . "</code>\n";
        $msg .= "💬 <b>کل پیام‌ها:</b> <code>$total_messages</code>";
        
        $keyboard = array(
            'inline_keyboard' => array(
                array(array('text' => '🔙 بازگشت', 'callback_data' => 'admin_panel'))
            )
        );
        
        $this->respondWithKeyboard($msg, 'HTML', $keyboard); // 🆕
    }
    
    // ================== هندلر کالبک ==================
    private function handleCallback() {
        if (!$this->callback_query_id) return;
        
        $data = $this->callback_data;
        $this->answerCallback('⏳');
        
        if (strpos($data, 'admin_') === 0 && !$this->is_admin) {
            $this->answerCallback('⛔️ دسترسی ندارید!', true);
            return;
        }
        
        // دستورات کاربری
        if ($data == 'show_models') $this->showModelSelector();
        elseif ($data == 'show_profile') $this->showProfile();
        elseif ($data == 'show_help') $this->sendHelpMessage();
        elseif ($data == 'test_models') $this->testAllModels();
        elseif ($data == 'back_main') $this->sendStartMessage();
        elseif ($data == 'check_join') {
            if ($this->checkForceJoin()) {
                $this->answerCallback('✅ عضویت تایید شد!', true);
                $this->sendStartMessage();
            } else {
                $this->answerCallback('❌ هنوز عضو نیستید!', true);
            }
        }
        elseif ($data == 'edit_photo') {
            $this->sendMessage("🚧 <b>این بخش در آینده اضافه می‌شود!</b>\n\n💡 به زودی قابلیت ادیت عکس با هوش مصنوعی فعال خواهد شد.", 'HTML');
        }
        elseif (strpos($data, 'model_') === 0) {
            $model = str_replace('model_', '', $data);
            $this->db->updateUser($this->user_id, array('preferred_model' => $model));
            $models = array('gpt' => '🧠 GPT', 'deepseek' => '🔬 DeepSeek', 'grok' => '⚡ Grok');
            $this->answerCallback('✅ مدل تغییر کرد به ' . ($models[$model] ?? $model), true);
            $this->showModelSelector();
        }
        // دستورات ادمین
        elseif ($data == 'admin_panel') $this->showAdminPanel();
        elseif ($data == 'admin_broadcast') $this->showBroadcastMenu();
        elseif ($data == 'broadcast_text') {
            $this->db->setUserState($this->user_id, 'waiting_broadcast_text');
            $this->sendMessage("✍️ <b>متن پیام همگانی را بفرستید:</b>\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'broadcast_forward') {
            $this->db->setUserState($this->user_id, 'waiting_forward_broadcast');
            $this->sendMessage("🔄 <b>پیام مورد نظر را فوروارد کنید:</b>\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'admin_api') $this->showApiSettings();
        elseif ($data == 'admin_settings') $this->showSettingsPanel();
        elseif ($data == 'admin_admins') $this->showAdminsList();
        elseif ($data == 'admin_channels') $this->showChannelsList();
        elseif ($data == 'admin_style') $this->showStylePanel();
        elseif ($data == 'admin_activity') $this->showActivityPanel();
        elseif ($data == 'admin_clear_cache') {
            $states = $this->db->get('user_states');
            if (is_array($states)) {
                foreach ($states as $uid => $s) {
                    if (time() - ($s['time'] ?? 0) > 600) unset($states[$uid]);
                }
                $this->db->set('user_states', $states);
            }
            $this->answerCallback('✅ کش پاک شد', true);
            $this->showAdminPanel();
        }
        elseif ($data == 'admin_stats') $this->showStats();
        elseif (strpos($data, 'change_key_') === 0) {
            $model = str_replace('change_key_', '', $data);
            $this->db->setUserState($this->user_id, 'waiting_api_key_' . $model);
            $this->sendMessage("🔑 <b>کلید جدید " . strtoupper($model) . " را بفرستید:</b>\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'toggle_force_join') {
            $current = $this->db->getSetting('force_join', true);
            $this->db->setSetting('force_join', !$current);
            $this->answerCallback($current ? '❌ غیرفعال شد' : '✅ فعال شد', true);
            $this->showSettingsPanel();
        }
        elseif ($data == 'edit_welcome') {
            $this->db->setUserState($this->user_id, 'waiting_welcome_message');
            $this->sendMessage("📝 <b>پیام خوش‌آمدگویی جدید را بفرستید:</b>\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'add_admin') {
            $this->db->setUserState($this->user_id, 'waiting_add_admin');
            $this->sendMessage("➕ <b>آیدی عددی ادمین جدید را بفرستید:</b>\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'remove_admin') {
            $this->db->setUserState($this->user_id, 'waiting_remove_admin');
            $this->sendMessage("➖ <b>آیدی عددی ادمین برای حذف را بفرستید:</b>\n\n⚠️ ادمین اصلی قابل حذف نیست.\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif ($data == 'add_channel') {
            $this->db->setUserState($this->user_id, 'waiting_add_channel');
            $this->sendMessage("➕ <b>آیدی کانال را بفرستید:</b>\n\nمثال:\n• <code>-1001234567890</code>\n• @mychannel\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif (strpos($data, 'remove_channel_') === 0) {
            $channel_id = str_replace('remove_channel_', '', $data);
            $this->db->removeChannel($channel_id);
            $this->answerCallback('✅ کانال حذف شد', true);
            $this->showChannelsList();
        }
        elseif ($data == 'change_emoji') {
            $this->db->setUserState($this->user_id, 'waiting_style_emoji');
            $this->sendMessage("✨ <b>ایموجی جدید را بفرستید:</b>\n\nمثال: 🤖 🚀 💎\n\nبرای لغو /cancel را بزنید.", 'HTML');
        }
        elseif (strpos($data, 'style_') === 0) {
            $theme = str_replace('style_', '', $data);
            $style = $this->db->getSetting('style', array());
            $style['theme'] = $theme;
            $this->db->setSetting('style', $style);
            $this->answerCallback('✅ تم تغییر کرد', true);
            $this->showStylePanel();
        }
        else {
            $this->answerCallback('❓ دستور ناشناخته', true);
        }
    }
    
    private function respondWithKeyboard($text, $parse_mode = 'HTML', $reply_markup = null) {
        if ($this->is_callback && $this->message_id) {
            return $this->editMessage($text, $parse_mode, $reply_markup);
        }
        return $this->sendMessage($text, $parse_mode, $reply_markup);
    }
    
    private function editMessage($text, $parse_mode = 'HTML', $reply_markup = null) {
        $data = array(
            'chat_id' => $this->chat_id,
            'message_id' => $this->message_id,
            'text' => $text,
            'parse_mode' => $parse_mode
        );
        if ($reply_markup) $data['reply_markup'] = json_encode($reply_markup);
        
        $result = $this->sendRequest('editMessageText', $data);
        $res = json_decode($result, true);
        
        if ($res && isset($res['ok']) && !$res['ok']) {
            $desc = $res['description'] ?? '';
            if (strpos($desc, 'message is not modified') !== false) {
                
                return $result;
            }
            if (strpos($desc, 'message can\'t be edited') !== false || 
                strpos($desc, 'MESSAGE_EDIT_INACTIVE') !== false) {
                return $this->sendMessage($text, $parse_mode, $reply_markup);
            }
        }
        
        return $result;
    }
    
    // ================== توابع کمکی ==================
    private function sendMessage($text, $parse_mode = 'HTML', $reply_markup = null) {
        $data = array(
            'chat_id' => $this->chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode
        );
        if ($reply_markup) $data['reply_markup'] = json_encode($reply_markup);
        return $this->sendRequest('sendMessage', $data);
    }
    
    private function sendChatAction($action) {
        return $this->sendRequest('sendChatAction', array('chat_id' => $this->chat_id, 'action' => $action));
    }
    
    private function answerCallback($text, $show_alert = false) {
        if (!$this->callback_query_id) return;
        return $this->sendRequest('answerCallbackQuery', array(
            'callback_query_id' => $this->callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert
        ));
    }
    
    private function sendRequest($method, $data) {
        $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

// ================== اجرای ربات ==================
$bot = new AIChatBot();
$bot->run();
?>