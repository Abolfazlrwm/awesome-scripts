<?php

define('BEARER_TOKEN', getenv('X_BEARER_TOKEN') ?: 'AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA');
define('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
define('API_BASE_URL', 'https://api.x.com');


define('COOKIE_FILE', 'cookie.json');
define('GUEST_TOKEN_FILE', 'guest_token.txt');

function Client() {
    $cookieJar = new GuzzleHttp\Cookie\FileCookieJar(COOKIE_FILE, true);
    
    return new GuzzleHttp\Client([
        'cookies' => $cookieJar,
        'headers' => [
            'User-Agent' => USER_AGENT,
            'Authorization' => 'Bearer ' . BEARER_TOKEN,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
        'timeout' => 30,
        'connect_timeout' => 10,
        'verify' => true
    ]);
}


function extractTweetIdFromUrl($url) {
    if (preg_match('/status\/(\d+)/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

function getGuestToken() {
    return file_exists('guest_token.txt') ? trim(file_get_contents('guest_token.txt')) : refreshGuestToken(new Client());
}

function saveGuestToken($token) {
    file_put_contents('guest_token.txt', $token);
    return true;
}




function isValidTwitterUrl($url) {

    $patterns = [
        '/^https?:\/\/(?:www\.)?(?:twitter\.com|x\.com)\/(?:#!\/)?[a-zA-Z0-9_]+\/status\/\d+(\?[a-zA-Z0-9_=&-]+)?$/i',
        
        '/^https?:\/\/(?:www\.)?(?:twitter\.com|x\.com)\/(?:#!\/)?[a-zA-Z0-9_]+\/status\/\d+$/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, trim($url))) {
            return true;
        }
    }
    
    return false;
}

