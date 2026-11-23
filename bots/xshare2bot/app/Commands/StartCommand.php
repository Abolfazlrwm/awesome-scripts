<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Commands;

use DevDasher\XShare2Bot\Models\Post;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\MessageType;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class StartCommand
{
    public function __construct(public ?string $text = null)
    {
    }
     
    public function __invoke(Nutgram $bot)
    {
        $user = $bot->get('user');
        if ($user->isOwner()) {
            $bot->sendMessage(
                text: $this->text ?? $bot->getText('descriptions.start'),
                reply_markup: $bot->getKeyboard('start'),
                disable_web_page_preview: true,
                parse_mode: ParseMode::MARKDOWN_LEGACY,
            );
        } else {
            $bot->sendMessage($bot->getText('messages.welcome_to_bot'));
        }
    }

    public function sendPost(Nutgram $bot, $id, $postId)
    {
        $keyboard = $bot->getKeyboard('start->post->get', compact('id', 'postId'));
        if ($keyboard) {
            return $bot->sendMessage(
                text: $bot->getText('messages.join_our_channels_first'),
                reply_to_message_id: $bot->messageId(),
                reply_markup: $keyboard,
            );
        }
        $post = Post::where([
            'id' => $id,
            'post_id' => $postId,
        ])->first();
        if (!$post) {
            return $bot->sendMessage(
                text: $bot->getText('errors.post_not_found'),
                reply_to_message_id: $bot->messageId(),
            );
        }
        /** @var Message $message */
        $message = unserialize($post->details);
        $ok = match ($message->getType()) {
            MessageType::TEXT => $bot->sendMessage(
                text: $message->text,
                entities: $message->entities,
                reply_markup: $message->reply_markup,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::ANIMATION => $bot->sendAnimation(
                animation: $message->animation->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::AUDIO => $bot->sendAudio(
                audio: $message->audio->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::DOCUMENT => $bot->sendDocument(
                document: $message->document->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::PHOTO => $bot->sendPhoto(
                photo: end($message->photo)->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::STICKER => $bot->sendSticker(
                sticker: $message->sticker->file_id,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::VIDEO => $bot->sendVideo(
                video: $message->video->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::VIDEO_NOTE => $bot->sendVideoNote(
                video_note: $message->video_note->file_id,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
            MessageType::VOICE => $bot->sendVoice(
                voice: $message->voice->file_id,
                caption: $message->caption,
                caption_entities: $message->caption_entities,
                reply_to_message_id: $bot->messageId(),
                allow_sending_without_reply: true,
            ),
        };
        if ($ok) {
            $post->update([
                'views' => ++$post->views,
            ]);
        }
    }
}