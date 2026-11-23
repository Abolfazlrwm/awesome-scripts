<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

use DevDasher\XShare2Bot\Models\Channel;
use DevDasher\XShare2Bot\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use SergiX44\Nutgram\Telegram\Properties\ChatMemberStatus;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

return [
    'start' => fn() => ReplyKeyboardMarkup::make(true)->addRow(
        KeyboardButton::make($this->getText('buttons.settings')),
        KeyboardButton::make($this->getText('buttons.new_post')),
    ),
    
    'settings' => fn() => InlineKeyboardMarkup::make()->addRow(
        InlineKeyboardButton::make(
            text: $this->getText('buttons.stats'),
            callback_data: 'sett/stats',
        ),
    )->addRow(
        InlineKeyboardButton::make(
            text: $this->getText('buttons.channels'),
            callback_data: 'sett/channels',
        ),
    )->addRow(
        InlineKeyboardButton::make(
            text: $this->getText('buttons.posts'),
            callback_data: 'sett/posts',
        ),
    ),

    'settings->stats' => fn() => InlineKeyboardMarkup::make()->addRow(
        InlineKeyboardButton::make(
            text: $this->getText('buttons.back'),
            callback_data: 'sett'
        ),
    ),

    'settings->channels' => function() {
        $keyboard = InlineKeyboardMarkup::make();
        $channels = Channel::get();
        if ($channels->isNotEmpty()) {
            foreach ($channels as $channel) {
                $keyboard->addRow(
                    InlineKeyboardButton::make(
                        text: $channel->title,
                        callback_data: "sett/channels/{$channel->id}"
                    ),
                );
            }
        }
        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: $this->getText('buttons.back'),
                callback_data: 'sett'
            ),
            InlineKeyboardButton::make(
                text: $this->getText('buttons.add'),
                url: "https://t.me/".config('bot.username')."?startchannel&admin=invite_users",
            ),
        );
        return $keyboard;
    },

    'settings->channels->channel' => function(Channel $channel) {
        $keyboard = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make(
                text: $this->getText('buttons.back'),
                callback_data: 'sett/channels'
            ),
            InlineKeyboardButton::make(
                text: $this->getText('buttons.delete'),
                callback_data: "sett/channels/{$channel->id}/delete"
            ),
        );
        return $keyboard;
    },

    'settings->posts' => function() {
        $keyboard = InlineKeyboardMarkup::make();
        $posts = Post::orderBy('id', 'DESC')->limit(10)->get();
        if ($posts->isNotEmpty()) {
            foreach ($posts as $post) {
                $keyboard->addRow(
                    InlineKeyboardButton::make(
                        text: $post->post_id,
                        callback_data: "sett/posts/{$post->id}"
                    )
                );
            }
        }
        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: $this->getText('buttons.back'),
                callback_data: 'sett'
            ),
        );
        return $keyboard;
    },

    'settings->posts->post' => function(Post $post) {
        $keyboard = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make(
                text: $this->getText('buttons.back'),
                callback_data: 'sett/posts'
            ),
            InlineKeyboardButton::make(
                text: $this->getText('buttons.delete'),
                callback_data: "sett/posts/{$post->id}/delete"
            ),
        );
        return $keyboard;
    },

    'start->post->get' => function(int $id, string $postId) {
        $channels = Channel::get();
        $requiredChannels = [];
        foreach ($channels as $channel) {
            $chatMember = $this->getChatMember(
                chat_id: "-100{$channel->channel_id}",
                user_id: $this->userId(),
            );
            if (!in_array($chatMember->status, [
                ChatMemberStatus::ADMINISTRATOR,
                ChatMemberStatus::CREATOR,
                ChatMemberStatus::MEMBER
            ])) {
                $requiredChannels[] = $channel;
            }
        }
        if (!$requiredChannels) {
            return null;
        }
        $keyboard = InlineKeyboardMarkup::make();
        foreach ($requiredChannels as $channel) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $channel->title,
                    url: $channel->primary_link,
                ),
            );
        }
        $shareLink = "t.me/".config('bot.username')."?start=P{$id}-{$postId}";
        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: $this->getText('buttons.check_membership'),
                url: $shareLink,
            ),
        );
        return $keyboard;
    }
];