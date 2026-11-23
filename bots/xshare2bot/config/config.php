<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

use SergiX44\Nutgram\Telegram\Properties\UpdateType;

return [
    'bot' => [
        'id' => 111,
        'token' => 'token',
        'username' => 'usernamebot',
        'allowed_updates' => [
            UpdateType::MESSAGE,
            UpdateType::CALLBACK_QUERY,
            UpdateType::MY_CHAT_MEMBER,
        ],
        'owner' => [
            'id' => 222,
            'username' => null,
        ],
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'xshare2bot',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'prefix' => '',
        'options' => [
            PDO::ATTR_PERSISTENT => true,
        ],
    ],
];
