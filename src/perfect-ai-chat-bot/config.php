<?php
// Telegram: @Abolfazl_rwm
define('API_KEY', getenv('BOT_TOKEN') ?: 'توکن ربات');

define('ADMIN_IDS', array_values(array_filter(array_map('intval', explode(',', (string) getenv('ADMIN_IDS')))))); 
define('BOT_USERNAME', 'PerfectAiRobot'); 
define('REQUIRED_CHANNEL', '@Abolfazl_rwm');
define('DEBUG', false);
define('BOT_ID', (int) explode(':', API_KEY)[0]);
define('DATA_DIR', __DIR__ . '/data');
