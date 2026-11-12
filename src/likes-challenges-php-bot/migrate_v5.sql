ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `default_channel` varchar(255) DEFAULT NULL AFTER `like_emoji`,
  ADD COLUMN IF NOT EXISTS `default_channel_id` varchar(64) DEFAULT NULL AFTER `default_channel`,
  ADD COLUMN IF NOT EXISTS `default_channel_title` varchar(255) DEFAULT NULL AFTER `default_channel_id`,
  ADD COLUMN IF NOT EXISTS `force_channels` longtext DEFAULT NULL AFTER `default_channel_title`;

ALTER TABLE `items`
  ADD COLUMN IF NOT EXISTS `fake_likes` int NOT NULL DEFAULT 0 AFTER `options_json`,
  ADD COLUMN IF NOT EXISTS `target_channel` varchar(255) DEFAULT NULL AFTER `fake_likes`,
  ADD COLUMN IF NOT EXISTS `channel_chat_id` varchar(64) DEFAULT NULL AFTER `target_channel`,
  ADD COLUMN IF NOT EXISTS `channel_message_id` bigint DEFAULT NULL AFTER `channel_chat_id`,
  ADD COLUMN IF NOT EXISTS `source_chat_id` varchar(64) DEFAULT NULL AFTER `channel_message_id`,
  ADD COLUMN IF NOT EXISTS `source_message_id` bigint DEFAULT NULL AFTER `source_chat_id`,
  ADD COLUMN IF NOT EXISTS `updated_at` datetime DEFAULT NULL AFTER `created_at`;
