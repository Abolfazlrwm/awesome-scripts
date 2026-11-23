<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

namespace DevDasher\XShare2Bot\Commands;

use DevDasher\XShare2Bot\Models\Post;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class PostCommand
{
    public function settings_posts(Nutgram $bot)
    {
        $bot->editMessageText(
            text: $bot->getText('descriptions.settings->posts'),
            reply_markup: $bot->getKeyboard('settings->posts'),
        );
    }

    public function settings_posts_post(Nutgram $bot, $id)
    {
        $post = Post::find($id);
        $bot->editMessageText(
            text: $bot->getText('descriptions.settings->posts->post', [
                ':post_id' => $post->post_id,
                ':views_count' => $post->views,
            ]),
            reply_markup: $bot->getKeyboard('settings->posts->post', compact('post')),
            parse_mode: ParseMode::MARKDOWN_LEGACY,
        );
    }

    public function settings_posts_post_delete(Nutgram $bot, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $bot->answerCallbackQuery(
                text: $bot->getText('errors.post_not_found'),
                show_alert: true,
                cache_time: 10,
            );
        }
        $ok = $post->delete();
        if (!$ok) {
            return $bot->answerCallbackQuery(
                text: $bot->getText('errors.unknown_error'),
                show_alert: true,
                cache_time: 10,
            );
        }
        $this->settings_posts($bot);
    }
}