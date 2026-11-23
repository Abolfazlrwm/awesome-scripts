<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

return [
    'buttons' => [
        'stats' => '📊 آمار',
        'settings' => '⚙️ تنظیمات',
        'new_post' => '➕ پست جدید',
        'back' => 'برگشت 👈',
        'delete' => '📛 حذف',
        'channels' => '📣 کانال ها',
        'posts' => '🗂 پست ها',
        'add' => '➕ افزودن',
        'check_membership' => '📥 بررسی عضویت و نمایش پست',
    ],
    'errors' => [
        'unknown_command' => 'دستور ناشناخته!',
        'unknown_error' => 'خطای ناشناخته ای رخ داد!',
        'not_allowed_message_type' => 'نوع پست پشتیبانی نمیشود.',
        'post_not_found' => 'پست پیدا نشد!',
        'channel_not_found' => 'کانال پیدا نشد!',
    ],
    'messages' => [
        'welcome_to_bot' => 'به ربات ما خوشامدید',
        'send_your_post' => 'پست خود را ارسال کنید:',
        'post_saved' => "پست ذخیره شد\n\nلینک اشتراک گذاری:\n:share_link",
        'post_deleted' => 'پست با آیدی `:id` و شناسه `:post_id` حذف شد.',
        'channel_added' => "کانال :title با موفقیت اضافه شد\n\nبرای رفتن به تنظیمات آن، لطفا به بخش «کانال ها» مراجعه کنید",
        'bot_kicked_from_channel' => "بنظر میرسد ربات شما از کانال :title اخراج شده است! به همین علت این کانال از لیست کانال های شما در ربات حذف شد\n\nبرای بررسی عضویت اجباری کاربران، نیاز است ربات حتما در کانال تان ادمین باشد",
        'join_our_channels_first' => 'برای دریافت پست (فایل) مورد نظر، ابتدا عضو کانال های ما شوید و سپس دوباره تلاش کنید:',
    ],
    'descriptions' => [
        'start' => "به ربات XShare2Bot خوشامدید!\n\nربات XShare2Bot یک ربات ساده و کاربردی برای ذخیره و اشتراک گذاری پست های تلگرامی است که سورس کد آن با استفاده از کتابخانه [Nutgram](https://github.com/nutgram/nutgram) نوشته شده و بسیار منعطف، ماژولار و تمیز است. سورس کد این ربات تحت مجوز MIT منتشر شده و برای استفاده کاملا رایگان است.\n\nنسخه اولیه این سورس کد با اهداف آموزشی توسعه داده شده است و مسئولیت هرگونه سوء استفاده از آن در راستای خلاف قوانین کشور بر عهده کاربر مصرف کننده می باشد.\n\nتوسعه دهنده: devdasher\n\nلینک سورس کد:\nhttps://github.com/devdasher/xshare2bot",
        'settings' => 'تنظیمات ربات شما:',
        'settings->stats' => "تعداد کاربران: :users_count\nتعداد پست ها: :posts_count",
        'settings->channels' => 'با افزودن کانال، کاربران برای دریافت پست های شما باید در کانال های شما عضو باشند',
        'settings->channels->channel' => "شناسه کانال: :channel_id",
        'settings->posts' => 'در اینجا 10 پست آخر ذخیره شده را مشاهده میکنید، برای ورود به تنظیمات هر کدام روی شناسه آن کلیک کنید',
        'settings->posts->post' => "شناسه پست: :post_id\nبازدید: :views_count",
    ],

    // ENGLISH LANG:
    // To use this language, comment everything above.
    /*
    'buttons' => [
        'stats' => '📊 Stats',
        'settings' => '⚙️ Settings',
        'new_post' => '➕ New Post',
        'back' => '👈 Back',
        'delete' => '📛 Delete',
        'channels' => '📣 Channels',
        'posts' => '🗂 Posts',
        'add' => '➕ Add',
        'check_membership' => '📥 Check membership and get post',
    ],
    'errors' => [
        'unknown_command' => 'Unknown command!',
        'unknown_error' => 'Unknown error!',
        'not_allowed_message_type' => 'Message type is not allowed.',
        'post_not_found' => 'Post not found!',
        'channel_not_found' => 'Channel not found!',
    ],
    'messages' => [
        'welcome_to_bot' => 'Welcome to our bot!',
        'send_your_post' => 'Send your post:',
        'post_saved' => "Post saved.\n\nShare link:\n:share_link",
        'post_deleted' => 'Post with id `:id` and `:post_id` deleted.',
        'channel_added' => "Channel :title added.\n\nTo go to its settings, please refer to the «Channels» section",
        'bot_kicked_from_channel' => "It seems that your bot has been fired from the channel :title! For this reason, this channel was removed from the list of your channels in the bot.\n\nTo check the mandatory membership of users, the bot must be an admin in your channel.",
        'join_our_channels_first' => 'To receive the desired post (file), first become a member of our channels and then try again:',
    ],
    'descriptions' => [
        'start' => "Welcome to XShare2Bot!\n\nThe XShare2Bot bot is a simple and practical bot for saving and sharing Telegram posts, whose source code is written using the [Nutgram](https://github.com/nutgram/nutgram) library and is very flexible, modular and clean. The source code of this bot is released under the MIT license and is free to use.\n\nThe initial version of this source code has been developed for educational purposes, and the responsibility of any misuse of it against the laws of the country rests with the user.\n\nDeveloper: devdasher\n\nSource code link:\nhttps://github.com/devdasher/xshare2bot",
        'settings' => 'Your bot settings:',
        'settings->stats' => "Users count: :users_count\nPosts count: :posts_count",
        'settings->channels' => 'By adding a channel, users must be members of your channels to receive your posts',
        'settings->channels->channel' => "Channel ID: :channel_id",
        'settings->posts' => 'Here you can see the last 10 saved posts, click on the ID to enter the settings of each one',
        'settings->posts->post' => "Post ID: :post_id\nViews count: :views_count",
    ],
    */
];