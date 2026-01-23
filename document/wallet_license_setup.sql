-- SQL Queries to create required tables for Wallet and Seller License System

CREATE TABLE IF NOT EXISTS `wallets` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XOF',
  `status` enum('active','frozen') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_wallets_users` (`user_id`),
  CONSTRAINT `fk_wallets_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_wallet_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_wallet_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('deposit','withdrawal','transfer','payment','loan_disbursement','loan_repayment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `fk_trx_receiver` (`receiver_wallet_id`),
  KEY `fk_trx_sender` (`sender_wallet_id`),
  CONSTRAINT `fk_trx_receiver` FOREIGN KEY (`receiver_wallet_id`) REFERENCES `wallets` (`id`),
  CONSTRAINT `fk_trx_sender` FOREIGN KEY (`sender_wallet_id`) REFERENCES `wallets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `seller_profiles` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `licence_paid_at` datetime DEFAULT NULL,
  `licence_expire_at` datetime DEFAULT NULL,
  `commission_rate` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_seller_profiles_users_idx` (`user_id`),
  CONSTRAINT `fk_seller_profiles_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
