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
('coin_packages', '[{"coins":50,"amount":50000},{"coins":100,"amount":90000},{"coins":250,"amount":200000}]'),
('payment_card_enabled', '1'),
('payment_zibal_enabled', '0'),
('card_number', ''),
('card_owner', ''),
('card_text', 'پس از واریز، رسید پرداخت را همینجا ارسال کنید.'),
('zibal_merchant', ''),
('schema_version', '2.1')
ON DUPLICATE KEY UPDATE `svalue` = `svalue`;
