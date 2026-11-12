CREATE TABLE IF NOT EXISTS `settings` (
  `skey` varchar(100) NOT NULL,
  `svalue` longtext,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL,
  `username` varchar(64) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `coins` int NOT NULL DEFAULT 0,
  `like_emoji` varchar(32) NOT NULL DEFAULT '❤️',
  `default_channel` varchar(255) DEFAULT NULL,
  `default_channel_id` varchar(64) DEFAULT NULL,
  `default_channel_title` varchar(255) DEFAULT NULL,
  `force_channels` longtext DEFAULT NULL,
  `ref_by` bigint DEFAULT NULL,
  `step` varchar(100) DEFAULT NULL,
  `temp_json` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `last_daily_reward_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ref_by` (`ref_by`),
  KEY `idx_last_seen` (`last_seen`),
  KEY `idx_default_channel_id` (`default_channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `created_by` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_admin_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint NOT NULL,
  `type` enum('like','poll','challenge_like','auto_like') NOT NULL,
  `text` longtext DEFAULT NULL,
  `media_type` varchar(30) NOT NULL DEFAULT 'text',
  `file_id` text DEFAULT NULL,
  `caption` longtext DEFAULT NULL,
  `emoji` varchar(32) DEFAULT '❤️',
  `options_json` longtext DEFAULT NULL,
  `fake_likes` int NOT NULL DEFAULT 0,
  `target_channel` varchar(255) DEFAULT NULL,
  `channel_chat_id` varchar(64) DEFAULT NULL,
  `channel_message_id` bigint DEFAULT NULL,
  `source_chat_id` varchar(64) DEFAULT NULL,
  `source_message_id` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_owner_type` (`owner_id`, `type`),
  KEY `idx_channel_msg` (`channel_chat_id`, `channel_message_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `user_id` bigint NOT NULL,
  `vote_value` varchar(50) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_vote` (`item_id`, `user_id`),
  KEY `idx_item_value` (`item_id`, `vote_value`),
  CONSTRAINT `fk_votes_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `speed_challenges` (
  `token` varchar(64) NOT NULL,
  `owner_id` bigint NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `winner_id` bigint DEFAULT NULL,
  `winner_json` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `won_at` datetime DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `idx_owner_active` (`owner_id`, `is_active`),
  KEY `idx_winner` (`winner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `auto_like_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint NOT NULL,
  `channel_id` varchar(255) NOT NULL,
  `channel_chat_id` varchar(64) NOT NULL,
  `channel_title` varchar(255) DEFAULT NULL,
  `remaining_posts` int NOT NULL DEFAULT 0,
  `total_posts` int NOT NULL DEFAULT 0,
  `fake_likes` int NOT NULL DEFAULT 0,
  `price_per_post` int NOT NULL DEFAULT 2,
  `status` enum('active','done','stopped') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_channel_status` (`channel_chat_id`, `status`),
  KEY `idx_owner_status` (`owner_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `coin_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `coins` int NOT NULL DEFAULT 0,
  `amount` int NOT NULL DEFAULT 0,
  `method` enum('card','zibal') NOT NULL DEFAULT 'card',
  `status` enum('created','pending','paid','rejected','failed') NOT NULL DEFAULT 'created',
  `receipt_file_id` text DEFAULT NULL,
  `receipt_type` varchar(30) DEFAULT NULL,
  `receipt_text` longtext DEFAULT NULL,
  `admin_message_id` bigint DEFAULT NULL,
  `zibal_track_id` varchar(128) DEFAULT NULL,
  `zibal_ref_id` varchar(128) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`, `status`),
  KEY `idx_track` (`zibal_track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`skey`, `svalue`) VALUES
('price_like', '2'),
('price_poll', '2'),
('price_challenge_like', '3'),
('price_speed', '0'),
('price_auto_like', '2'),
('referral_reward', '10'),
('new_user_coins', '5'),
('global_join_channels', '[]'),
('sponsor_text', '☕ برای حمایت یا تبلیغات، با مدیریت ربات در ارتباط باشید.'),
('coin_packages', '[{"coins":50,"amount":50000},{"coins":100,"amount":90000},{"coins":250,"amount":200000}]'),
('payment_card_enabled', '1'),
('payment_zibal_enabled', '0'),
('card_number', ''),
('card_owner', ''),
('card_text', 'پس از واریز، رسید پرداخت را همینجا ارسال کنید.'),
('zibal_merchant', ''),
('zibal_bridge_enabled', '1'),
('zibal_bridge_url', 'https://your-domain.ir/zibal_bridge.php'),
('zibal_bridge_secret', 'CHANGE_THIS_SECRET'),
('daily_reward_enabled', '1'),
('daily_rewards', '{"1":0,"2":3,"3":1,"4":2,"5":3,"6":0}'),
('button_style_enabled', '1'),
('disabled_buttons', '[]'),
('button_styles', '{"❤️ ساخت لایک و چالش ❤️":"success","❤️ ساخت لایک ❤️":"success","♦️ ساخت نظرسنجی ♦️":"primary","💙 ساخت چالش لایک 💙":"primary","🏃 ساخت چالش سرعتی 🏃":"primary","🤍 ساخت خودکار لایک 🤍":"success","دکمه لایک زیر پست":"success","دکمه چالش لایک زیر پست":"primary","LIKE_BUTTON":"success","CHALLENGE_LIKE_BUTTON":"primary","👤 پروفایل":"primary","💰 افزایش سکه":"success","🎯 جایزه روزانه":"success","🎁 جمع‌آوری سکه با زیرمجموعه":"success","💳 خرید سکه":"primary","📖 راهنما":"primary","⚙️ تنظیمات":"primary","☕ اسپانسر ما":"primary","🌐 Language / زبان":"primary","🛠 پنل مدیریت":"danger","↩️ بازگشت":"danger","لغو":"danger","📢 تنظیم کانال ارسال پست‌ها":"primary","❤️ تنظیم اموجی لایک":"success","🔐 تنظیم عضویت اجباری":"danger","💾 ذخیره تنظیمات پیشفرض":"success","✅ بررسی عضویت":"success","✅ تایید":"success","❌ رد":"danger","💳 کارت به کارت":"primary","🔵 پرداخت آنلاین زیبال":"primary","پرداخت آنلاین":"primary","📊 آمار":"primary","💰 تنظیم قیمت‌ها":"primary","🎁 تنظیم سکه اولیه کاربران":"success","🎁 تنظیم سکه زیرمجموعه":"success","🎯 تنظیم جایزه روزانه":"success","💳 تنظیم خرید سکه":"primary","➕ افزایش/کاهش سکه":"success","🎨 تنظیم رنگ دکمه‌ها":"primary","🚫 خاموش/روشن دکمه‌ها":"danger","➕ افزودن کانال جوین اجباری":"success","➖ حذف کانال جوین اجباری":"danger","📋 لیست کانال‌ها":"primary","☕ تنظیم اسپانسر":"primary","📣 پیام همگانی":"primary","➕ افزودن ادمین":"success","📦 تنظیم پلن‌های خرید سکه":"primary","💳 تنظیم کارت به کارت":"primary","🔄 وضعیت کارت به کارت":"success","🔵 تنظیم مرچنت زیبال":"primary","🌐 تنظیم اتصال زیبال":"primary","🔄 وضعیت زیبال":"success","مقدار دلخواه":"primary"}'),
('schema_version', '2.4')
ON DUPLICATE KEY UPDATE `svalue` = `svalue`;
