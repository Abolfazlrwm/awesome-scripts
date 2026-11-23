<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Nutgram;

use Illuminate\Support\Arr;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class CustomMethods
{
    public function getText()
    {
        return function(string $key, array $values = []): ?string {
            static $texts;
            if (!$texts) {
                $texts = require(__DIR__.'/../../config/texts.php');
            }
            $text = Arr::get($texts, $key);
            if (!is_string($text)) {
                return null;
            }
            return strtr($text, $values);
        };
    }

    public function getKeyboard()
    {
        return function(string $key, array $args = []): null|ReplyKeyboardMarkup|InlineKeyboardMarkup {
            static $keyboards;
            if (!$keyboards) {
                $keyboards = require(__DIR__.'/../../config/keyboards.php');
            }
            $keyboard = Arr::get($keyboards, $key);
            if (!$keyboard) {
                return null;
            }
            return $keyboard(...$args);
        };
    }
}