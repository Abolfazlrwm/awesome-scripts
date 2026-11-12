<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — توابع اصلی
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

require_once __DIR__ . '/db.php';

date_default_timezone_set(DEFAULT_TIMEZONE);

function logError(string $message, array $context = []): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if ($context) $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @file_put_contents(__DIR__ . '/error.log', $line . PHP_EOL, FILE_APPEND);
}

function tg(string $method, array $params = [], bool $logApiErrors = true): array
{
    $ch = curl_init('https://api.telegram.org/bot' . BOT_TOKEN . '/' . $method);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $err) {
        logError('Telegram cURL failed', ['method' => $method, 'error' => $err, 'code' => $code]);
        return ['ok' => false, 'description' => $err ?: 'curl failed', 'http_code' => $code];
    }
    $json = json_decode((string)$raw, true);
    if (!is_array($json)) {
        logError('Telegram invalid json', ['method' => $method, 'raw' => $raw]);
        return ['ok' => false, 'description' => 'invalid json', 'raw' => $raw];
    }
    if (!($json['ok'] ?? false) && $logApiErrors) {
        logError('Telegram API error', ['method' => $method, 'response' => $json]);
    }
    return $json;
}

function e(string $text): string { return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function uLen(string $text): int { return function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text); }
function normalizeButtonStyle(?string $style): ?string
{
    $style = strtolower(trim((string)$style));
    return in_array($style, ['danger', 'success', 'primary'], true) ? $style : null;
}

function defaultButtonStyles(): array
{
    return [
        '❤️ ساخت لایک و چالش ❤️' => 'success',
        '❤️ ساخت لایک ❤️' => 'success',
        '♦️ ساخت نظرسنجی ♦️' => 'primary',
        '💙 ساخت چالش لایک 💙' => 'primary',
        '🏃 ساخت چالش سرعتی 🏃' => 'primary',
        '🤍 ساخت خودکار لایک 🤍' => 'success',
        'دکمه لایک زیر پست' => 'success',
        'دکمه چالش لایک زیر پست' => 'primary',
        'LIKE_BUTTON' => 'success',
        'CHALLENGE_LIKE_BUTTON' => 'primary',
        '👤 پروفایل' => 'primary',
        '💰 افزایش سکه' => 'success',
        '🎯 جایزه روزانه' => 'success',
        '🎁 جمع‌آوری سکه با زیرمجموعه' => 'success',
        '💳 خرید سکه' => 'primary',
        '📖 راهنما' => 'primary',
        '⚙️ تنظیمات' => 'primary',
        '☕ اسپانسر ما' => 'primary',
        '🌐 Language / زبان' => 'primary',
        '🛠 پنل مدیریت' => 'danger',
        '↩️ بازگشت' => 'danger',
        'لغو' => 'danger',
        '📢 تنظیم کانال ارسال پست‌ها' => 'primary',
        '📋 کانال‌های من' => 'primary',
        '❤️ تنظیم اموجی لایک' => 'success',
        '💾 ذخیره تنظیمات پیشفرض' => 'success',
        '✅ بررسی عضویت' => 'success',
        '✅ تایید' => 'success',
        '✅ تایید سفارش' => 'success',
        '❌ لغو سفارش' => 'danger',
        '❌ رد' => 'danger',
        '💳 کارت به کارت' => 'primary',
        '🔵 پرداخت آنلاین زیبال' => 'primary',
        'پرداخت آنلاین' => 'primary',
        '📊 آمار' => 'primary',
        '💰 تنظیم قیمت‌ها' => 'primary',
        '🎁 تنظیم سکه اولیه کاربران' => 'success',
        '🎁 تنظیم سکه زیرمجموعه' => 'success',
        '🎯 تنظیم جایزه روزانه' => 'success',
        '💳 تنظیم خرید سکه' => 'primary',
        '➕ افزایش/کاهش سکه' => 'success',
        '🎨 تنظیم رنگ دکمه‌ها' => 'primary',
        '🚫 خاموش/روشن دکمه‌ها' => 'danger',
        '➕ افزودن کانال جوین اجباری' => 'success',
        '➖ حذف کانال جوین اجباری' => 'danger',
        '📋 لیست کانال‌ها' => 'primary',
        '☕ تنظیم اسپانسر' => 'primary',
        '📣 پیام همگانی' => 'primary',
        '➕ افزودن ادمین' => 'success',
        '📦 تنظیم پلن‌های خرید سکه' => 'primary',
        '💳 تنظیم کارت به کارت' => 'primary',
        '🔄 وضعیت کارت به کارت' => 'success',
        '🟢 کارت به کارت روشن' => 'success',
        '🔴 کارت به کارت خاموش' => 'danger',
        '🔄 روشن/خاموش کارت به کارت' => 'primary',
        '🔵 تنظیم مرچنت زیبال' => 'primary',
        '🌐 تنظیم اتصال زیبال' => 'primary',
        '🔄 وضعیت زیبال' => 'success',
        '🟢 زیبال روشن' => 'success',
        '🔴 زیبال خاموش' => 'danger',
        '🔄 روشن/خاموش زیبال' => 'primary',
        '🧪 تست اتصال زیبال' => 'primary',
        'مقدار دلخواه' => 'primary',
        '❤️ تعداد دلخواه' => 'primary',
    ];
}

function buttonStyles(): array
{
    $default = defaultButtonStyles();
    $custom = setting('button_styles', []);
    if (!is_array($custom)) $custom = [];
    $out = $default;
    foreach ($custom as $text => $style) {
        $text = trim((string)$text);
        if ($text === '') continue;
        $norm = normalizeButtonStyle(is_string($style) ? $style : '');
        if ($norm) $out[$text] = $norm;
        else unset($out[$text]);
    }
    return $out;
}

function buttonStyleForText(string $text): ?string
{
    if ((string)setting('button_style_enabled', '1') !== '1') return null;
    $text = trim($text);
    if ($text === '') return null;
    foreach (buttonStyles() as $label => $style) {
        if ($text === (string)$label) return normalizeButtonStyle((string)$style);
    }
    if (preg_match('/^❤️\s*[۰-۹0-9]+$/u', $text)) return 'success';
    if (preg_match('/✅|تایید|ذخیره|افزودن|خرید|عضویت|بررسی/u', $text)) return 'success';
    if (preg_match('/❌|حذف|لغو|بازگشت|رد|خطا/u', $text)) return 'danger';
    if (preg_match('/پرداخت|زیبال|تنظیم|آمار|پروفایل|راهنما|لینک/u', $text)) return 'primary';
    return null;
}

function styleReplyButton(mixed $button): mixed
{
    if (is_string($button) || is_numeric($button)) {
        $text = (string)$button;
        $style = buttonStyleForText($text);
        return $style ? ['text' => $text, 'style' => $style] : $text;
    }
    if (is_array($button)) {
        if (isset($button['text']) && !isset($button['style'])) {
            $style = buttonStyleForText((string)$button['text']);
            if ($style) $button['style'] = $style;
        }
        return $button;
    }
    return $button;
}

function styleReplyKeyboardRows(array $keyboard): array
{
    $out = [];
    foreach ($keyboard as $row) {
        $newRow = [];
        foreach ((array)$row as $button) $newRow[] = styleReplyButton($button);
        $out[] = $newRow;
    }
    return $out;
}

function styleInlineButton(mixed $button): mixed
{
    if (!is_array($button)) return $button;
    if (isset($button['text']) && !isset($button['style'])) {
        $style = buttonStyleForText((string)$button['text']);
        if ($style) $button['style'] = $style;
    }
    return $button;
}

function styleInlineKeyboardRows(array $keyboard): array
{
    $out = [];
    foreach ($keyboard as $row) {
        $newRow = [];
        foreach ((array)$row as $button) $newRow[] = styleInlineButton($button);
        $out[] = $newRow;
    }
    return $out;
}

function j(array $keyboard, bool $resize = true): string
{
    return json_encode(['keyboard' => styleReplyKeyboardRows($keyboard), 'resize_keyboard' => $resize], JSON_UNESCAPED_UNICODE);
}

function inline(array $keyboard): string
{
    return json_encode(['inline_keyboard' => styleInlineKeyboardRows($keyboard)], JSON_UNESCAPED_UNICODE);
}

