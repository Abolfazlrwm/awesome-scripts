<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — ربات لایک و چالش کانال
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

require_once __DIR__ . '/functions.php';

if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

try {
    ensureSchema();
    initDefaultSettings();
    $update = json_decode(file_get_contents('php://input'), true);
    if (!is_array($update)) exit('OK');

    if (isset($update['callback_query'])) { handleCallback($update['callback_query']); exit('OK'); }
    if (isset($update['channel_post'])) { handleChannelPost($update['channel_post'], false); exit('OK'); }
    if (isset($update['edited_channel_post'])) { handleChannelPost($update['edited_channel_post'], true); exit('OK'); }
    if (isset($update['message'])) { handleMessage($update['message']); exit('OK'); }
} catch (Throwable $e) {
    logError('Webhook fatal', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}

echo 'OK';

function cleanButtonText(string $text): string
{
    $text = str_replace(["\xE2\x80\x8C", "\xC2\xA0"], ['', ' '], $text);
    $text = preg_replace('/\s+/u', ' ', trim($text));
    return $text ?: '';
}

function isButtonText(string $text, array $choices): bool
{
    $t = cleanButtonText($text);
    foreach ($choices as $choice) {
        if ($t === cleanButtonText($choice)) return true;
    }
    return false;
}

function isChannelSettingText(string $text): bool
{
    return isButtonText($text, [
        'تنظیم کانال ارسال پست',
        'تنظیم کانال ارسال پست ها',
        'تنظیم کانال ارسال پستها',
        'تنظیم کانال ارسال پست‌ها',
        '📢 تنظیم کانال ارسال پست',
        '📢 تنظیم کانال ارسال پست‌ها',
    ]);
}

function isLikeEmojiSettingText(string $text): bool
{
    return isButtonText($text, ['تنظیم اموجی لایک', '❤️ تنظیم اموجی لایک']);
}

function isForceJoinSettingText(string $text): bool
{
    // عضویت اجباری شخصی دیگر دکمه تنظیم ندارد؛ پس از تنظیم کانال به صورت خودکار فعال است.
    return false;
}

function isUserChannelsListText(string $text): bool
{
    return isButtonText($text, ['📋 کانال‌های من', '📋 کانال های من', 'کانال‌های من', 'کانال های من']);
}

function isSaveDefaultsText(string $text): bool
{
    return isButtonText($text, ['ذخیره تنظیمات پیشفرض', '💾 ذخیره تنظیمات پیشفرض']);
}

function isAdminNewUserCoinsText(string $text): bool
{
    return isButtonText($text, ['🎁 تنظیم سکه اولیه کاربران', 'تنظیم سکه اولیه کاربران']);
}

function isCreateHubText(string $text): bool
{
    return isButtonText($text, ['❤️ ساخت لایک و چالش ❤️', 'ساخت لایک و چالش']);
}

function isCoinMenuText(string $text): bool
{
    return isButtonText($text, ['💰 افزایش سکه', 'افزایش سکه']);
}

function isCoinReferralText(string $text): bool
{
    return isButtonText($text, ['🎁 جمع‌آوری سکه با زیرمجموعه', 'جمع آوری سکه با زیر مجموعه', 'جمع‌آوری سکه با زیرمجموعه']);
}

function isBuyCoinsText(string $text): bool
{
    return isButtonText($text, ['💳 خرید سکه', 'خرید سکه']);
}

function isDailyPrizeText(string $text): bool
{
    return isButtonText($text, ['🎯 جایزه روزانه', 'جایزه روزانه']);
}

function isAdminPaymentText(string $text): bool
{
    return isButtonText($text, ['💳 تنظیم خرید سکه', 'تنظیم خرید سکه']);
}

function isAdminAdjustCoinsText(string $text): bool
{
    return isButtonText($text, ['➕ افزایش/کاهش سکه', 'افزایش/کاهش سکه', 'افزایش و کاهش سکه']);
}

function isAdminDailyRewardsText(string $text): bool
{
    return isButtonText($text, ['🎯 تنظیم جایزه روزانه', 'تنظیم جایزه روزانه']);
}

function isAdminButtonStylesText(string $text): bool
{
    return isButtonText($text, ['🎨 تنظیم رنگ دکمه‌ها', '🎨 تنظیم رنگ دکمه ها', 'تنظیم رنگ دکمه‌ها', 'تنظیم رنگ دکمه ها']);
}

function isAdminButtonDisableText(string $text): bool
{
    return isButtonText($text, ['🚫 خاموش/روشن دکمه‌ها', '🚫 خاموش/روشن دکمه ها', 'خاموش/روشن دکمه‌ها', 'خاموش/روشن دکمه ها', 'مدیریت دکمه‌ها', 'مدیریت دکمه ها']);
}

function isAdminZibalBridgeText(string $text): bool
{
    return isButtonText($text, ['🌐 تنظیم اتصال زیبال', 'تنظیم اتصال زیبال']);
}

function isPaymentAdminSubText(string $text): bool
{
    return isButtonText($text, [
        '📦 تنظیم بسته‌های سکه',
        '📦 تنظیم پلن‌های خرید سکه',
        '💳 تنظیم کارت به کارت',
        '🔄 وضعیت کارت به کارت',
        '🟢 کارت به کارت روشن',
        '🔴 کارت به کارت خاموش',
        '🔄 روشن/خاموش کارت به کارت',
        '🔵 تنظیم مرچنت زیبال',
        '🌐 تنظیم اتصال زیبال',
        '🔄 وضعیت زیبال',
        '🟢 زیبال روشن',
        '🔴 زیبال خاموش',
        '🔄 روشن/خاموش زیبال',
        '🧭 تنظیم IP دامنه زیبال',
        '🟢 اتصال دامنه دوم روشن',
        '⚪️ اتصال مستقیم زیبال',
        '🔄 حالت اتصال زیبال',
        '🧪 تست اتصال زیبال',
    ]);
}

function isHardMenuCommand(string $text): bool
{
    return isChannelSettingText($text)
        || isLikeEmojiSettingText($text)
        || isForceJoinSettingText($text)
        || isUserChannelsListText($text)
        || isSaveDefaultsText($text)
        || isCreateHubText($text)
        || isCoinMenuText($text)
        || isCoinReferralText($text)
        || isBuyCoinsText($text)
        || isDailyPrizeText($text)
        || isAdminPaymentText($text)
        || isAdminAdjustCoinsText($text)
        || isAdminDailyRewardsText($text)
        || isAdminButtonStylesText($text)
        || isAdminButtonDisableText($text)
        || isPaymentAdminSubText($text)
        || isButtonText($text, ['❤️ ساخت لایک ❤️', '♦️ ساخت نظرسنجی ♦️', '💙 ساخت چالش لایک 💙', '🏃 ساخت چالش سرعتی 🏃', '🤍 ساخت خودکار لایک 🤍'])
        || isButtonText($text, ['⚙️ تنظیمات', '🛠 پنل مدیریت', '↩️ بازگشت', '/admin'])
        || isAdminNewUserCoinsText($text);
}

function routeButtonCommandIfNeeded(int $chatId, array $user, string $text): bool
{
    $uid = (int)$user['id'];
    if (function_exists('isButtonDisabled') && isButtonDisabled(cleanButtonText($text)) && !isAdminButtonDisableText($text) && !isAdminButtonStylesText($text) && !isButtonText($text, ['🛠 پنل مدیریت', '/admin'])) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'این گزینه فعلاً غیرفعال است.', 'reply_markup' => mainKeyboard(isAdmin($uid))]);
        return true;
    }
    if (isCreateHubText($text)) { setStep($uid, null); menuCreateHub($chatId, $user); return true; }
    if (isButtonText($text, ['❤️ ساخت لایک ❤️'])) { setStep($uid, null); menuCreateLike($chatId, $user); return true; }
    if (isButtonText($text, ['♦️ ساخت نظرسنجی ♦️'])) { setStep($uid, null); menuCreatePoll($chatId, $user); return true; }
    if (isButtonText($text, ['💙 ساخت چالش لایک 💙'])) { setStep($uid, null); menuCreateChallengeLike($chatId, $user); return true; }
    if (isButtonText($text, ['🏃 ساخت چالش سرعتی 🏃'])) { setStep($uid, null); menuCreateSpeed($chatId, $user); return true; }
    if (isButtonText($text, ['🤍 ساخت خودکار لایک 🤍'])) { setStep($uid, null); menuCreateAutoLike($chatId, $user); return true; }
    if (isCoinMenuText($text)) { setStep($uid, null); menuCoins($chatId, $user); return true; }
    if (isCoinReferralText($text)) { setStep($uid, null); menuReferralCoins($chatId, $user); return true; }
    if (isBuyCoinsText($text)) { setStep($uid, null); menuBuyCoins($chatId, $user); return true; }
    if (isDailyPrizeText($text)) { setStep($uid, null); menuDailyPrize($chatId, $user); return true; }
    if (isChannelSettingText($text)) { setStep($uid, null); askUserChannel($chatId, $user); return true; }
    if (isLikeEmojiSettingText($text)) { setStep($uid, null); askLikeEmoji($chatId, $user); return true; }
    if (isUserChannelsListText($text)) { setStep($uid, null); showUserChannels($chatId, getUser($uid) ?: $user); return true; }
    if (isSaveDefaultsText($text)) { setStep($uid, null); tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ تنظیمات فعلی شما ذخیره شد.', 'reply_markup' => settingsKeyboard()]); return true; }
    if (isAdminNewUserCoinsText($text) && isAdmin($uid)) { setStep($uid, null); adminAskNewUserCoins($chatId, $user); return true; }
    if (isAdminPaymentText($text) && isAdmin($uid)) { setStep($uid, null); adminPaymentMenu($chatId, $user); return true; }
    if (isAdminAdjustCoinsText($text) && isAdmin($uid)) { setStep($uid, null); adminAskAdjustCoins($chatId, $user); return true; }
    if (isAdminDailyRewardsText($text) && isAdmin($uid)) { setStep($uid, null); adminAskDailyRewards($chatId, $user); return true; }
    if (isAdminButtonStylesText($text) && isAdmin($uid)) { setStep($uid, null); adminAskButtonStyles($chatId, $user); return true; }
    if (isAdminButtonDisableText($text) && isAdmin($uid)) { setStep($uid, null); adminAskButtonVisibility($chatId, $user); return true; }
    if (isPaymentAdminSubText($text) && isAdmin($uid)) { setStep($uid, null); adminPaymentSubRoute($chatId, $user, $text); return true; }
    return false;
}

