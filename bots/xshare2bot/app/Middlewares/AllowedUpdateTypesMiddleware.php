<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Middlewares;

use SergiX44\Nutgram\Middleware\Link;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;

class AllowedUpdateTypesMiddleware
{
    public function __invoke(Nutgram $bot, Link $next)
    {
        $allowedUpdates = config('bot.allowed_updates');
        $updateType = $bot->update()->getType();
        if (!in_array($updateType, $allowedUpdates)) {
            return;
        }
        $next($bot);
    }
}