function botUsername(): string
{
    static $username = null;
    if ($username) return $username;
    if (defined('BOT_USERNAME') && BOT_USERNAME && BOT_USERNAME !== 'PUT_YOUR_BOT_USERNAME_HERE') {
        $username = ltrim(BOT_USERNAME, '@');
        return $username;
    }
    $res = tg('getMe');
    $username = (string)($res['result']['username'] ?? '');
    return $username;
}

function botId(): int
{
    static $id = null;
    if ($id) return $id;
    $res = tg('getMe');
    $id = (int)($res['result']['id'] ?? 0);
    return $id;
}

function botLink(string $payload): string
{
    return 'https://t.me/' . botUsername() . '?start=' . rawurlencode($payload);
}

function disabledButtons(): array
{
    $list = setting('disabled_buttons', []);
    return is_array($list) ? array_values(array_unique(array_filter(array_map('strval', $list)))) : [];
}

function isButtonDisabled(string $text): bool
{
    $text = trim($text);
    if ($text === '') return false;
    return in_array($text, disabledButtons(), true);
}

function filterKeyboardRows(array $rows, bool $adminArea = false): array
{
    $out = [];
    foreach ($rows as $row) {
        $newRow = [];
        foreach ((array)$row as $btn) {
            $label = is_array($btn) ? (string)($btn['text'] ?? '') : (string)$btn;
            if (!$adminArea && isButtonDisabled($label)) continue;
            $newRow[] = $btn;
        }
        if ($newRow) $out[] = $newRow;
    }
    return $out;
}

function mainKeyboard(bool $admin = false): string
{
    $keys = [
        ['❤️ ساخت لایک و چالش ❤️'],
        ['👤 پروفایل', '💰 افزایش سکه'],
        ['📖 راهنما', '⚙️ تنظیمات', '☕ اسپانسر ما'],
        ['🌐 Language / زبان'],
    ];
    if ($admin) $keys[] = ['🛠 پنل مدیریت'];
    return j(filterKeyboardRows($keys, false));
}

function createHubKeyboard(): string
{
    return j(filterKeyboardRows([
        ['❤️ ساخت لایک ❤️', '♦️ ساخت نظرسنجی ♦️'],
        ['💙 ساخت چالش لایک 💙'],
        ['🏃 ساخت چالش سرعتی 🏃'],
        ['🤍 ساخت خودکار لایک 🤍'],
        ['↩️ بازگشت'],
    ], false));
}

function coinsKeyboard(): string
{
    return j(filterKeyboardRows([
        ['🎯 جایزه روزانه'],
        ['🎁 جمع‌آوری سکه با زیرمجموعه'],
        ['💳 خرید سکه'],
        ['↩️ بازگشت'],
    ], false));
}

function cancelKeyboard(): string { return j([['لغو']]); }

function settingsKeyboard(): string
{
    return j(filterKeyboardRows([
        ['📢 تنظیم کانال ارسال پست‌ها'],
        ['📋 کانال‌های من'],
        ['❤️ تنظیم اموجی لایک'],
        ['💾 ذخیره تنظیمات پیشفرض'],
        ['↩️ بازگشت'],
    ], false));
}

function adminKeyboard(): string
{
    return j([
        ['📊 آمار', '💰 تنظیم قیمت‌ها'],
        ['🎁 تنظیم سکه اولیه کاربران', '🎁 تنظیم سکه زیرمجموعه'],
        ['🎯 تنظیم جایزه روزانه'],
        ['💳 تنظیم خرید سکه', '➕ افزایش/کاهش سکه'],
        ['🎨 تنظیم رنگ دکمه‌ها', '🚫 خاموش/روشن دکمه‌ها'],
        ['➕ افزودن کانال جوین اجباری', '➖ حذف کانال جوین اجباری'],
        ['📋 لیست کانال‌ها', '☕ تنظیم اسپانسر'],
        ['📣 پیام همگانی'],
        ['➕ افزودن ادمین'],
        ['↩️ بازگشت'],
    ]);
}

function paymentAdminKeyboard(): string
{
    $cardEnabled = (string)setting('payment_card_enabled', '1') === '1';
    $zibalEnabled = (string)setting('payment_zibal_enabled', '0') === '1';
    $bridgeEnabled = (string)setting('zibal_bridge_enabled', '1') === '1';
    $cardToggle = $cardEnabled ? '🟢 کارت به کارت روشن' : '🔴 کارت به کارت خاموش';
    $zibalToggle = $zibalEnabled ? '🟢 زیبال روشن' : '🔴 زیبال خاموش';
    $bridgeToggle = $bridgeEnabled ? '🟢 اتصال دامنه دوم روشن' : '⚪️ اتصال مستقیم زیبال';
    return j([
        ['📦 تنظیم پلن‌های خرید سکه'],
        ['💳 تنظیم کارت به کارت'],
        [$cardToggle, '🔄 روشن/خاموش کارت به کارت'],
        ['🔵 تنظیم مرچنت زیبال', '🌐 تنظیم اتصال زیبال'],
        ['🧭 تنظیم IP دامنه زیبال', $bridgeToggle],
        [$zibalToggle, '🔄 روشن/خاموش زیبال'],
        ['🧪 تست اتصال زیبال'],
        ['↩️ بازگشت'],
    ]);
}

function faDigits(int|string $value): string
{
    return strtr((string)$value, [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ]);
}

function countPromptText(string $title = 'تعیین تعداد سفارش', string $hint = 'لطفاً تعداد مورد نظر را انتخاب کنید.'): string
{
    return "📦 <b>{$title}</b>
"
        . "━━━━━━━━━━━━

"
        . "{$hint}

"
        . "گزینه‌ها دو به دو کنار هم چیده شده‌اند.
"
        . "در انتها می‌توانی تعداد دلخواه را وارد کنی.";
}

function countKeyboard(array $suggestions): string
{
    $rows = [];
    $row = [];
    foreach ($suggestions as $s) {
        $n = (int)$s;
        $row[] = ['text' => '❤️ ' . faDigits($n), 'callback_data' => 'count_value:' . $n];
        if (count($row) === 2) {
            $rows[] = $row;
            $row = [];
        }
    }
    if ($row) $rows[] = $row;
    $rows[] = [['text' => '❤️ تعداد دلخواه', 'callback_data' => 'count_custom']];
    $rows[] = [
        ['text' => '↩️ بازگشت', 'callback_data' => 'count_cancel'],
        ['text' => '❌ لغو', 'callback_data' => 'count_cancel'],
    ];
    return inline($rows);
}

function startText(array $user): string
{
    return "<b>چالش • لایک • نظرسنجی</b>\n\n" .
        "به بخش ساخت چالش، لایک و نظرسنجی خوش آمدید 😄\n\n" .
        "از گزینه‌های زیر استفاده کنید:";
}

function sendHome(int $chatId, array $user): void
{
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => startText($user),
        'parse_mode' => 'HTML',
        'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
    ]);
}

function normalizeChannels(string $text): array
{
    $lines = preg_split('/[\r\n,،\s]+/u', trim($text));
    $out = [];
    foreach ($lines ?: [] as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $line = rtrim($line, '/');
        if (preg_match('~^(?:https?://)?t\.me/c/(\d+)(?:/\d+)?$~i', $line, $m)) {
            $line = '-100' . $m[1];
        } elseif (preg_match('~^(?:https?://)?t\.me/([A-Za-z0-9_]{5,})(?:/\d+)?$~i', $line, $m)) {
            $line = '@' . $m[1];
        } elseif (preg_match('~^(?:https?://)?t\.me/(?:\+|joinchat/)~i', $line)) {
            // لینک دعوت قابل بررسی با getChat/getChatMember نیست. باید @username، آیدی -100 یا فوروارد پست ارسال شود.
            continue;
        }
        if ($line[0] !== '@' && !preg_match('/^-?\d+$/', $line)) $line = '@' . ltrim($line, '@');
        $out[] = $line;
    }
    return array_values(array_unique($out));
}


function normalizeOneChannel(string $text): string
{
    $arr = normalizeChannels($text);
    return $arr[0] ?? '';
}

function getGlobalJoinChannels(): array
{
    $channels = setting('global_join_channels', []);
    return is_array($channels) ? $channels : [];
}