function handleMessage(array $message): void
{
    $chatId = (int)($message['chat']['id'] ?? 0);
    $from = $message['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    if ($chatId === 0 || $userId === 0) return;

    $text = trim((string)($message['text'] ?? ''));
    $payload = '';
    $refBy = null;
    if (str_starts_with($text, '/start')) {
        $parts = explode(' ', $text, 2);
        $payload = trim($parts[1] ?? '');
        if (str_starts_with($payload, 'ref_')) $refBy = (int)substr($payload, 4);
    }

    $user = ensureUser($from, $refBy);
    // جوین اجباری عمومی ربات برای مشاهده منو و شرکت در چالش‌ها الزامی نیست.
    // فقط هنگام ساخت سفارش بررسی می‌شود.

    if ($text === 'لغو' || $text === '↩️ بازگشت') {
        setStep($userId, null);
        $fresh = getUser($userId) ?: $user;
        sendHome($chatId, $fresh);
        return;
    }

    if (str_starts_with($text, '/start')) {
        if (str_starts_with($payload, 'item_')) { openItemPayload($chatId, $userId, (int)substr($payload, 5)); return; }
        if (str_starts_with($payload, 'speed_')) { openSpeedPayload($chatId, $from, substr($payload, 6)); return; }
        sendHome($chatId, $user);
        return;
    }

    if ($text === '/admin' || $text === '🛠 پنل مدیریت') {
        if (!isAdmin($userId)) { tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ دسترسی ندارید.']); return; }
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "🛠 پنل مدیریت\nیکی از گزینه‌های زیر را انتخاب کنید:", 'reply_markup' => adminKeyboard()]);
        return;
    }

    $fresh = getUser($userId) ?: $user;
    $step = $fresh['step'] ?? null;
    if ($step && isHardMenuCommand($text)) {
        if (routeButtonCommandIfNeeded($chatId, $fresh, $text)) return;
    }
    if ($step) { handleStep($message, $fresh, $step); return; }

    if (routeButtonCommandIfNeeded($chatId, $fresh, $text)) return;

    match ($text) {
        '❤️ ساخت لایک و چالش ❤️' => menuCreateHub($chatId, $fresh),
        '❤️ ساخت لایک ❤️' => menuCreateLike($chatId, $fresh),
        '♦️ ساخت نظرسنجی ♦️' => menuCreatePoll($chatId, $fresh),
        '💙 ساخت چالش لایک 💙' => menuCreateChallengeLike($chatId, $fresh),
        '🏃 ساخت چالش سرعتی 🏃' => menuCreateSpeed($chatId, $fresh),
        '🤍 ساخت خودکار لایک 🤍' => menuCreateAutoLike($chatId, $fresh),
        '👤 پروفایل' => menuProfile($chatId, $fresh),
        '💰 افزایش سکه' => menuCoins($chatId, $fresh),
        '🎁 جمع‌آوری سکه با زیرمجموعه' => menuReferralCoins($chatId, $fresh),
        '💳 خرید سکه' => menuBuyCoins($chatId, $fresh),
        '🎯 جایزه روزانه' => menuDailyPrize($chatId, $fresh),
        '📖 راهنما' => tg('sendMessage', ['chat_id' => $chatId, 'text' => helpText(), 'parse_mode' => 'HTML']),
        '⚙️ تنظیمات' => tg('sendMessage', ['chat_id' => $chatId, 'text' => "⚙️ تنظیمات
از گزینه‌های زیر استفاده کنید:", 'reply_markup' => settingsKeyboard()]),
        '☕ اسپانسر ما' => tg('sendMessage', ['chat_id' => $chatId, 'text' => (string)setting('sponsor_text', 'فعلاً اسپانسری تنظیم نشده است.')]),
        '🌐 Language / زبان' => tg('sendMessage', ['chat_id' => $chatId, 'text' => "🌐 در این نسخه زبان پیش‌فرض فارسی است."]),
        'تنظیم کانال ارسال پست' => askUserChannel($chatId, $fresh),
        'تنظیم اموجی لایک' => askLikeEmoji($chatId, $fresh),
        '📋 کانال‌های من' => showUserChannels($chatId, $fresh),
        'ذخیره تنظیمات پیشفرض' => tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ تنظیمات فعلی شما ذخیره شد.', 'reply_markup' => settingsKeyboard()]),
        '📊 آمار' => adminStats($chatId, $fresh),
        '💰 تنظیم قیمت‌ها' => adminAskPrices($chatId, $fresh),
        '🎁 تنظیم سکه اولیه کاربران' => adminAskNewUserCoins($chatId, $fresh),
        '🎁 تنظیم سکه زیرمجموعه' => adminAskReferral($chatId, $fresh),
        '🎯 تنظیم جایزه روزانه' => adminAskDailyRewards($chatId, $fresh),
        '💳 تنظیم خرید سکه' => adminPaymentMenu($chatId, $fresh),
        '➕ افزایش/کاهش سکه' => adminAskAdjustCoins($chatId, $fresh),
        '🎨 تنظیم رنگ دکمه‌ها' => adminAskButtonStyles($chatId, $fresh),
        '🚫 خاموش/روشن دکمه‌ها' => adminAskButtonVisibility($chatId, $fresh),
        '📦 تنظیم بسته‌های سکه' => adminAskCoinPackages($chatId, $fresh),
        '📦 تنظیم پلن‌های خرید سکه' => adminAskCoinPackages($chatId, $fresh),
        '💳 تنظیم کارت به کارت' => adminAskCardInfo($chatId, $fresh),
        '🔄 وضعیت کارت به کارت' => adminToggleCard($chatId, $fresh),
        '🔵 تنظیم مرچنت زیبال' => adminAskZibalMerchant($chatId, $fresh),
        '🌐 تنظیم اتصال زیبال' => adminAskZibalBridge($chatId, $fresh),
        '🧭 تنظیم IP دامنه زیبال' => adminAskZibalBridgeIp($chatId, $fresh),
        '🟢 اتصال دامنه دوم روشن' => adminToggleZibalBridge($chatId, $fresh),
        '⚪️ اتصال مستقیم زیبال' => adminToggleZibalBridge($chatId, $fresh),
        '🔄 حالت اتصال زیبال' => adminToggleZibalBridge($chatId, $fresh),
        '🔄 وضعیت زیبال' => adminToggleZibal($chatId, $fresh),
        '🟢 زیبال روشن' => adminToggleZibal($chatId, $fresh),
        '🔴 زیبال خاموش' => adminToggleZibal($chatId, $fresh),
        '🔄 روشن/خاموش زیبال' => adminToggleZibal($chatId, $fresh),
        '🧪 تست اتصال زیبال' => adminTestZibal($chatId, $fresh),
        '➕ افزودن کانال جوین اجباری' => adminAskAddChannel($chatId, $fresh),
        '➖ حذف کانال جوین اجباری' => adminAskRemoveChannel($chatId, $fresh),
        '📋 لیست کانال‌ها' => adminListChannels($chatId, $fresh),
        '☕ تنظیم اسپانسر' => adminAskSponsor($chatId, $fresh),
        '📣 پیام همگانی' => adminAskBroadcast($chatId, $fresh),
        '➕ افزودن ادمین' => adminAskAddAdmin($chatId, $fresh),
        default => tg('sendMessage', ['chat_id' => $chatId, 'text' => 'گزینه نامعتبر است. از منوی زیر استفاده کنید.', 'reply_markup' => mainKeyboard(isAdmin($userId))]),
    };
}

function handleStep(array $message, array $user, string $step): void
{
    $chatId = (int)$message['chat']['id'];
    $userId = (int)$user['id'];
    $text = trim((string)($message['text'] ?? ''));
    $temp = getTemp($user);

    if ($step === 'await_user_channel') {
        handleSetUserChannel($message, $user);
        return;
    }

    if (in_array($step, ['await_like_channel_post', 'await_challenge_like_channel_post'], true)) {
        $content = extractMessageContent($message);
        if (!$content) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'لطفاً پست معتبر ارسال یا فوروارد کنید؛ متن، عکس، ویدئو، گیف یا فایل.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        $next = $step === 'await_challenge_like_channel_post' ? 'await_challenge_like_fake' : 'await_like_fake';
        setStep($userId, $next, ['content' => $content]);
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => countPromptText('تعیین تعداد لایک اولیه', 'زیر پست با چند لایک اولیه شروع شود؟'),
            'parse_mode' => 'HTML',
            'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000]),
        ]);
        return;
    }

    if (in_array($step, ['await_like_fake', 'await_challenge_like_fake'], true)) {
        if ($text === 'مقدار دلخواه') {
            setStep($userId, $step . '_custom', $temp);
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد دلخواه را فقط با رقم ارسال کن؛ مثال: 350', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        if (!preg_match('/^\d{1,7}$/', $text)) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین تعداد لایک اولیه', 'عدد انتخاب‌شده معتبر نیست؛ لطفاً دوباره یکی از گزینه‌ها را انتخاب کنید.'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000])]);
            return;
        }
        askConfirmPublishLike($chatId, $user, $temp, (int)$text, $step === 'await_challenge_like_fake' ? 'challenge_like' : 'like');
        return;
    }

    if (in_array($step, ['await_like_fake_custom', 'await_challenge_like_fake_custom'], true)) {
        if (!preg_match('/^\d{1,7}$/', $text)) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد معتبر ارسال کن؛ فقط رقم مجاز است.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        $base = str_replace('_custom', '', $step);
        askConfirmPublishLike($chatId, $user, $temp, (int)$text, $base === 'await_challenge_like_fake' ? 'challenge_like' : 'like');
        return;
    }

    if ($step === 'await_poll_question') {
        if ($text === '') { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'لطفاً سوال نظرسنجی را به صورت متن ارسال کنید.', 'reply_markup' => cancelKeyboard()]); return; }
        setStep($userId, 'await_poll_options', ['question' => $text]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "حالا گزینه‌های نظرسنجی را هرکدام در یک خط ارسال کنید.\n\nمثال:\nعالی\nخوب\nمتوسط", 'reply_markup' => cancelKeyboard()]);
        return;
    }

    if ($step === 'await_poll_options') {
        $options = array_values(array_filter(array_map('trim', preg_split('/\R/u', $text))));
        if (count($options) < 2) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'حداقل ۲ گزینه لازم است. هر گزینه را در یک خط بنویسید.', 'reply_markup' => cancelKeyboard()]); return; }
        if (count($options) > 10) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'حداکثر ۱۰ گزینه مجاز است.', 'reply_markup' => cancelKeyboard()]); return; }
        $question = (string)($temp['question'] ?? 'نظرسنجی');
        askConfirmPoll($chatId, $user, $question, $options);
        return;
    }

    if ($step === 'await_auto_count') {
        if ($text === 'مقدار دلخواه') {
            setStep($userId, 'await_auto_count_custom', $temp);
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'چند پست آینده لایک بخورند؟ فقط عدد ارسال کن؛ مثال: 5', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        if (!preg_match('/^\d{1,4}$/', $text) || (int)$text < 1) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین تعداد پست', 'تعداد پست معتبر نیست؛ لطفاً دوباره انتخاب کنید.'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([1, 2, 3, 5, 10, 20])]);
            return;
        }
        setStep($userId, 'await_auto_fake', ['auto_posts' => (int)$text]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین لایک هر پست', 'هر پست با چند لایک اولیه شروع شود؟'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000])]);
        return;
    }

    if ($step === 'await_auto_count_custom') {
        if (!preg_match('/^\d{1,4}$/', $text) || (int)$text < 1 || (int)$text > 1000) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد بین ۱ تا ۱۰۰۰ ارسال کن.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        setStep($userId, 'await_auto_fake', ['auto_posts' => (int)$text]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین لایک هر پست', 'هر پست با چند لایک اولیه شروع شود؟'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000])]);
        return;
    }

    if ($step === 'await_auto_fake') {
        if ($text === 'مقدار دلخواه') {
            setStep($userId, 'await_auto_fake_custom', $temp);
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد دلخواه لایک اولیه را ارسال کن.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        if (!preg_match('/^\d{1,7}$/', $text)) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین لایک هر پست', 'عدد انتخاب‌شده معتبر نیست؛ لطفاً دوباره انتخاب کنید.'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000])]);
            return;
        }
        askConfirmAutoLike($chatId, $user, $temp, (int)$text);
        return;
    }

    if ($step === 'await_auto_fake_custom') {
        if (!preg_match('/^\d{1,7}$/', $text)) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد معتبر ارسال کن.', 'reply_markup' => cancelKeyboard()]); return; }
        askConfirmAutoLike($chatId, $user, $temp, (int)$text);
        return;
    }

    if ($step === 'await_like_emoji') {
        if (uLen($text) < 1 || uLen($text) > 4) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'اموجی یا متن دکمه باید بین ۱ تا ۴ کاراکتر باشد.', 'reply_markup' => cancelKeyboard()]); return; }
        db()->prepare('UPDATE users SET like_emoji = ? WHERE id = ?')->execute([$text, $userId]);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ اموجی لایک شما تنظیم شد: {$text}", 'reply_markup' => settingsKeyboard()]);
        return;
    }

    if ($step === 'await_user_force_join') {
        setStep($userId, null);
        $fresh = getUser($userId) ?: $user;
        if (!empty($fresh['default_channel']) || !empty($fresh['default_channel_id'])) {
            $channels = defaultForceChannelsFromUser($fresh);
            if ($channels) {
                db()->prepare('UPDATE users SET force_channels = ? WHERE id = ?')->execute([json_encode($channels, JSON_UNESCAPED_UNICODE), $userId]);
            }
            tg('sendMessage', [
                'chat_id' => $chatId,
                'text' => '✅ عضویت اجباری کانال شما به صورت خودکار فعال است. کاربران برای شرکت در لایک‌ها، نظرسنجی‌ها و چالش‌های شما باید عضو کانال تنظیم‌شده باشند.',
                'reply_markup' => settingsKeyboard(),
            ]);
        } else {
            tg('sendMessage', [
                'chat_id' => $chatId,
                'text' => '📢 ابتدا کانال خود را تنظیم کنید. پس از تنظیم کانال، عضویت اجباری به صورت خودکار فعال می‌شود.',
                'reply_markup' => settingsKeyboard(),
            ]);
        }
        return;
    }

    if ($step === 'await_card_receipt') {
        handleCardReceipt($message, $user, $temp);
        return;
    }

    if (in_array($step, ['confirm_publish_like', 'confirm_auto_like', 'confirm_poll', 'confirm_speed'], true)) {
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => 'برای ادامه، از دکمه‌های تایید سفارش یا لغو سفارش استفاده کنید.',
            'reply_markup' => orderConfirmKeyboard(),
        ]);
        return;
    }

    if (!isAdmin($userId)) {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ دسترسی ندارید.', 'reply_markup' => mainKeyboard(false)]);
        return;
    }
    handleAdminStep($message, $user, $step);
}

function handleSetUserChannel(array $message, array $user): void
{
    $chatId = (int)$message['chat']['id'];
    $userId = (int)$user['id'];
    $text = trim((string)($message['text'] ?? ''));

    $forward = channelFromForward($message);
    if ($forward && $forward['id'] !== '') {
        $info = $forward;
        $channelInput = $forward['username'] ?: $forward['id'];
    } else {
        if ($text === '') {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => 'آیدی کانال مثل @channel را بفرست یا یک پست از کانال فوروارد کن.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
        $channelInput = normalizeOneChannel($text);
        $info = getTelegramChannelInfo($channelInput);
        if (!$info) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ کانال پیدا نشد. آیدی کانال را درست بفرست و مطمئن شو ربات داخل کانال ادمین است.', 'reply_markup' => cancelKeyboard()]);
            return;
        }
    }

    $admin = checkBotAdminInChannel((string)$info['id']);
    if (!($admin['ok'] ?? false)) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "❌ {$admin['msg']}\n\nربات را داخل کانال ادمین کن و دسترسی ارسال/ویرایش پیام بده، بعد دوباره آیدی کانال را بفرست.", 'reply_markup' => cancelKeyboard()]);
        return;
    }

    // قفل عضویت را همیشه با شناسه عددی کانال ذخیره می‌کنیم تا کاربران بعد از عضویت گیر نکنند.
    $forceChannels = [];
    if (!empty($info['id'])) $forceChannels = normalizeChannels((string)$info['id']);
    if (!$forceChannels) $forceChannels = normalizeChannels($channelInput);
    if (!$forceChannels) $forceChannels = [(string)$channelInput];
    $forceChannels = array_values(array_unique(array_filter(array_map('strval', $forceChannels))));

    db()->prepare('UPDATE users SET default_channel = ?, default_channel_id = ?, default_channel_title = ?, force_channels = ? WHERE id = ?')->execute([
        $channelInput,
        (string)$info['id'],
        (string)$info['title'],
        json_encode($forceChannels, JSON_UNESCAPED_UNICODE),
        $userId
    ]);
    setStep($userId, null);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "✅ کانال تنظیم شد:\n📢 {$info['title']}\n🆔 {$channelInput}\n\n🔐 عضویت اجباری برای شرکت در لایک‌ها، نظرسنجی‌ها و چالش‌های شما به صورت خودکار روشن شد. کاربرانی که عضو این کانال نباشند، نمی‌توانند شرکت کنند.\n\nحالا می‌توانی ساخت لایک، نظرسنجی، چالش یا خودکار لایک را استفاده کنی.",
        'reply_markup' => settingsKeyboard(),
    ]);
}

function menuCreateHub(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "❤️ ساخت لایک و چالش
یکی از گزینه‌های زیر را انتخاب کنید:",
        'reply_markup' => createHubKeyboard(),
    ]);
}

function menuCreateLike(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    if (!requireCoinsOrSend($chatId, $user, 'like')) return;
    setStep((int)$user['id'], 'await_like_channel_post');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "❤️ ساخت لایک\n\nپستی که می‌خواهی زیرش لایک بیاید را برای ربات ارسال یا از کانال فوروارد کن.\n\nاگر پست از همان کانال فوروارد شود، ربات تلاش می‌کند همان پست را ویرایش کند؛ در غیر این صورت پست را با دکمه لایک داخل کانال ارسال می‌کند.\n\n📢 کانال: {$user['default_channel_title']}\n💳 هزینه: " . price('like') . ' سکه',
        'reply_markup' => cancelKeyboard(),
    ]);
}

function menuCreateChallengeLike(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    if (!requireCoinsOrSend($chatId, $user, 'challenge_like')) return;
    setStep((int)$user['id'], 'await_challenge_like_channel_post');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "💙 ساخت چالش لایک\n\nپست چالش را ارسال یا فوروارد کن تا زیر آن دکمه شرکت در چالش قرار بگیرد.\n\n💳 هزینه: " . price('challenge_like') . ' سکه',
        'reply_markup' => cancelKeyboard(),
    ]);
}


function clearInlinePanel(?array $message): void
{
    if (!$message) return;
    $chatId = (int)($message['chat']['id'] ?? 0);
    $messageId = (int)($message['message_id'] ?? 0);
    if ($chatId === 0 || $messageId === 0) return;
    tg('editMessageReplyMarkup', [
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'reply_markup' => json_encode(['inline_keyboard' => []], JSON_UNESCAPED_UNICODE),
    ], false);
}

function orderConfirmKeyboard(): string
{
    return inline([
        [['text' => '✅ تایید سفارش', 'callback_data' => 'order_confirm']],
        [['text' => '❌ لغو سفارش', 'callback_data' => 'order_cancel']],
    ]);
}

function askConfirmPublishLike(int $chatId, array $user, array $temp, int $fakeLikes, string $type): void
{
    $userId = (int)$user['id'];
    $temp['fake_likes'] = max(0, $fakeLikes);
    $temp['type'] = $type;
    setStep($userId, 'confirm_publish_like', $temp);
    $title = $type === 'challenge_like' ? '💙 چالش لایک' : '❤️ ساخت لایک';
    $cost = price($type === 'challenge_like' ? 'challenge_like' : 'like');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "📋 تایید سفارش\n\nنوع سفارش: {$title}\n📢 کانال: " . (string)($user['default_channel_title'] ?? 'تنظیم‌شده') . "\n❤️ لایک اولیه: {$fakeLikes}\n💳 هزینه: {$cost} سکه\n\nبرای انجام سفارش، تایید سفارش را بزنید.",
        'reply_markup' => orderConfirmKeyboard(),
    ]);
}

function askConfirmAutoLike(int $chatId, array $user, array $temp, int $fakeLikes): void
{
    $userId = (int)$user['id'];
    $posts = (int)($temp['auto_posts'] ?? 0);
    $temp['fake_likes'] = max(0, $fakeLikes);
    setStep($userId, 'confirm_auto_like', $temp);
    $totalCost = price('auto_like') * max(0, $posts);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "📋 تایید سفارش\n\nنوع سفارش: 🤍 خودکار لایک\n📢 کانال: " . (string)($user['default_channel_title'] ?? 'تنظیم‌شده') . "\n📌 تعداد پست: {$posts}\n❤️ لایک اولیه هر پست: {$fakeLikes}\n💳 هزینه کل: {$totalCost} سکه\n\nبرای فعال‌سازی سفارش، تایید سفارش را بزنید.",
        'reply_markup' => orderConfirmKeyboard(),
    ]);
}

function askConfirmPoll(int $chatId, array $user, string $question, array $options): void
{
    $userId = (int)$user['id'];
    setStep($userId, 'confirm_poll', ['question' => $question, 'options' => $options]);
    $list = '';
    foreach ($options as $i => $option) $list .= ($i + 1) . '. ' . $option . "\n";
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "📋 تایید سفارش\n\nنوع سفارش: ♦️ نظرسنجی\n📢 کانال: " . (string)($user['default_channel_title'] ?? 'تنظیم‌شده') . "\n❓ سوال: {$question}\n\nگزینه‌ها:\n{$list}\n💳 هزینه: " . price('poll') . " سکه\n\nبرای ساخت نظرسنجی، تایید سفارش را بزنید.",
        'reply_markup' => orderConfirmKeyboard(),
    ]);
}

