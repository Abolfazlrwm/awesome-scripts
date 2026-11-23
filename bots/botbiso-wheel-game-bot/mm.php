<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
Developer: @Cristianoronal_d_o
Channel: @SourceCrystal
Channel: @botbiso
*/
header('Content-Type: application/json');

$channels = [
    '@NPROXY', '@ConfigsHubPlus', '@ConfigTech1', '@e2vpn', '@proxyiranip', '@vpnplusee_free', '@iMTProto', '@YamYamProxy',
    '@sunflowerplato', '@StreisandAp', '@Confings_K', '@horn_proxy', '@RUSSIAPROXYY', '@V2All', '@GetConfigIR', '@neo_proxy',
    '@Proxy_Qavi', '@Proxycj', '@proxyvr', '@HiProxy', '@Proxyqawi', '@proxy_ghavy', '@Up_Proxy', '@proxy_i2', '@ProxyKhabri',
    '@ProxyKL', '@VPNOD', '@Proxyjadidi', '@proxyjt', '@PROXYA0', '@proxyag', '@Myporoxy', '@saghivpnx', '@ProtoJet', '@vpns',
    '@HotProxy_Free', '@SuperFreeVpnProxy', '@proxy_who', '@ProxyMTProto', '@WarV2Ray', '@mti_vpn7', '@proxymtprotoj',
    '@darkproxy', '@GlypeX', '@v2rayfree_iran', '@iRFilteringVpn', '@mtpproxyirani', '@ProxyGH', '@ProxyDaemi', '@irproxy',
    '@net_baz1', '@PabloProxy', '@TP_MTProxy', '@v2rayng_fars', '@Vpnsmartcam', '@freedomnetir', '@xsfilternet', '@tuenvpn',
    '@v2rayTG', '@proxymoments', '@Hoorvpn', '@ROJproxy', '@Super_v2ray24', '@donot66', '@iphone02016vpn', '@pricedolar1',
    '@BestSpeedProxy', '@proxyhive', '@ProxyWR', '@proxym', '@Evay_vpn', '@Spotify_Porteghali', '@PewezaVPN', '@V2rey_Hiddify',
    '@ToxicVid', '@NewWarp', '@aataatee', '@V2ranNG_vpn', '@ghalagyann', '@ghalagyann2', '@nofilter_proxi', '@V2rayfastt',
    '@FarsiProxi', '@Vahid_Page', '@VPN_IRANT', '@iRoProxy', '@ProxyNewsVatani', '@vonline247', '@V2VIPCHANNEL',
    '@Surfboardv2ray', '@Express_freevpn', '@IRBestFree', '@ProxyMTProto_tel', '@marketing_marziyeh', '@config_proxy',
    '@Scary_Proxy', '@iproxy', '@Academi_vpn', '@v2rayshahin', '@hex_proxy', '@iranproxy80', '@channel_proxy1', '@zedmodeonVPN',
    '@v2FreeHub'
];

$patterns = ['vmess://', 'vless://', 'trojan://'];

function fetchConfigs($channel) {
    $configs = [];
    $url = "https://t.me/s/" . ltrim($channel, '@');
    $html = @file_get_contents($url);
    if (!$html) {
        return [];
    }
    foreach ($GLOBALS['patterns'] as $pattern) {
        preg_match_all('/' . preg_quote($pattern, '/') . '[^\s<"]+/', $html, $matches);
        $configs = array_merge($configs, $matches[0]);
    }
    return $configs;
}

shuffle($channels);
foreach ($channels as $channel) {
    $results = fetchConfigs($channel);
    if (count($results) > 0) {
        $selected = $results[array_rand($results)];
        echo json_encode([
            'config' => $selected,
            'channel' => @botbiso,
            'developer' => '@Cristianoronal_d_o'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'هیچ کانفیگی یافت نشد!'], JSON_UNESCAPED_UNICODE);
exit;
?>