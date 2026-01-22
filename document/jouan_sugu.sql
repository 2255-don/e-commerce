-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 22 jan. 2026 à 12:20
-- Version du serveur : 8.0.44-0ubuntu0.24.04.2
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `jouan_sugu`
--

-- --------------------------------------------------------

--
-- Structure de la table `collaterals`
--

CREATE TABLE `collaterals` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loan_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `portfolio_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked_quantity` bigint NOT NULL,
  `locked_value_at_time` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `companies`
--

CREATE TABLE `companies` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_registration_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `current_share_price` decimal(15,2) NOT NULL DEFAULT '1000.00',
  `total_valuation` decimal(20,2) NOT NULL DEFAULT '0.00',
  `trust_score` int DEFAULT '50',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `loans`
--

CREATE TABLE `loans` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_principal` decimal(15,2) NOT NULL,
  `amount_interest` decimal(15,2) NOT NULL,
  `total_due` decimal(15,2) NOT NULL,
  `due_date` datetime NOT NULL,
  `status` enum('pending','active','paid','defaulted','liquidated') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `market_orders`
--

CREATE TABLE `market_orders` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('buy','sell') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` bigint NOT NULL,
  `price_limit` decimal(15,2) NOT NULL,
  `status` enum('open','filled','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','disputed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_owned` bigint NOT NULL DEFAULT '0',
  `average_buy_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) NOT NULL,
  `type` enum('physical_good','service','tender_offer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '1',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_wallet_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_wallet_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('deposit','withdrawal','transfer','payment','loan_disbursement','loan_repayment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','user','company','bank_simulator') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `kyc_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `kyc_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `wallets`
--

CREATE TABLE `wallets` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XOF',
  `status` enum('active','frozen') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `collaterals`
--
ALTER TABLE `collaterals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_collaterals_loan` (`loan_id`),
  ADD KEY `fk_collaterals_portfolio` (`portfolio_id`);

--
-- Index pour la table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_companies_users` (`owner_id`);

--
-- Index pour la table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_loans_user` (`user_id`);

--
-- Index pour la table `market_orders`
--
ALTER TABLE `market_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_market_orders_users1_idx` (`user_id`),
  ADD KEY `fk_orders_company` (`company_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_users` (`buyer_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Index pour la table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_company` (`user_id`,`company_id`),
  ADD KEY `fk_portfolios_company` (`company_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_users` (`seller_id`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `fk_trx_receiver` (`receiver_wallet_id`),
  ADD KEY `fk_trx_sender` (`sender_wallet_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wallets_users` (`user_id`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `collaterals`
--
ALTER TABLE `collaterals`
  ADD CONSTRAINT `fk_collaterals_loan` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_collaterals_portfolio` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`);

--
-- Contraintes pour la table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_companies_users` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `market_orders`
--
ALTER TABLE `market_orders`
  ADD CONSTRAINT `fk_market_orders_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_orders_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Contraintes pour la table `portfolios`
--
ALTER TABLE `portfolios`
  ADD CONSTRAINT `fk_portfolios_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_portfolios_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_users` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_trx_receiver` FOREIGN KEY (`receiver_wallet_id`) REFERENCES `wallets` (`id`),
  ADD CONSTRAINT `fk_trx_sender` FOREIGN KEY (`sender_wallet_id`) REFERENCES `wallets` (`id`);

--
-- Contraintes pour la table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `fk_wallets_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