function askConfirmSpeed(int $chatId, array $user): void
{
    $userId = (int)$user['id'];
    setStep($userId, 'confirm_speed', ['type' => 'speed']);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "📋 تایید سفارش\n\nنوع سفارش: 🏃 چالش سرعتی\n📢 کانال: " . (string)($user['default_channel_title'] ?? 'تنظیم‌شده') . "\n💳 هزینه: " . price('speed') . " سکه\n\nبعد از تایید، لینک اختصاصی چالش ساخته می‌شود.",
        'reply_markup' => orderConfirmKeyboard(),
    ]);
}

function finishPollOrder(int $chatId, array $user, array $temp): void
{
    $userId = (int)$user['id'];
    $question = trim((string)($temp['question'] ?? '')) ?: 'نظرسنجی';
    $options = $temp['options'] ?? [];
    if (!is_array($options) || count($options) < 2) {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ اطلاعات نظرسنجی کامل نیست. دوباره شروع کنید.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        return;
    }

    $targetChatId = trim((string)($user['default_channel_id'] ?? ''));
    if ($targetChatId === '') {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ ابتدا کانال خود را از تنظیمات ثبت کنید.', 'reply_markup' => settingsKeyboard()]);
        return;
    }

    $price = price('poll');
    if (!takeCoins($userId, $price)) {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ موجودی کافی نیست.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        return;
    }

    $channel = [
        'default_channel' => $user['default_channel'] ?? null,
        'default_channel_id' => $targetChatId,
    ];
    $itemId = createItem($userId, 'poll', ['media_type' => 'text', 'text' => $question], array_values($options), null, 0, $channel);
    $item = getItem($itemId);
    if (!$item) {
        addCoins($userId, $price);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ خطا در ساخت نظرسنجی. دوباره تلاش کنید.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        return;
    }

    $sent = sendItemToChat($targetChatId, $item);
    if (!($sent['ok'] ?? false)) {
        addCoins($userId, $price);
        setStep($userId, null);
        $desc = (string)($sent['description'] ?? 'ربات نتوانست نظرسنجی را داخل کانال ارسال کند.');
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "❌ ارسال نظرسنجی به کانال انجام نشد.

📢 کانال: " . (string)($user['default_channel_title'] ?? 'تنظیم‌شده') . "
پیام تلگرام: {$desc}

لطفاً مطمئن شوید ربات داخل کانال ادمین است و دسترسی ارسال پیام دارد.",
            'reply_markup' => mainKeyboard(isAdmin($userId)),
        ]);
        return;
    }

    $messageId = (int)($sent['result']['message_id'] ?? 0);
    if ($messageId > 0) updateItemChannelMessage($itemId, $targetChatId, $messageId);

    setStep($userId, null);
    $fresh = getUser($userId) ?: $user;
    $link = $messageId > 0 ? postLink($fresh['default_channel'] ?? null, $messageId) : '';
    $extra = $link ? "
🔗 لینک پست:
{$link}" : '';
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "✅ نظرسنجی با موفقیت به کانال ارسال شد.{$extra}

💰 موجودی جدید: " . (int)($fresh['coins'] ?? 0) . ' سکه',
        'reply_markup' => mainKeyboard(isAdmin($userId)),
    ]);
}

function finishSpeedOrder(int $chatId, array $user): void
{
    $userId = (int)$user['id'];
    if (!takeCoins($userId, price('speed'))) {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ موجودی کافی نیست.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        return;
    }
    $token = createSpeedChallenge($userId);
    $link = botLink('speed_' . $token);
    setStep($userId, null);
    $fresh = getUser($userId) ?: $user;
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🏃 چالش سرعتی ساخته شد.\n\nهر کاربری که اولین نفر لینک زیر را باز کند برنده می‌شود و مشخصاتش برای شما ارسال خواهد شد.\n\n🔗 لینک چالش:\n{$link}\n\n⚠️ اگر قبلاً چالش فعال داشتید، لینک قبلی باطل شد.\n\n💰 موجودی: {$fresh['coins']} سکه",
        'reply_markup' => mainKeyboard(isAdmin($userId)),
    ]);
}

function handleOrderConfirmCallback(array $cb): void
{
    $data = (string)($cb['data'] ?? '');
    $cbId = (string)($cb['id'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    if (!$message || $userId <= 0) return;
    $chatId = (int)$message['chat']['id'];
    $user = ensureUser($from);
    $fresh = getUser($userId) ?: $user;
    $step = (string)($fresh['step'] ?? '');
    $temp = getTemp($fresh);

    if ($data === 'order_cancel') {
        setStep($userId, null);
        clearInlinePanel($message);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'لغو شد.']);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ سفارش لغو شد.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        return;
    }

    if ($data !== 'order_confirm') return;
    tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'سفارش تایید شد.']);
    clearInlinePanel($message);

    if ($step === 'confirm_publish_like') {
        finishPublishLike($chatId, $fresh, $temp, (int)($temp['fake_likes'] ?? 0), (string)($temp['type'] ?? 'like'));
        return;
    }
    if ($step === 'confirm_auto_like') {
        finishAutoLikeJob($chatId, $fresh, $temp, (int)($temp['fake_likes'] ?? 0));
        return;
    }
    if ($step === 'confirm_poll') {
        finishPollOrder($chatId, $fresh, $temp);
        return;
    }
    if ($step === 'confirm_speed') {
        finishSpeedOrder($chatId, $fresh);
        return;
    }

    tg('sendMessage', ['chat_id' => $chatId, 'text' => 'این سفارش دیگر فعال نیست. دوباره از منو شروع کنید.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
}

function finishPublishLike(int $chatId, array $user, array $temp, int $fakeLikes, string $type): void
{
    $content = $temp['content'] ?? null;
    if (!is_array($content)) {
        setStep((int)$user['id'], null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ اطلاعات پست از بین رفت. دوباره شروع کن.', 'reply_markup' => mainKeyboard(isAdmin((int)$user['id']))]);
        return;
    }
    $res = publishContentToUserChannel($user, $content, $fakeLikes, $type);
    setStep((int)$user['id'], null);
    $fresh = getUser((int)$user['id']) ?: $user;
    if ($res['ok'] ?? false) {
        $modeText = ($res['mode'] ?? '') === 'edited' ? 'پست اصلی ویرایش شد' : 'پست داخل کانال ارسال شد';
        $link = postLink($fresh['default_channel'] ?? null, (int)($res['message_id'] ?? 0));
        $extra = $link ? "\n🔗 لینک پست:\n{$link}" : '';
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "✅ انجام شد.\n{$modeText}\n❤️ لایک اولیه: {$fakeLikes}{$extra}\n\n💰 موجودی جدید: {$fresh['coins']} سکه",
            'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
        ]);
        if (!empty($res['item_id'])) sendLikeControlPanel($chatId, (int)$res['item_id']);
    } else {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => (string)($res['msg'] ?? '❌ عملیات ناموفق بود.'), 'reply_markup' => mainKeyboard(isAdmin((int)$user['id']))]);
    }
}

function menuCreatePoll(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    if (!requireCoinsOrSend($chatId, $user, 'poll')) return;
    setStep((int)$user['id'], 'await_poll_question');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "♦️ ساخت نظرسنجی\n\nسوال نظرسنجی را ارسال کنید.\n\n💳 هزینه: " . price('poll') . ' سکه', 'reply_markup' => cancelKeyboard()]);
}

function menuCreateSpeed(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    if (!requireCoinsOrSend($chatId, $user, 'speed')) return;
    askConfirmSpeed($chatId, $user);
}

function menuCreateAutoLike(int $chatId, array $user): void
{
    if (!requireCreatorGlobalJoinOrSend($chatId, (int)$user['id'])) return;
    if (!requireUserChannelOrAsk($chatId, $user)) return;
    setStep((int)$user['id'], 'await_auto_count');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🤍 <b>ساخت خودکار لایک</b>\n\nربات زیر پست‌های جدید کانال دکمه لایک می‌گذارد.\n\n📢 کانال: {$user['default_channel_title']}\n💳 هزینه هر پست: " . price('auto_like') . " سکه\n\n" . countPromptText('تعیین تعداد پست', 'چند پست آینده لایک بخورند؟'),
        'parse_mode' => 'HTML',
        'reply_markup' => countKeyboard([1, 2, 3, 5, 10, 20]),
    ]);
}

function finishAutoLikeJob(int $chatId, array $user, array $temp, int $fakeLikes): void
{
    $posts = (int)($temp['auto_posts'] ?? 0);
    if ($posts < 1) {
        setStep((int)$user['id'], null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ تعداد پست معتبر نیست. دوباره شروع کن.', 'reply_markup' => mainKeyboard(isAdmin((int)$user['id']))]);
        return;
    }

    $admin = checkBotAdminInChannelForAuto((string)$user['default_channel_id']);
    if (!($admin['ok'] ?? false)) {
        setStep((int)$user['id'], null);
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "❌ خودکار لایک فعال نشد.\n\n{$admin['msg']}\n\nبرای خودکار لایک، ربات باید داخل کانال ادمین باشد و دسترسی Edit Messages / ویرایش پیام‌ها داشته باشد.",
            'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
        ]);
        return;
    }

    $totalCost = price('auto_like') * $posts;
    $freshBefore = getUser((int)$user['id']) ?: $user;
    if ((int)$freshBefore['coins'] < $totalCost && price('auto_like') > 0) {
        setStep((int)$user['id'], null);
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "❌ موجودی برای کل سفارش کافی نیست.\n\n💰 موجودی شما: {$freshBefore['coins']} سکه\n💳 هزینه کل {$posts} پست: {$totalCost} سکه\n\nمی‌توانی تعداد پست کمتری انتخاب کنی یا سکه افزایش بدهی.",
            'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
        ]);
        return;
    }

    // هر کانال فقط یک سفارش خودکار فعال داشته باشد؛ سفارش‌های قدیمی همان کانال باعث تغییر عدد لایک پست‌های بعدی نشوند.
    $stoppedOldJobs = function_exists('stopActiveAutoJobsForChannel') ? stopActiveAutoJobsForChannel((string)$user['default_channel_id']) : 0;
    $jobId = createAutoLikeJob((int)$user['id'], (string)$user['default_channel'], (string)$user['default_channel_id'], (string)$user['default_channel_title'], $posts, $fakeLikes);
    logAutoLike('job_created', [
        'job_id' => $jobId,
        'owner_id' => (int)$user['id'],
        'channel_chat_id' => (string)$user['default_channel_id'],
        'posts' => $posts,
        'fake_likes' => $fakeLikes,
        'stopped_old_jobs' => $stoppedOldJobs,
    ]);
    setStep((int)$user['id'], null);
    $oldNote = !empty($stoppedOldJobs) ? "\n\nℹ️ سفارش‌های خودکار قبلی همین کانال متوقف شد تا عدد لایک‌ها قاطی نشود." : '';
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "✅ خودکار لایک فعال شد.\n\n📢 کانال: {$user['default_channel_title']}\n📌 تعداد پست: {$posts}\n❤️ لایک اولیه هر پست: {$fakeLikes}\n💳 هزینه هر پست: " . price('auto_like') . " سکه\n\nاز این به بعد هر پست جدیدی که در همان کانال ارسال شود، ربات زیر همان پست دکمه لایک می‌گذارد.{$oldNote}\nشناسه سفارش: #{$jobId}",
        'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
    ]);
}

function menuProfile(int $chatId, array $user): void
{
    $ref = botLink('ref_' . $user['id']);
    $channel = !empty($user['default_channel_title']) ? $user['default_channel_title'] . ' / ' . $user['default_channel'] : 'تنظیم نشده';
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "👤 پروفایل\n\n🆔 آیدی: {$user['id']}\n💰 موجودی: {$user['coins']} سکه\n❤️ اموجی لایک: {$user['like_emoji']}\n📢 کانال ارسال پست: {$channel}\n\n🔗 لینک زیرمجموعه‌گیری:\n{$ref}",
        'reply_markup' => mainKeyboard(isAdmin((int)$user['id'])),
    ]);
}

function menuCoins(int $chatId, array $user): void
{
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "💰 افزایش سکه
یکی از روش‌های زیر را انتخاب کنید:",
        'reply_markup' => coinsKeyboard(),
    ]);
}

function menuDailyPrize(int $chatId, array $user): void
{
    if ((string)setting('daily_reward_enabled', '1') !== '1') {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '🎯 جایزه روزانه فعلاً غیرفعال است.', 'reply_markup' => coinsKeyboard()]);
        return;
    }
    $userId = (int)$user['id'];
    $today = date('Y-m-d');
    $fresh = getUser($userId) ?: $user;
    if ((string)($fresh['last_daily_reward_date'] ?? '') === $today) {
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "🎯 جایزه امروزتان را دریافت کرده‌اید.\nفردا دوباره می‌توانید تاس بیندازید.\n\nلیست جوایز:\n" . dailyRewardsText(),
            'reply_markup' => coinsKeyboard(),
        ]);
        return;
    }

    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🎯 لیست جوایز:\n" . dailyRewardsText() . "\n\nجایزه شما بر حسب عدد تاس داده می‌شود.",
        'reply_markup' => coinsKeyboard(),
    ]);
    $diceRes = tg('sendDice', ['chat_id' => $chatId, 'emoji' => '🎲']);
    $value = (int)($diceRes['result']['dice']['value'] ?? random_int(1, 6));
    $rewards = dailyRewards();
    $coins = (int)($rewards[(string)$value] ?? 0);
    db()->prepare('UPDATE users SET last_daily_reward_date = ? WHERE id = ?')->execute([$today, $userId]);
    if ($coins > 0) addCoins($userId, $coins);
    $fresh = getUser($userId) ?: $fresh;
    $msg = $coins > 0
        ? "🎯 جایزه امروز شما: {$coins} سکه ✅\n💰 موجودی فعلی: " . (int)$fresh['coins'] . ' سکه'
        : "🎯 عدد امروز شما پوچ بود.\nفردا دوباره شانس خود را امتحان کنید.";
    tg('sendMessage', ['chat_id' => $chatId, 'text' => $msg, 'reply_markup' => coinsKeyboard()]);
}

function menuReferralCoins(int $chatId, array $user): void
{
    $reward = (int)setting('referral_reward', 10);
    $ref = botLink('ref_' . $user['id']);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🎁 جمع‌آوری سکه با زیرمجموعه

با دعوت هر کاربر جدید {$reward} سکه دریافت می‌کنید.

🔗 لینک دعوت شما:
{$ref}",
        'reply_markup' => coinsKeyboard(),
    ]);
}

function menuBuyCoins(int $chatId, array $user): void
{
    $packages = coinPackages();
    if (!$packages) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'فعلاً بسته‌ای برای خرید سکه فعال نیست.', 'reply_markup' => coinsKeyboard()]);
        return;
    }
    if (!paymentMethodsEnabled()) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'فعلاً روش پرداختی فعال نیست.', 'reply_markup' => coinsKeyboard()]);
        return;
    }
    $buttons = [];
    foreach ($packages as $i => $p) {
        $buttons[] = [[
            'text' => $p['coins'] . ' سکه — ' . formatToman((int)$p['amount']),
            'callback_data' => 'buy_pkg:' . $i,
        ]];
    }
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "💳 خرید سکه
بسته موردنظر را انتخاب کنید:",
        'reply_markup' => inline($buttons),
    ]);
}


function showPaymentMethods(int $chatId, int $pkgIndex): void
{
    $packages = coinPackages();
    if (!isset($packages[$pkgIndex])) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'بسته انتخاب‌شده معتبر نیست.']); return; }
    $p = $packages[$pkgIndex];
    $buttons = [];
    if ((string)setting('payment_card_enabled', '1') === '1' && !isButtonDisabled('💳 کارت به کارت')) $buttons[] = [['text' => '💳 کارت به کارت', 'callback_data' => 'buy_method:card:' . $pkgIndex]];
    if ((string)setting('payment_zibal_enabled', '0') === '1' && trim((string)setting('zibal_merchant', '')) !== '' && !isButtonDisabled('🔵 پرداخت آنلاین زیبال')) $buttons[] = [['text' => '🔵 پرداخت آنلاین زیبال', 'callback_data' => 'buy_method:zibal:' . $pkgIndex]];
    if (!$buttons) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'فعلاً روش پرداختی فعال نیست.']); return; }
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "بسته انتخابی:\n💰 {$p['coins']} سکه\n💳 مبلغ: " . formatToman((int)$p['amount']) . "\n\nروش پرداخت را انتخاب کنید:",
        'reply_markup' => inline($buttons),
    ]);
}

