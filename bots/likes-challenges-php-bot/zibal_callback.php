<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — کال‌بک درگاه زیبال
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
require_once __DIR__ . '/functions.php';

header('Content-Type: text/html; charset=utf-8');

try {
    ensureSchema();
    initDefaultSettings();

    $trackId = (string)($_GET['trackId'] ?? $_POST['trackId'] ?? '');
    $success = (string)($_GET['success'] ?? $_POST['success'] ?? '');
    $status = (string)($_GET['status'] ?? $_POST['status'] ?? '');

    if ($trackId === '') {
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>پرداخت پیدا نشد.</h3></body></html>';
        exit;
    }

    $order = getCoinOrderByTrackId($trackId);
    if (!$order) {
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>سفارش پیدا نشد.</h3></body></html>';
        exit;
    }

    if ((string)$order['status'] === 'paid') {
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>این سفارش قبلاً تایید شده است.</h3><p>می‌توانید به ربات برگردید.</p></body></html>';
        exit;
    }

    $verify = verifyZibalPayment($trackId);
    if ($verify['ok'] ?? false) {
        markCoinOrderPaid((int)$order['id'], (string)($verify['ref_id'] ?? ''));
        $fresh = getUser((int)$order['user_id']);
        tg('sendMessage', [
            'chat_id' => (int)$order['user_id'],
            'text' => "✅ پرداخت شما تایید شد.\n💰 {$order['coins']} سکه به حساب شما اضافه شد.\nموجودی فعلی: " . (int)($fresh['coins'] ?? 0) . ' سکه',
        ]);
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>پرداخت با موفقیت تایید شد.</h3><p>سکه به حساب شما اضافه شد. می‌توانید به ربات برگردید.</p></body></html>';
        exit;
    }

    updateCoinOrder((int)$order['id'], ['status' => 'failed']);
    tg('sendMessage', ['chat_id' => (int)$order['user_id'], 'text' => "❌ پرداخت آنلاین تایید نشد.\nشماره سفارش: #{$order['id']}"]);
    echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>پرداخت تایید نشد.</h3><p>در صورت کسر وجه، وضعیت را از پشتیبانی پیگیری کنید.</p></body></html>';
} catch (Throwable $e) {
    logError('zibal callback fatal', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>خطا در بررسی پرداخت.</h3></body></html>';
}
