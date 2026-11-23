<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Middlewares;

use SergiX44\Nutgram\Middleware\Link;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseConnectionMiddleware
{
    public function __invoke(Nutgram $bot, Link $next)
    {
        $capsule = new Capsule;
        $capsule->addConnection(config('database'));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $next($bot);
    }
}