function startCardPayment(int $chatId, array $user, int $pkgIndex): void
{
    $packages = coinPackages();
    if (!isset($packages[$pkgIndex])) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'بسته انتخاب‌شده معتبر نیست.']); return; }
    if ((string)setting('payment_card_enabled', '1') !== '1') { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'پرداخت کارت به کارت فعال نیست.']); return; }
    $p = $packages[$pkgIndex];
    $orderId = createCoinOrder((int)$user['id'], (int)$p['coins'], (int)$p['amount'], 'card', 'pending');
    setStep((int)$user['id'], 'await_card_receipt', ['order_id' => $orderId]);
    $cardNumber = trim((string)setting('card_number', '')) ?: 'تنظیم نشده';
    $cardOwner = trim((string)setting('card_owner', '')) ?: 'تنظیم نشده';
    $cardText = trim((string)setting('card_text', 'پس از واریز، رسید پرداخت را همینجا ارسال کنید.'));
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "💳 پرداخت کارت به کارت\n\nشماره سفارش: #{$orderId}\n💰 بسته: {$p['coins']} سکه\n💳 مبلغ: " . formatToman((int)$p['amount']) . "\n\nشماره کارت:\n{$cardNumber}\n\nبه نام:\n{$cardOwner}\n\n{$cardText}",
        'reply_markup' => cancelKeyboard(),
    ]);
}

function startZibalPayment(int $chatId, array $user, int $pkgIndex): void
{
    $packages = coinPackages();
    if (!isset($packages[$pkgIndex])) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'بسته انتخاب‌شده معتبر نیست.']); return; }
    if ((string)setting('payment_zibal_enabled', '0') !== '1') { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'پرداخت آنلاین فعال نیست.']); return; }
    $p = $packages[$pkgIndex];
    $orderId = createCoinOrder((int)$user['id'], (int)$p['coins'], (int)$p['amount'], 'zibal', 'created');
    $pay = createZibalPayment($orderId, (int)$p['amount'] * 10, 'خرید ' . $p['coins'] . ' سکه');
    if (!($pay['ok'] ?? false)) {
        updateCoinOrder($orderId, ['status' => 'failed']);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => (string)($pay['message'] ?? 'ساخت پرداخت ناموفق بود.'), 'reply_markup' => coinsKeyboard()]);
        return;
    }
    $trackId = (string)($pay['track_id'] ?? '');
    $payUrl = $trackId !== '' ? paymentNoticeUrl($orderId, $trackId) : (string)$pay['url'];
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🔵 پرداخت آنلاین\n\nشماره سفارش: #{$orderId}\n💰 بسته: {$p['coins']} سکه\n💳 مبلغ: " . formatToman((int)$p['amount']) . "\n\nابتدا راهنمای پرداخت را باز کنید، VPN را خاموش کنید و سپس وارد صفحه پرداخت شوید.",
        'reply_markup' => inline([[['text' => 'راهنمای پرداخت و ورود به درگاه', 'url' => $payUrl]]]),
    ]);
}

function handleCardReceipt(array $message, array $user, array $temp): void
{
    $chatId = (int)$message['chat']['id'];
    $userId = (int)$user['id'];
    $orderId = (int)($temp['order_id'] ?? 0);
    $order = $orderId > 0 ? getCoinOrder($orderId) : null;
    if (!$order || (int)$order['user_id'] !== $userId || (string)$order['status'] !== 'pending') {
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'سفارش پرداخت پیدا نشد. دوباره از بخش خرید سکه اقدام کنید.', 'reply_markup' => coinsKeyboard()]);
        return;
    }
    $receiptFile = null;
    $receiptType = 'text';
    $receiptText = trim((string)($message['text'] ?? $message['caption'] ?? ''));
    if (isset($message['photo'])) { $photo = end($message['photo']); $receiptFile = $photo['file_id']; $receiptType = 'photo'; }
    elseif (isset($message['document'])) { $receiptFile = $message['document']['file_id']; $receiptType = 'document'; }
    if (!$receiptFile && $receiptText === '') {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'لطفاً تصویر رسید یا کد پیگیری پرداخت را ارسال کنید.', 'reply_markup' => cancelKeyboard()]);
        return;
    }
    updateCoinOrder($orderId, ['receipt_file_id' => $receiptFile, 'receipt_type' => $receiptType, 'receipt_text' => $receiptText, 'status' => 'pending']);
    setStep($userId, null);
    notifyPaymentAdmins($orderId);
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ رسید شما ثبت شد.\nپس از تایید مدیریت، سکه به حساب شما اضافه می‌شود.", 'reply_markup' => mainKeyboard(isAdmin($userId))]);
}

function notifyPaymentAdmins(int $orderId): void
{
    $order = getCoinOrder($orderId);
    if (!$order) return;
    $user = getUser((int)$order['user_id']) ?: ['id' => $order['user_id'], 'username' => '', 'first_name' => '', 'last_name' => ''];
    $username = !empty($user['username']) ? '@' . $user['username'] : 'ندارد';
    $name = trim((string)($user['first_name'] ?? '') . ' ' . (string)($user['last_name'] ?? '')) ?: 'کاربر';
    $text = "🧾 رسید خرید سکه\n\nشماره سفارش: #{$order['id']}\n👤 کاربر: {$name}\n🆔 آیدی: {$order['user_id']}\n🔗 یوزرنیم: {$username}\n💰 سکه: {$order['coins']}\n💳 مبلغ: " . formatToman((int)$order['amount']) . "\n\nوضعیت: در انتظار بررسی";
    $markup = inline([[[ 'text' => '✅ تایید', 'callback_data' => 'pay_approve:' . $orderId ], [ 'text' => '❌ رد', 'callback_data' => 'pay_reject:' . $orderId ]]]);
    foreach (adminIds() as $adminId) {
        if (!empty($order['receipt_file_id']) && $order['receipt_type'] === 'photo') {
            tg('sendPhoto', ['chat_id' => $adminId, 'photo' => $order['receipt_file_id'], 'caption' => $text, 'reply_markup' => $markup]);
        } elseif (!empty($order['receipt_file_id']) && $order['receipt_type'] === 'document') {
            tg('sendDocument', ['chat_id' => $adminId, 'document' => $order['receipt_file_id'], 'caption' => $text, 'reply_markup' => $markup]);
        } else {
            $extra = trim((string)$order['receipt_text']) !== '' ? "\n\nمتن رسید:\n" . $order['receipt_text'] : '';
            tg('sendMessage', ['chat_id' => $adminId, 'text' => $text . $extra, 'reply_markup' => $markup]);
        }
    }
}


function askUserChannel(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'await_user_channel');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "📢 تنظیم کانال ارسال پست\n\nربات را داخل کانال ادمین کن و دسترسی ارسال/ویرایش بده. سپس آیدی کانال را بفرست؛ مثل @yourchannel\n\nیا یک پست از همان کانال را برای ربات فوروارد کن.", 'reply_markup' => cancelKeyboard()]);
}

function askLikeEmoji(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'await_like_emoji');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "❤️ تنظیم اموجی لایک\n\nاین متن هنگام ساخت لایک به عنوان متن دکمه و لایک آنها استفاده می‌شود.\n\nاموجی یا حرف جدید با طول نهایتاً ۴ کاراکتر ارسال کنید.", 'reply_markup' => cancelKeyboard()]);
}

function askUserForceJoin(int $chatId, array $user): void
{
    $uid = (int)$user['id'];
    $fresh = getUser($uid) ?: $user;
    if (!empty($fresh['default_channel']) || !empty($fresh['default_channel_id'])) {
        $channels = defaultForceChannelsFromUser($fresh);
        if ($channels) {
            db()->prepare('UPDATE users SET force_channels = ? WHERE id = ?')->execute([json_encode($channels, JSON_UNESCAPED_UNICODE), $uid]);
        }
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => '✅ عضویت اجباری به صورت خودکار فعال است و دکمه جداگانه‌ای برای خاموش/روشن کردن ندارد.',
            'reply_markup' => settingsKeyboard(),
        ]);
    } else {
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => '📢 ابتدا کانال خود را تنظیم کنید؛ بعد از تنظیم کانال، عضویت اجباری خودکار فعال می‌شود.',
            'reply_markup' => settingsKeyboard(),
        ]);
    }
}

function openItemPayload(int $chatId, int $userId, int $itemId): void
{
    $item = getItem($itemId);
    if (!$item) { tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ این لینک پیدا نشد یا حذف شده است.']); return; }
    $channels = requiredChannelsForOwner((int)$item['owner_id']);
    if (!requireJoinOrSend($chatId, $userId, $channels, 'item_' . $itemId)) return;
    sendItemToChat($chatId, $item);
}

function openSpeedPayload(int $chatId, array $from, string $token): void
{
    $speed = getSpeedChallenge($token);
    if (!$speed || !(int)$speed['is_active']) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '❌ این چالش دیگر فعال نیست یا برنده آن مشخص شده است.']);
        return;
    }
    $userId = (int)($from['id'] ?? 0);
    if ($userId <= 0) return;
    if ($userId === (int)$speed['owner_id']) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'شما سازنده چالش هستید؛ باید کاربر دیگری اولین نفر وارد شود.']);
        return;
    }

    $channels = requiredChannelsForOwner((int)$speed['owner_id']);
    if (!requireJoinOrSend($chatId, $userId, $channels, 'speed_' . $token)) return;

    $winner = winnerInfo($from);
    $stmt = db()->prepare('UPDATE speed_challenges SET is_active = 0, winner_id = ?, winner_json = ?, won_at = ? WHERE token = ? AND is_active = 1');
    $stmt->execute([$userId, json_encode($winner, JSON_UNESCAPED_UNICODE), date('Y-m-d H:i:s'), $token]);
    if ($stmt->rowCount() < 1) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => 'متاسفانه کمی دیر رسیدی؛ برنده قبلاً مشخص شده است.']);
        return;
    }

    $name = displayName($from);
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "🏆 تبریک! شما اولین نفر وارد لینک شدید و برنده چالش هستید."]);
    $username = !empty($winner['username']) ? '@' . $winner['username'] : 'ندارد';
    tg('sendMessage', [
        'chat_id' => (int)$speed['owner_id'],
        'text' => "🏁 برنده چالش سرعتی شما مشخص شد:\n\n👤 نام: " . userMention($userId, $name) . "\n🆔 آیدی عددی: {$userId}\n🔗 یوزرنیم: {$username}",
        'parse_mode' => 'HTML',
    ]);
}


function handleCountCallback(array $cb, string $data): void
{
    $cbId = (string)($cb['id'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    if (!$message || $userId <= 0) return;
    $chatId = (int)$message['chat']['id'];
    $user = ensureUser($from);
    $fresh = getUser($userId) ?: $user;
    $step = (string)($fresh['step'] ?? '');
    $temp = getTemp($fresh);

    if ($data === 'count_cancel') {
        setStep($userId, null);
        clearInlinePanel($message);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'لغو شد.']);
        sendHome($chatId, $fresh);
        return;
    }

    $allowed = ['await_like_fake', 'await_challenge_like_fake', 'await_auto_count', 'await_auto_fake'];
    if (!in_array($step, $allowed, true)) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'این انتخاب دیگر فعال نیست.', 'show_alert' => true]);
        return;
    }

    if ($data === 'count_custom') {
        $map = [
            'await_like_fake' => 'await_like_fake_custom',
            'await_challenge_like_fake' => 'await_challenge_like_fake_custom',
            'await_auto_count' => 'await_auto_count_custom',
            'await_auto_fake' => 'await_auto_fake_custom',
        ];
        setStep($userId, $map[$step], $temp);
        clearInlinePanel($message);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        $prompt = $step === 'await_auto_count'
            ? 'چند پست آینده لایک بخورند؟ فقط عدد ارسال کن؛ مثال: 5'
            : ($step === 'await_auto_fake' ? 'عدد دلخواه لایک اولیه را ارسال کن.' : 'عدد دلخواه را فقط با رقم ارسال کن؛ مثال: 350');
        tg('sendMessage', ['chat_id' => $chatId, 'text' => $prompt, 'reply_markup' => cancelKeyboard()]);
        return;
    }

    if (!str_starts_with($data, 'count_value:')) return;
    $value = (int)substr($data, strlen('count_value:'));
    clearInlinePanel($message);
    tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'انتخاب شد: ' . $value]);

    if ($step === 'await_like_fake' || $step === 'await_challenge_like_fake') {
        askConfirmPublishLike($chatId, $fresh, $temp, $value, $step === 'await_challenge_like_fake' ? 'challenge_like' : 'like');
        return;
    }

    if ($step === 'await_auto_count') {
        if ($value < 1) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین تعداد پست', 'تعداد پست باید حداقل ۱ باشد؛ دوباره انتخاب کنید.'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([1, 2, 3, 5, 10, 20])]);
            return;
        }
        setStep($userId, 'await_auto_fake', ['auto_posts' => $value]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => countPromptText('تعیین لایک هر پست', 'هر پست با چند لایک اولیه شروع شود؟'), 'parse_mode' => 'HTML', 'reply_markup' => countKeyboard([0, 10, 25, 50, 100, 250, 500, 1000])]);
        return;
    }

    if ($step === 'await_auto_fake') {
        askConfirmAutoLike($chatId, $fresh, $temp, $value);
        return;
    }
}

