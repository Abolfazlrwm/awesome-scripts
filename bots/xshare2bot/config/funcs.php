<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

use Illuminate\Support\Arr;

function config(string $key = null): mixed {
    static $config;
    if (!$config) {
        $config = require(__DIR__.'/config.php');
    }
    return Arr::get($config, $key);
}
