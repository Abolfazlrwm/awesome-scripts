<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
/*
Developer: @Cristianoronal_d_o
Channel: @MakranSource
Channel: @botbiso
*/
header('Content-Type: application/json; charset=utf-8');

define('CACHE_FILE', 'crypto_cache.json');
define('CACHE_DURATION', 300);
define('NAMES_CACHE_FILE', 'crypto_names.json');
define('NAMES_CACHE_DURATION', 86400);

function getCryptoPrice($crypto_id) {
    $crypto_id = strtoupper($crypto_id);
    $cache = getCachedPrice($crypto_id);
    if ($cache !== false && !empty($cache)) {
        return $cache;
    }

    $crypto_configs = [
        'binance' => [
            'url' => "https://api.binance.com/api/v3/ticker/price?symbol={$crypto_id}USDT",
            'source' => 'binance'
        ],
        'bybit' => [
            'url' => "https://api.bybit.com/v5/market/tickers?category=spot&symbol={$crypto_id}USDT",
            'source' => 'bybit'
        ],
        'kucoin' => [
            'url' => "https://api.kucoin.com/api/v1/market/allTickers",
            'source' => 'kucoin',
            'symbol' => "{$crypto_id}-USDT"
        ],
        'arzdigital' => [
            'url' => "https://arzdigital.com/coins/" . strtolower($crypto_id) . "/",
            'source' => 'arzdigital'
        ]
    ];

    $name = getCryptoName($crypto_id);
    $price_entry = ['name' => $name];
    $usdt_rate = getTetherRate();

    foreach ($crypto_configs as $source => $config) {
        $response = fetchData($config['url']);
        if ($response === false) {
            continue;
        }

        $data = json_decode($response, true);

        if ($config['source'] === 'binance') {
            if (isset($data['price'])) {
                $price_entry['usd'] = floatval($data['price']);
                break;
            }
        } elseif ($config['source'] === 'bybit') {
            if (isset($data['result']['list'][0]['lastPrice'])) {
                $price_entry['usd'] = floatval($data['result']['list'][0]['lastPrice']);
                break;
            }
        } elseif ($config['source'] === 'kucoin') {
            if (isset($data['data']['ticker'])) {
                foreach ($data['data']['ticker'] as $item) {
                    if ($item['symbol'] === $config['symbol']) {
                        $price_usd = floatval($item['last'] ?? 0);
                        if ($price_usd > 0) {
                            $price_entry['usd'] = $price_usd;
                            break 2;
                        }
                    }
                }
            }
        } elseif ($config['source'] === 'arzdigital') {
            preg_match('/<span class="arz-toman-price">\s*([\d,]+)\s*تومان\s*<\/span>/', $response, $toman_matches);
            preg_match('/<span class="arz-usd-price">\s*([\d.]+)\s*دلار\s*<\/span>/', $response, $usd_matches);
            $toman_price = isset($toman_matches[1]) ? floatval(str_replace(',', '', $toman_matches[1])) : null;
            $usd_price = isset($usd_matches[1]) ? floatval($usd_matches[1]) : null;
            if ($toman_price) {
                $price_entry['toman'] = $toman_price;
            }
            if ($usd_price) {
                $price_entry['usd'] = $usd_price;
            }
            if ($toman_price || $usd_price) {
                break;
            }
        }
    }

    if (!isset($price_entry['usd']) && !isset($price_entry['toman'])) {
        return getCachedPrice($crypto_id) ?: ['error' => 'ارز یافت نشد یا داده‌ها در دسترس نیست! 😔'];
    }

    if ($usdt_rate !== null && !isset($price_entry['toman']) && isset($price_entry['usd'])) {
        $price_entry['toman'] = round($price_entry['usd'] * $usdt_rate);
    } elseif (!isset($price_entry['toman']) && isset($price_entry['usd'])) {
        $external_rate = getExternalTomanRate();
        if ($external_rate !== false) {
            $price_entry['toman'] = round($price_entry['usd'] * $external_rate);
        }
    }

    cachePrice($crypto_id, $price_entry);
    return $price_entry;
}