function handleCallback(array $cb): void
{
    $data = (string)($cb['data'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    $cbId = (string)($cb['id'] ?? '');
    if ($userId <= 0) return;

    if (str_starts_with($data, 'order_')) {
        handleOrderConfirmCallback($cb);
        return;
    }

    if (str_starts_with($data, 'bd_')) {
        handleButtonVisibilityCallback($cb, $data);
        return;
    }

    if (str_starts_with($data, 'count_')) {
        handleCountCallback($cb, $data);
        return;
    }

    if (str_starts_with($data, 'lkadj:') || $data === 'lkadj_home') {
        handleLikeAdjustCallback($cb);
        return;
    }

    if (str_starts_with($data, 'bs_')) {
        handleButtonStyleCallback($cb);
        return;
    }

    if (str_starts_with($data, 'buy_pkg:')) {
        $idx = (int)substr($data, 8);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        if ($message) showPaymentMethods((int)$message['chat']['id'], $idx);
        return;
    }

    if (str_starts_with($data, 'buy_method:')) {
        $parts = explode(':', $data);
        $method = (string)($parts[1] ?? '');
        $idx = (int)($parts[2] ?? -1);
        $user = ensureUser($from);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        if ($message) {
            $targetChat = (int)$message['chat']['id'];
            if ($method === 'card') startCardPayment($targetChat, $user, $idx);
            elseif ($method === 'zibal') startZibalPayment($targetChat, $user, $idx);
        }
        return;
    }

    if (str_starts_with($data, 'pay_approve:') || str_starts_with($data, 'pay_reject:')) {
        if (!isAdmin($userId)) { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'دسترسی ندارید.', 'show_alert' => true]); return; }
        $approve = str_starts_with($data, 'pay_approve:');
        $orderId = (int)substr($data, $approve ? 12 : 11);
        $order = getCoinOrder($orderId);
        if (!$order) { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'سفارش پیدا نشد.', 'show_alert' => true]); return; }
        if ($approve) {
            if (markCoinOrderPaid($orderId, 'card_manual')) {
                $fresh = getUser((int)$order['user_id']);
                tg('sendMessage', ['chat_id' => (int)$order['user_id'], 'text' => "✅ خرید سکه شما تایید شد.\n💰 {$order['coins']} سکه به حساب شما اضافه شد.\nموجودی فعلی: " . (int)($fresh['coins'] ?? 0) . ' سکه']);
                tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'تایید شد.']);
            } else {
                tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'این سفارش قبلاً بررسی شده است.', 'show_alert' => true]);
            }
        } else {
            if ((string)$order['status'] !== 'paid') {
                updateCoinOrder($orderId, ['status' => 'rejected']);
                tg('sendMessage', ['chat_id' => (int)$order['user_id'], 'text' => "❌ رسید خرید سکه تایید نشد.\nشماره سفارش: #{$orderId}"]);
                tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'رد شد.']);
            } else {
                tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'این سفارش قبلاً پرداخت شده است.', 'show_alert' => true]);
            }
        }
        if ($message) {
            $newText = ($approve ? '✅ تایید شد' : '❌ رد شد') . "\n\n" . (($message['caption'] ?? null) ?: ($message['text'] ?? ''));
            if (isset($message['photo']) || isset($message['document'])) {
                tg('editMessageCaption', ['chat_id' => $message['chat']['id'], 'message_id' => $message['message_id'], 'caption' => $newText]);
            } else {
                tg('editMessageText', ['chat_id' => $message['chat']['id'], 'message_id' => $message['message_id'], 'text' => $newText]);
            }
        }
        return;
    }

    if (str_starts_with($data, 'check_join:')) {
        $payload = substr($data, strlen('check_join:'));
        $channels = getGlobalJoinChannels();
        if (str_starts_with($payload, 'item_')) {
            $item = getItem((int)substr($payload, 5));
            if ($item) $channels = requiredChannelsForOwner((int)$item['owner_id']);
        } elseif (str_starts_with($payload, 'speed_')) {
            $speed = getSpeedChallenge(substr($payload, 6));
            if ($speed) $channels = requiredChannelsForOwner((int)$speed['owner_id']);
        }
        $missing = missingJoinChannels($userId, $channels);
        if ($missing) { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'هنوز عضو همه کانال‌های لازم نیستید.', 'show_alert' => true]); return; }
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'عضویت تایید شد.']);
        if ($message) {
            $targetChat = (int)$message['chat']['id'];
            if (str_starts_with($payload, 'item_')) { openItemPayload($targetChat, $userId, (int)substr($payload, 5)); return; }
            if (str_starts_with($payload, 'speed_')) { openSpeedPayload($targetChat, $from, substr($payload, 6)); return; }
            if ($payload === 'create') { menuCreateHub($targetChat, ensureUser($from)); return; }
            sendHome($targetChat, ensureUser($from));
        }
        return;
    }

    if (str_starts_with($data, 'like:')) {
        $itemId = (int)substr($data, 5);
        $item = getItem($itemId);
        if (!$item) { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'پیدا نشد.', 'show_alert' => true]); return; }
        $channels = requiredChannelsForOwner((int)$item['owner_id']);
        if ($channels && missingJoinChannels($userId, $channels)) {
            tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'برای ثبت لایک باید اول عضو کانال‌های خواسته‌شده شوید.', 'show_alert' => true]);
            return;
        }
        $stmt = db()->prepare('SELECT id FROM votes WHERE item_id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$itemId, $userId]);
        if ($row = $stmt->fetch()) {
            db()->prepare('DELETE FROM votes WHERE id = ?')->execute([(int)$row['id']]);
            $answer = 'لایک شما برداشته شد.';
        } else {
            db()->prepare('INSERT INTO votes (item_id, user_id, vote_value, created_at) VALUES (?, ?, ?, ?)')->execute([$itemId, $userId, '1', date('Y-m-d H:i:s')]);
            $answer = $item['type'] === 'challenge_like' ? 'در چالش ثبت شدید.' : 'لایک شما ثبت شد.';
        }
        $item = getItem($itemId) ?: $item;
        if ($message) {
            tg('editMessageReplyMarkup', ['chat_id' => $message['chat']['id'], 'message_id' => $message['message_id'], 'reply_markup' => itemMarkup($item)]);
        }
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => $answer]);
        return;
    }

    if (str_starts_with($data, 'poll:')) {
        $parts = explode(':', $data);
        $itemId = (int)($parts[1] ?? 0);
        $option = (string)($parts[2] ?? '0');
        $item = getItem($itemId);
        if (!$item || $item['type'] !== 'poll') { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'نظرسنجی پیدا نشد.', 'show_alert' => true]); return; }
        db()->prepare('INSERT INTO votes (item_id, user_id, vote_value, created_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE vote_value = VALUES(vote_value), created_at = VALUES(created_at)')->execute([$itemId, $userId, $option, date('Y-m-d H:i:s')]);
        $item = getItem($itemId) ?: $item;
        if ($message) tg('editMessageReplyMarkup', ['chat_id' => $message['chat']['id'], 'message_id' => $message['message_id'], 'reply_markup' => itemMarkup($item)]);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'رأی شما ثبت شد.']);
    }
}


function handleLikeAdjustCallback(array $cb): void
{
    $data = (string)($cb['data'] ?? '');
    $cbId = (string)($cb['id'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    if ($userId <= 0) return;

    if ($data === 'lkadj_home') {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'بازگشت به منوی اصلی']);
        if ($message) {
            tg('editMessageReplyMarkup', [
                'chat_id' => $message['chat']['id'],
                'message_id' => $message['message_id'],
                'reply_markup' => json_encode(['inline_keyboard' => []], JSON_UNESCAPED_UNICODE),
            ], false);
            tg('sendMessage', ['chat_id' => (int)$message['chat']['id'], 'text' => 'به منوی اصلی بازگشتید.', 'reply_markup' => mainKeyboard(isAdmin($userId))]);
        }
        return;
    }

    $parts = explode(':', $data);
    $itemId = (int)($parts[1] ?? 0);
    $delta = (int)($parts[2] ?? 0);
    $item = $itemId > 0 ? getItem($itemId) : null;
    if (!$item) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'پست پیدا نشد.', 'show_alert' => true]);
        return;
    }
    if ((int)$item['owner_id'] !== $userId && !isMainAdmin($userId)) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'این پنل مربوط به سفارش شما نیست.', 'show_alert' => true]);
        return;
    }
    if ($delta === 0) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'مقدار نامعتبر است.', 'show_alert' => true]);
        return;
    }

    $updated = adjustItemFakeLikes($itemId, $delta);
    if (!$updated) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'خطا در تغییر عدد.', 'show_alert' => true]);
        return;
    }
    $channelOk = refreshItemMarkupOnChannel($updated);

    if ($message) {
        tg('editMessageText', [
            'chat_id' => $message['chat']['id'],
            'message_id' => $message['message_id'],
            'text' => likeControlText($updated, 'عدد لایک به‌روزرسانی شد.'),
            'reply_markup' => likeControlKeyboard($itemId),
        ], false);
    }
    $sign = $delta > 0 ? '+' : '';
    $msg = $channelOk ? "{$sign}{$delta} اعمال شد." : "{$sign}{$delta} ثبت شد، اما به‌روزرسانی پست کانال ناموفق بود.";
    tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => $msg, 'show_alert' => !$channelOk]);
}

function handleChannelPost(array $message, bool $isEdited = false): void
{
    $chat = $message['chat'] ?? [];
    $chatId = (string)($chat['id'] ?? '');
    $messageId = (int)($message['message_id'] ?? 0);
    if ($chatId === '' || $messageId <= 0) return;

    logAutoLike('channel_post_received', [
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'edited' => $isEdited ? 1 : 0,
        'has_markup' => isset($message['reply_markup']) ? 1 : 0,
        'type' => channelPostType($message),
    ]);

    if (itemExistsForChannelMessage($chatId, $messageId)) {
        logAutoLike('skip_already_processed', ['chat_id' => $chatId, 'message_id' => $messageId]);
        return;
    }

    if (isset($message['reply_markup'])) {
        logAutoLike('skip_has_reply_markup', ['chat_id' => $chatId, 'message_id' => $messageId]);
        return;
    }

    if (!isSupportedAutoLikeChannelPost($message)) {
        logAutoLike('skip_unsupported_post', ['chat_id' => $chatId, 'message_id' => $messageId, 'type' => channelPostType($message)]);
        return;
    }

    $job = getActiveAutoJobForChannel($chatId);
    if (!$job) {
        logAutoLike('skip_no_active_job', ['chat_id' => $chatId, 'message_id' => $messageId]);
        return;
    }

    $ownerId = (int)$job['owner_id'];
    $price = (int)$job['price_per_post'];
    $owner = getUser($ownerId);
    if (!$owner) {
        stopAutoJob((int)$job['id']);
        logAutoLike('stop_owner_not_found', ['job_id' => (int)$job['id'], 'owner_id' => $ownerId]);
        return;
    }

    // برای سرعت بیشتر، در هر پست دوباره getChatMember نمی‌زنیم؛ اگر ویرایش ناموفق باشد پایین‌تر خطای تلگرام هندل می‌شود.

    if (!takeCoins($ownerId, $price)) {
        stopAutoJob((int)$job['id']);
        tg('sendMessage', ['chat_id' => $ownerId, 'text' => "❌ خودکار لایک متوقف شد؛ موجودی سکه برای پست جدید کافی نبود.\n📢 کانال: {$job['channel_title']}"]);
        logAutoLike('stop_no_coins', ['job_id' => (int)$job['id'], 'owner_id' => $ownerId, 'price' => $price]);
        return;
    }

    $content = [
        'media_type' => 'channel_post',
        'text' => null,
        'source_chat_id' => $chatId,
        'source_message_id' => $messageId,
    ];
    $channel = ['default_channel' => $job['channel_id'], 'default_channel_id' => $chatId];
    $itemId = createItem($ownerId, 'auto_like', $content, null, $owner['like_emoji'] ?: DEFAULT_LIKE_EMOJI, (int)$job['fake_likes'], $channel);
    $item = getItem($itemId);
    if (!$item) {
        addCoins($ownerId, $price);
        logAutoLike('create_item_failed', ['job_id' => (int)$job['id']]);
        return;
    }

    $res = tg('editMessageReplyMarkup', [
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'reply_markup' => itemMarkup($item),
    ]);

    $finalMessageId = $messageId;
    $mode = 'edited';

    if (!($res['ok'] ?? false)) {
        $desc = (string)($res['description'] ?? 'خطای نامشخص تلگرام');
        logAutoLike('edit_failed_try_fallback', ['job_id' => (int)$job['id'], 'chat_id' => $chatId, 'message_id' => $messageId, 'description' => $desc]);

        // اگر تلگرام اجازه ویرایش همان پست را نداد، ربات با روش جایگزین عمل می‌کند:
        // 1) کپی همان پست با دکمه لایک
        // 2) اگر کپی نشد، ارسال یک پیام لایک برای پست بالا
        // در صورت موفقیت، سفارش انجام‌شده حساب می‌شود و خطای مزاحم برای کاربر ارسال نمی‌شود.
        $copy = tg('copyMessage', [
            'chat_id' => $chatId,
            'from_chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => itemMarkup($item),
        ], false);

        if (($copy['ok'] ?? false) && !empty($copy['result']['message_id'])) {
            $finalMessageId = (int)$copy['result']['message_id'];
            $mode = 'copied_fallback';
            // اگر دسترسی حذف پیام فعال باشد، پست بدون دکمه حذف می‌شود تا پست تکراری دیده نشود.
            $del = tg('deleteMessage', ['chat_id' => $chatId, 'message_id' => $messageId], false);
            logAutoLike('fallback_copy_success', ['job_id' => (int)$job['id'], 'old_message_id' => $messageId, 'new_message_id' => $finalMessageId, 'deleted_old' => (bool)($del['ok'] ?? false)]);
        } else {
            logAutoLike('fallback_copy_failed', ['job_id' => (int)$job['id'], 'chat_id' => $chatId, 'message_id' => $messageId, 'response' => $copy]);
            $send = tg('sendMessage', [
                'chat_id' => $chatId,
                'text' => "❤️ لایک این پست",
                'reply_markup' => itemMarkup($item),
            ], false);
            if (($send['ok'] ?? false) && !empty($send['result']['message_id'])) {
                $finalMessageId = (int)$send['result']['message_id'];
                $mode = 'separate_like_message';
                logAutoLike('fallback_message_success', ['job_id' => (int)$job['id'], 'source_message_id' => $messageId, 'like_message_id' => $finalMessageId]);
            } else {
                addCoins($ownerId, $price);
                db()->prepare('DELETE FROM items WHERE id = ?')->execute([$itemId]);
                logAutoLike('fallback_all_failed', ['job_id' => (int)$job['id'], 'chat_id' => $chatId, 'message_id' => $messageId, 'edit_error' => $desc, 'send_response' => $send]);

                tg('sendMessage', [
                    'chat_id' => $ownerId,
                    'text' => "⚠️ لایک خودکار برای پست جدید انجام نشد.

📢 کانال: {$job['channel_title']}
🆔 پیام: {$messageId}
برای اجرای خودکار، ربات باید در کانال ادمین باشد و دسترسی ارسال پیام، ویرایش پیام‌ها و ترجیحاً حذف پیام‌ها را داشته باشد. سکه این پست برگشت داده شد.",
                ]);
                return;
            }
        }
    }

    updateItemChannelMessage($itemId, $chatId, $finalMessageId);
    decrementAutoJob((int)$job['id']);
    logAutoLike('auto_like_success', ['job_id' => (int)$job['id'], 'mode' => $mode, 'item_id' => $itemId, 'chat_id' => $chatId, 'source_message_id' => $messageId, 'final_message_id' => $finalMessageId]);

    $left = max(0, (int)$job['remaining_posts'] - 1);
    $fresh = getUser($ownerId) ?: $owner;
    $modeNote = $mode === 'edited' ? "زیر همان پست" : ($mode === 'copied_fallback' ? "با بازنشر خودکار پست" : "با پیام لایک زیر پست");
    if ($left === 0) {
        tg('sendMessage', ['chat_id' => $ownerId, 'text' => "✅ خودکار لایک سفارش #{$job['id']} کامل شد.
📢 کانال: {$job['channel_title']}
📌 روش اجرا: {$modeNote}
💰 موجودی جدید: {$fresh['coins']} سکه"]);
        sendLikeControlPanel($ownerId, $itemId, 'لایک خودکار برای پست کانال ثبت شد.');
    } else {
        tg('sendMessage', ['chat_id' => $ownerId, 'text' => "✅ لایک خودکار ثبت شد.
📢 کانال: {$job['channel_title']}
🆔 پیام: {$finalMessageId}
📌 روش اجرا: {$modeNote}
📌 باقی‌مانده سفارش: {$left}
💰 موجودی: {$fresh['coins']} سکه"]);
        sendLikeControlPanel($ownerId, $itemId, 'لایک خودکار برای پست کانال ثبت شد.');
    }
}


function adminPaymentMenu(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $card = (string)setting('payment_card_enabled', '1') === '1' ? 'روشن' : 'خاموش';
    $zibal = (string)setting('payment_zibal_enabled', '0') === '1' ? 'روشن' : 'خاموش';
    $cardNumber = trim((string)setting('card_number', '')) ?: 'ثبت نشده';
    $zibalMerchant = trim((string)setting('zibal_merchant', '')) ?: 'ثبت نشده';
    $bridgeUrl = trim((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php')) ?: 'ثبت نشده';
    $bridgeIp = trim((string)setting('zibal_bridge_ip', '')) ?: 'ثبت نشده';
    $bridgeMode = (string)setting('zibal_bridge_enabled', '1') === '1' ? 'دامنه دوم' : 'مستقیم';
    $packages = coinPackages();
    $list = [];
    foreach ($packages as $p) $list[] = $p['coins'] . ' سکه = ' . formatToman((int)$p['amount']);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "💳 تنظیم خرید سکه\n\nوضعیت کارت به کارت: {$card}\nشماره کارت: {$cardNumber}\nوضعیت زیبال: {$zibal}\nمرچنت زیبال: {$zibalMerchant}\nآدرس اتصال زیبال: {$bridgeUrl}\n\nپلن‌های خرید سکه:\n" . ($list ? implode("\n", $list) : 'ثبت نشده') . "\n\nبرای روشن یا خاموش کردن هر روش، دکمه وضعیت همان روش را بزنید.",
        'reply_markup' => paymentAdminKeyboard(),
    ]);
}

function adminPaymentSubRoute(int $chatId, array $user, string $text): void
{
    if (isButtonText($text, ['📦 تنظیم بسته‌های سکه', '📦 تنظیم پلن‌های خرید سکه'])) { adminAskCoinPackages($chatId, $user); return; }
    if (isButtonText($text, ['💳 تنظیم کارت به کارت'])) { adminAskCardInfo($chatId, $user); return; }
    if (isButtonText($text, ['🔄 وضعیت کارت به کارت', '🟢 کارت به کارت روشن', '🔴 کارت به کارت خاموش', '🔄 روشن/خاموش کارت به کارت'])) { adminToggleCard($chatId, $user); return; }
    if (isButtonText($text, ['🔵 تنظیم مرچنت زیبال'])) { adminAskZibalMerchant($chatId, $user); return; }
    if (isButtonText($text, ['🌐 تنظیم اتصال زیبال'])) { adminAskZibalBridge($chatId, $user); return; }
    if (isButtonText($text, ['🧭 تنظیم IP دامنه زیبال'])) { adminAskZibalBridgeIp($chatId, $user); return; }
    if (isButtonText($text, ['🟢 اتصال دامنه دوم روشن', '⚪️ اتصال مستقیم زیبال', '🔄 حالت اتصال زیبال'])) { adminToggleZibalBridge($chatId, $user); return; }
    if (isButtonText($text, ['🔄 وضعیت زیبال', '🟢 زیبال روشن', '🔴 زیبال خاموش', '🔄 روشن/خاموش زیبال'])) { adminToggleZibal($chatId, $user); return; }
    if (isButtonText($text, ['🧪 تست اتصال زیبال'])) { adminTestZibal($chatId, $user); return; }
}

function adminAskCoinPackages(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_coin_packages');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "📦 تنظیم پلن‌های خرید سکه\n\nهر پلن را در یک خط با فرمت زیر ارسال کنید:\nسکه=مبلغ تومان\n\nمثال:\n50=50000\n100=90000\n250=200000", 'reply_markup' => cancelKeyboard()]);
}

function adminAskDailyRewards(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_daily_rewards');
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🎯 تنظیم جایزه روزانه\n\nبرای هر عدد تاس مقدار سکه را با فرمت زیر بفرستید:\n1=0\n2=3\n3=1\n4=2\n5=3\n6=0\n\nعدد ۰ یعنی پوچ.",
        'reply_markup' => cancelKeyboard(),
    ]);
}

function adminAskCardInfo(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_card_info');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "💳 تنظیم کارت به کارت\n\nاطلاعات را به ترتیب زیر ارسال کنید:\nشماره کارت\nنام صاحب کارت\nمتن راهنمای پرداخت\n\nمثال:\n6037990000000000\nعلی رضایی\nپس از واریز، رسید را ارسال کنید.", 'reply_markup' => cancelKeyboard()]);
}

function adminToggleCard(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $cur = (string)setting('payment_card_enabled', '1') === '1';
    set_setting('payment_card_enabled', $cur ? '0' : '1');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ وضعیت کارت به کارت ' . ($cur ? 'خاموش' : 'روشن') . ' شد.', 'reply_markup' => paymentAdminKeyboard()]);
}

function adminAskZibalMerchant(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_zibal_merchant');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '🔵 مرچنت زیبال را ارسال کنید.', 'reply_markup' => cancelKeyboard()]);
}

function adminAskZibalBridge(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_zibal_bridge');
    $curUrl = function_exists('normalizeBridgeUrl') ? normalizeBridgeUrl((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php')) : trim((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php'));
    $curSecret = trim((string)setting('zibal_bridge_secret', 'CHANGE_THIS_SECRET'));
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🌐 تنظیم اتصال زیبال\n\nفایل zibal_bridge.php را روی دامنه زیبال آپلود کن، سپس اینجا آدرس فایل و کد امنیتی را در دو خط بفرست.\n\nنمونه:\nhttps://your-domain.ir/zibal_bridge.php\n{$curSecret}\n\nآدرس فعلی:\n{$curUrl}",
        'reply_markup' => cancelKeyboard(),
    ]);
}

