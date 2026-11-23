<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Middlewares;

use DevDasher\XShare2Bot\Models\User;
use SergiX44\Nutgram\Middleware\Link;
use SergiX44\Nutgram\Nutgram;

class CollectUserMiddleware
{
    public function __invoke(Nutgram $bot, Link $next)
    {
        if ($bot->user()->is_bot) {
            return;
        }
        $user = User::updateOrCreate([
            'user_id' => $bot->userId(),
        ], [
            'first_name' => $bot->user()->first_name,
            'last_name' => $bot->user()->last_name,
            'username' => $bot->user()->username,
        ]);
        if (!$user) {
            return $bot->sendMessage("Error while storing user info!");
        }
        $bot->set('user', $user);
        $next($bot);
    }
}