function fetchData($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json, text/html']);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function getTetherRate() {
    $response = fetchData('https://arzdigital.com/coins/tether/');
    if ($response !== false) {
        preg_match('/<span class="arz-toman-price">\s*([\d,]+)\s*تومان\s*<\/span>/', $response, $toman_matches);
        preg_match('/<span class="arz-usd-price">\s*([\d.]+)\s*دلار\s*<\/span>/', $response, $usd_matches);
        $toman_price = isset($toman_matches[1]) ? floatval(str_replace(',', '', $toman_matches[1])) : null;
        $usd_price = isset($usd_matches[1]) ? floatval($usd_matches[1]) : null;
        if ($toman_price && $usd_price) {
            return $toman_price / $usd_price;
        }
    }
    return null;
}

function getExternalTomanRate() {
    $sources = [
        'https://api.exchangerate-api.com/v4/latest/USD'
    ];
    foreach ($sources as $url) {
        $response = fetchData($url);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['rates']['IRR'])) {
                return $data['rates']['IRR'] / 10;
            }
        }
    }
    return false;
}

function getCryptoName($crypto_id) {
    $names = getCachedNames();
    if (isset($names[$crypto_id])) {
        return $names[$crypto_id];
    }

    $response = fetchData('https://api.coingecko.com/api/v3/coins/list');
    if ($response !== false) {
        $coins = json_decode($response, true);
        foreach ($coins as $coin) {
            if (strtoupper($coin['symbol']) === $crypto_id) {
                $names[$crypto_id] = $coin['name'];
                cacheNames($names);
                return $coin['name'];
            }
        }
    }
    return $crypto_id;
}

function getCachedPrice($crypto_id) {
    if (file_exists(CACHE_FILE) && (time() - filemtime(CACHE_FILE)) < CACHE_DURATION) {
        $data = @file_get_contents(CACHE_FILE);
        if ($data) {
            $cached = json_decode($data, true);
            if (is_array($cached) && isset($cached[$crypto_id])) {
                return $cached[$crypto_id];
            }
        }
    }
    return false;
}

function cachePrice($crypto_id, $price_entry) {
    $cached = file_exists(CACHE_FILE) ? json_decode(@file_get_contents(CACHE_FILE), true) : [];
    $cached[$crypto_id] = $price_entry;
    if (is_writable(dirname(CACHE_FILE)) || (file_exists(CACHE_FILE) && is_writable(CACHE_FILE))) {
        file_put_contents(CACHE_FILE, json_encode($cached, JSON_UNESCAPED_UNICODE));
    }
}

function getCachedNames() {
    if (file_exists(NAMES_CACHE_FILE) && (time() - filemtime(NAMES_CACHE_FILE)) < NAMES_CACHE_DURATION) {
        $data = @file_get_contents(NAMES_CACHE_FILE);
        if ($data) {
            return json_decode($data, true) ?: [];
        }
    }
    return [];
}

function cacheNames($names) {
    if (is_writable(dirname(NAMES_CACHE_FILE)) || (file_exists(NAMES_CACHE_FILE) && is_writable(NAMES_CACHE_FILE))) {
        file_put_contents(NAMES_CACHE_FILE, json_encode($names, JSON_UNESCAPED_UNICODE));
    }
}

$crypto_id = isset($_GET['name']) ? strtoupper($_GET['name']) : null;
if (!$crypto_id) {
    http_response_code(400);
    echo json_encode(['error' => 'نام ارز وارد نشده است! 😔'], JSON_UNESCAPED_UNICODE);
    exit;
}

$price = getCryptoPrice($crypto_id);

if (isset($price['error'])) {
    http_response_code(500);
    echo json_encode(['error' => $price['error']], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode($price, JSON_UNESCAPED_UNICODE);
}
/*
Developer: @Cristianoronal_d_o
Channel: @MakranSource
Channel: @botbiso
*/
/*
Developer: @Cristianoronal_d_o
Channel: @MakranSource
Channel: @botbiso
*/
?>