function adminToggleZibal(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $cur = (string)setting('payment_zibal_enabled', '0') === '1';
    set_setting('payment_zibal_enabled', $cur ? '0' : '1');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ وضعیت زیبال ' . ($cur ? 'خاموش' : 'روشن') . ' شد.', 'reply_markup' => paymentAdminKeyboard()]);
}


function adminToggleZibalBridge(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $cur = (string)setting('zibal_bridge_enabled', '1') === '1';
    set_setting('zibal_bridge_enabled', $cur ? '0' : '1');
    $text = $cur
        ? '✅ اتصال دامنه دوم خاموش شد و پرداخت زیبال از مسیر مستقیم انجام می‌شود.'
        : '✅ اتصال دامنه دوم روشن شد. اگر DNS دامنه دوم روی هاست اصلی مشکل دارد، IP دامنه زیبال را هم تنظیم کن.';
    tg('sendMessage', ['chat_id' => $chatId, 'text' => $text, 'reply_markup' => paymentAdminKeyboard()]);
}

function adminAskZibalBridgeIp(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_zibal_bridge_ip');
    $curIp = trim((string)setting('zibal_bridge_ip', '')) ?: 'ثبت نشده';
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🧭 تنظیم IP دامنه زیبال\n\nاگر هاست اصلی خطای Could not resolve host می‌دهد، IP هاست دوم را اینجا بفرست تا ربات با همان دامنه ولی از طریق IP مستقیم وصل شود.\n\nIP فعلی: {$curIp}\n\nمثال:\n1.2.3.4\n\nبرای حذف IP بنویس: حذف",
        'reply_markup' => cancelKeyboard(),
    ]);
}

function adminTestZibal(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $merchant = trim((string)setting('zibal_merchant', ''));
    $bridgeUrl = function_exists('normalizeBridgeUrl') ? normalizeBridgeUrl((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php')) : trim((string)setting('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php'));
    $secret = trim((string)setting('zibal_bridge_secret', ''));
    $bridgeIp = trim((string)setting('zibal_bridge_ip', ''));
    $enabled = (string)setting('payment_zibal_enabled', '0') === '1';
    $bridgeEnabled = (string)setting('zibal_bridge_enabled', '1') === '1';

    $errors = [];
    if (!$enabled) $errors[] = 'زیبال خاموش است.';
    if ($merchant === '') $errors[] = 'مرچنت زیبال ثبت نشده است.';
    if ($bridgeEnabled && $bridgeUrl === '') $errors[] = 'آدرس اتصال زیبال ثبت نشده است.';
    if ($bridgeEnabled && ($secret === '' || $secret === 'CHANGE_THIS_SECRET')) $errors[] = 'کد امنیتی اتصال زیبال را تغییر بده و همان مقدار را داخل فایل هاست دوم قرار بده.';

    if ($errors) {
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "❌ اتصال زیبال آماده نیست:\n" . implode("\n", array_map(fn($x) => '• ' . $x, $errors)),
            'reply_markup' => paymentAdminKeyboard(),
        ]);
        return;
    }

    if ($bridgeEnabled) {
        $res = zibalBridgeApi('ping', ['merchant' => $merchant]);
        if (($res['ok'] ?? false) || (int)($res['result'] ?? 0) === 100 || (int)($res['result'] ?? 0) === 1) {
            $sslNote = !empty($res['ssl_bypass']) ? "\n⚠️ اتصال با IP دستی انجام شد. بهتر است SSL دامنه درگاه را روی هاست دوم درست کنی، ولی فعلاً لینک‌سازی انجام می‌شود." : '';
            tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ اتصال به فایل زیبال برقرار است.\nحالت اتصال: دامنه دوم{$sslNote}\nحالا خرید آنلاین باید لینک پرداخت بسازد.", 'reply_markup' => paymentAdminKeyboard()]);
            return;
        }

        $msg = (string)($res['message'] ?? 'پاسخ نامعتبر از فایل اتصال زیبال');
        $raw = trim((string)($res['raw'] ?? ''));

        // اگر خود فایل bridge از هاست اصلی JSON نداد، لینک‌سازی را با حالت callback-only تست می‌کنیم:
        // درخواست مستقیم به API زیبال، اما callback روی دامنه دوم. این روش معمولاً مشکل DNS/SSL داخلی هاست را دور می‌زند.
        if ((int)($res['result'] ?? 0) === -2 || stripos($msg, 'پاسخ فایل اتصال') !== false) {
            $payload = [
                'merchant' => $merchant,
                'amount' => 10000,
                'callbackUrl' => function_exists('zibalBridgeCallbackUrl') ? zibalBridgeCallbackUrl() : zibalCallbackUrl(),
                'description' => 'تست اتصال زیبال',
                'orderId' => 'test-' . time(),
            ];
            $direct = zibalApi('request', $payload);
            if ((int)($direct['result'] ?? 0) === 100 && !empty($direct['trackId'])) {
                tg('sendMessage', [
                    'chat_id' => $chatId,
                    'text' => "✅ لینک‌سازی زیبال فعال است.\n\nفایل اتصال دامنه دوم از هاست اصلی خروجی معتبر نمی‌دهد، اما حالت جایگزین فعال شد: درخواست مستقیم به زیبال + برگشت پرداخت از دامنه دوم.\nحالا خرید آنلاین باید لینک پرداخت بسازد.\n\nبرای برگشت موفق پرداخت، مطمئن شو فایل zibal_bridge.php روی دامنه دوم واقعاً باز می‌شود.",
                    'reply_markup' => paymentAdminKeyboard(),
                ]);
                return;
            }
            $dmsg = (string)($direct['message'] ?? 'تست مستقیم هم ناموفق بود.');
            $dres = (int)($direct['result'] ?? 0);
            if ($dres !== 0) $dmsg .= "\nکد خطا: {$dres}";
            $msg .= "\n\nتست جایگزین زیبال هم ناموفق بود:\n{$dmsg}";
        }

        if (stripos($msg, 'Could not resolve host') !== false || stripos($msg, 'DNS') !== false || stripos($msg, 'SSL') !== false || stripos($msg, 'certificate') !== false || stripos($msg, 'hostname') !== false) {
            $extra = $bridgeIp === ''
                ? "\n\nراه‌حل سریع: دکمه «🧭 تنظیم IP دامنه زیبال» را بزن و IP هاست دوم را ثبت کن، یا حالت اتصال را روی مستقیم بگذار."
                : "\n\nIP تنظیم‌شده: {$bridgeIp}\nاگر هنوز خطا داری، IP هاست دوم درست نیست یا SSL دامنه روی همان هاست فعال نیست.";
            tg('sendMessage', [
                'chat_id' => $chatId,
                'text' => "❌ اتصال دامنه دوم زیبال برقرار نشد.\n\nپیام: {$msg}{$extra}" . ($raw !== '' ? "\n\nبخشی از پاسخ دریافتی:\n" . mb_substr($raw, 0, 250) : ''),
                'reply_markup' => paymentAdminKeyboard(),
            ]);
            return;
        }

        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => "❌ اتصال زیبال برقرار نشد.\nآدرس اتصال، کد امنیتی و آپلود بودن فایل روی دامنه دوم را بررسی کنید.\n\nپیام: {$msg}" . ($raw !== '' ? "\n\nبخشی از پاسخ دریافتی:\n" . mb_substr($raw, 0, 250) : ''),
            'reply_markup' => paymentAdminKeyboard(),
        ]);
        return;
    }

    $payload = [
        'merchant' => $merchant,
        'amount' => 10000,
        'callbackUrl' => zibalCallbackUrl(),
        'description' => 'تست اتصال زیبال',
        'orderId' => 'test-' . time(),
    ];
    $direct = zibalApi('request', $payload);
    if ((int)($direct['result'] ?? 0) === 100 && !empty($direct['trackId'])) {
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ اتصال مستقیم زیبال برقرار است.\nحالت اتصال: مستقیم\nلینک پرداخت در خرید سکه ساخته می‌شود.", 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    $msg = (string)($direct['message'] ?? 'ساخت درخواست تست زیبال ناموفق بود.');
    $result = (int)($direct['result'] ?? 0);
    if ($result !== 0) $msg .= "\nکد خطا: {$result}";
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "❌ اتصال مستقیم زیبال برقرار نشد.\n\nپیام: {$msg}", 'reply_markup' => paymentAdminKeyboard()]);
}

function adminAskAdjustCoins(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_adjust_coins');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "➕ افزایش/کاهش سکه کاربر\n\nفرمت ارسال:\nآیدی عددی کاربر\nمقدار سکه\n\nمثال افزایش:\n123456789\n50\n\nمثال کاهش:\n123456789\n-20", 'reply_markup' => cancelKeyboard()]);
}


function buttonStyleFaName(?string $style): string
{
    return match (normalizeButtonStyle((string)$style) ?? '') {
        'success' => 'سبز',
        'primary' => 'آبی',
        'danger' => 'قرمز',
        default => 'ساده / بی‌رنگ',
    };
}

function buttonStyleIcon(?string $style): string
{
    return match (normalizeButtonStyle((string)$style) ?? '') {
        'success' => '🟢',
        'primary' => '🔵',
        'danger' => '🔴',
        default => '⚪️',
    };
}

function buttonStyleAdminCategories(): array
{
    return [
        'main' => '🏠 منوی اصلی',
        'create' => '❤️ ساخت لایک و چالش',
        'coins' => '💰 افزایش سکه',
        'settings' => '⚙️ تنظیمات کاربر',
        'admin' => '🛠 پنل مدیریت',
        'payment' => '💳 پرداخت و خرید سکه',
        'post' => '📌 دکمه‌های زیر پست',
        'other' => '🔘 سایر دکمه‌ها',
    ];
}

