<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Commands;

use SergiX44\Nutgram\Nutgram;

class SettingsCommand
{
    public function __invoke(Nutgram $bot)
    {
        $bot->{$bot->isCallbackQuery() ? 'editMessageText' : 'sendMessage'}(
            text: $bot->getText('descriptions.settings'),
            reply_markup: $bot->getKeyboard('settings'),
        );
    }
}