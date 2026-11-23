<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Commands;

use DevDasher\XShare2Bot\Models\Post;
use DevDasher\XShare2Bot\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StatsCommand
{
    public function settings_stats(Nutgram $bot)
    {
        $bot->editMessageText(
            text: $bot->getText('descriptions.settings->stats', [
                ':users_count' => User::count(),
                ':posts_count' => Post::count(),
            ]),
            reply_markup: $bot->getKeyboard('settings->stats'),
        );
    }
}