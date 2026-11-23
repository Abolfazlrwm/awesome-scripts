<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Conversations;

use DevDasher\XShare2Bot\Models\Post;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\MessageType;

class NewPostConversation extends Conversation
{
    public function start(Nutgram $bot)
    {
        $bot->sendMessage($bot->getText('messages.send_your_post'));
        $this->next('savePost');
    }

    public function savePost(Nutgram $bot)
    {
        $message = $bot->message();
        $messageType = $message?->getType();
        $allowedTypes = [
            MessageType::TEXT,
            MessageType::ANIMATION,
            MessageType::AUDIO,
            MessageType::DOCUMENT,
            MessageType::PHOTO,
            MessageType::STICKER,
            MessageType::VIDEO,
            MessageType::VIDEO_NOTE,
            MessageType::VOICE,
        ];
        if (!in_array($messageType, $allowedTypes)) {
            return $bot->sendMessage(
                text: $bot->getText('errors.not_allowed_message_type'),
                reply_to_message_id: $bot->messageId(),
            );
        }
        $post = Post::create([
            'post_id' => bin2hex(random_bytes(6)),
            'details' => serialize($message),
        ]);
        $shareLink = "t.me/".config('bot.username')."?start=P{$post->id}-{$post->post_id}";
        $bot->sendMessage(
            text: $bot->getText('messages.post_saved', [
                ':share_link' => $shareLink,
            ]),
            reply_to_message_id: $bot->messageId(),
        );
    }
}