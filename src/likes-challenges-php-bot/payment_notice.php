<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — اعلان پرداخت
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
require_once __DIR__ . '/functions.php';

header('Content-Type: text/html; charset=utf-8');

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

try {
    ensureSchema();
    initDefaultSettings();

    $orderId = (int)($_GET['order'] ?? 0);
    $trackId = trim((string)($_GET['track'] ?? ''));
    $sig = trim((string)($_GET['sig'] ?? ''));
    $go = (string)($_GET['go'] ?? '') === '1';

    $valid = false;
    $order = null;
    if ($orderId > 0 && $trackId !== '' && $sig !== '' && function_exists('paymentNoticeSign')) {
        $valid = hash_equals(paymentNoticeSign($orderId, $trackId), $sig);
        $order = getCoinOrder($orderId);
        if (!$order || (string)($order['zibal_track_id'] ?? '') !== $trackId) $valid = false;
    }

    if (!$valid) {
        http_response_code(400);
        echo '<!doctype html><html dir="rtl" lang="fa"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>پرداخت نامعتبر</title><style>body{margin:0;font-family:tahoma,Arial;background:#f5f7fb;color:#222;display:flex;min-height:100vh;align-items:center;justify-content:center}.box{max-width:460px;background:#fff;padding:28px;border-radius:24px;box-shadow:0 15px 45px rgba(0,0,0,.08);text-align:center}.bad{font-size:46px}.muted{color:#6b7280;line-height:2}</style></head><body><div class="box"><div class="bad">❌</div><h2>لینک پرداخت معتبر نیست</h2><p class="muted">لطفاً از داخل ربات دوباره لینک پرداخت جدید دریافت کنید.</p></div></body></html>';
        exit;
    }

    $gatewayUrl = 'https://gateway.zibal.ir/start/' . rawurlencode($trackId);
    if ($go) {
        header('Location: ' . $gatewayUrl, true, 302);
        exit;
    }

    $sep = str_contains($_SERVER['REQUEST_URI'] ?? '', '?') ? '&' : '?';
    $continueUrl = paymentNoticeBaseUrl() . '?order=' . rawurlencode((string)$orderId) . '&track=' . rawurlencode($trackId) . '&sig=' . rawurlencode($sig) . '&go=1';
    $coins = (int)($order['coins'] ?? 0);
    $amount = (int)($order['amount'] ?? 0);
    $orderLabel = '#' . $orderId;

    echo '<!doctype html><html dir="rtl" lang="fa"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>راهنمای پرداخت</title><style>
        *{box-sizing:border-box}body{margin:0;font-family:tahoma,Arial,sans-serif;background:linear-gradient(135deg,#e8f1ff,#f8fbff 48%,#eefcf5);color:#172033;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:18px}.card{width:100%;max-width:520px;background:rgba(255,255,255,.94);border:1px solid rgba(255,255,255,.7);border-radius:28px;box-shadow:0 22px 60px rgba(20,60,120,.14);overflow:hidden}.top{padding:28px 24px 16px;text-align:center}.icon{width:70px;height:70px;margin:0 auto 14px;border-radius:24px;background:#eaf2ff;display:flex;align-items:center;justify-content:center;font-size:34px}.title{font-size:22px;font-weight:800;margin:0 0 8px}.desc{line-height:2;color:#4b5563;font-size:15px;margin:0}.warn{margin:18px 22px;padding:16px;border-radius:20px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;line-height:2;font-size:14px}.details{margin:0 22px 18px;padding:14px;border-radius:18px;background:#f8fafc;border:1px solid #eef2f7}.row{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #e5e7eb}.row:last-child{border-bottom:0}.k{color:#64748b}.v{font-weight:700}.steps{margin:0 22px 20px;padding:0;list-style:none}.steps li{background:#f9fafb;margin:8px 0;padding:12px 14px;border-radius:16px;border:1px solid #eef2f7;line-height:1.9}.actions{padding:0 22px 24px}.btn{display:block;width:100%;border:0;text-decoration:none;text-align:center;background:#1663ff;color:#fff;padding:15px 16px;border-radius:18px;font-size:16px;font-weight:800;box-shadow:0 12px 25px rgba(22,99,255,.22)}.small{font-size:12px;color:#6b7280;line-height:1.9;text-align:center;margin-top:14px}@media(max-width:420px){.title{font-size:20px}.card{border-radius:22px}.top{padding-top:24px}}
    </style></head><body><main class="card"><section class="top"><div class="icon">💳</div><h1 class="title">قبل از پرداخت VPN را خاموش کنید</h1><p class="desc">برای جلوگیری از خطای درگاه، ابتدا VPN یا فیلترشکن را خاموش کنید، سپس وارد صفحه پرداخت شوید.</p></section><div class="warn">اگر با VPN وارد درگاه شوید، ممکن است پرداخت ناموفق شود یا صفحه بانک باز نشود.</div><section class="details"><div class="row"><span class="k">شماره سفارش</span><span class="v">'.h($orderLabel).'</span></div><div class="row"><span class="k">بسته</span><span class="v">'.h($coins).' سکه</span></div><div class="row"><span class="k">مبلغ</span><span class="v">'.h(number_format($amount)).' تومان</span></div></section><ul class="steps"><li>۱. VPN را خاموش کنید.</li><li>۲. چند ثانیه صبر کنید تا اینترنت بدون VPN وصل شود.</li><li>۳. روی دکمه زیر بزنید و پرداخت را انجام دهید.</li></ul><section class="actions"><a class="btn" href="'.h($continueUrl).'">VPN را خاموش کردم؛ ورود به درگاه</a><div class="small">پس از پرداخت موفق، سکه‌ها به صورت خودکار به حساب شما اضافه می‌شود.</div></section></main></body></html>';
} catch (Throwable $e) {
    logError('payment notice fatal', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo '<!doctype html><html dir="rtl" lang="fa"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>خطا</title></head><body style="font-family:tahoma;text-align:center;padding:40px"><h3>خطا در آماده‌سازی پرداخت</h3><p>لطفاً دوباره از داخل ربات تلاش کنید.</p></body></html>';
}
