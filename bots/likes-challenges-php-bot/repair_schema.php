<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — ترمیم اسکیمای دیتابیس
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

try {
    require_once __DIR__ . '/functions.php';
    ensureSchema();
    initDefaultSettings();

    $required = [
        'users' => ['id','coins','like_emoji','default_channel','default_channel_id','default_channel_title','force_channels','ref_by','step','temp_json','created_at','last_seen','last_daily_reward_date'],
        'items' => ['id','owner_id','type','text','media_type','file_id','caption','emoji','options_json','fake_likes','target_channel','channel_chat_id','channel_message_id','source_chat_id','source_message_id','created_at','updated_at'],
        'settings' => ['skey','svalue'],
        'admins' => ['id','user_id','created_by','created_at'],
        'votes' => ['id','item_id','user_id','vote_value','created_at'],
        'speed_challenges' => ['token','owner_id','is_active','winner_id','winner_json','created_at','won_at'],
        'auto_like_jobs' => ['id','owner_id','channel_id','channel_chat_id','channel_title','remaining_posts','total_posts','fake_likes','price_per_post','status','created_at','updated_at'],
        'coin_orders' => ['id','user_id','coins','amount','method','status','receipt_file_id','receipt_type','receipt_text','admin_message_id','zibal_track_id','zibal_ref_id','created_at','updated_at'],
    ];

    // بک‌فیل نسخه v26: اگر کاربری قبلاً کانال را تنظیم کرده ولی عضویت اجباری شخصی خاموش/خالی مانده، همان کانال برای شرکت در چالش‌ها و لایک‌ها فعال شود.
    try {
        if (dbHasTable('users') && dbHasColumn('users', 'default_channel') && dbHasColumn('users', 'force_channels')) {
            db()->exec("UPDATE `users` SET `force_channels` = JSON_ARRAY(`default_channel_id`) WHERE `default_channel_id` IS NOT NULL AND `default_channel_id` <> '' AND `default_channel_id` LIKE '-100%'");
            db()->exec("UPDATE `users` SET `force_channels` = JSON_ARRAY(`default_channel`) WHERE (`default_channel_id` IS NULL OR `default_channel_id` = '') AND `default_channel` IS NOT NULL AND `default_channel` <> '' AND (`force_channels` IS NULL OR `force_channels` = '' OR `force_channels` = '[]')");
        }
    } catch (Throwable $e) {
        // فقط نمایش وضعیت اصلی تعمیر ادامه پیدا کند.
    }

    $rows = [];
    foreach ($required as $table => $cols) {
        $tableOk = dbHasTable($table);
        $missing = [];
        if ($tableOk) {
            foreach ($cols as $col) if (!dbHasColumn($table, $col)) $missing[] = $col;
        }
        $rows[] = ['table' => $table, 'ok' => $tableOk && !$missing, 'detail' => !$tableOk ? 'table missing' : ($missing ? 'missing: ' . implode(', ', $missing) : 'OK')];
    }

    echo '<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Repair Schema</title><style>body{font-family:tahoma,Arial;background:#f6f7fb;color:#111827}.wrap{max-width:850px;margin:30px auto;background:#fff;border-radius:16px;padding:22px;box-shadow:0 8px 30px #0001}table{width:100%;border-collapse:collapse}td,th{padding:10px;border-bottom:1px solid #eee;text-align:right}.ok{color:#067647;font-weight:700}.bad{color:#b42318;font-weight:700}.btn{display:inline-block;margin-top:12px;background:#2563eb;color:white;padding:10px 14px;border-radius:10px;text-decoration:none}</style></head><body><div class="wrap">';
    echo '<h2>تعمیر دیتابیس انجام شد</h2><p>اگر همه ردیف‌ها OK هستند، حالا داخل ربات دوباره دکمه تنظیم کانال را بزنید.</p><table><tr><th>جدول</th><th>وضعیت</th><th>جزئیات</th></tr>';
    foreach ($rows as $r) echo '<tr><td>' . h($r['table']) . '</td><td class="' . ($r['ok'] ? 'ok' : 'bad') . '">' . ($r['ok'] ? '✅ OK' : '❌ مشکل') . '</td><td>' . h($r['detail']) . '</td></tr>';
    echo '</table><a class="btn" href="setup_check.php">بررسی نصب</a></div></body></html>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><title>Repair Error</title></head><body style="font-family:tahoma"><h2>خطا در تعمیر دیتابیس</h2><pre dir="ltr">' . h($e->getMessage()) . '</pre></body></html>';
}