function buttonStyleAdminTargets(): array
{
    return [
        ['key' => '❤️ ساخت لایک و چالش ❤️', 'label' => '❤️ ساخت لایک و چالش ❤️', 'cat' => 'main'],
        ['key' => '👤 پروفایل', 'label' => '👤 پروفایل', 'cat' => 'main'],
        ['key' => '💰 افزایش سکه', 'label' => '💰 افزایش سکه', 'cat' => 'main'],
        ['key' => '📖 راهنما', 'label' => '📖 راهنما', 'cat' => 'main'],
        ['key' => '⚙️ تنظیمات', 'label' => '⚙️ تنظیمات', 'cat' => 'main'],
        ['key' => '☕ اسپانسر ما', 'label' => '☕ اسپانسر ما', 'cat' => 'main'],
        ['key' => '🌐 Language / زبان', 'label' => '🌐 Language / زبان', 'cat' => 'main'],
        ['key' => '🛠 پنل مدیریت', 'label' => '🛠 پنل مدیریت', 'cat' => 'main'],

        ['key' => '❤️ ساخت لایک ❤️', 'label' => '❤️ ساخت لایک ❤️', 'cat' => 'create'],
        ['key' => '♦️ ساخت نظرسنجی ♦️', 'label' => '♦️ ساخت نظرسنجی ♦️', 'cat' => 'create'],
        ['key' => '💙 ساخت چالش لایک 💙', 'label' => '💙 ساخت چالش لایک 💙', 'cat' => 'create'],
        ['key' => '🏃 ساخت چالش سرعتی 🏃', 'label' => '🏃 ساخت چالش سرعتی 🏃', 'cat' => 'create'],
        ['key' => '🤍 ساخت خودکار لایک 🤍', 'label' => '🤍 ساخت خودکار لایک 🤍', 'cat' => 'create'],
        ['key' => 'مقدار دلخواه', 'label' => 'مقدار دلخواه', 'cat' => 'create'],

        ['key' => '🎯 جایزه روزانه', 'label' => '🎯 جایزه روزانه', 'cat' => 'coins'],
        ['key' => '🎁 جمع‌آوری سکه با زیرمجموعه', 'label' => '🎁 جمع‌آوری سکه با زیرمجموعه', 'cat' => 'coins'],
        ['key' => '💳 خرید سکه', 'label' => '💳 خرید سکه', 'cat' => 'coins'],

        ['key' => '📢 تنظیم کانال ارسال پست‌ها', 'label' => '📢 تنظیم کانال ارسال پست‌ها', 'cat' => 'settings'],
        ['key' => '❤️ تنظیم اموجی لایک', 'label' => '❤️ تنظیم اموجی لایک', 'cat' => 'settings'],
        ['key' => '💾 ذخیره تنظیمات پیشفرض', 'label' => '💾 ذخیره تنظیمات پیشفرض', 'cat' => 'settings'],

        ['key' => '📊 آمار', 'label' => '📊 آمار', 'cat' => 'admin'],
        ['key' => '💰 تنظیم قیمت‌ها', 'label' => '💰 تنظیم قیمت‌ها', 'cat' => 'admin'],
        ['key' => '🎁 تنظیم سکه اولیه کاربران', 'label' => '🎁 تنظیم سکه اولیه کاربران', 'cat' => 'admin'],
        ['key' => '🎁 تنظیم سکه زیرمجموعه', 'label' => '🎁 تنظیم سکه زیرمجموعه', 'cat' => 'admin'],
        ['key' => '🎯 تنظیم جایزه روزانه', 'label' => '🎯 تنظیم جایزه روزانه', 'cat' => 'admin'],
        ['key' => '💳 تنظیم خرید سکه', 'label' => '💳 تنظیم خرید سکه', 'cat' => 'admin'],
        ['key' => '➕ افزایش/کاهش سکه', 'label' => '➕ افزایش/کاهش سکه', 'cat' => 'admin'],
        ['key' => '🎨 تنظیم رنگ دکمه‌ها', 'label' => '🎨 تنظیم رنگ دکمه‌ها', 'cat' => 'admin'],
        ['key' => '🚫 خاموش/روشن دکمه‌ها', 'label' => '🚫 خاموش/روشن دکمه‌ها', 'cat' => 'admin'],
        ['key' => '➕ افزودن کانال جوین اجباری', 'label' => '➕ افزودن کانال جوین اجباری', 'cat' => 'admin'],
        ['key' => '➖ حذف کانال جوین اجباری', 'label' => '➖ حذف کانال جوین اجباری', 'cat' => 'admin'],
        ['key' => '📋 لیست کانال‌ها', 'label' => '📋 لیست کانال‌ها', 'cat' => 'admin'],
        ['key' => '☕ تنظیم اسپانسر', 'label' => '☕ تنظیم اسپانسر', 'cat' => 'admin'],
        ['key' => '📣 پیام همگانی', 'label' => '📣 پیام همگانی', 'cat' => 'admin'],
        ['key' => '➕ افزودن ادمین', 'label' => '➕ افزودن ادمین', 'cat' => 'admin'],

        ['key' => '📦 تنظیم پلن‌های خرید سکه', 'label' => '📦 تنظیم پلن‌های خرید سکه', 'cat' => 'payment'],
        ['key' => '💳 تنظیم کارت به کارت', 'label' => '💳 تنظیم کارت به کارت', 'cat' => 'payment'],
        ['key' => '🔄 وضعیت کارت به کارت', 'label' => '🔄 وضعیت کارت به کارت', 'cat' => 'payment'],
        ['key' => '🔵 تنظیم مرچنت زیبال', 'label' => '🔵 تنظیم مرچنت زیبال', 'cat' => 'payment'],
        ['key' => '🌐 تنظیم اتصال زیبال', 'label' => '🌐 تنظیم اتصال زیبال', 'cat' => 'payment'],
        ['key' => '🔄 وضعیت زیبال', 'label' => '🔄 وضعیت زیبال', 'cat' => 'payment'],
        ['key' => '💳 کارت به کارت', 'label' => '💳 کارت به کارت', 'cat' => 'payment'],
        ['key' => '🔵 پرداخت آنلاین زیبال', 'label' => '🔵 پرداخت آنلاین زیبال', 'cat' => 'payment'],
        ['key' => 'پرداخت آنلاین', 'label' => 'پرداخت آنلاین', 'cat' => 'payment'],
        ['key' => '✅ تایید', 'label' => '✅ تایید رسید', 'cat' => 'payment'],
        ['key' => '❌ رد', 'label' => '❌ رد رسید', 'cat' => 'payment'],

        ['key' => 'دکمه لایک زیر پست', 'label' => '❤️ دکمه لایک زیر پست', 'cat' => 'post'],
        ['key' => 'دکمه چالش لایک زیر پست', 'label' => '💙 دکمه چالش لایک زیر پست', 'cat' => 'post'],
        ['key' => '✅ بررسی عضویت', 'label' => '✅ بررسی عضویت', 'cat' => 'post'],

        ['key' => '↩️ بازگشت', 'label' => '↩️ بازگشت', 'cat' => 'other'],
        ['key' => 'لغو', 'label' => 'لغو', 'cat' => 'other'],
    ];
}

function buttonStyleTargetByIndex(int $idx): ?array
{
    $targets = buttonStyleAdminTargets();
    return $targets[$idx] ?? null;
}

function currentButtonStyleForKey(string $key): ?string
{
    $custom = setting('button_styles', []);
    if (is_array($custom) && array_key_exists($key, $custom)) {
        return normalizeButtonStyle((string)$custom[$key]);
    }
    $defaults = defaultButtonStyles();
    return isset($defaults[$key]) ? normalizeButtonStyle((string)$defaults[$key]) : null;
}

function adminButtonStylesKeyboard(): string
{
    $rows = [];
    $row = [];
    foreach (buttonStyleAdminCategories() as $cat => $title) {
        $row[] = ['text' => $title, 'callback_data' => 'bs_cat:' . $cat];
        if (count($row) === 2) { $rows[] = $row; $row = []; }
    }
    if ($row) $rows[] = $row;
    $enabled = (string)setting('button_style_enabled', '1') === '1';
    $rows[] = [['text' => $enabled ? '🔴 خاموش کردن رنگ‌ها' : '🟢 روشن کردن رنگ‌ها', 'callback_data' => 'bs_toggle']];
    $rows[] = [['text' => '♻️ بازگشت به رنگ‌های پیش‌فرض', 'callback_data' => 'bs_reset']];
    $rows[] = [['text' => '↩️ برگشت به پنل مدیریت', 'callback_data' => 'bs_admin']];
    return inline($rows);
}

function adminButtonStylesCategoryKeyboard(string $cat): string
{
    $rows = [];
    foreach (buttonStyleAdminTargets() as $idx => $target) {
        if (($target['cat'] ?? '') !== $cat) continue;
        $style = currentButtonStyleForKey((string)$target['key']);
        $rows[] = [[
            'text' => buttonStyleIcon($style) . ' ' . $target['label'] . ' — ' . buttonStyleFaName($style),
            'callback_data' => 'bs_pick:' . $idx,
        ]];
    }
    $rows[] = [['text' => '↩️ برگشت به دسته‌ها', 'callback_data' => 'bs_menu']];
    return inline($rows);
}

function adminButtonStyleColorKeyboard(int $idx): string
{
    $target = buttonStyleTargetByIndex($idx);
    $current = $target ? currentButtonStyleForKey((string)$target['key']) : null;
    $rows = [
        [
            ['text' => ($current === 'success' ? '✅ ' : '') . '🟢 سبز', 'callback_data' => 'bs_set:' . $idx . ':success'],
            ['text' => ($current === 'primary' ? '✅ ' : '') . '🔵 آبی', 'callback_data' => 'bs_set:' . $idx . ':primary'],
        ],
        [
            ['text' => ($current === 'danger' ? '✅ ' : '') . '🔴 قرمز', 'callback_data' => 'bs_set:' . $idx . ':danger'],
            ['text' => (($current === null || $current === '') ? '✅ ' : '') . '⚪️ ساده / بی‌رنگ', 'callback_data' => 'bs_set:' . $idx . ':none'],
        ],
    ];
    $cat = $target ? (string)$target['cat'] : '';
    $rows[] = [['text' => '↩️ برگشت به همین دسته', 'callback_data' => 'bs_cat:' . $cat]];
    return inline($rows);
}

function adminAskButtonStyles(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], null);
    $enabled = (string)setting('button_style_enabled', '1') === '1' ? 'روشن' : 'خاموش';
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => "🎨 تنظیم رنگ دکمه‌ها\n\nاز دسته‌بندی زیر، دکمه موردنظر را انتخاب کن و بعد رنگش را بزن.\n\nوضعیت رنگی‌سازی: {$enabled}",
        'reply_markup' => adminButtonStylesKeyboard(),
    ]);
}

function handleButtonStyleCallback(array $cb): void
{
    $data = (string)($cb['data'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    $cbId = (string)($cb['id'] ?? '');
    if (!isAdmin($userId)) {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'دسترسی ندارید.', 'show_alert' => true]);
        return;
    }
    tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
    if (!$message) return;
    $chatId = (int)$message['chat']['id'];
    $messageId = (int)$message['message_id'];

    if ($data === 'bs_menu') {
        $enabled = (string)setting('button_style_enabled', '1') === '1' ? 'روشن' : 'خاموش';
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🎨 تنظیم رنگ دکمه‌ها\n\nیک دسته را انتخاب کن.\n\nوضعیت رنگی‌سازی: {$enabled}",
            'reply_markup' => adminButtonStylesKeyboard(),
        ]);
        return;
    }

    if ($data === 'bs_admin') {
        tg('deleteMessage', ['chat_id' => $chatId, 'message_id' => $messageId]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '🛠 پنل مدیریت', 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($data === 'bs_toggle') {
        $cur = (string)setting('button_style_enabled', '1') === '1';
        set_setting('button_style_enabled', $cur ? '0' : '1');
        $enabled = $cur ? 'خاموش' : 'روشن';
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🎨 تنظیم رنگ دکمه‌ها\n\nوضعیت رنگی‌سازی {$enabled} شد.\n\nبرای تغییر رنگ یک دسته را انتخاب کن.",
            'reply_markup' => adminButtonStylesKeyboard(),
        ]);
        return;
    }

    if ($data === 'bs_reset') {
        set_setting('button_styles', defaultButtonStyles());
        set_setting('button_style_enabled', '1');
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "✅ رنگ دکمه‌ها به حالت پیش‌فرض برگشت.\n\nبرای تغییر دوباره، یک دسته را انتخاب کن.",
            'reply_markup' => adminButtonStylesKeyboard(),
        ]);
        return;
    }

    if (str_starts_with($data, 'bs_cat:')) {
        $cat = substr($data, strlen('bs_cat:'));
        $categories = buttonStyleAdminCategories();
        if (!isset($categories[$cat])) return;
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🎨 {$categories[$cat]}\n\nدکمه‌ای که می‌خواهی رنگش تغییر کند را انتخاب کن:",
            'reply_markup' => adminButtonStylesCategoryKeyboard($cat),
        ]);
        return;
    }

    if (str_starts_with($data, 'bs_pick:')) {
        $idx = (int)substr($data, strlen('bs_pick:'));
        $target = buttonStyleTargetByIndex($idx);
        if (!$target) return;
        $style = currentButtonStyleForKey((string)$target['key']);
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🎨 انتخاب رنگ\n\nدکمه: {$target['label']}\nرنگ فعلی: " . buttonStyleIcon($style) . ' ' . buttonStyleFaName($style) . "\n\nرنگ جدید را انتخاب کن:",
            'reply_markup' => adminButtonStyleColorKeyboard($idx),
        ]);
        return;
    }

    if (str_starts_with($data, 'bs_set:')) {
        $parts = explode(':', $data);
        $idx = (int)($parts[1] ?? -1);
        $styleRaw = (string)($parts[2] ?? 'none');
        $target = buttonStyleTargetByIndex($idx);
        if (!$target) return;
        $style = $styleRaw === 'none' ? '' : normalizeButtonStyle($styleRaw);
        if ($styleRaw !== 'none' && $style === null) return;
        $custom = setting('button_styles', []);
        if (!is_array($custom)) $custom = [];
        $key = (string)$target['key'];
        $custom[$key] = $style;
        if ($key === 'دکمه لایک زیر پست') $custom['LIKE_BUTTON'] = $style;
        if ($key === 'دکمه چالش لایک زیر پست') $custom['CHALLENGE_LIKE_BUTTON'] = $style;
        set_setting('button_style_enabled', '1');
        set_setting('button_styles', $custom);
        $saved = $style === '' ? 'ساده / بی‌رنگ' : buttonStyleFaName($style);
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "✅ رنگ ذخیره شد.\n\nدکمه: {$target['label']}\nرنگ جدید: {$saved}\n\nمی‌توانی دکمه دیگری را هم از همین دسته انتخاب کنی.",
            'reply_markup' => adminButtonStylesCategoryKeyboard((string)$target['cat']),
        ]);
        return;
    }
}

function adminNormalizeStyleWord(string $word): ?string
{
    $w = trim(function_exists('mb_strtolower') ? mb_strtolower($word, 'UTF-8') : strtolower($word));
    return match ($w) {
        'سبز', 'green', 'success' => 'success',
        'آبی', 'ابي', 'ابی', 'blue', 'primary' => 'primary',
        'قرمز', 'red', 'danger' => 'danger',
        'معمولی', 'معمولي', 'عادی', 'عادي', 'default', 'normal', 'none', 'off' => '',
        default => null,
    };
}

function adminStats(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    $users = (int)db()->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
    $items = (int)db()->query('SELECT COUNT(*) c FROM items')->fetch()['c'];
    $votes = (int)db()->query('SELECT COUNT(*) c FROM votes')->fetch()['c'];
    $speed = (int)db()->query('SELECT COUNT(*) c FROM speed_challenges')->fetch()['c'];
    $jobs = (int)db()->query("SELECT COUNT(*) c FROM auto_like_jobs WHERE status = 'active'")->fetch()['c'];
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "📊 آمار ربات\n\n👥 کاربران: {$users}\n📌 آیتم‌ها: {$items}\n❤️ رأی/لایک واقعی: {$votes}\n🏃 چالش سرعتی: {$speed}\n🤍 خودکار لایک فعال: {$jobs}", 'reply_markup' => adminKeyboard()]);
}

function adminAskPrices(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_prices');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "💰 تنظیم قیمت‌ها\n\nقیمت‌ها را به این شکل بفرست:\nlike=2\npoll=2\nchallenge_like=3\nspeed=0\nauto_like=2", 'reply_markup' => cancelKeyboard()]);
}


function buttonVisibilityAdminCategories(): array
{
    return buttonStyleAdminCategories();
}

function buttonVisibilityAdminTargets(): array
{
    return array_values(array_filter(buttonStyleAdminTargets(), function ($t) {
        $key = (string)($t['key'] ?? '');
        return !in_array($key, ['دکمه لایک زیر پست', 'دکمه چالش لایک زیر پست', 'LIKE_BUTTON', 'CHALLENGE_LIKE_BUTTON'], true);
    }));
}

function buttonVisibilityTargetByIndex(int $idx): ?array
{
    $targets = buttonVisibilityAdminTargets();
    return $targets[$idx] ?? null;
}

function buttonVisibilityKeyboard(): string
{
    $rows = [];
    foreach (buttonVisibilityAdminCategories() as $cat => $title) {
        $rows[] = [['text' => $title, 'callback_data' => 'bd_cat:' . $cat]];
    }
    $rows[] = [['text' => '♻️ روشن کردن همه دکمه‌ها', 'callback_data' => 'bd_reset']];
    $rows[] = [['text' => '↩️ بازگشت', 'callback_data' => 'bd_back_admin']];
    return inline($rows);
}

function buttonVisibilityListKeyboard(string $cat): string
{
    $rows = [];
    foreach (buttonVisibilityAdminTargets() as $idx => $target) {
        if (($target['cat'] ?? '') !== $cat) continue;
        $key = (string)$target['key'];
        $disabled = isButtonDisabled($key);
        $rows[] = [[
            'text' => ($disabled ? '🔴 خاموش — ' : '🟢 روشن — ') . $target['label'],
            'callback_data' => 'bd_toggle:' . $idx . ':' . $cat,
        ]];
    }
    if (!$rows) $rows[] = [['text' => 'در این دسته دکمه‌ای ثبت نشده', 'callback_data' => 'bd_noop']];
    $rows[] = [['text' => '↩️ دسته‌بندی‌ها', 'callback_data' => 'bd_home']];
    return inline($rows);
}

function adminButtonVisibilityText(): string
{
    $disabled = disabledButtons();
    $count = count($disabled);
    $list = $disabled ? implode("\n", array_map(fn($b) => '• ' . $b, $disabled)) : 'همه دکمه‌ها روشن هستند.';
    return "🚫 خاموش/روشن دکمه‌ها\n\nاز دسته‌بندی زیر دکمه موردنظر را انتخاب کن. دکمه خاموش از منوهای کاربران حذف می‌شود و اگر کاربر همان متن را دستی ارسال کند، اجرا نمی‌شود.\n\nتعداد دکمه‌های خاموش: {$count}\n{$list}";
}

function adminAskButtonVisibility(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], null);
    tg('sendMessage', [
        'chat_id' => $chatId,
        'text' => adminButtonVisibilityText(),
        'reply_markup' => buttonVisibilityKeyboard(),
    ]);
}

