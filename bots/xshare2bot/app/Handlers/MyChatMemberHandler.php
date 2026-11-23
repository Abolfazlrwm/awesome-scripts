<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Handlers;

use DevDasher\XShare2Bot\Commands\ChannelCommand;
use DevDasher\XShare2Bot\Models\Channel;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatMemberStatus;
use \SergiX44\Nutgram\Telegram\Types\User\User;

class MyChatMemberHandler
{
    public function __invoke(Nutgram $bot)
    {
        $newChatMember = $bot->chatMember()->new_chat_member;
        if ($newChatMember->status === ChatMemberStatus::ADMINISTRATOR) {
            $chat = $bot->getChat($bot->chatId());
            $chatId = preg_replace('/-100|-/', '', $chat->id);
            $channel = Channel::create([
                'channel_id' => $chatId,
                'title' => $chat->title,
                'username' => $chat->username,
                'primary_link' => $chat->invite_link,
            ]);
            $bot->sendMessage(
                text: $bot->getText('messages.channel_added', [
                    ':title' => $channel->title,
                ]),
                chat_id: config('bot.owner.id'),
            );
        } elseif ($newChatMember->status === ChatMemberStatus::KICKED) {
            $chatId = preg_replace('/-100|-/', '', $bot->chatId());
            $channel = Channel::where(['channel_id' => $chatId])->first();
            if (!$channel) {
                return;
            }
            $ok = $channel->delete();
            if ($ok) {
                $bot->sendMessage(
                    text: $bot->getText('messages.bot_kicked_from_channel', [
                        ':title' => $channel->title,
                    ]),
                    chat_id: config('bot.owner.id'),
                );
            }
        }
    }
}