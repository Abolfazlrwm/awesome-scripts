<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Handlers;

use DevDasher\XShare2Bot\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

class FallbackHandler
{
    public function __invoke(Nutgram $bot)
    {
        $command = new StartCommand($bot->getText('errors.unknown_command'));
        call_user_func($command, $bot);
    }

    public function callbackQuery(Nutgram $bot)
    {
        $bot->answerCallbackQuery(
            text: $bot->getText('errors.unknown_command'),
            show_alert: true,
            cache_time: 10,
        );
    }
}