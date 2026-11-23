### 🇮🇷 [داکیومنت فارسی](./README_FA.md)

# 🤖 XShare2Bot
The XShare2Bot bot is a simple and practical bot for saving and sharing Telegram posts, whose source code is written using the [Nutgram](https://github.com/nutgram/nutgram) library and is very flexible, modular and clean. The source code of this bot is released under the MIT license and is free to use. The initial version of this source code has been developed for educational purposes, and the responsibility of any misuse of it against the laws of the country rests with the user.

You can edit and use this source code according to your needs.

There may be some bugs in the source code, But it is worth fixing it yourself.

# 🔮 Preview
![photo_2023-10-14_02-39-55](https://github.com/devdasher/xshare2bot/assets/78247242/5989f3e8-dd03-45f9-91bb-7a830e0f9a42)

# 🛠️ Installation
- Clone this repository: `git clone https://github.com/devdasher/xshare2bot`
- In root path, run this command to install dependencies: `composer install` (You need to install [Composer](https://getcomposer.org) first)
- Create a database named `xshare2bot` (in MySQL)
- Then import `sql/xshare2bot.sql` to your database
- Add your bot & database info in the `config/config.php` (Remember to add your Telegram ID too)
- You can run your bot using this command in terminal: `php index.php`, this will run your bot in LongPolling running mode
- But if you want to use Webhook running mode, Uncomment this piece of code `$bot->setRunningMode($webhook);` in `index.php` at line 39. Now you can setWebhook on `index.php` file for production

# 🙌 Credits 
[devdasher](https://github.com/devdasher)

# 📜 License 
The MIT License. Please see License File for more information.

# ⚠️ Disclaimer of Warranty 

