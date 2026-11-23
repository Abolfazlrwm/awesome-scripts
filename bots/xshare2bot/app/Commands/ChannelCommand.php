<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Commands;

use DevDasher\XShare2Bot\Models\Channel;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class ChannelCommand
{
    public function settings_channels(Nutgram $bot)
    {
        $bot->editMessageText(
            text: $bot->getText('descriptions.settings->channels'),
            reply_markup: $bot->getKeyboard('settings->channels'),
        );
    }

    public function settings_channels_channel(Nutgram $bot, $id)
    {
        $channel = Channel::find($id);
        $bot->editMessageText(
            text: $bot->getText('descriptions.settings->channels->channel', [
                ':channel_id' => $channel->channel_id,
            ]),
            reply_markup: $bot->getKeyboard('settings->channels->channel', compact('channel')),
            parse_mode: ParseMode::MARKDOWN_LEGACY,
        );
    }

    public function settings_channels_channel_delete(Nutgram $bot, $id)
    {
        $channel = Channel::find($id);
        if (!$channel) {
            return $bot->answerCallbackQuery(
                text: $bot->getText('errors.channel_not_found'),
                show_alert: true,
                cache_time: 10,
            );
        }
        $bot->leaveChat("-100{$channel->channel_id}");
        $ok = $channel->delete();
        if (!$ok) {
            return $bot->answerCallbackQuery(
                text: $bot->getText('errors.unknown_error'),
                show_alert: true,
                cache_time: 10,
            );
        }
        $this->settings_channels($bot);
    }
}
