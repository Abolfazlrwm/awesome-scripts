<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

use DevDasher\XShare2Bot\Commands\ChannelCommand;
use DevDasher\XShare2Bot\Commands\PostCommand;
use DevDasher\XShare2Bot\Commands\StatsCommand;
use DevDasher\XShare2Bot\Commands\SettingsCommand;
use DevDasher\XShare2Bot\Commands\StartCommand;
use DevDasher\XShare2Bot\Conversations\NewPostConversation;
use DevDasher\XShare2Bot\Exceptions\BotWasKickedFromChannelException;
use DevDasher\XShare2Bot\Handlers\FallbackHandler;
use DevDasher\XShare2Bot\Handlers\MyChatMemberHandler;
use DevDasher\XShare2Bot\Middlewares\AdminOnlyMiddleware;
use DevDasher\XShare2Bot\Middlewares\AllowedUpdateTypesMiddleware;
use DevDasher\XShare2Bot\Middlewares\CollectUserMiddleware;
use DevDasher\XShare2Bot\Middlewares\DatabaseConnectionMiddleware;
use DevDasher\XShare2Bot\Middlewares\PrivateOnlyMiddleware;
use DevDasher\XShare2Bot\Nutgram\CustomMethods;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

ini_set('error_log', __DIR__.'/logs/error.log');

require(__DIR__.'/vendor/autoload.php');

$cache = new Psr16Cache(new FilesystemAdapter(defaultLifetime: 3600));

$bot = new Nutgram(
    token: config('bot.token'),
    config: new Configuration(
        enableHttp2: true,
        cache: $cache,
    ),
);

// $webhook = new Webhook(secretToken: 'my-secret-token');
// $webhook->setSafeMode(true);
// $bot->setRunningMode($webhook);

$bot->mixin(new CustomMethods);

$bot->middlewares([
    AllowedUpdateTypesMiddleware::class,
    PrivateOnlyMiddleware::class,
    DatabaseConnectionMiddleware::class,
    CollectUserMiddleware::class,
]);

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('start\sP{id}-{postId}', [StartCommand::class, 'sendPost']);

$bot->group(function(Nutgram $bot) {
    $bot->onText($bot->getText('buttons.new_post'), NewPostConversation::class);
    $bot->onText($bot->getText('buttons.settings'), SettingsCommand::class);
    $bot->onCallbackQueryData('sett', SettingsCommand::class);
    $bot->onCallbackQueryData('sett/stats', [StatsCommand::class, 'settings_stats']);
    $bot->onCallbackQueryData('sett/channels/{id}/delete', [ChannelCommand::class, 'settings_channels_channel_delete']);
    $bot->onCallbackQueryData('sett/channels/{id}$',[ChannelCommand::class, 'settings_channels_channel'])
        ->where('id', '\d+');
    $bot->onCallbackQueryData('sett/channels',[ChannelCommand::class, 'settings_channels']);
    $bot->onCallbackQueryData('sett/posts/{id}/delete', [PostCommand::class, 'settings_posts_post_delete']);
    $bot->onCallbackQueryData('sett/posts/{id}$', [PostCommand::class, 'settings_posts_post'])
        ->where('id', '\d+');
    $bot->onCallbackQueryData('sett/posts', [PostCommand::class, 'settings_posts']);
})->middleware(AdminOnlyMiddleware::class);

$bot->onMyChatMember(MyChatMemberHandler::class)
    ->skipGlobalMiddlewares([
        PrivateOnlyMiddleware::class
    ]);

$bot->fallbackOn(UpdateType::CALLBACK_QUERY, [FallbackHandler::class, 'callbackQuery']);

$bot->fallback(FallbackHandler::class);

// $bot->registerApiException(BotWasKickedFromChannelException::class);

$bot->run();