<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Exceptions;

use DevDasher\XShare2Bot\Models\Channel;
use SergiX44\Nutgram\Exception\ApiException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class BotWasKickedFromChannelException extends ApiException
{
    public static ?string $pattern = '.*bot was kicked from the channel.*';

    public function __invoke(Nutgram $bot, TelegramException $e)
    {
        // ....
    }
}