function defaultForceChannelsFromUser(array $owner): array
{
    // برای قفل عضویت، شناسه عددی کانال (-100...) از @username مطمئن‌تر است؛
    // چون تغییر یوزرنیم یا مشکل resolve باعث گیر کردن کاربران بعد از عضویت می‌شود.
    $channel = trim((string)($owner['default_channel'] ?? ''));
    $channelId = trim((string)($owner['default_channel_id'] ?? ''));
    $channels = [];
    if ($channelId !== '') {
        $channels = normalizeChannels($channelId);
    }
    if (!$channels && $channel !== '') {
        $channels = normalizeChannels($channel);
    }
    return array_values(array_unique(array_filter(array_map('strval', $channels))));
}

function getUserJoinChannels(int $ownerId): array
{
    $owner = getUser($ownerId);
    if (!$owner) return [];
    $channels = json_decode((string)($owner['force_channels'] ?? ''), true);
    $channels = is_array($channels) ? array_values(array_filter(array_map('strval', $channels))) : [];

    // اگر کاربر کانال ارسال پست را تنظیم کرده ولی قفل عضویت شخصی هنوز خالی است،
    // به صورت خودکار همان کانال برای شرکت در لایک‌ها، نظرسنجی‌ها و چالش‌ها فعال می‌شود.
    if (!$channels) {
        $channels = defaultForceChannelsFromUser($owner);
        if ($channels) {
            try {
                db()->prepare('UPDATE users SET force_channels = ? WHERE id = ?')->execute([json_encode($channels, JSON_UNESCAPED_UNICODE), $ownerId]);
            } catch (Throwable $e) {
                if (function_exists('logError')) logError('Auto enable user force join failed', ['user_id' => $ownerId, 'error' => $e->getMessage()]);
            }
        }
    }

    return $channels;
}

function requiredChannelsForOwner(int $ownerId): array
{
    // شرکت‌کنندگان چالش/لایک/نظرسنجی فقط باید عضو کانال تنظیم‌شده سازنده همان لینک باشند.
    // کانال‌های جوین اجباری عمومی ربات فقط برای کسانی بررسی می‌شود که می‌خواهند سفارش بسازند.
    return array_values(array_unique(getUserJoinChannels($ownerId)));
}

function requireCreatorGlobalJoinOrSend(int $chatId, int $userId): bool
{
    $channels = getGlobalJoinChannels();
    if (!$channels) return true;
    return requireJoinOrSend($chatId, $userId, $channels, 'create');
}

function channelUrl(string $channel): string
{
    $c = ltrim($channel, '@');
    if (preg_match('/^-100(\d+)$/', $channel, $m)) return 'https://t.me/c/' . $m[1];
    if (preg_match('/^-?\d+$/', $channel)) return 'https://t.me/';
    return 'https://t.me/' . $c;
}


function isJoinedChannel(int $userId, string $channel): bool
{
    $channel = trim($channel);
    if ($channel === '') return true;

    $checks = [$channel];

    // اگر کانال با @ ذخیره شده بود، یک بار شناسه عددی را هم resolve می‌کنیم و دوباره تست می‌کنیم.
    // این مشکل باعث می‌شد کاربر عضو باشد ولی در چالش/لایک گیر کند.
    if (str_starts_with($channel, '@')) {
        $info = getTelegramChannelInfo($channel);
        if ($info && !empty($info['id'])) $checks[] = (string)$info['id'];
    }

    foreach (array_values(array_unique($checks)) as $ch) {
        $res = tg('getChatMember', ['chat_id' => $ch, 'user_id' => $userId], false);
        if (!($res['ok'] ?? false)) {
            $desc = (string)($res['description'] ?? '');
            // خطا را لاگ می‌کنیم ولی نتیجه را قطعی عضو حساب نمی‌کنیم؛ فقط مسیر بعدی را تست می‌کنیم.
            if (function_exists('logError') && $desc !== '') {
                logError('Join check failed', ['user_id' => $userId, 'channel' => $ch, 'error' => $desc]);
            }
            continue;
        }
        $m = $res['result'] ?? [];
        $status = (string)($m['status'] ?? 'left');
        if (in_array($status, ['creator', 'administrator', 'member'], true)) return true;
        if ($status === 'restricted' && (($m['is_member'] ?? false) === true)) return true;
    }

    return false;
}

function isJoinExemptUser(int $userId): bool
{
    // فقط ادمین اصلی ربات از جوین اجباری عمومی و شخصی معاف است.
    return in_array($userId, MAIN_ADMINS, true);
}

function missingJoinChannels(int $userId, array $channels): array
{
    if (isJoinExemptUser($userId)) return [];
    $missing = [];
    foreach ($channels as $ch) if (!isJoinedChannel($userId, $ch)) $missing[] = $ch;
    return $missing;
}

function requireJoinOrSend(int $chatId, int $userId, array $channels, string $payload = ''): bool
{
    if (!$channels || isJoinExemptUser($userId)) return true;
    $missing = missingJoinChannels($userId, $channels);
    if (!$missing) return true;
    $buttons = [];
    foreach ($missing as $ch) $buttons[] = [['text' => '🔐 عضویت در ' . $ch, 'url' => channelUrl($ch)]];
    $buttons[] = [['text' => '✅ بررسی عضویت', 'callback_data' => 'check_join:' . substr($payload, 0, 45)]];
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => (str_starts_with($payload, 'item_') || str_starts_with($payload, 'speed_'))
            ? "🔐 برای شرکت در این چالش/لایک، ابتدا در کانال زیر عضو شوید و سپس روی «بررسی عضویت» بزنید."
            : "🔐 برای ساخت سفارش، ابتدا در کانال‌های زیر عضو شوید و سپس روی «بررسی عضویت» بزنید.",
        'reply_markup' => inline($buttons),
    ]);
    return false;
}

function extractMessageContent(array $message): ?array
{
    $base = [
        'source_chat_id' => (string)($message['chat']['id'] ?? ''),
        'source_message_id' => (int)($message['message_id'] ?? 0),
        'forward_channel_chat_id' => null,
        'forward_channel_message_id' => null,
    ];

    if (isset($message['forward_origin']) && is_array($message['forward_origin'])) {
        $origin = $message['forward_origin'];
        if (($origin['type'] ?? '') === 'channel') {
            $base['forward_channel_chat_id'] = (string)($origin['chat']['id'] ?? '');
            $base['forward_channel_message_id'] = (int)($origin['message_id'] ?? 0);
        }
    } elseif (isset($message['forward_from_chat'], $message['forward_from_message_id'])) {
        $base['forward_channel_chat_id'] = (string)($message['forward_from_chat']['id'] ?? '');
        $base['forward_channel_message_id'] = (int)($message['forward_from_message_id'] ?? 0);
    }

    if (isset($message['text'])) return $base + ['media_type' => 'text', 'text' => $message['text'], 'file_id' => null, 'caption' => null];
    if (isset($message['photo'])) { $photo = end($message['photo']); return $base + ['media_type' => 'photo', 'text' => null, 'file_id' => $photo['file_id'], 'caption' => $message['caption'] ?? '']; }
    if (isset($message['video'])) return $base + ['media_type' => 'video', 'text' => null, 'file_id' => $message['video']['file_id'], 'caption' => $message['caption'] ?? ''];
    if (isset($message['animation'])) return $base + ['media_type' => 'animation', 'text' => null, 'file_id' => $message['animation']['file_id'], 'caption' => $message['caption'] ?? ''];
    if (isset($message['document'])) return $base + ['media_type' => 'document', 'text' => null, 'file_id' => $message['document']['file_id'], 'caption' => $message['caption'] ?? ''];
    if (isset($message['audio'])) return $base + ['media_type' => 'audio', 'text' => null, 'file_id' => $message['audio']['file_id'], 'caption' => $message['caption'] ?? ''];
    if (isset($message['voice'])) return $base + ['media_type' => 'voice', 'text' => null, 'file_id' => $message['voice']['file_id'], 'caption' => $message['caption'] ?? ''];
    return null;
}

function channelFromForward(array $message): ?array
{
    if (isset($message['forward_origin']) && is_array($message['forward_origin']) && ($message['forward_origin']['type'] ?? '') === 'channel') {
        $chat = $message['forward_origin']['chat'] ?? [];
        return ['id' => (string)($chat['id'] ?? ''), 'title' => (string)($chat['title'] ?? ''), 'username' => isset($chat['username']) ? '@' . $chat['username'] : ''];
    }
    if (isset($message['forward_from_chat']) && (($message['forward_from_chat']['type'] ?? '') === 'channel')) {
        $chat = $message['forward_from_chat'];
        return ['id' => (string)($chat['id'] ?? ''), 'title' => (string)($chat['title'] ?? ''), 'username' => isset($chat['username']) ? '@' . $chat['username'] : ''];
    }
    return null;
}