function handleButtonVisibilityCallback(array $cb, string $data): void
{
    $cbId = (string)($cb['id'] ?? '');
    $from = $cb['from'] ?? [];
    $userId = (int)($from['id'] ?? 0);
    $message = $cb['message'] ?? null;
    if (!isAdmin($userId)) { tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'دسترسی ندارید.', 'show_alert' => true]); return; }
    if (!$message) return;
    $chatId = (int)$message['chat']['id'];
    $messageId = (int)$message['message_id'];

    if ($data === 'bd_noop') { tg('answerCallbackQuery', ['callback_query_id' => $cbId]); return; }
    if ($data === 'bd_back_admin') {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        tg('deleteMessage', ['chat_id' => $chatId, 'message_id' => $messageId]);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '🛠 پنل مدیریت', 'reply_markup' => adminKeyboard()]);
        return;
    }
    if ($data === 'bd_home') {
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        tg('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => adminButtonVisibilityText(), 'reply_markup' => buttonVisibilityKeyboard()]);
        return;
    }
    if ($data === 'bd_reset') {
        set_setting('disabled_buttons', []);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => 'همه دکمه‌ها روشن شد.']);
        tg('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => adminButtonVisibilityText(), 'reply_markup' => buttonVisibilityKeyboard()]);
        return;
    }
    if (str_starts_with($data, 'bd_cat:')) {
        $cat = substr($data, 7);
        $categories = buttonVisibilityAdminCategories();
        if (!isset($categories[$cat])) return;
        tg('answerCallbackQuery', ['callback_query_id' => $cbId]);
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🚫 {$categories[$cat]}\n\nروی هر دکمه بزن تا خاموش یا روشن شود:",
            'reply_markup' => buttonVisibilityListKeyboard($cat),
        ]);
        return;
    }
    if (str_starts_with($data, 'bd_toggle:')) {
        $parts = explode(':', $data);
        $idx = (int)($parts[1] ?? -1);
        $cat = (string)($parts[2] ?? 'main');
        $target = buttonVisibilityTargetByIndex($idx);
        if (!$target) return;
        $key = (string)$target['key'];
        $disabled = disabledButtons();
        if (in_array($key, $disabled, true)) {
            $disabled = array_values(array_diff($disabled, [$key]));
            $status = 'روشن شد';
        } else {
            $disabled[] = $key;
            $disabled = array_values(array_unique($disabled));
            $status = 'خاموش شد';
        }
        set_setting('disabled_buttons', $disabled);
        tg('answerCallbackQuery', ['callback_query_id' => $cbId, 'text' => $status]);
        $categories = buttonVisibilityAdminCategories();
        tg('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => "🚫 " . ($categories[$cat] ?? 'دکمه‌ها') . "\n\nروی هر دکمه بزن تا خاموش یا روشن شود:",
            'reply_markup' => buttonVisibilityListKeyboard($cat),
        ]);
        return;
    }
}

function adminAskNewUserCoins(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) return;
    setStep((int)$user['id'], 'admin_new_user_coins');
    $current = (int)setting('new_user_coins', 5);
    tg('sendMessage', ['chat_id' => $chatId, 'text' => "🎁 تنظیم سکه اولیه کاربران جدید\n\nمقدار فعلی: {$current} سکه\nعدد جدید را ارسال کنید؛ مثال: 10", 'reply_markup' => cancelKeyboard()]);
}

function adminAskReferral(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_referral');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '🎁 مقدار سکه زیرمجموعه‌گیری را عددی ارسال کنید.', 'reply_markup' => cancelKeyboard()]);
}

function adminAskAddChannel(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_add_channel');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '➕ کانال‌های جوین اجباری عمومی را ارسال کنید؛ هرکدام در یک خط.', 'reply_markup' => cancelKeyboard()]);
}

function adminAskRemoveChannel(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_remove_channel');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '➖ کانال‌هایی که باید حذف شوند را ارسال کنید؛ هرکدام در یک خط.', 'reply_markup' => cancelKeyboard()]);
}

function adminListChannels(int $chatId, array $user): void
{
    if (!isAdmin((int)$user['id'])) { showUserChannels($chatId, $user); return; }
    $globalChannels = getGlobalJoinChannels();
    $globalTxt = $globalChannels ? implode("\n", $globalChannels) : 'کانالی ثبت نشده است.';

    $stmt = db()->query("SELECT id, first_name, username, default_channel, default_channel_id, default_channel_title, force_channels FROM users WHERE default_channel IS NOT NULL AND default_channel <> '' ORDER BY id DESC LIMIT 200");
    $rows = $stmt ? $stmt->fetchAll() : [];
    $userLines = [];

    foreach ($rows as $row) {
        $name = trim((string)($row['first_name'] ?? ''));
        $username = trim((string)($row['username'] ?? ''));
        $owner = $name !== '' ? $name : 'کاربر';
        if ($username !== '') $owner .= ' @' . ltrim($username, '@');

        $title = trim((string)($row['default_channel_title'] ?? ''));
        if ($title === '') $title = 'بدون عنوان';
        $channel = trim((string)($row['default_channel'] ?? ''));
        $channelId = trim((string)($row['default_channel_id'] ?? ''));
        $force = json_decode((string)($row['force_channels'] ?? ''), true);
        $autoChannels = defaultForceChannelsFromUser($row);
        $forceOn = ($autoChannels || (is_array($force) && count($force) > 0)) ? 'خودکار روشن' : 'نیازمند تنظیم کانال';

        $userLines[] = "• {$title}
  سازنده: {$owner} | آیدی کاربر: {$row['id']}
  کانال: {$channel}
  شناسه کانال: {$channelId}
  قفل شرکت در چالش‌ها: {$forceOn}";
    }

    $userCount = count($userLines);
    $globalCount = count($globalChannels);
    $usersTxt = $userLines ? implode("\n\n", $userLines) : 'هنوز هیچ کاربری کانال خودش را ثبت نکرده است.';
    $text = "📋 لیست کانال‌ها

تعداد کانال عمومی: {$globalCount}
تعداد کانال ثبت‌شده کاربران: {$userCount}


" .
        "🔐 کانال‌های جوین اجباری عمومی:
{$globalTxt}

" .
        "📢 کانال‌های ثبت‌شده کاربران:
{$usersTxt}";

    $tooLong = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') > 3900 : strlen($text) > 3900;
    if ($tooLong) {
        $shortUsers = $userLines ? implode("\n\n", array_slice($userLines, 0, 35)) : $usersTxt;
        $text = "📋 لیست کانال‌ها

" .
            "🔐 کانال‌های جوین اجباری عمومی:
{$globalTxt}

" .
            "📢 کانال‌های ثبت‌شده کاربران:
{$shortUsers}

برای جلوگیری از طولانی شدن پیام، فقط ۳۵ کانال آخر نمایش داده شد.";
    }

    tg('sendMessage', ['chat_id' => $chatId, 'text' => $text, 'reply_markup' => adminKeyboard()]);
}

function adminAskSponsor(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_sponsor');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '☕ متن اسپانسر را ارسال کنید.', 'reply_markup' => cancelKeyboard()]);
}

function adminAskBroadcast(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_broadcast');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '📣 پیام همگانی را ارسال کنید. فعلاً متن ساده پشتیبانی می‌شود.', 'reply_markup' => cancelKeyboard()]);
}

function adminAskAddAdmin(int $chatId, array $user): void
{
    setStep((int)$user['id'], 'admin_add_admin');
    tg('sendMessage', ['chat_id' => $chatId, 'text' => '➕ آیدی عددی ادمین جدید را ارسال کنید.', 'reply_markup' => cancelKeyboard()]);
}

function handleAdminStep(array $message, array $user, string $step): void
{
    $chatId = (int)$message['chat']['id'];
    $userId = (int)$user['id'];
    $text = trim((string)($message['text'] ?? ''));

    if ($step === 'admin_prices') {
        $allowed = ['like','poll','challenge_like','speed','auto_like'];
        foreach (preg_split('/\R/u', $text) ?: [] as $line) {
            if (!str_contains($line, '=')) continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            if (in_array($k, $allowed, true) && preg_match('/^\d+$/', $v)) set_setting('price_' . $k, $v);
        }
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ قیمت‌ها ذخیره شد.', 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_new_user_coins') {
        if (!preg_match('/^\d+$/', $text)) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد معتبر ارسال کنید.']); return; }
        set_setting('new_user_coins', $text);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ سکه اولیه کاربران جدید ذخیره شد.', 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_referral') {
        if (!preg_match('/^\d+$/', $text)) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد معتبر ارسال کنید.']); return; }
        set_setting('referral_reward', $text);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ سکه زیرمجموعه ذخیره شد.', 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_daily_rewards') {
        $rewards = [];
        foreach (preg_split('/\R/u', $text) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, '=')) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'فرمت هر خط باید مثل 2=3 باشد.']); return; }
            [$dice, $coins] = array_map('trim', explode('=', $line, 2));
            if (!preg_match('/^[1-6]$/', $dice) || !preg_match('/^\d+$/', $coins)) {
                tg('sendMessage', ['chat_id' => $chatId, 'text' => 'عدد تاس باید ۱ تا ۶ باشد و مقدار جایزه فقط عدد باشد.']);
                return;
            }
            $rewards[$dice] = (int)$coins;
        }
        for ($i = 1; $i <= 6; $i++) if (!array_key_exists((string)$i, $rewards)) $rewards[(string)$i] = 0;
        ksort($rewards);
        set_setting('daily_rewards', $rewards);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ جایزه روزانه ذخیره شد.\n\n" . dailyRewardsText(), 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_button_styles') {
        setStep($userId, null);
        tg('sendMessage', [
            'chat_id' => $chatId,
            'text' => '🎨 تنظیم رنگ دکمه‌ها به صورت دکمه‌ای انجام می‌شود. از منوی زیر استفاده کنید:',
            'reply_markup' => adminButtonStylesKeyboard(),
        ]);
        return;
    }

    if ($step === 'admin_add_channel') {
        $old = getGlobalJoinChannels();
        $new = array_values(array_unique(array_merge($old, normalizeChannels($text))));
        set_setting('global_join_channels', $new);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ کانال‌ها ذخیره شدند:\n" . implode("\n", $new), 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_remove_channel') {
        $remove = normalizeChannels($text);
        $old = getGlobalJoinChannels();
        $new = array_values(array_diff($old, $remove));
        set_setting('global_join_channels', $new);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ لیست جدید:\n" . ($new ? implode("\n", $new) : 'خالی'), 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_sponsor') {
        set_setting('sponsor_text', $text);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ متن اسپانسر ذخیره شد.', 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_broadcast') {
        setStep($userId, null);
        $ids = db()->query('SELECT id FROM users')->fetchAll();
        $ok = 0; $bad = 0;
        foreach ($ids as $row) {
            $res = tg('sendMessage', ['chat_id' => (int)$row['id'], 'text' => $text]);
            ($res['ok'] ?? false) ? $ok++ : $bad++;
            usleep(50000);
        }
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ پیام همگانی ارسال شد.\nموفق: {$ok}\nناموفق: {$bad}", 'reply_markup' => adminKeyboard()]);
        return;
    }

    if ($step === 'admin_coin_packages') {
        $packages = [];
        foreach (preg_split('/\R/u', $text) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!str_contains($line, '=')) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'فرمت هر خط باید مثل 100=90000 باشد.']); return; }
            [$coins, $amount] = array_map('trim', explode('=', $line, 2));
            if (!preg_match('/^[1-9][0-9]*$/', $coins) || !preg_match('/^[1-9][0-9]*$/', $amount)) {
                tg('sendMessage', ['chat_id' => $chatId, 'text' => 'مقادیر بسته‌ها باید عددی باشد. مثال: 100=90000']);
                return;
            }
            $packages[] = ['coins' => (int)$coins, 'amount' => (int)$amount];
        }
        if (!$packages) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'حداقل یک بسته وارد کنید.']); return; }
        set_setting('coin_packages', $packages);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ پلن‌های خرید سکه ذخیره شد.', 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    if ($step === 'admin_card_info') {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $text) ?: [])));
        if (count($lines) < 2) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => "حداقل شماره کارت و نام صاحب کارت را در دو خط ارسال کنید.\nمثال:\n6037990000000000\nنام صاحب کارت\nمتن دلخواه پرداخت"]);
            return;
        }
        set_setting('card_number', $lines[0]);
        set_setting('card_owner', $lines[1]);
        set_setting('card_text', $lines[2] ?? 'پس از واریز، رسید پرداخت را همینجا ارسال کنید.');
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ اطلاعات کارت به کارت ذخیره شد.', 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    if ($step === 'admin_zibal_merchant') {
        set_setting('zibal_merchant', trim($text));
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ مرچنت زیبال ذخیره شد.', 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    if ($step === 'admin_zibal_bridge') {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $text) ?: [])));
        if (count($lines) < 2) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => "آدرس فایل و کد امنیتی را در دو خط ارسال کنید.\nمثال:\nhttps://your-domain.ir/zibal_bridge.php\nCHANGE_THIS_SECRET"]);
            return;
        }
        $bridgeUrl = function_exists('normalizeBridgeUrl') ? normalizeBridgeUrl($lines[0]) : trim($lines[0]);
        set_setting('zibal_bridge_url', $bridgeUrl);
        set_setting('zibal_bridge_secret', $lines[1]);
        set_setting('zibal_bridge_enabled', '1');
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ اتصال زیبال ذخیره شد.', 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    if ($step === 'admin_zibal_bridge_ip') {
        $value = trim($text);
        if (in_array(mb_strtolower($value), ['حذف', 'پاک', 'delete', 'remove', '0'], true)) {
            set_setting('zibal_bridge_ip', '');
            setStep($userId, null);
            tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ IP دامنه زیبال حذف شد.', 'reply_markup' => paymentAdminKeyboard()]);
            return;
        }
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => "IP وارد شده معتبر نیست.\nمثال صحیح: 1.2.3.4\nبرای حذف بنویس: حذف"]);
            return;
        }
        set_setting('zibal_bridge_ip', $value);
        set_setting('zibal_bridge_enabled', '1');
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => '✅ IP دامنه زیبال ذخیره شد. حالا تست اتصال زیبال را بزن.', 'reply_markup' => paymentAdminKeyboard()]);
        return;
    }

    if ($step === 'admin_adjust_coins') {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $text) ?: [])));
        if (count($lines) < 2 || !preg_match('/^-?[0-9]+$/', $lines[1])) {
            tg('sendMessage', ['chat_id' => $chatId, 'text' => "فرمت صحیح:\nآیدی عددی کاربر\nمقدار سکه\n\nبرای کم کردن سکه، مقدار را منفی بفرستید؛ مثال: -20"]);
            return;
        }
        $targetId = (int)$lines[0];
        $amount = (int)$lines[1];
        if ($targetId <= 0 || $amount === 0) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'آیدی یا مقدار سکه معتبر نیست.']); return; }
        $target = getUser($targetId);
        if (!$target) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'کاربر در دیتابیس ربات پیدا نشد.']); return; }
        addCoins($targetId, $amount);
        $freshTarget = getUser($targetId) ?: $target;
        setStep($userId, null);
        $sign = $amount > 0 ? '+' . $amount : (string)$amount;
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ موجودی کاربر تغییر کرد.\n🆔 کاربر: {$targetId}\n🔢 تغییر: {$sign}\n💰 موجودی جدید: {$freshTarget['coins']} سکه", 'reply_markup' => adminKeyboard()]);
        tg('sendMessage', ['chat_id' => $targetId, 'text' => "💰 موجودی سکه شما به‌روزرسانی شد.\n🔢 تغییر: {$sign}\n💰 موجودی جدید: {$freshTarget['coins']} سکه"]);
        return;
    }

    if ($step === 'admin_add_admin') {
        $newAdmin = (int)$text;
        if ($newAdmin <= 0) { tg('sendMessage', ['chat_id' => $chatId, 'text' => 'آیدی عددی معتبر نیست.']); return; }
        db()->prepare('INSERT IGNORE INTO admins (user_id, created_by, created_at) VALUES (?, ?, ?)')->execute([$newAdmin, $userId, date('Y-m-d H:i:s')]);
        setStep($userId, null);
        tg('sendMessage', ['chat_id' => $chatId, 'text' => "✅ ادمین {$newAdmin} اضافه شد.", 'reply_markup' => adminKeyboard()]);
    }
}