function getTelegramChannelInfo(string $channel): ?array
{
    if ($channel === '' || preg_match('~^(?:https?://)?t\.me/(?:\+|joinchat/)~i', $channel)) return null;
    $res = tg('getChat', ['chat_id' => $channel]);
    if (!($res['ok'] ?? false)) return null;
    $chat = $res['result'] ?? [];
    $type = (string)($chat['type'] ?? '');
    if ($type !== '' && !in_array($type, ['channel', 'supergroup'], true)) return null;
    return [
        'id' => (string)($chat['id'] ?? $channel),
        'title' => (string)($chat['title'] ?? $chat['username'] ?? $channel),
        'username' => isset($chat['username']) ? '@' . $chat['username'] : $channel,
        'type' => $type,
    ];
}


function checkBotAdminInChannel(string $chatId): array
{
    $botId = botId();
    if (!$botId) return ['ok' => false, 'msg' => 'امکان بررسی دسترسی ربات فراهم نشد. کمی بعد دوباره تلاش کنید.'];
    $res = tg('getChatMember', ['chat_id' => $chatId, 'user_id' => $botId]);
    if (!($res['ok'] ?? false)) {
        $desc = (string)($res['description'] ?? '');
        if (str_contains($desc, 'member list is inaccessible')) {
            return ['ok' => false, 'msg' => 'لیست اعضای کانال برای ربات قابل دسترسی نیست. ربات باید ادمین کانال باشد و دسترسی‌های لازم را داشته باشد.'];
        }
        return ['ok' => false, 'msg' => 'ربات در این کانال ادمین نیست، کانال پیدا نشد، یا آیدی کانال اشتباه است.'];
    }
    $m = $res['result'] ?? [];
    $status = $m['status'] ?? 'left';
    if (!in_array($status, ['administrator', 'creator'], true)) return ['ok' => false, 'msg' => 'ربات باید داخل کانال ادمین باشد.'];
    if ($status !== 'creator') {
        if (!($m['can_post_messages'] ?? false)) return ['ok' => false, 'msg' => 'دسترسی ارسال پیام / Post Messages برای ربات در کانال فعال نیست.'];
        if (!($m['can_edit_messages'] ?? false)) return ['ok' => false, 'msg' => 'دسترسی ویرایش پیام‌ها / Edit Messages برای ربات در کانال فعال نیست.'];
    }
    return ['ok' => true, 'msg' => 'OK'];
}


function userHasChannel(array $user): bool
{
    return !empty($user['default_channel']) && !empty($user['default_channel_id']);
}

function requireUserChannelOrAsk(int $chatId, array $user): bool
{
    if (userHasChannel($user)) return true;
    setStep((int)$user['id'], 'await_user_channel');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "📢 اول کانال مقصد را تنظیم کن.\n\nربات را داخل کانال ادمین کن، سپس آیدی کانال را بفرست؛ مثل:\n@yourchannel\n\nیا یک پست از همان کانال را برای ربات فوروارد کن.",
        'reply_markup' => cancelKeyboard(),
    ]);
    return false;
}


function userChannelsText(array $user): string
{
    $title = trim((string)($user['default_channel_title'] ?? ''));
    $channel = trim((string)($user['default_channel'] ?? ''));
    $channelId = trim((string)($user['default_channel_id'] ?? ''));

    if ($channel === '' && $channelId === '') {
        return "📋 کانال‌های من

هنوز کانالی برای ارسال پست‌ها تنظیم نکرده‌ای.

از گزینه «📢 تنظیم کانال ارسال پست‌ها» استفاده کن. پس از تنظیم کانال، عضویت اجباری برای شرکت در لایک‌ها، نظرسنجی‌ها و چالش‌ها خودکار روشن می‌شود.";
    }

    $force = defaultForceChannelsFromUser($user);
    if (!$force) {
        $storedForce = json_decode((string)($user['force_channels'] ?? ''), true);
        $force = is_array($storedForce) ? array_values(array_filter(array_map('strval', $storedForce))) : [];
    }
    $forceList = $force ? implode("
", array_map(fn($c) => '• ' . $c, $force)) : 'همان کانال تنظیم‌شده شما';

    if ($title === '') $title = 'بدون عنوان';
    if ($channel === '') $channel = $channelId ?: 'نامشخص';
    if ($channelId === '') $channelId = 'نامشخص';

    return "📋 کانال‌های من

" .
        "📢 کانال ارسال پست‌ها:
" .
        "• عنوان: {$title}
" .
        "• آیدی/یوزرنیم: {$channel}
" .
        "• شناسه عددی: {$channelId}

" .
        "🔐 قفل شرکت در چالش‌ها، لایک‌ها و نظرسنجی‌ها: خودکار روشن
" .
        "کانال قفل‌شده:
{$forceList}

" .
        "کاربرانی که عضو کانال بالا نباشند نمی‌توانند در لایک‌ها، نظرسنجی‌ها و چالش‌های شما شرکت کنند.";
}

function showUserChannels(int $chatId, array $user): void
{
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => userChannelsText($user),
        'reply_markup' => settingsKeyboard(),
    ]);
}

function createItem(int $ownerId, string $type, array $content, ?array $options = null, ?string $emoji = null, int $fakeLikes = 0, ?array $channel = null): int
{
    $stmt = db()->prepare('INSERT INTO items (owner_id, type, text, media_type, file_id, caption, emoji, options_json, fake_likes, target_channel, channel_chat_id, channel_message_id, source_chat_id, source_message_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $ownerId,
        $type,
        $content['text'] ?? null,
        $content['media_type'] ?? 'text',
        $content['file_id'] ?? null,
        $content['caption'] ?? null,
        $emoji ?: DEFAULT_LIKE_EMOJI,
        $options ? json_encode($options, JSON_UNESCAPED_UNICODE) : null,
        max(0, $fakeLikes),
        $channel['default_channel'] ?? null,
        isset($channel['default_channel_id']) ? (string)$channel['default_channel_id'] : null,
        null,
        $content['source_chat_id'] ?? null,
        $content['source_message_id'] ?? null,
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s'),
    ]);
    return (int)db()->lastInsertId();
}

function updateItemChannelMessage(int $itemId, string $chatId, int $messageId): void
{
    $stmt = db()->prepare('UPDATE items SET channel_chat_id = ?, channel_message_id = ?, updated_at = ? WHERE id = ?');
    $stmt->execute([(string)$chatId, $messageId, date('Y-m-d H:i:s'), $itemId]);
}


function updateItemFakeLikes(int $itemId, int $newFakeLikes): void
{
    $stmt = db()->prepare('UPDATE items SET fake_likes = ?, updated_at = ? WHERE id = ?');
    $stmt->execute([max(0, $newFakeLikes), date('Y-m-d H:i:s'), $itemId]);
}

function adjustItemFakeLikes(int $itemId, int $delta): ?array
{
    $item = getItem($itemId);
    if (!$item) return null;
    $current = max(0, (int)($item['fake_likes'] ?? 0));
    $next = max(0, $current + $delta);
    updateItemFakeLikes($itemId, $next);
    return getItem($itemId);
}

function refreshItemMarkupOnChannel(array $item): bool
{
    $chatId = (string)($item['channel_chat_id'] ?? '');
    $messageId = (int)($item['channel_message_id'] ?? 0);
    if ($chatId === '' || $messageId <= 0) return false;
    $res = tg('editMessageReplyMarkup', [
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'reply_markup' => itemMarkup($item),
    ], false);
    return (bool)($res['ok'] ?? false);
}

function likeControlKeyboard(int $itemId): string
{
    return inline([
        [
            ['text' => '+1 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':1'],
            ['text' => '+5 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':5'],
        ],
        [
            ['text' => '+10 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':10'],
            ['text' => '+50 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':50'],
        ],
        [
            ['text' => '+100 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':100'],
            ['text' => '+500 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':500'],
        ],
        [
            ['text' => '-1 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-1'],
            ['text' => '-5 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-5'],
        ],
        [
            ['text' => '-10 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-10'],
            ['text' => '-50 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-50'],
        ],
        [
            ['text' => '-100 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-100'],
            ['text' => '-500 ❤️', 'callback_data' => 'lkadj:' . $itemId . ':-500'],
        ],
        [
            ['text' => '↩️ به منوی اصلی', 'callback_data' => 'lkadj_home'],
        ],
    ]);
}

function likeControlText(array $item, string $title = 'پست شما با موفقیت ارسال شد!'): string
{
    $fake = max(0, (int)($item['fake_likes'] ?? 0));
    $real = realVoteCount((int)$item['id']);
    $total = likeCount($item);
    return "✅ {$title}\n\nبرای تغییر عدد لایک از گزینه‌های زیر استفاده کنید.\n\n❤️ عدد نمایشی فعلی: {$total}\n➕ لایک دستی: {$fake}\n👥 لایک واقعی کاربران: {$real}";
}

function sendLikeControlPanel(int|string $chatId, int $itemId, string $title = 'پست شما با موفقیت ارسال شد!'): void
{
    $item = getItem($itemId);
    if (!$item || (string)($item['type'] ?? '') === 'poll') return;
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => likeControlText($item, $title),
        'reply_markup' => likeControlKeyboard($itemId),
    ]);
}

function getItem(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM items WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function realVoteCount(int $itemId): int
{
    $stmt = db()->prepare('SELECT COUNT(*) c FROM votes WHERE item_id = ?');
    $stmt->execute([$itemId]);
    return (int)($stmt->fetch()['c'] ?? 0);
}

function likeCount(array $item): int
{
    return max(0, (int)($item['fake_likes'] ?? 0)) + realVoteCount((int)$item['id']);
}

function pollCounts(int $itemId): array
{
    $stmt = db()->prepare('SELECT vote_value, COUNT(*) c FROM votes WHERE item_id = ? GROUP BY vote_value');
    $stmt->execute([$itemId]);
    $counts = [];
    foreach ($stmt->fetchAll() as $row) $counts[(string)$row['vote_value']] = (int)$row['c'];
    return $counts;
}

function likeButtonStyleForItem(array $item): ?string
{
    if ((string)setting('button_style_enabled', '1') !== '1') return null;

    $aliases = ((string)($item['type'] ?? '') === 'challenge_like')
        ? ['دکمه چالش لایک زیر پست', 'CHALLENGE_LIKE_BUTTON', 'دکمه لایک زیر پست', 'LIKE_BUTTON']
        : ['دکمه لایک زیر پست', 'LIKE_BUTTON'];

    $custom = setting('button_styles', []);
    if (!is_array($custom)) $custom = [];

    foreach ($aliases as $alias) {
        if (array_key_exists($alias, $custom)) {
            return normalizeButtonStyle((string)$custom[$alias]);
        }
    }

    $defaults = defaultButtonStyles();
    foreach ($aliases as $alias) {
        if (array_key_exists($alias, $defaults)) {
            return normalizeButtonStyle((string)$defaults[$alias]);
        }
    }

    return normalizeButtonStyle((string)setting('like_button_style', 'success'));
}

function likeMarkup(array $item): string
{
    $id = (int)$item['id'];
    $count = likeCount($item);
    $emoji = $item['emoji'] ?: DEFAULT_LIKE_EMOJI;
    $text = $item['type'] === 'challenge_like' ? "{$emoji} شرکت در چالش - {$count}" : "{$emoji} {$count}";
    $button = ['text' => $text, 'callback_data' => 'like:' . $id];
    $style = likeButtonStyleForItem($item);
    if ($style) $button['style'] = $style;
    return inline([[$button]]);
}

function pollMarkup(array $item): string
{
    $id = (int)$item['id'];
    $options = json_decode((string)$item['options_json'], true);
    $options = is_array($options) ? $options : [];
    $counts = pollCounts($id);
    $keyboard = [];
    foreach ($options as $i => $opt) {
        $c = $counts[(string)$i] ?? 0;
        $keyboard[] = [['text' => $opt . ' — ' . $c, 'callback_data' => 'poll:' . $id . ':' . $i]];
    }
    return inline($keyboard);
}

function itemMarkup(array $item): string { return $item['type'] === 'poll' ? pollMarkup($item) : likeMarkup($item); }

function sendItemToChat(int|string $chatId, array $item): array
{
    $markup = itemMarkup($item);
    $type = $item['media_type'] ?: 'text';
    if ($item['type'] === 'poll') {
        $text = "📊 <b>نظرسنجی</b>\n\n" . e((string)$item['text']) . "\n\nیکی از گزینه‌ها را انتخاب کنید:";
        return tg('sendMessage', ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML', 'reply_markup' => $markup]);
    }
    $prefix = $item['type'] === 'challenge_like' ? "💙 چالش لایک\n\n" : '';
    if ($type === 'text') return tg('sendMessage', ['chat_id' => $chatId, 'text' => $prefix . (string)$item['text'], 'reply_markup' => $markup]);
    $caption = $prefix . (string)($item['caption'] ?? '');
    $params = ['chat_id' => $chatId, 'caption' => $caption, 'reply_markup' => $markup];
    if ($type === 'photo') { $params['photo'] = $item['file_id']; return tg('sendPhoto', $params); }
    if ($type === 'video') { $params['video'] = $item['file_id']; return tg('sendVideo', $params); }
    if ($type === 'animation') { $params['animation'] = $item['file_id']; return tg('sendAnimation', $params); }
    if ($type === 'audio') { $params['audio'] = $item['file_id']; return tg('sendAudio', $params); }
    if ($type === 'voice') { $params['voice'] = $item['file_id']; return tg('sendVoice', $params); }
    $params['document'] = $item['file_id'];
    return tg('sendDocument', $params);
}

function publishContentToUserChannel(array $user, array $content, int $fakeLikes, string $type = 'like'): array
{
    $priceKey = $type === 'challenge_like' ? 'challenge_like' : 'like';
    $ownerId = (int)$user['id'];
    $price = price($priceKey);
    if (!takeCoins($ownerId, $price)) return ['ok' => false, 'msg' => '❌ موجودی کافی نیست.'];

    $channel = [
        'default_channel' => $user['default_channel'],
        'default_channel_id' => (string)$user['default_channel_id'],
    ];
    $itemId = createItem($ownerId, $type, $content, null, $user['like_emoji'] ?: DEFAULT_LIKE_EMOJI, $fakeLikes, $channel);
    $item = getItem($itemId);
    if (!$item) { addCoins($ownerId, $price); return ['ok' => false, 'msg' => 'خطا در ساخت آیتم.']; }

    $targetChatId = (string)$user['default_channel_id'];
    $res = ['ok' => false];

    if (!empty($content['forward_channel_chat_id']) && !empty($content['forward_channel_message_id']) && (string)$content['forward_channel_chat_id'] === $targetChatId) {
        $res = tg('editMessageReplyMarkup', [
            'chat_id' => $targetChatId,
            'message_id' => (int)$content['forward_channel_message_id'],
            'reply_markup' => itemMarkup($item),
        ]);
        if ($res['ok'] ?? false) {
            updateItemChannelMessage($itemId, $targetChatId, (int)$content['forward_channel_message_id']);
            return ['ok' => true, 'mode' => 'edited', 'item_id' => $itemId, 'message_id' => (int)$content['forward_channel_message_id']];
        }
    }

    if (!empty($content['source_chat_id']) && !empty($content['source_message_id'])) {
        $res = tg('copyMessage', [
            'chat_id' => $targetChatId,
            'from_chat_id' => $content['source_chat_id'],
            'message_id' => (int)$content['source_message_id'],
            'reply_markup' => itemMarkup($item),
        ]);
        if ($res['ok'] ?? false) {
            $mid = (int)($res['result']['message_id'] ?? 0);
            if ($mid > 0) updateItemChannelMessage($itemId, $targetChatId, $mid);
            return ['ok' => true, 'mode' => 'copied', 'item_id' => $itemId, 'message_id' => $mid];
        }
    }

    $res = sendItemToChat($targetChatId, $item);
    if ($res['ok'] ?? false) {
        $mid = (int)($res['result']['message_id'] ?? 0);
        if ($mid > 0) updateItemChannelMessage($itemId, $targetChatId, $mid);
        return ['ok' => true, 'mode' => 'sent', 'item_id' => $itemId, 'message_id' => $mid];
    }

    addCoins($ownerId, $price);
    return ['ok' => false, 'msg' => '❌ ارسال/ویرایش پست در کانال انجام نشد. ربات باید ادمین کانال باشد و دسترسی ارسال/ویرایش پیام داشته باشد.'];
}

function sendCreatedLink(int $chatId, array $user, int $itemId, string $label): void
{
    $link = botLink('item_' . $itemId);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "✅ {$label} ساخته شد.\n\n🔗 لینک شما:\n{$link}\n\nمی‌توانید این لینک را در کانال، بیو یا گروه قرار دهید.",
        'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
    ]);
}

function price(string $key): int { return max(0, (int)setting('price_' . $key, 0)); }

function requireCoinsOrSend(int $chatId, array $user, string $priceKey, int $multiplier = 1): bool
{
    $need = price($priceKey) * max(1, $multiplier);
    if ((int)$user['coins'] >= $need) return true;
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "❌ موجودی سکه کافی نیست.\n💰 موجودی شما: {$user['coins']}\n💳 هزینه لازم: {$need}",
        'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
    ]);
    return false;
}


function logAutoLike(string $stage, array $context = []): void
{
    $file = __DIR__ . '/auto_like.log';
    if (is_file($file) && filesize($file) > 1024 * 1024 * 3) {
        @rename($file, __DIR__ . '/auto_like_' . date('Ymd_His') . '.log');
    }
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $stage;
    if ($context) $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @file_put_contents($file, $line . PHP_EOL, FILE_APPEND);
}

function channelPostType(array $message): string
{
    foreach (['text','photo','video','animation','document','audio','voice','video_note','sticker','poll','location','venue','contact'] as $type) {
        if (isset($message[$type])) return $type;
    }
    if (isset($message['new_chat_title']) || isset($message['new_chat_photo']) || isset($message['pinned_message'])) return 'service';
    return 'unknown';
}

function isSupportedAutoLikeChannelPost(array $message): bool
{
    $type = channelPostType($message);
    return in_array($type, ['text','photo','video','animation','document','audio','voice','video_note','sticker','poll','location','venue','contact'], true);
}

function itemExistsForChannelMessage(string $chatId, int $messageId): bool
{
    $stmt = db()->prepare('SELECT id FROM items WHERE channel_chat_id = ? AND channel_message_id = ? LIMIT 1');
    $stmt->execute([(string)$chatId, $messageId]);
    return (bool)$stmt->fetch();
}

function checkBotAdminInChannelForAuto(string $chatId): array
{
    $botId = botId();
    if (!$botId) return ['ok' => false, 'msg' => 'امکان بررسی دسترسی ربات فراهم نشد. کمی بعد دوباره تلاش کنید.'];
    $res = tg('getChatMember', ['chat_id' => $chatId, 'user_id' => $botId]);
    if (!($res['ok'] ?? false)) {
        $desc = (string)($res['description'] ?? '');
        if (str_contains($desc, 'member list is inaccessible')) {
            return ['ok' => false, 'msg' => 'لیست اعضای کانال برای ربات قابل دسترسی نیست؛ ربات باید ادمین کانال باشد.'];
        }
        return ['ok' => false, 'msg' => 'ربات در این کانال ادمین نیست یا آیدی کانال اشتباه است.'];
    }
    $m = $res['result'] ?? [];
    $status = (string)($m['status'] ?? 'left');
    if (!in_array($status, ['administrator', 'creator'], true)) return ['ok' => false, 'msg' => 'ربات باید داخل کانال ادمین باشد.'];
    if ($status !== 'creator' && !($m['can_edit_messages'] ?? false)) {
        return ['ok' => false, 'msg' => 'دسترسی Edit Messages / ویرایش پیام‌ها برای ربات فعال نیست.'];
    }
    return ['ok' => true, 'msg' => 'OK'];
}

function helpText(): string
{
    return "📖 <b>راهنما</b>\n\n" .
        "🔷 <b>این ربات چه کاربردی دارد؟</b>\n" .
        "این ربات برای ساخت لایک، نظرسنجی و چالش‌های مختلف کانالی است.\n\n" .
        "🔶 <b>ساخت لایک</b>\n" .
        "اول کانال را تنظیم می‌کنید، ربات را ادمین می‌کنید، پست را برای ربات می‌فرستید، تعداد لایک اولیه را انتخاب می‌کنید و ربات زیر همان پست یا نسخه ارسالی در کانال دکمه لایک می‌گذارد.\n\n" .
        "🔷 <b>ساخت خودکار لایک</b>\n" .
        "مشخص می‌کنید چند پست آینده کانال لایک بخورد و هرکدام با چند لایک اولیه شروع شود. بابت هر پست، هزینه تنظیم‌شده کم می‌شود.\n\n" .
        "🔶 <b>چالش سرعتی</b>\n" .
        "ربات یک لینک اختصاصی می‌دهد؛ هر کاربری که اولین نفر لینک را باز کند برنده می‌شود و اطلاعات او برای سازنده چالش ارسال می‌شود.\n\n" .
        "با تشکر از همراهی شما 🙏";
}

function createSpeedChallenge(int $ownerId): string
{
    db()->prepare('UPDATE speed_challenges SET is_active = 0 WHERE owner_id = ? AND is_active = 1')->execute([$ownerId]);
    $token = bin2hex(random_bytes(16));
    $stmt = db()->prepare('INSERT INTO speed_challenges (token, owner_id, is_active, created_at) VALUES (?, ?, 1, ?)');
    $stmt->execute([$token, $ownerId, date('Y-m-d H:i:s')]);
    return $token;
}

function getSpeedChallenge(string $token): ?array
{
    $stmt = db()->prepare('SELECT * FROM speed_challenges WHERE token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function winnerInfo(array $from): array
{
    return [
        'id' => (int)($from['id'] ?? 0),
        'username' => $from['username'] ?? null,
        'first_name' => $from['first_name'] ?? '',
        'last_name' => $from['last_name'] ?? '',
        'language_code' => $from['language_code'] ?? null,
    ];
}

function displayName(array $from): string
{
    $name = trim((string)($from['first_name'] ?? '') . ' ' . (string)($from['last_name'] ?? ''));
    if ($name === '' && !empty($from['username'])) $name = '@' . $from['username'];
    return $name ?: 'کاربر';
}

function userMention(int $userId, string $name): string { return '<a href="tg://user?id=' . $userId . '">' . e($name) . '</a>'; }

function createAutoLikeJob(int $ownerId, string $channelId, string $channelChatId, string $title, int $posts, int $fakeLikes): int
{
    $stmt = db()->prepare('INSERT INTO auto_like_jobs (owner_id, channel_id, channel_chat_id, channel_title, remaining_posts, total_posts, fake_likes, price_per_post, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$ownerId, $channelId, $channelChatId, $title, $posts, $posts, $fakeLikes, price('auto_like'), 'active', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    return (int)db()->lastInsertId();
}

function getActiveAutoJobForChannel(string $channelChatId): ?array
{
    $stmt = db()->prepare("SELECT * FROM auto_like_jobs WHERE channel_chat_id = ? AND status = 'active' AND remaining_posts > 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$channelChatId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function stopActiveAutoJobsForChannel(string $channelChatId, ?int $exceptJobId = null): int
{
    if ($exceptJobId && $exceptJobId > 0) {
        $stmt = db()->prepare("UPDATE auto_like_jobs SET status = 'stopped', updated_at = ? WHERE channel_chat_id = ? AND status = 'active' AND id <> ?");
        $stmt->execute([date('Y-m-d H:i:s'), $channelChatId, $exceptJobId]);
    } else {
        $stmt = db()->prepare("UPDATE auto_like_jobs SET status = 'stopped', updated_at = ? WHERE channel_chat_id = ? AND status = 'active'");
        $stmt->execute([date('Y-m-d H:i:s'), $channelChatId]);
    }
    return $stmt->rowCount();
}

function decrementAutoJob(int $jobId): void
{
    $stmt = db()->prepare("UPDATE auto_like_jobs SET remaining_posts = remaining_posts - 1, status = IF(remaining_posts - 1 <= 0, 'done', status), updated_at = ? WHERE id = ? AND remaining_posts > 0");
    $stmt->execute([date('Y-m-d H:i:s'), $jobId]);
}

function stopAutoJob(int $jobId): void
{
    db()->prepare("UPDATE auto_like_jobs SET status = 'stopped', updated_at = ? WHERE id = ?")->execute([date('Y-m-d H:i:s'), $jobId]);
}

function postLink(?string $channel, ?int $messageId): string
{
    if (!$channel || !$messageId) return '';
    if (str_starts_with($channel, '@')) return 'https://t.me/' . ltrim($channel, '@') . '/' . $messageId;
    return '';
}


function coinPackages(): array
{
    $packages = setting('coin_packages', []);
    if (!is_array($packages)) $packages = [];
    $out = [];
    foreach ($packages as $p) {
        $coins = (int)($p['coins'] ?? 0);
        $amount = (int)($p['amount'] ?? 0);
        if ($coins > 0 && $amount > 0) $out[] = ['coins' => $coins, 'amount' => $amount];
    }
    return $out;
}


function dailyRewards(): array
{
    $rewards = setting('daily_rewards', ['1' => 0, '2' => 3, '3' => 1, '4' => 2, '5' => 3, '6' => 0]);
    if (!is_array($rewards)) $rewards = [];
    $out = [];
    for ($i = 1; $i <= 6; $i++) $out[(string)$i] = max(0, (int)($rewards[(string)$i] ?? $rewards[$i] ?? 0));
    return $out;
}

function dailyRewardsText(): string
{
    $rewards = dailyRewards();
    $lines = [];
    for ($i = 1; $i <= 6; $i++) {
        $coins = (int)$rewards[(string)$i];
        $lines[] = $i . '️⃣ : ' . ($coins > 0 ? $coins . ' سکه' : 'پوچ');
    }
    return implode("\n", $lines);
}

function formatToman(int $amount): string
{
    return number_format($amount) . ' تومان';
}

function paymentMethodsEnabled(): array
{
    $methods = [];
    if ((string)setting('payment_card_enabled', '1') === '1') $methods[] = 'card';
    if ((string)setting('payment_zibal_enabled', '0') === '1' && trim((string)setting('zibal_merchant', '')) !== '') $methods[] = 'zibal';
    return $methods;
}

function createCoinOrder(int $userId, int $coins, int $amount, string $method, string $status = 'created'): int
{
    $stmt = db()->prepare('INSERT INTO coin_orders (user_id, coins, amount, method, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $now = date('Y-m-d H:i:s');
    $stmt->execute([$userId, $coins, $amount, $method, $status, $now, $now]);
    return (int)db()->lastInsertId();
}

function getCoinOrder(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM coin_orders WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getCoinOrderByTrackId(string $trackId): ?array
{
    $stmt = db()->prepare('SELECT * FROM coin_orders WHERE zibal_track_id = ? LIMIT 1');
    $stmt->execute([$trackId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function updateCoinOrder(int $id, array $fields): void
{
    if (!$fields) return;
    $allowed = ['status','receipt_file_id','receipt_type','receipt_text','admin_message_id','zibal_track_id','zibal_ref_id','updated_at'];
    $sets = [];
    $vals = [];
    foreach ($fields as $k => $v) {
        if (!in_array($k, $allowed, true)) continue;
        $sets[] = dbIdentifier($k) . ' = ?';
        $vals[] = $v;
    }
    if (!$sets) return;
    if (!array_key_exists('updated_at', $fields)) {
        $sets[] = '`updated_at` = ?';
        $vals[] = date('Y-m-d H:i:s');
    }
    $vals[] = $id;
    db()->prepare('UPDATE coin_orders SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($vals);
}

function markCoinOrderPaid(int $orderId, string $refId = ''): bool
{
    $order = getCoinOrder($orderId);
    if (!$order || (string)$order['status'] === 'paid') return false;
    db()->beginTransaction();
    try {
        $stmt = db()->prepare("UPDATE coin_orders SET status = 'paid', zibal_ref_id = ?, updated_at = ? WHERE id = ? AND status <> 'paid'");
        $stmt->execute([$refId, date('Y-m-d H:i:s'), $orderId]);
        if ($stmt->rowCount() < 1) { db()->rollBack(); return false; }
        db()->prepare('UPDATE users SET coins = coins + ? WHERE id = ?')->execute([(int)$order['coins'], (int)$order['user_id']]);
        db()->commit();
        return true;
    } catch (Throwable $e) {
        if (db()->inTransaction()) db()->rollBack();
        logError('mark order paid failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        return false;
    }
}

function adminIds(): array
{
    $ids = MAIN_ADMINS;
    try {
        foreach (db()->query('SELECT user_id FROM admins')->fetchAll() as $row) $ids[] = (int)$row['user_id'];
    } catch (Throwable $e) {}
    return array_values(array_unique(array_filter(array_map('intval', $ids))));
}

function zibalCallbackUrl(): string
{
    if (str_ends_with(WEBHOOK_URL, '/bot.php')) return substr(WEBHOOK_URL, 0, -7) . 'zibal_callback.php';
    return rtrim(dirname(WEBHOOK_URL), '/') . '/zibal_callback.php';
}

function paymentNoticeBaseUrl(): string
{
    if (str_ends_with(WEBHOOK_URL, '/bot.php')) return substr(WEBHOOK_URL, 0, -7) . 'payment_notice.php';
    return rtrim(dirname(WEBHOOK_URL), '/') . '/payment_notice.php';
}

function paymentNoticeSign(int $orderId, string $trackId): string
{
    $key = BOT_TOKEN . '|' . trim((string)setting('zibal_bridge_secret', ''));
    return hash_hmac('sha256', $orderId . '|' . $trackId, $key);
}

function paymentNoticeUrl(int $orderId, string $trackId): string
{
    $sep = str_contains(paymentNoticeBaseUrl(), '?') ? '&' : '?';
    return paymentNoticeBaseUrl() . $sep . 'order=' . rawurlencode((string)$orderId) . '&track=' . rawurlencode($trackId) . '&sig=' . rawurlencode(paymentNoticeSign($orderId, $trackId));
}

function base64urlEncodeMain(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function zibalBridgeCallbackUrl(): string
{
    $bridgeUrl = normalizeBridgeUrl((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php'));
    $secret = trim((string)setting('zibal_bridge_secret', 'CHANGE_THIS_SECRET'));
    if ($bridgeUrl === '' || $secret === '' || $secret === 'CHANGE_THIS_SECRET') return zibalCallbackUrl();
    $return = base64urlEncodeMain(zibalCallbackUrl());
    $sig = hash_hmac('sha256', $return, $secret);
    $sep = str_contains($bridgeUrl, '?') ? '&' : '?';
    return $bridgeUrl . $sep . 'action=callback&return=' . rawurlencode($return) . '&sig=' . rawurlencode($sig);
}

function normalizeBridgeUrl(string $url): string
{
    $url = trim($url);
    if ($url === '') return '';
    if (!preg_match('~^https?://~i', $url)) $url = 'https://' . $url;
    $parts = parse_url($url);
    if (!$parts || empty($parts['host'])) return $url;
    $path = $parts['path'] ?? '';
    if ($path === '' || $path === '/') {
        $url = rtrim($url, '/') . '/zibal_bridge.php';
    }
    return $url;
}

function zibalBridgeApiCurl(string $url, array $payload, string $host, int $port, string $forcedIp, bool $verifySsl): array
{
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ];

    if ($forcedIp !== '' && filter_var($forcedIp, FILTER_VALIDATE_IP) && $host !== '') {
        $opts[CURLOPT_RESOLVE] = ["{$host}:{$port}:{$forcedIp}"];
    }

    if (!$verifySsl) {
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_SSL_VERIFYHOST] = 0;
    }

    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $errno = curl_errno($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    return ['raw' => $raw, 'error' => $err, 'errno' => $errno, 'http' => $http, 'ssl_verify' => $verifySsl];
}

function zibalBridgeApi(string $action, array $payload): array
{
    $enabled = (string)setting('zibal_bridge_enabled', '1') === '1';
    $url = normalizeBridgeUrl((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php'));
    $secret = trim((string)setting('zibal_bridge_secret', 'CHANGE_THIS_SECRET'));
    if (!$enabled || $url === '') return ['ok' => false, 'bridge_disabled' => true];

    $payload['secret'] = $secret;
    $payload['action'] = $action;

    $parts = parse_url($url);
    $host = (string)($parts['host'] ?? '');
    $scheme = strtolower((string)($parts['scheme'] ?? 'https'));
    $port = (int)($parts['port'] ?? ($scheme === 'http' ? 80 : 443));
    $forcedIp = trim((string)setting('zibal_bridge_ip', ''));

    $attempt = zibalBridgeApiCurl($url, $payload, $host, $port, $forcedIp, true);
    $raw = $attempt['raw'];
    $err = (string)$attempt['error'];
    $errno = (int)$attempt['errno'];
    $http = (int)$attempt['http'];
    $sslBypass = false;

    // وقتی IP دستی ثبت می‌شود، ممکن است SSL هاست دوم روی دامنه دیگری صادر شده باشد.
    // در این حالت فقط برای ارتباط داخلی bridge، یک بار با همان secret و بدون verify SSL دوباره تست می‌کنیم.
    if (($raw === false || $err) && $forcedIp !== '' && preg_match('/SSL|certificate|subject name|hostname|verify/i', $err)) {
        $retry = zibalBridgeApiCurl($url, $payload, $host, $port, $forcedIp, false);
        if (!($retry['raw'] === false || $retry['error'])) {
            $raw = $retry['raw'];
            $err = '';
            $errno = (int)$retry['errno'];
            $http = (int)$retry['http'];
            $sslBypass = true;
        }
    }

    if ($raw === false || $err) {
        $msg = $err ?: 'bridge connection failed';
        if (stripos($msg, 'Could not resolve host') !== false) {
            $msg = 'دامنه فایل اتصال زیبال از DNS هاست اصلی شناسایی نشد. IP هاست دوم را ثبت کن یا حالت اتصال زیبال را روی مستقیم بگذار. خطای cURL: ' . $msg;
        } elseif (preg_match('/SSL|certificate|subject name|hostname|verify/i', $msg)) {
            $msg = 'SSL دامنه فایل اتصال زیبال با دامنه تنظیم‌شده هماهنگ نیست. روی هاست دوم SSL همین دامنه را فعال کن، یا IP هاست دوم را ثبت کن، یا حالت اتصال زیبال را روی مستقیم بگذار. خطای cURL: ' . $msg;
        }
        return ['ok' => false, 'result' => -1, 'message' => $msg, 'curl_errno' => $errno, 'http_code' => $http, 'bridge_url' => $url, 'forced_ip' => $forcedIp];
    }

    $json = json_decode((string)$raw, true);
    if (!is_array($json)) {
        $rawShort = trim(strip_tags((string)$raw));
        if ($rawShort === '') $rawShort = mb_substr((string)$raw, 0, 350);
        $rawShort = mb_substr($rawShort, 0, 350);
        return [
            'ok' => false,
            'result' => -2,
            'message' => 'پاسخ فایل اتصال زیبال معتبر نیست. یعنی آدرس تنظیم‌شده به JSON فایل zibal_bridge.php نمی‌رسد و احتمالاً صفحه پیش‌فرض هاست، خطای SSL، 404 یا خروجی HTML برمی‌گرداند.',
            'raw' => $rawShort,
            'http_code' => $http,
            'bridge_url' => $url,
            'ssl_bypass' => $sslBypass
        ];
    }
    $json['http_code'] = $json['http_code'] ?? $http;
    if ($sslBypass) $json['ssl_bypass'] = true;
    return $json;
}

function zibalApi(string $endpoint, array $payload): array
{
    $ch = curl_init('https://gateway.zibal.ir/v1/' . ltrim($endpoint, '/'));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($raw === false || $err) return ['ok' => false, 'result' => -1, 'message' => $err ?: 'connection failed'];
    $json = json_decode((string)$raw, true);
    return is_array($json) ? $json : ['ok' => false, 'result' => -2, 'message' => 'invalid response', 'raw' => $raw];
}

function createZibalPayment(int $orderId, int $amount, string $description): array
{
    $merchant = trim((string)setting('zibal_merchant', ''));
    if ($merchant === '') return ['ok' => false, 'message' => 'درگاه زیبال فعال نیست.'];

    $callbackUrl = zibalCallbackUrl();
    $payload = [
        'merchant' => $merchant,
        'amount' => $amount,
        'callbackUrl' => $callbackUrl,
        'description' => $description,
        'orderId' => (string)$orderId,
    ];

    $attempts = [];
    $bridgeEnabled = (string)setting('zibal_bridge_enabled', '1') === '1';
    if ($bridgeEnabled) {
        $bridge = zibalBridgeApi('request', $payload);
        $attempts[] = ['type' => 'bridge', 'response' => $bridge];
        if ((int)($bridge['result'] ?? 0) === 100 && !empty($bridge['trackId'])) {
            $trackId = (string)$bridge['trackId'];
            updateCoinOrder($orderId, ['zibal_track_id' => $trackId, 'status' => 'created']);
            return ['ok' => true, 'url' => (string)($bridge['url'] ?? ('https://gateway.zibal.ir/start/' . rawurlencode($trackId))), 'track_id' => $trackId, 'via' => 'bridge'];
        }
        logError('zibal bridge request failed', ['order_id' => $orderId, 'response' => $bridge]);
    }

    // fallback هوشمند: درخواست را مستقیم به API زیبال می‌فرستیم، اما اگر دامنه دوم فعال است
    // callback را روی همان zibal_bridge.php می‌گذاریم. در این حالت برای ساخت لینک، هاست اصلی
    // لازم نیست فایل bridge را باز کند و فقط کاربر بعد از پرداخت از دامنه ثبت‌شده برمی‌گردد.
    $directPayload = $payload;
    if ($bridgeEnabled) {
        $directPayload['callbackUrl'] = zibalBridgeCallbackUrl();
    }
    $direct = zibalApi('request', $directPayload);
    $attempts[] = ['type' => $bridgeEnabled ? 'direct_with_bridge_callback' : 'direct', 'response' => $direct, 'callbackUrl' => $directPayload['callbackUrl'] ?? ''];
    if ((int)($direct['result'] ?? 0) === 100 && !empty($direct['trackId'])) {
        $trackId = (string)$direct['trackId'];
        updateCoinOrder($orderId, ['zibal_track_id' => $trackId, 'status' => 'created']);
        return ['ok' => true, 'url' => (string)($direct['url'] ?? ('https://gateway.zibal.ir/start/' . rawurlencode($trackId))), 'track_id' => $trackId, 'via' => $bridgeEnabled ? 'direct_with_bridge_callback' : 'direct'];
    }

    logError('zibal request failed', ['order_id' => $orderId, 'attempts' => $attempts]);

    $last = $attempts ? end($attempts)['response'] : [];
    $msg = trim((string)($last['message'] ?? ''));
    $result = (int)($last['result'] ?? 0);
    if ($msg === '') $msg = 'ساخت لینک پرداخت ناموفق بود.';
    if ($result !== 0) $msg .= "\nکد خطا: {$result}";
    if ($bridgeEnabled) {
        $bridgeUrl = trim((string)setting('zibal_bridge_url', ''));
        $msg .= "\nاتصال زیبال از مسیر bridge و مسیر مستقیم تست شد. اگر دامنه زیبال جداست، آدرس اتصال و DNS همین دامنه را بررسی کن.";
        if ($bridgeUrl !== '') $msg .= "\nآدرس اتصال فعلی: {$bridgeUrl}";
    }
    return ['ok' => false, 'message' => $msg];
}

function verifyZibalPayment(string $trackId): array
{
    $merchant = trim((string)setting('zibal_merchant', ''));
    if ($merchant === '') return ['ok' => false, 'message' => 'merchant missing'];
    $payload = ['merchant' => $merchant, 'trackId' => $trackId];
    $res = zibalBridgeApi('verify', $payload);
    if (($res['bridge_disabled'] ?? false) || (int)($res['result'] ?? 0) < 0) {
        $res = zibalApi('verify', $payload);
    }
    $result = (int)($res['result'] ?? 0);
    if (in_array($result, [100, 201], true)) return ['ok' => true, 'ref_id' => (string)($res['refNumber'] ?? $res['ref_id'] ?? '') , 'raw' => $res];
    return ['ok' => false, 'raw' => $res];
}
