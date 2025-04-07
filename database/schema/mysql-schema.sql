/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `achievement_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int unsigned NOT NULL DEFAULT '1',
  `secret` tinyint(1) NOT NULL DEFAULT '0',
  `class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `achievement_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement_progress` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `achievement_id` bigint unsigned NOT NULL,
  `achiever_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `achiever_id` int unsigned NOT NULL,
  `points` int unsigned NOT NULL DEFAULT '0',
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `achievement_progress_achiever_type_achiever_id_index` (`achiever_type`,`achiever_id`),
  KEY `achievement_progress_achievement_id_foreign` (`achievement_id`),
  KEY `achievement_progress_achiever_id_foreign` (`achiever_id`),
  CONSTRAINT `achievement_progress_achievement_id_foreign` FOREIGN KEY (`achievement_id`) REFERENCES `achievement_details` (`id`),
  CONSTRAINT `achievement_progress_achiever_id_foreign` FOREIGN KEY (`achiever_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `announces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `uploaded` bigint unsigned NOT NULL,
  `downloaded` bigint unsigned NOT NULL,
  `left` bigint unsigned NOT NULL,
  `corrupt` bigint unsigned NOT NULL,
  `peer_id` binary(20) NOT NULL,
  `port` smallint unsigned NOT NULL,
  `numwant` smallint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `announces_user_id_torrent_id_index` (`user_id`,`torrent_id`),
  KEY `announces_torrent_id_index` (`torrent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `apikeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apikeys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `apikeys_user_id_foreign` (`user_id`),
  CONSTRAINT `apikeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_image_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_image_proofs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_image_proofs_application_id_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_url_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_url_proofs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_url_proofs_application_id_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `moderated_at` datetime DEFAULT NULL,
  `moderated_by` int unsigned DEFAULT NULL,
  `accepted_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_email_unique` (`email`),
  KEY `applications_moderated_by_foreign` (`moderated_by`),
  KEY `applications_accepted_by_foreign` (`accepted_by`),
  CONSTRAINT `applications_accepted_by_foreign` FOREIGN KEY (`accepted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `applications_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articles_created_at_index` (`created_at`),
  KEY `articles_user_id_foreign` (`user_id`),
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `model_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_entry_id` bigint unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `record` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_user_id_action_created_at_index` (`user_id`,`action`,`created_at`),
  CONSTRAINT `audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `automatic_torrent_freeleeches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automatic_torrent_freeleeches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `position` int unsigned NOT NULL,
  `name_regex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `type_id` int DEFAULT NULL,
  `resolution_id` bigint unsigned DEFAULT NULL,
  `freeleech_percentage` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bans` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `owned_by` int unsigned NOT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `ban_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unban_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `removed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bans_owned_by_foreign` (`owned_by`),
  KEY `bans_created_by_foreign` (`created_by`),
  CONSTRAINT `bans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `bans_owned_by_foreign` FOREIGN KEY (`owned_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blacklist_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blacklist_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `peer_id_prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blacklist_clients_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blocked_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocked_ips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blocked_ips_ip_address_unique` (`ip_address`),
  KEY `blocked_ips_user_id_foreign` (`user_id`),
  CONSTRAINT `blocked_ips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_earning_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bon_earning_conditions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `bon_earning_id` int unsigned NOT NULL,
  `operand1` enum('1','age','size','seeders','leechers','times_completed','personal_release','internal','type_id','seedtime','connectable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `operator` enum('<','>','<=','>=','=','!=') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `operand2` decimal(27,15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bon_earning_conditions_bon_earning_id_foreign` (`bon_earning_id`),
  CONSTRAINT `bon_earning_conditions_bon_earning_id_foreign` FOREIGN KEY (`bon_earning_id`) REFERENCES `bon_earnings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_earnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bon_earnings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `position` int unsigned NOT NULL,
  `variable` enum('1','age','size','seeders','leechers','times_completed','personal_release','internal','seedtime','connectable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiplier` decimal(27,15) NOT NULL,
  `operation` enum('append','multiply') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bon_earnings_position_unique` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_exchanges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bon_exchanges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` bigint unsigned NOT NULL DEFAULT '0',
  `cost` int unsigned NOT NULL DEFAULT '0',
  `upload` tinyint(1) NOT NULL DEFAULT '0',
  `download` tinyint(1) NOT NULL DEFAULT '0',
  `personal_freeleech` tinyint(1) NOT NULL DEFAULT '0',
  `invite` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bon_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bon_exchange_id` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cost` decimal(22,2) NOT NULL DEFAULT '0.00',
  `sender_id` int unsigned DEFAULT NULL,
  `receiver_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bon_transactions_itemid_index` (`bon_exchange_id`),
  KEY `bon_transactions_sender_foreign` (`sender_id`),
  KEY `bon_transactions_receiver_foreign` (`receiver_id`),
  CONSTRAINT `bon_transactions_receiver_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `bon_transactions_sender_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookmarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmarks_user_id_foreign` (`user_id`),
  KEY `bookmarks_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `bookmarks_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `position` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `command` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emoji` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `is_protected` tinyint(1) NOT NULL DEFAULT '0',
  `is_nerdbot` tinyint(1) NOT NULL DEFAULT '0',
  `is_systembot` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bots_active_index` (`active`),
  KEY `bots_is_protected_index` (`is_protected`),
  KEY `bots_is_nerdbot_index` (`is_nerdbot`),
  KEY `bots_is_systembot_index` (`is_systembot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `no_meta` tinyint(1) NOT NULL DEFAULT '0',
  `music_meta` tinyint(1) NOT NULL DEFAULT '0',
  `game_meta` tinyint(1) NOT NULL DEFAULT '0',
  `tv_meta` tinyint(1) NOT NULL DEFAULT '0',
  `movie_meta` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_statuses_name_unique` (`name`),
  UNIQUE KEY `chat_statuses_color_unique` (`color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chatrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatrooms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chatrooms_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `claimed_prizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claimed_prizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `event_id` int unsigned NOT NULL,
  `bon` bigint unsigned NOT NULL,
  `fl_tokens` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claimed_prizes_event_id_foreign` (`event_id`),
  KEY `claimed_prizes_user_id_foreign` (`user_id`),
  CONSTRAINT `claimed_prizes_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `claimed_prizes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `commentable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentable_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_created_at_index` (`created_at`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_updated_at_index` (`updated_at`),
  KEY `conversations_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `distributors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `distributors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `distributors_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donation_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_gateways` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `position` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donation_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `position` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `upload_value` bigint unsigned DEFAULT NULL,
  `invite_value` bigint unsigned DEFAULT NULL,
  `bonus_value` bigint unsigned DEFAULT NULL,
  `donor_value` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donation_packages_position_index` (`position`),
  KEY `donation_packages_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `gifted_user_id` int unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `package_id` int unsigned NOT NULL,
  `transaction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_gifted` tinyint(1) NOT NULL DEFAULT '0',
  `starts_at` date DEFAULT NULL,
  `ends_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donations_user_id_index` (`user_id`),
  KEY `donations_gifted_user_id_index` (`gifted_user_id`),
  KEY `donations_package_id_index` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_updates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_updates_user_id_foreign` (`user_id`),
  CONSTRAINT `email_updates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `starts_at` date NOT NULL,
  `ends_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_login_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_login_attempts_user_id_foreign` (`user_id`),
  CONSTRAINT `failed_login_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `featured_torrents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `featured_torrents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `featured_torrents_user_id_foreign` (`user_id`),
  KEY `featured_torrents_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `featured_torrents_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `featured_torrents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `files_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `follows` (
  `user_id` int unsigned NOT NULL,
  `target_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`target_id`),
  KEY `follows_target_id_foreign` (`target_id`),
  CONSTRAINT `follows_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `follows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `forum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_categories` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `position` smallint unsigned NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `forum_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `forum_id` smallint unsigned NOT NULL,
  `group_id` int NOT NULL,
  `read_topic` tinyint(1) NOT NULL,
  `reply_topic` tinyint(1) NOT NULL,
  `start_topic` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_group_id_forum_id_unique` (`group_id`,`forum_id`),
  KEY `fk_permissions_forums1_idx` (`forum_id`),
  KEY `fk_permissions_groups1_idx` (`group_id`),
  KEY `permissions_group_id_forum_id_read_topic_index` (`group_id`,`forum_id`,`read_topic`),
  CONSTRAINT `permissions_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `position` int DEFAULT NULL,
  `num_topic` int DEFAULT NULL,
  `num_post` int DEFAULT NULL,
  `last_topic_id` int unsigned DEFAULT NULL,
  `last_post_id` int DEFAULT NULL,
  `last_post_user_id` int unsigned DEFAULT NULL,
  `last_post_created_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `forum_category_id` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `default_topic_state_filter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forums_last_post_user_id_foreign` (`last_post_user_id`),
  KEY `forums_last_topic_id_foreign` (`last_topic_id`),
  KEY `forums_last_post_id_foreign` (`last_post_id`),
  KEY `forums_last_post_created_at_index` (`last_post_created_at`),
  KEY `forums_forum_category_id_foreign` (`forum_category_id`),
  CONSTRAINT `forums_forum_category_id_foreign` FOREIGN KEY (`forum_category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `forums_last_post_id_foreign` FOREIGN KEY (`last_post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `forums_last_post_user_id_foreign` FOREIGN KEY (`last_post_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `forums_last_topic_id_foreign` FOREIGN KEY (`last_topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `freeleech_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `freeleech_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `freeleech_tokens_user_id_foreign` (`user_id`),
  KEY `freeleech_tokens_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `freeleech_tokens_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `freeleech_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gifts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned DEFAULT NULL,
  `recipient_id` int unsigned DEFAULT NULL,
  `bon` decimal(22,2) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gifts_sender_id_foreign` (`sender_id`),
  KEY `gifts_recipient_id_foreign` (`recipient_id`),
  CONSTRAINT `gifts_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `gifts_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `git_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `git_updates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `git_updates_name_unique` (`name`),
  UNIQUE KEY `git_updates_hash_unique` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `level` int NOT NULL DEFAULT '0',
  `download_slots` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `effect` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `is_uploader` tinyint(1) NOT NULL DEFAULT '0',
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `is_editor` tinyint(1) NOT NULL DEFAULT '0',
  `is_torrent_modo` tinyint(1) NOT NULL DEFAULT '0',
  `is_owner` tinyint(1) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_modo` tinyint(1) NOT NULL DEFAULT '0',
  `is_trusted` tinyint(1) NOT NULL DEFAULT '0',
  `is_immune` tinyint(1) NOT NULL DEFAULT '0',
  `is_freeleech` tinyint(1) NOT NULL DEFAULT '0',
  `is_double_upload` tinyint(1) NOT NULL DEFAULT '0',
  `is_refundable` tinyint(1) NOT NULL DEFAULT '0',
  `can_chat` tinyint(1) NOT NULL,
  `can_comment` tinyint(1) NOT NULL,
  `can_invite` tinyint(1) NOT NULL,
  `can_request` tinyint(1) NOT NULL,
  `can_upload` tinyint(1) NOT NULL DEFAULT '1',
  `is_incognito` tinyint(1) NOT NULL DEFAULT '0',
  `autogroup` tinyint(1) NOT NULL DEFAULT '0',
  `min_uploaded` bigint unsigned DEFAULT NULL,
  `min_seedsize` bigint unsigned DEFAULT NULL,
  `min_avg_seedtime` bigint unsigned DEFAULT NULL,
  `min_ratio` decimal(4,2) DEFAULT NULL,
  `min_age` bigint unsigned DEFAULT NULL,
  `system_required` tinyint(1) NOT NULL DEFAULT '0',
  `min_uploads` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_slug_unique` (`slug`),
  KEY `groups_download_slots_index` (`download_slots`),
  KEY `groups_is_editor_index` (`is_editor`),
  KEY `groups_is_torrent_modo_index` (`is_torrent_modo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history` (
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `agent` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded` bigint unsigned NOT NULL DEFAULT '0',
  `actual_uploaded` bigint unsigned NOT NULL DEFAULT '0',
  `client_uploaded` bigint unsigned NOT NULL,
  `downloaded` bigint unsigned NOT NULL DEFAULT '0',
  `refunded_download` bigint unsigned NOT NULL DEFAULT '0',
  `actual_downloaded` bigint unsigned NOT NULL DEFAULT '0',
  `client_downloaded` bigint unsigned NOT NULL,
  `seeder` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `seedtime` bigint unsigned NOT NULL DEFAULT '0',
  `immune` tinyint(1) NOT NULL,
  `hitrun` tinyint(1) NOT NULL DEFAULT '0',
  `prewarned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`torrent_id`),
  KEY `history_user_id_foreign` (`user_id`),
  KEY `history_immune_index` (`immune`),
  KEY `history_hitrun_index` (`hitrun`),
  KEY `history_user_id_torrent_id_index` (`user_id`,`torrent_id`),
  KEY `history_idx_prewa_hitru_immun_activ_actua` (`prewarned_at`,`hitrun`,`immune`,`active`,`actual_downloaded`),
  KEY `history_torrent_id_completed_at_created_at_index` (`torrent_id`,`completed_at`,`created_at`),
  CONSTRAINT `history_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_image_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_company_igdb_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_company_igdb_game` (
  `igdb_game_id` int unsigned NOT NULL,
  `igdb_company_id` int unsigned NOT NULL,
  PRIMARY KEY (`igdb_game_id`,`igdb_company_id`),
  KEY `igdb_company_igdb_game_igdb_company_id_index` (`igdb_company_id`),
  CONSTRAINT `igdb_company_igdb_game_igdb_company_id_foreign` FOREIGN KEY (`igdb_company_id`) REFERENCES `igdb_companies` (`id`),
  CONSTRAINT `igdb_company_igdb_game_igdb_game_id_foreign` FOREIGN KEY (`igdb_game_id`) REFERENCES `igdb_games` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_game_igdb_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_game_igdb_genre` (
  `igdb_game_id` int unsigned NOT NULL,
  `igdb_genre_id` int unsigned NOT NULL,
  PRIMARY KEY (`igdb_game_id`,`igdb_genre_id`),
  KEY `igdb_game_igdb_genre_igdb_genre_id_index` (`igdb_genre_id`),
  CONSTRAINT `igdb_game_igdb_genre_igdb_game_id_foreign` FOREIGN KEY (`igdb_game_id`) REFERENCES `igdb_games` (`id`),
  CONSTRAINT `igdb_game_igdb_genre_igdb_genre_id_foreign` FOREIGN KEY (`igdb_genre_id`) REFERENCES `igdb_genres` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_game_igdb_platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_game_igdb_platform` (
  `igdb_game_id` int unsigned NOT NULL,
  `igdb_platform_id` int unsigned NOT NULL,
  PRIMARY KEY (`igdb_game_id`,`igdb_platform_id`),
  KEY `igdb_game_igdb_platform_igdb_platform_id_index` (`igdb_platform_id`),
  CONSTRAINT `igdb_game_igdb_platform_igdb_game_id_foreign` FOREIGN KEY (`igdb_game_id`) REFERENCES `igdb_games` (`id`),
  CONSTRAINT `igdb_game_igdb_platform_igdb_platform_id_foreign` FOREIGN KEY (`igdb_platform_id`) REFERENCES `igdb_platforms` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_games` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `first_artwork_image_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_release_date` timestamp NULL DEFAULT NULL,
  `cover_image_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `rating_count` int unsigned DEFAULT NULL,
  `first_video_video_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_genres` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `igdb_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `igdb_platforms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_logo_image_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `internal_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internal_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `internal_id` int unsigned NOT NULL,
  `position` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `internal_user_user_id_internal_id_unique` (`user_id`,`internal_id`),
  KEY `internal_user_internal_id_foreign` (`internal_id`),
  CONSTRAINT `internal_user_internal_id_foreign` FOREIGN KEY (`internal_id`) REFERENCES `internals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `internal_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `internals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `effect` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `internals_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_on` datetime DEFAULT NULL,
  `accepted_by` int unsigned DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `custom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `internal_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invites_user_id_foreign` (`user_id`),
  KEY `invites_accepted_by_foreign` (`accepted_by`),
  CONSTRAINT `invites_accepted_by_foreign` FOREIGN KEY (`accepted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(1) NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keywords` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keywords_torrent_id_name_unique` (`torrent_id`,`name`),
  KEY `keywords_name_index` (`name`),
  CONSTRAINT `keywords_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `post_id` int NOT NULL,
  `like` tinyint(1) DEFAULT NULL,
  `dislike` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `likes_user_id_foreign` (`user_id`),
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `chatroom_id` int unsigned NOT NULL,
  `receiver_id` int unsigned DEFAULT NULL,
  `bot_id` int unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_user_id_foreign` (`user_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` int unsigned NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`),
  KEY `notifications_notifiable_type_notifiable_id_read_at_index` (`notifiable_type`,`notifiable_id`,`read_at`),
  CONSTRAINT `notifications_notifiable_id_foreign` FOREIGN KEY (`notifiable_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `occupations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `occupations` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `position` smallint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `votes` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_options_poll` (`poll_id`),
  CONSTRAINT `fk_options_poll` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_user_id_conversation_id_unique` (`user_id`,`conversation_id`),
  KEY `participants_user_id_read_deleted_at_index` (`user_id`,`read`,`deleted_at`),
  KEY `participants_conversation_id_index` (`conversation_id`),
  CONSTRAINT `participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passkeys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `passkeys_user_id_foreign` (`user_id`),
  CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `password_reset_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `password_reset_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `peers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peers` (
  `peer_id` binary(20) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `port` smallint unsigned NOT NULL,
  `agent` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded` bigint unsigned NOT NULL,
  `downloaded` bigint unsigned NOT NULL,
  `left` bigint unsigned NOT NULL,
  `seeder` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `torrent_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `connectable` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`,`torrent_id`,`peer_id`),
  KEY `peers_idx_seeder_user_id` (`seeder`,`user_id`),
  KEY `peers_torrent_id_foreign` (`torrent_id`),
  KEY `peers_active_index` (`active`),
  CONSTRAINT `peers_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `peers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_freeleeches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_freeleeches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personal_freeleech_user_id_foreign` (`user_id`),
  CONSTRAINT `personal_freeleech_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlist_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `position` smallint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlist_torrents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_torrents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `position` int DEFAULT NULL,
  `playlist_id` int NOT NULL DEFAULT '0',
  `torrent_id` int unsigned NOT NULL DEFAULT '0',
  `tmdb_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playlist_torrents_playlist_id_torrent_id_tmdb_id_unique` (`playlist_id`,`torrent_id`,`tmdb_id`),
  KEY `playlist_torrents_playlist_id_index` (`playlist_id`),
  KEY `playlist_torrents_tmdb_id_index` (`tmdb_id`),
  KEY `playlist_torrents_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `playlist_torrents_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `playlist_category_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `playlists_is_private_index` (`is_private`),
  KEY `playlists_is_pinned_index` (`is_pinned`),
  KEY `playlists_is_featured_index` (`is_featured`),
  KEY `playlists_user_id_foreign` (`user_id`),
  KEY `playlists_playlist_category_id_foreign` (`playlist_category_id`),
  CONSTRAINT `playlists_playlist_category_id_foreign` FOREIGN KEY (`playlist_category_id`) REFERENCES `playlist_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `playlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_choice` tinyint(1) NOT NULL DEFAULT '0',
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `polls_user_id_foreign` (`user_id`),
  CONSTRAINT `polls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `post_tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_tips` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned DEFAULT NULL,
  `recipient_id` int unsigned DEFAULT NULL,
  `post_id` int DEFAULT NULL,
  `bon` decimal(22,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_tips_sender_id_foreign` (`sender_id`),
  KEY `post_tips_recipient_id_foreign` (`recipient_id`),
  KEY `post_tips_post_id_foreign` (`post_id`),
  CONSTRAINT `post_tips_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `post_tips_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `post_tips_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `topic_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_posts_topics1_idx` (`topic_id`),
  KEY `posts_created_at_index` (`created_at`),
  KEY `posts_user_id_foreign` (`user_id`),
  CONSTRAINT `posts_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `private_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int unsigned NOT NULL,
  `sender_id` int unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `private_messages_sender_id_foreign` (`sender_id`),
  KEY `private_messages_conversation_id_foreign` (`conversation_id`),
  CONSTRAINT `private_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `private_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min` int unsigned NOT NULL,
  `max` int unsigned NOT NULL,
  `weight` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prizes_event_id_foreign` (`event_id`),
  CONSTRAINT `prizes_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_id` int unsigned NOT NULL,
  `staff_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `solved` tinyint(1) NOT NULL DEFAULT '0',
  `verdict` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reported_user` int unsigned DEFAULT NULL,
  `torrent_id` int unsigned DEFAULT NULL,
  `request_id` int unsigned DEFAULT NULL,
  `snoozed_until` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_reporter_id_foreign` (`reporter_id`),
  KEY `reports_staff_id_foreign` (`staff_id`),
  KEY `reports_reported_user_foreign` (`reported_user`),
  KEY `reports_torrent_id_foreign` (`torrent_id`),
  KEY `reports_solved_snoozed_until_index` (`solved`,`snoozed_until`),
  CONSTRAINT `reports_reported_user_foreign` FOREIGN KEY (`reported_user`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `request_bounty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_bounty` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `seedbonus` decimal(12,2) NOT NULL DEFAULT '0.00',
  `requests_id` int NOT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`requests_id`),
  KEY `request_bounty_user_id_foreign` (`user_id`),
  CONSTRAINT `request_bounty_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `check_request_bounty_seedbonus` CHECK ((`seedbonus` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `request_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_claims` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `user_id` int unsigned NOT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  KEY `request_claims_user_id_foreign` (`user_id`),
  CONSTRAINT `request_claims_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` smallint unsigned NOT NULL,
  `imdb` int unsigned DEFAULT NULL,
  `tvdb` int unsigned DEFAULT NULL,
  `mal` int unsigned DEFAULT NULL,
  `igdb` int unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned DEFAULT NULL,
  `tmdb_tv_id` int unsigned DEFAULT NULL,
  `bounty` decimal(12,2) NOT NULL,
  `votes` int NOT NULL DEFAULT '0',
  `claimed` tinyint(1) DEFAULT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `filled_by` int unsigned DEFAULT NULL,
  `torrent_id` int unsigned DEFAULT NULL,
  `filled_when` datetime DEFAULT NULL,
  `filled_anon` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by` int unsigned DEFAULT NULL,
  `approved_when` datetime DEFAULT NULL,
  `type_id` smallint unsigned NOT NULL,
  `resolution_id` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `imdb` (`imdb`),
  KEY `tvdb` (`tvdb`),
  KEY `mal` (`mal`),
  KEY `requests_igdb_index` (`igdb`),
  KEY `requests_type_id_index` (`type_id`),
  KEY `requests_resolution_id_index` (`resolution_id`),
  KEY `requests_user_id_foreign` (`user_id`),
  KEY `requests_filled_by_foreign` (`filled_by`),
  KEY `requests_approved_by_foreign` (`approved_by`),
  KEY `requests_torrent_id_foreign` (`torrent_id`),
  KEY `requests_movie_id_index` (`tmdb_movie_id`),
  KEY `requests_tv_id_index` (`tmdb_tv_id`),
  CONSTRAINT `requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `requests_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `requests_filled_by_foreign` FOREIGN KEY (`filled_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `requests_resolution_id_foreign` FOREIGN KEY (`resolution_id`) REFERENCES `resolutions` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `requests_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `requests_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `check_requests_bounty` CHECK ((`bounty` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resolutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resolutions` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resurrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resurrections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `seedtime` bigint unsigned NOT NULL,
  `rewarded` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `graveyard_user_id_foreign` (`user_id`),
  KEY `graveyard_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `graveyard_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `graveyard_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rss` (
  `id` int NOT NULL AUTO_INCREMENT,
  `position` int NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Default',
  `user_id` int unsigned NOT NULL DEFAULT '1',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `is_torrent` tinyint(1) NOT NULL DEFAULT '0',
  `json_torrent` json NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rss_is_private_index` (`is_private`),
  KEY `rss_is_torrent_index` (`is_torrent`),
  KEY `rss_user_id_foreign` (`user_id`),
  CONSTRAINT `rss_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rsskeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rsskeys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rsskeys_user_id_foreign` (`user_id`),
  CONSTRAINT `rsskeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seedboxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seedboxes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_ip_unique` (`ip`),
  KEY `clients_user_id_foreign` (`user_id`),
  KEY `clients_name_unique` (`name`),
  CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sessions_id_unique` (`id`),
  KEY `sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `forum_id` smallint unsigned DEFAULT NULL,
  `topic_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_topic_id_index` (`topic_id`),
  KEY `subscriptions_forum_id_index` (`forum_id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `subscriptions_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscriptions_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subtitles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subtitles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL,
  `language_id` int NOT NULL,
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `downloads` int DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `status` smallint NOT NULL DEFAULT '0',
  `moderated_at` datetime DEFAULT NULL,
  `moderated_by` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subtitles_language_id_index` (`language_id`),
  KEY `subtitles_verified_index` (`verified`),
  KEY `subtitles_user_id_foreign` (`user_id`),
  KEY `subtitles_moderated_by_foreign` (`moderated_by`),
  KEY `subtitles_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `subtitles_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `subtitles_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subtitles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `thanks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thanks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thanks_user_id_foreign` (`user_id`),
  KEY `thanks_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `thanks_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `thanks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `ticket_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_attachments_ticket_id_index` (`ticket_id`),
  KEY `ticket_attachments_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `ticket_id` int unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_notes_user_id_index` (`user_id`),
  KEY `ticket_notes_ticket_id_index` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_priorities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `category_id` int NOT NULL,
  `priority_id` int NOT NULL,
  `staff_id` int unsigned DEFAULT NULL,
  `user_read` tinyint(1) NOT NULL DEFAULT '0',
  `staff_read` tinyint(1) NOT NULL DEFAULT '0',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `reminded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_category_id_index` (`category_id`),
  KEY `tickets_priority_id_index` (`priority_id`),
  KEY `tickets_user_id_foreign` (`user_id`),
  KEY `tickets_staff_id_foreign` (`staff_id`),
  CONSTRAINT `tickets_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_collection_tmdb_movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_collection_tmdb_movie` (
  `tmdb_collection_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_collection_id`,`tmdb_movie_id`),
  KEY `collection_movie_movie_id_foreign` (`tmdb_movie_id`),
  CONSTRAINT `collection_movie_collection_id_foreign` FOREIGN KEY (`tmdb_collection_id`) REFERENCES `tmdb_collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `collection_movie_movie_id_foreign` FOREIGN KEY (`tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_collections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_sort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parts` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overview` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `poster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backdrop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `headquarters` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_company_tmdb_movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_company_tmdb_movie` (
  `tmdb_company_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_company_id`,`tmdb_movie_id`),
  KEY `company_movie_movie_id_foreign` (`tmdb_movie_id`),
  CONSTRAINT `company_movie_company_id_foreign` FOREIGN KEY (`tmdb_company_id`) REFERENCES `tmdb_companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_movie_movie_id_foreign` FOREIGN KEY (`tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_company_tmdb_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_company_tmdb_tv` (
  `tmdb_company_id` int unsigned NOT NULL,
  `tmdb_tv_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_company_id`,`tmdb_tv_id`),
  KEY `company_tv_tv_id_foreign` (`tmdb_tv_id`),
  CONSTRAINT `company_tv_company_id_foreign` FOREIGN KEY (`tmdb_company_id`) REFERENCES `tmdb_companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_tv_tv_id_foreign` FOREIGN KEY (`tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_credits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tmdb_person_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned DEFAULT NULL,
  `tmdb_tv_id` int unsigned DEFAULT NULL,
  `occupation_id` smallint unsigned NOT NULL,
  `order` int unsigned DEFAULT NULL,
  `character` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credits_person_id_movie_id_tv_id_occupation_id_character_unique` (`tmdb_person_id`,`tmdb_movie_id`,`tmdb_tv_id`,`occupation_id`,`character`),
  KEY `credits_occupation_id_foreign` (`occupation_id`),
  KEY `credits_movie_id_foreign` (`tmdb_movie_id`),
  KEY `credits_tv_id_foreign` (`tmdb_tv_id`),
  CONSTRAINT `credits_movie_id_foreign` FOREIGN KEY (`tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credits_occupation_id_foreign` FOREIGN KEY (`occupation_id`) REFERENCES `occupations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credits_person_id_foreign` FOREIGN KEY (`tmdb_person_id`) REFERENCES `tmdb_people` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `credits_tv_id_foreign` FOREIGN KEY (`tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_genre_tmdb_movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_genre_tmdb_movie` (
  `tmdb_genre_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_genre_id`,`tmdb_movie_id`),
  KEY `genre_movie_movie_id_index` (`tmdb_movie_id`),
  CONSTRAINT `genre_movie_genre_id_foreign` FOREIGN KEY (`tmdb_genre_id`) REFERENCES `tmdb_genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genre_movie_movie_id_foreign` FOREIGN KEY (`tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_genre_tmdb_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_genre_tmdb_tv` (
  `tmdb_genre_id` int unsigned NOT NULL,
  `tmdb_tv_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_genre_id`,`tmdb_tv_id`),
  KEY `genre_tv_tv_id_foreign` (`tmdb_tv_id`),
  CONSTRAINT `genre_tv_genre_id_foreign` FOREIGN KEY (`tmdb_genre_id`) REFERENCES `tmdb_genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genre_tv_tv_id_foreign` FOREIGN KEY (`tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_genres` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `genres_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_movies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_movies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tmdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_sort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adult` tinyint(1) DEFAULT NULL,
  `backdrop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overview` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `popularity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `revenue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `runtime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tagline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote_average` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote_count` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trailer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movie_title_index` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_network_tmdb_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_network_tmdb_tv` (
  `tmdb_network_id` int unsigned NOT NULL,
  `tmdb_tv_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_network_id`,`tmdb_tv_id`),
  KEY `network_tv_tv_id_foreign` (`tmdb_tv_id`),
  CONSTRAINT `network_tv_network_id_foreign` FOREIGN KEY (`tmdb_network_id`) REFERENCES `tmdb_networks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `network_tv_tv_id_foreign` FOREIGN KEY (`tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_networks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_networks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headquarters` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `networks_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_people` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `imdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `known_for_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `popularity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `still` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adult` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `also_known_as` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `biography` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `birthday` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deathday` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_recommended_movies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_recommended_movies` (
  `tmdb_movie_id` int unsigned NOT NULL,
  `recommended_tmdb_movie_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_movie_id`,`recommended_tmdb_movie_id`),
  KEY `tmdb_recommended_movies_recommended_tmdb_movie_id_foreign` (`recommended_tmdb_movie_id`),
  CONSTRAINT `tmdb_recommended_movies_recommended_tmdb_movie_id_foreign` FOREIGN KEY (`recommended_tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tmdb_recommended_movies_tmdb_movie_id_foreign` FOREIGN KEY (`tmdb_movie_id`) REFERENCES `tmdb_movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_recommended_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_recommended_tv` (
  `tmdb_tv_id` int unsigned NOT NULL,
  `recommended_tmdb_tv_id` int unsigned NOT NULL,
  PRIMARY KEY (`tmdb_tv_id`,`recommended_tmdb_tv_id`),
  KEY `tmdb_recommended_tv_recommended_tmdb_tv_id_foreign` (`recommended_tmdb_tv_id`),
  CONSTRAINT `tmdb_recommended_tv_recommended_tmdb_tv_id_foreign` FOREIGN KEY (`recommended_tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tmdb_recommended_tv_tmdb_tv_id_foreign` FOREIGN KEY (`tmdb_tv_id`) REFERENCES `tmdb_tv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmdb_tv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tmdb_tv` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tmdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tvdb_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_sort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `overview` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `number_of_episodes` int DEFAULT NULL,
  `count_existing_episodes` int DEFAULT NULL,
  `count_total_episodes` int DEFAULT NULL,
  `number_of_seasons` int DEFAULT NULL,
  `episode_run_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_air_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_production` tinyint(1) DEFAULT NULL,
  `last_air_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_episode_to_air` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `popularity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backdrop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote_average` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote_count` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trailer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tv_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_reads` (
  `user_id` int unsigned NOT NULL,
  `topic_id` int unsigned NOT NULL,
  `last_read_post_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`),
  KEY `topic_reads_last_read_post_id_foreign` (`last_read_post_id`),
  KEY `topic_reads_topic_id_index` (`topic_id`),
  CONSTRAINT `topic_reads_last_read_post_id_foreign` FOREIGN KEY (`last_read_post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `topic_reads_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `topic_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` tinyint unsigned NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `denied` tinyint(1) NOT NULL DEFAULT '0',
  `solved` tinyint(1) NOT NULL DEFAULT '0',
  `invalid` tinyint(1) NOT NULL DEFAULT '0',
  `bug` tinyint(1) NOT NULL DEFAULT '0',
  `suggestion` tinyint(1) NOT NULL DEFAULT '0',
  `implemented` tinyint(1) NOT NULL DEFAULT '0',
  `num_post` int DEFAULT NULL,
  `first_post_user_id` int unsigned DEFAULT NULL,
  `last_post_id` int DEFAULT NULL,
  `last_post_user_id` int unsigned DEFAULT NULL,
  `last_post_created_at` timestamp NULL DEFAULT NULL,
  `views` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `forum_id` smallint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_topics_forums1_idx` (`forum_id`),
  KEY `topics_created_at_index` (`created_at`),
  KEY `topics_first_post_user_id_foreign` (`first_post_user_id`),
  KEY `topics_last_post_user_id_foreign` (`last_post_user_id`),
  KEY `topics_last_post_id_foreign` (`last_post_id`),
  KEY `topics_last_post_created_at_index` (`last_post_created_at`),
  CONSTRAINT `topics_first_post_user_id_foreign` FOREIGN KEY (`first_post_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `topics_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `topics_last_post_id_foreign` FOREIGN KEY (`last_post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `topics_last_post_user_id_foreign` FOREIGN KEY (`last_post_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `torrent_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `torrent_downloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `torrent_id` int unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `torrent_downloads_user_id_foreign` (`user_id`),
  KEY `torrent_downloads_torrent_id_foreign` (`torrent_id`),
  KEY `torrent_downloads_created_at_index` (`created_at`),
  CONSTRAINT `torrent_downloads_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `torrent_downloads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `torrent_tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `torrent_tips` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned DEFAULT NULL,
  `recipient_id` int unsigned DEFAULT NULL,
  `torrent_id` int unsigned DEFAULT NULL,
  `bon` decimal(22,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `torrent_tips_sender_id_foreign` (`sender_id`),
  KEY `torrent_tips_recipient_id_foreign` (`recipient_id`),
  KEY `torrent_tips_torrent_id_foreign` (`torrent_id`),
  CONSTRAINT `torrent_tips_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `torrent_tips_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `torrent_tips_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `torrent_trumps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `torrent_trumps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `torrent_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `torrent_trumps_torrent_id_user_id_unique` (`torrent_id`,`user_id`),
  KEY `torrent_trumps_user_id_foreign` (`user_id`),
  CONSTRAINT `torrent_trumps_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `torrents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `torrent_trumps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `torrents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `torrents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mediainfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bdinfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_file` int NOT NULL,
  `folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` double NOT NULL,
  `nfo` blob,
  `leechers` int NOT NULL DEFAULT '0',
  `seeders` int NOT NULL DEFAULT '0',
  `times_completed` int NOT NULL DEFAULT '0',
  `category_id` smallint unsigned DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `tmdb_movie_id` int unsigned DEFAULT NULL,
  `tmdb_tv_id` int unsigned DEFAULT NULL,
  `imdb` int unsigned DEFAULT NULL,
  `tvdb` int unsigned DEFAULT NULL,
  `mal` int unsigned DEFAULT NULL,
  `igdb` int unsigned DEFAULT NULL,
  `season_number` int DEFAULT NULL,
  `episode_number` int DEFAULT NULL,
  `free` smallint NOT NULL DEFAULT '0',
  `doubleup` tinyint(1) NOT NULL DEFAULT '0',
  `refundable` tinyint(1) NOT NULL DEFAULT '0',
  `highspeed` tinyint(1) NOT NULL DEFAULT '0',
  `status` smallint NOT NULL DEFAULT '0',
  `moderated_at` datetime DEFAULT NULL,
  `moderated_by` int DEFAULT NULL,
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bumped_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `fl_until` datetime DEFAULT NULL,
  `du_until` datetime DEFAULT NULL,
  `type_id` smallint unsigned DEFAULT NULL,
  `resolution_id` smallint unsigned DEFAULT NULL,
  `distributor_id` int DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `personal_release` tinyint(1) NOT NULL DEFAULT '0',
  `balance` bigint DEFAULT NULL,
  `balance_offset` bigint DEFAULT NULL,
  `info_hash` binary(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `fk_table1_categories1_idx` (`category_id`),
  KEY `imdb` (`imdb`),
  KEY `tvdb` (`tvdb`),
  KEY `mal` (`mal`),
  KEY `moderated_by` (`moderated_by`),
  KEY `torrents_igdb_index` (`igdb`),
  KEY `torrents_type_id_index` (`type_id`),
  KEY `torrents_resolution_id_index` (`resolution_id`),
  KEY `torrents_personal_release_index` (`personal_release`),
  KEY `torrents_distributor_id_index` (`distributor_id`),
  KEY `torrents_region_id_index` (`region_id`),
  KEY `torrents_status_index` (`status`),
  KEY `torrents_seeders_index` (`seeders`),
  KEY `torrents_leechers_index` (`leechers`),
  KEY `torrents_sticky_index` (`sticky`),
  KEY `torrents_created_at_index` (`created_at`),
  KEY `torrents_bumped_at_index` (`bumped_at`),
  KEY `torrents_season_number_index` (`season_number`),
  KEY `torrents_episode_number_index` (`episode_number`),
  KEY `torrents_idx_status_resolut_created` (`status`,`resolution_id`,`created_at`),
  KEY `torrents_idx_status_catego_sticky_bumped` (`status`,`category_id`,`sticky`,`bumped_at`),
  KEY `torrents_idx_sticky_bumped_at` (`sticky`,`bumped_at`),
  KEY `torrents_idx_status_info_hash` (`status`),
  KEY `torrents_fl_until_du_until_index` (`fl_until`,`du_until`),
  KEY `torrents_info_hash_index` (`info_hash`),
  KEY `torrents_user_id_anon_status_created_at_index` (`user_id`,`anon`,`status`,`created_at`),
  KEY `torrents_status_sticky_created_at_index` (`status`,`sticky`,`created_at`),
  KEY `torrents_user_id_status_anon_deleted_at_index` (`user_id`,`status`,`anon`,`deleted_at`),
  KEY `torrents_name_index` (`name`),
  KEY `torrents_category_id_status_deleted_at_movie_id_size_index` (`category_id`,`status`,`deleted_at`,`tmdb_movie_id`,`size`),
  KEY `torrents_category_id_status_deleted_at_tv_id_size_index` (`category_id`,`status`,`deleted_at`,`tmdb_tv_id`,`size`),
  KEY `torrents_movie_id_index` (`tmdb_movie_id`),
  KEY `torrents_tv_id_index` (`tmdb_tv_id`),
  CONSTRAINT `torrents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `torrents_resolution_id_foreign` FOREIGN KEY (`resolution_id`) REFERENCES `resolutions` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `torrents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `torrents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `unregistered_info_hashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unregistered_info_hashes` (
  `user_id` int unsigned NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`info_hash`,`user_id`),
  KEY `unregistered_info_hashes_user_id_foreign` (`user_id`),
  CONSTRAINT `unregistered_info_hashes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_audibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_audibles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `room_id` int DEFAULT NULL,
  `target_id` int unsigned DEFAULT NULL,
  `bot_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_audibles_room_id_index` (`room_id`),
  KEY `user_audibles_bot_id_index` (`bot_id`),
  KEY `user_audibles_status_index` (`status`),
  KEY `user_audibles_user_id_foreign` (`user_id`),
  KEY `user_audibles_target_id_foreign` (`target_id`),
  CONSTRAINT `user_audibles_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_audibles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_echoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_echoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `room_id` int DEFAULT NULL,
  `target_id` int unsigned DEFAULT NULL,
  `bot_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_echoes_room_id_index` (`room_id`),
  KEY `user_echoes_bot_id_index` (`bot_id`),
  KEY `user_echoes_user_id_foreign` (`user_id`),
  KEY `user_echoes_target_id_foreign` (`target_id`),
  CONSTRAINT `user_echoes_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_echoes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `staff_id` int unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_notes_user_id_foreign` (`user_id`),
  KEY `user_notes_staff_id_foreign` (`staff_id`),
  CONSTRAINT `user_notes_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `block_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `show_bon_gift` tinyint(1) NOT NULL DEFAULT '1',
  `show_mention_forum_post` tinyint(1) NOT NULL DEFAULT '1',
  `show_mention_article_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_mention_request_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_mention_torrent_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_subscription_topic` tinyint(1) NOT NULL DEFAULT '1',
  `show_subscription_forum` tinyint(1) NOT NULL DEFAULT '1',
  `show_forum_topic` tinyint(1) NOT NULL DEFAULT '1',
  `show_following_upload` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_bounty` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_fill` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_fill_approve` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_fill_reject` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_claim` tinyint(1) NOT NULL DEFAULT '1',
  `show_request_unclaim` tinyint(1) NOT NULL DEFAULT '1',
  `show_torrent_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_torrent_tip` tinyint(1) NOT NULL DEFAULT '1',
  `show_torrent_thank` tinyint(1) NOT NULL DEFAULT '1',
  `show_account_follow` tinyint(1) NOT NULL DEFAULT '1',
  `show_account_unfollow` tinyint(1) NOT NULL DEFAULT '1',
  `json_account_groups` json NOT NULL,
  `json_bon_groups` json NOT NULL,
  `json_mention_groups` json NOT NULL,
  `json_request_groups` json NOT NULL,
  `json_torrent_groups` json NOT NULL,
  `json_forum_groups` json NOT NULL,
  `json_following_groups` json NOT NULL,
  `json_subscription_groups` json NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_notifications_user_id_unique` (`user_id`),
  KEY `user_notifications_show_bon_gift_index` (`show_bon_gift`),
  KEY `user_notifications_show_mention_forum_post_index` (`show_mention_forum_post`),
  KEY `user_notifications_show_mention_article_comment_index` (`show_mention_article_comment`),
  KEY `user_notifications_show_mention_request_comment_index` (`show_mention_request_comment`),
  KEY `user_notifications_show_mention_torrent_comment_index` (`show_mention_torrent_comment`),
  KEY `user_notifications_show_subscription_topic_index` (`show_subscription_topic`),
  KEY `user_notifications_show_subscription_forum_index` (`show_subscription_forum`),
  KEY `user_notifications_show_forum_topic_index` (`show_forum_topic`),
  KEY `user_notifications_show_following_upload_index` (`show_following_upload`),
  KEY `user_notifications_show_request_bounty_index` (`show_request_bounty`),
  KEY `user_notifications_show_request_comment_index` (`show_request_comment`),
  KEY `user_notifications_show_request_fill_index` (`show_request_fill`),
  KEY `user_notifications_show_request_fill_approve_index` (`show_request_fill_approve`),
  KEY `user_notifications_show_request_fill_reject_index` (`show_request_fill_reject`),
  KEY `user_notifications_show_request_claim_index` (`show_request_claim`),
  KEY `user_notifications_show_request_unclaim_index` (`show_request_unclaim`),
  KEY `user_notifications_show_torrent_comment_index` (`show_torrent_comment`),
  KEY `user_notifications_show_torrent_tip_index` (`show_torrent_tip`),
  KEY `user_notifications_show_torrent_thank_index` (`show_torrent_thank`),
  KEY `user_notifications_show_account_follow_index` (`show_account_follow`),
  KEY `user_notifications_show_account_unfollow_index` (`show_account_unfollow`),
  CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_privacy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_privacy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `private_profile` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `show_achievement` tinyint(1) NOT NULL DEFAULT '1',
  `show_bon` tinyint(1) NOT NULL DEFAULT '1',
  `show_comment` tinyint(1) NOT NULL DEFAULT '1',
  `show_download` tinyint(1) NOT NULL DEFAULT '0',
  `show_follower` tinyint(1) NOT NULL DEFAULT '1',
  `show_online` tinyint(1) NOT NULL DEFAULT '1',
  `show_peer` tinyint(1) NOT NULL DEFAULT '1',
  `show_post` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_about` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_achievement` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_badge` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_follower` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_title` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_bon_extra` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_comment_extra` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_forum_extra` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_request_extra` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_torrent_count` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_torrent_extra` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_torrent_ratio` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_torrent_seed` tinyint(1) NOT NULL DEFAULT '1',
  `show_profile_warning` tinyint(1) NOT NULL DEFAULT '1',
  `show_rank` tinyint(1) NOT NULL DEFAULT '1',
  `show_requested` tinyint(1) NOT NULL DEFAULT '1',
  `show_topic` tinyint(1) NOT NULL DEFAULT '1',
  `show_upload` tinyint(1) NOT NULL DEFAULT '0',
  `show_wishlist` tinyint(1) NOT NULL DEFAULT '1',
  `json_profile_groups` json NOT NULL,
  `json_torrent_groups` json NOT NULL,
  `json_forum_groups` json NOT NULL,
  `json_bon_groups` json NOT NULL,
  `json_comment_groups` json NOT NULL,
  `json_wishlist_groups` json NOT NULL,
  `json_follower_groups` json NOT NULL,
  `json_achievement_groups` json NOT NULL,
  `json_rank_groups` json NOT NULL,
  `json_request_groups` json NOT NULL,
  `json_other_groups` json NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_privacy_user_id_unique` (`user_id`),
  KEY `user_privacy_show_achievement_index` (`show_achievement`),
  KEY `user_privacy_show_bon_index` (`show_bon`),
  KEY `user_privacy_show_comment_index` (`show_comment`),
  KEY `user_privacy_show_download_index` (`show_download`),
  KEY `user_privacy_show_follower_index` (`show_follower`),
  KEY `user_privacy_show_post_index` (`show_post`),
  KEY `user_privacy_show_profile_index` (`show_profile`),
  KEY `user_privacy_show_profile_about_index` (`show_profile_about`),
  KEY `user_privacy_show_profile_achievement_index` (`show_profile_achievement`),
  KEY `user_privacy_show_profile_badge_index` (`show_profile_badge`),
  KEY `user_privacy_show_profile_follower_index` (`show_profile_follower`),
  KEY `user_privacy_show_profile_title_index` (`show_profile_title`),
  KEY `user_privacy_show_profile_bon_extra_index` (`show_profile_bon_extra`),
  KEY `user_privacy_show_profile_comment_extra_index` (`show_profile_comment_extra`),
  KEY `user_privacy_show_profile_forum_extra_index` (`show_profile_forum_extra`),
  KEY `user_privacy_show_profile_torrent_count_index` (`show_profile_torrent_count`),
  KEY `user_privacy_show_profile_torrent_extra_index` (`show_profile_torrent_extra`),
  KEY `user_privacy_show_profile_torrent_ratio_index` (`show_profile_torrent_ratio`),
  KEY `user_privacy_show_profile_torrent_seed_index` (`show_profile_torrent_seed`),
  KEY `user_privacy_show_profile_warning_index` (`show_profile_warning`),
  KEY `user_privacy_show_rank_index` (`show_rank`),
  KEY `user_privacy_show_topic_index` (`show_topic`),
  KEY `user_privacy_show_upload_index` (`show_upload`),
  KEY `user_privacy_show_wishlist_index` (`show_wishlist`),
  KEY `user_privacy_show_profile_request_extra_index` (`show_profile_request_extra`),
  KEY `user_privacy_show_online_index` (`show_online`),
  KEY `user_privacy_show_peer_index` (`show_peer`),
  KEY `user_privacy_show_requested_index` (`show_requested`),
  CONSTRAINT `user_privacy_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `censor` tinyint(1) NOT NULL DEFAULT '0',
  `chat_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `locale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `style` tinyint unsigned NOT NULL DEFAULT '0',
  `torrent_layout` tinyint unsigned NOT NULL DEFAULT '0',
  `torrent_filters` tinyint(1) NOT NULL DEFAULT '0',
  `custom_css` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `standalone_css` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_poster` tinyint(1) NOT NULL DEFAULT '0',
  `unbookmark_torrents_on_completion` tinyint(1) NOT NULL,
  `torrent_sort_field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `torrent_search_autofocus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_settings_user_id_unique` (`user_id`),
  CONSTRAINT `user_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `passkey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded` bigint unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint unsigned NOT NULL DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `signature` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fl_tokens` int unsigned NOT NULL DEFAULT '0',
  `seedbonus` decimal(12,2) NOT NULL DEFAULT '0.00',
  `invites` int unsigned NOT NULL DEFAULT '0',
  `hitandruns` int unsigned NOT NULL DEFAULT '0',
  `rsskey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `chatroom_id` int unsigned NOT NULL DEFAULT '1',
  `read_rules` tinyint(1) NOT NULL DEFAULT '0',
  `can_chat` tinyint(1) DEFAULT NULL,
  `can_comment` tinyint(1) DEFAULT NULL,
  `can_download` tinyint(1) NOT NULL DEFAULT '1',
  `can_invite` tinyint(1) DEFAULT NULL,
  `can_request` tinyint(1) DEFAULT NULL,
  `can_upload` tinyint(1) DEFAULT NULL,
  `is_donor` tinyint(1) NOT NULL DEFAULT '0',
  `is_lifetime` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_action` datetime DEFAULT NULL,
  `disabled_at` datetime DEFAULT NULL,
  `deleted_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `chat_status_id` int unsigned NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `own_flushes` tinyint NOT NULL DEFAULT '2',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_passkey_unique` (`passkey`),
  UNIQUE KEY `users_rsskey_unique` (`rsskey`),
  UNIQUE KEY `users_api_token_unique` (`api_token`),
  KEY `fk_users_groups_idx` (`group_id`),
  KEY `users_read_rules_index` (`read_rules`),
  KEY `users_deleted_at_index` (`deleted_at`),
  KEY `users_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `users_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `check_users_seedbonus` CHECK ((`seedbonus` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `voters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voters_poll_id_foreign` (`poll_id`),
  KEY `voters_user_id_foreign` (`user_id`),
  CONSTRAINT `voters_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `voters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warnings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `warned_by` int unsigned NOT NULL,
  `torrent` int unsigned DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_on` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warnings_user_id_foreign` (`user_id`),
  KEY `warnings_warned_by_foreign` (`warned_by`),
  KEY `warnings_torrent_foreign` (`torrent`),
  KEY `warnings_deleted_by_foreign` (`deleted_by`),
  KEY `warnings_user_id_active_deleted_at_index` (`user_id`,`active`,`deleted_at`),
  CONSTRAINT `warnings_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `warnings_torrent_foreign` FOREIGN KEY (`torrent`) REFERENCES `torrents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `warnings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `warnings_warned_by_foreign` FOREIGN KEY (`warned_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `watchlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `watchlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `staff_id` int unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `watchlists_user_id_unique` (`user_id`),
  KEY `watchlists_staff_id_foreign` (`staff_id`),
  CONSTRAINT `watchlists_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `watchlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `whitelisted_image_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whitelisted_image_urls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whitelisted_image_urls_pattern_unique` (`pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wiki_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wiki_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-book',
  `position` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wikis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wikis` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wikis_category_id_index` (`category_id`),
  CONSTRAINT `wikis_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `wiki_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wishes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tmdb_movie_id` int unsigned DEFAULT NULL,
  `tmdb_tv_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wishes_user_id_foreign` (`user_id`),
  KEY `wishes_tv_id_index` (`tmdb_tv_id`),
  CONSTRAINT `wishes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0000_00_00_000000_create_achievements_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2017_12_10_020753_create_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2017_12_10_020753_create_ban_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2017_12_10_020753_create_bon_exchange_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2017_12_10_020753_create_bon_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2017_12_10_020753_create_bookmarks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2017_12_10_020753_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2017_12_10_020753_create_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2017_12_10_020753_create_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2017_12_10_020753_create_failed_login_attempts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2017_12_10_020753_create_featured_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2017_12_10_020753_create_files_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2017_12_10_020753_create_follows_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2017_12_10_020753_create_forums_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2017_12_10_020753_create_graveyard_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2017_12_10_020753_create_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2017_12_10_020753_create_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2017_12_10_020753_create_invites_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2017_12_10_020753_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2017_12_10_020753_create_likes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2017_12_10_020753_create_log_activities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2017_12_10_020753_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2017_12_10_020753_create_options_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2017_12_10_020753_create_pages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2017_12_10_020753_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2017_12_10_020753_create_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2017_12_10_020753_create_permissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2017_12_10_020753_create_personal_freeleech_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2017_12_10_020753_create_polls_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2017_12_10_020753_create_posts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2017_12_10_020753_create_private_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2017_12_10_020753_create_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2017_12_10_020753_create_request_bounty_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2017_12_10_020753_create_request_claims_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2017_12_10_020753_create_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2017_12_10_020753_create_rss_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2017_12_10_020753_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2017_12_10_020753_create_shoutbox_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2017_12_10_020753_create_tag_torrent_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2017_12_10_020753_create_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2017_12_10_020753_create_thanks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2017_12_10_020753_create_topics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2017_12_10_020753_create_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2017_12_10_020753_create_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2017_12_10_020753_create_user_activations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2017_12_10_020753_create_user_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2017_12_10_020753_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2017_12_10_020753_create_voters_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2017_12_10_020753_create_warnings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2017_12_10_020754_add_foreign_keys_to_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2017_12_10_020754_add_foreign_keys_to_ban_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2017_12_10_020754_add_foreign_keys_to_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2017_12_10_020754_add_foreign_keys_to_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2017_12_10_020754_add_foreign_keys_to_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2017_12_10_020754_add_foreign_keys_to_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2017_12_10_020754_add_foreign_keys_to_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2017_12_10_020754_add_foreign_keys_to_rss_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2017_12_10_020754_add_foreign_keys_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2017_12_10_020754_add_foreign_keys_to_voters_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2017_12_10_020754_add_foreign_keys_to_warnings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2017_12_10_020755_add_two_factor_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2017_12_21_123452_add_custom_css_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2017_12_27_000000_add_locale_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2018_01_23_095412_add_implemented_to_topics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2018_01_25_000000_add_twostep_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2018_02_06_142024_add_last_reply_at_to_topics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2018_02_14_000000_add_is_internal_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2018_03_13_000000_add_position_to_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2018_03_21_000000_add_censor_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2018_03_27_000000_add_chat_hidden_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2018_04_19_221542_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2018_04_21_181026_create_wishes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2018_04_22_195516_alter_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2018_04_28_021651_alter_shoutbox_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2018_04_28_022305_create_chatrooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2018_04_28_022344_add_chatroom_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2018_05_04_101711_create_chat_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2018_05_04_102055_add_chat_status_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2018_05_07_183534_add_can_upload_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2018_05_15_223339_add_receiver_id_column_to_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2018_05_18_144651_rename_ban_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2018_05_21_022459_add_torrent_layout_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2018_05_21_192858_alter_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2018_05_22_224911_alter_private_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2018_05_31_120936_create_albums_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2018_05_31_120955_create_images_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2018_06_11_110000_create_topic_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2018_07_12_114125_add_soft_deletes_to_warnings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2018_08_19_212319_create_git_updates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2018_09_08_153849_add_soft_deletes_to_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2018_09_24_205852_add_internal_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2018_09_29_163937_add_anon_to_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2018_09_29_164525_add_anon_to_request_bounty_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2018_11_09_010002_add_immune_to_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2018_12_03_024251_create_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2018_12_03_032701_create_application_image_proofs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2018_12_03_032712_create_application_url_proofs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2018_12_06_012908_update_tag_torrent_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2018_1_10_020753_create_freeleech_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2018_1_20_070937_create_two_step_auth_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2019_01_09_151754_alter_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2019_01_09_175336_add_incognito_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2019_01_10_102512_add_request_id_to_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2019_01_11_001150_alter_rss_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2019_01_17_213210_add_torrent_filters_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2019_01_23_034500_alter_bon_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2019_01_24_033802_rename_topic_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2019_01_24_190220_alter_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2019_01_27_005216_create_user_privacy_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2019_01_28_031842_alter_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2019_01_28_225127_create_user_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2019_01_29_054104_alter_users_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2019_02_04_041644_create_user_echoes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2019_02_05_220444_create_bots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2019_02_06_005248_add_bot_id_to_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2019_02_06_075938_create_bot_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2019_02_07_022409_create_user_audibles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2019_02_10_010213_fix_chat_related_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2019_02_21_133950_add_is_owner_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2019_02_21_221047_add_request_to_user_privacy_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2019_03_20_214306_alter_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2019_06_17_172554_add_last_action_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2019_07_09_225645_add_release_year_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2019_07_30_210848_create_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2019_07_30_210849_create_seasons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2019_07_30_210850_create_cast_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2019_07_30_210850_create_collection_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2019_07_30_210850_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2019_07_30_210850_create_episodes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2019_07_30_210850_create_genres_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2019_07_30_210850_create_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2019_07_30_210850_create_networks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2019_07_30_210850_create_person_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2019_07_31_024816_alter_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2019_07_31_210850_create_cast_episode_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2019_07_31_210850_create_cast_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2019_07_31_210850_create_cast_season_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2019_07_31_210850_create_cast_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2019_07_31_210850_create_company_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2019_07_31_210850_create_crew_episode_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2019_07_31_210850_create_crew_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2019_07_31_210850_create_crew_season_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2019_07_31_210850_create_crew_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2019_07_31_210850_create_episode_guest_star_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2019_07_31_210850_create_episode_person_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2019_07_31_210850_create_genre_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2019_07_31_210850_create_network_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2019_07_31_210850_create_person_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2019_07_31_210850_create_person_tv_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2019_07_31_210851_create_collection_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2019_07_31_210851_create_company_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2019_07_31_210851_create_genre_movie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2019_07_31_210851_create_person_season_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2019_09_22_204439_create_playlists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2019_09_22_204613_create_playlist_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2019_09_24_160123_alter_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2019_11_05_233558_create_audits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2019_11_27_025048_add_api_token_field_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2019_12_17_030908_create_keywords_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2020_01_02_203432_bdinfo_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2020_02_14_185120_add_foreign_key_to_options_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2020_02_14_202935_drop_ip_checking_in_polls_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2020_02_14_203001_drop_ip_address_in_voters_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2020_03_02_031656_update_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2020_03_26_030235_create_subtitles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2020_03_26_034620_create_media_languages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2020_03_31_201107_add_is_double_upload_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2020_05_19_023939_add_type_id_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2020_05_26_053632_add_type_id_to_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2020_06_06_185230_create_resolutions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2020_06_07_023938_add_resolution_id_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2020_06_07_054632_add_resolution_id_to_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2020_06_10_014256_unique_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2020_06_18_115296_add_bumped_at_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2020_07_07_202935_drop_tags_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2020_10_06_143759_add_uuid_to_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2020_10_07_012129_create_job_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2020_10_18_235628_create_genre_torrent_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2020_11_01_165838_update_wishes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2021_01_02_230512_update_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2021_01_06_360572_update_nfo_column_on_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2021_01_18_191121_create_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2021_01_18_191321_create_ticket_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2021_01_18_191336_create_ticket_priorities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2021_01_18_191357_create_ticket_attachments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2021_01_18_191596_add_ticket_id_to_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2021_03_04_042851_create_watchlists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2021_03_11_024605_add_personal_release_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2021_03_14_093812_add_read_column_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2021_04_13_200421_update_about_column_on_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2021_04_18_085155_add_internals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2021_05_26_215430_create_recommendations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2021_06_28_123452_add_standalone_css_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2021_07_08_135537_add_flush_own_peers_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2021_07_27_140562_change_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2021_07_27_185231_create_distributors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2021_07_27_285231_create_regions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2021_07_31_172708_add_connectable_state_to_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2021_08_20_121103_change_torrent_to_nullable_in_warning',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2021_10_03_180121_add_indexes_to_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2021_11_22_115517_add_more_torrent_promos',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2021_11_26_024738_update_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2021_12_19_202317_fix_database_indexs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2022_01_23_232931_update_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2022_02_03_080630_update_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2022_02_03_090219_update_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2022_02_06_210013_update_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2022_02_21_162827_create_torrent_downloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2022_04_27_143156_update_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2022_08_29_030244_update_history_table_add_refundable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2022_08_29_030525_update_torrents_table_add_refundable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2022_08_29_031309_update_groups_table_add_refundable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2022_08_29_155715_create_client_blacklist_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2022_09_29_182332_alter_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2022_11_23_024350_update_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2022_11_23_195306_update_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2022_11_24_032502_update_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2022_11_24_032521_update_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2022_11_27_062458_drop_old_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2022_11_29_010000_alter_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2022_11_29_010010_alter_bon_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2022_11_29_030020_alter_user_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2022_12_05_012617_drop_conversations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2022_12_21_014703_alter_torrent_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2022_12_22_004317_update_peers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2022_12_22_213142_update_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2022_12_23_103322_update_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2022_12_24_222839_update_follows_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (229,'2022_12_30_090331_update_user_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2022_12_30_090351_update_user_privacy_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2023_01_06_194157_remove_slugs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2023_02_03_094806_update_rss_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (233,'2023_02_09_113903_clean_torrent_files',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2023_02_27_164336_credits_refactor',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2023_04_08_053641_alter_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2023_06_13_092029_alter_invites_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (237,'2023_06_14_102346_delete_user_activations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2023_07_16_010906_add_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (239,'2023_07_20_084446_drop_distributor_position',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2023_07_22_023920_alter_movie_and_tv_ids',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (241,'2023_07_22_043634_post_playlist_html_special_chars_decode',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (242,'2023_07_22_165745_add_active_column_to_peers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (243,'2023_07_22_204126_rename_bon_transactions_foreign_keys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (244,'2023_07_23_190319_drop_genre_torrent_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (245,'2023_07_23_192525_rename_graveyard_to_resurrections',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (246,'2023_07_23_220207_alter_mediahub_ids',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (247,'2023_07_29_205035_add_torrent_folder_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (248,'2023_07_31_043749_drop_announce_column_from_torrents',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (249,'2023_08_05_231341_swap_username_for_user_id_on_request_claims',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (250,'2023_08_13_234828_add_forum_foreign_key_constraints',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (251,'2023_09_10_234654_create_blocked_ips_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (252,'2023_11_06_152351_drop_2fa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2023_11_12_223126_create_passkeys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2023_11_15_170525_create_apikeys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (255,'2023_11_16_084506_create_rsskeys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (256,'2023_11_16_122533_create_announces',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (257,'2023_12_19_133124_create_wiki_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2023_12_19_233124_create_wikis_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (259,'2023_12_22_221619_plural_table_names',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2023_12_30_092415_add_peer_id_prefix_to_blacklist_client',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (261,'2024_01_08_025430_update_meta_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (262,'2024_01_12_092724_alter_history_table_64_int_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (263,'2024_01_15_151522_update_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (264,'2024_01_21_062125_create_email_updates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2024_01_23_115425_add_created_at_index_to_torrent_downloads',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2024_02_02_222845_create_automatic_torrent_freeleeches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2024_02_04_012321_remove_user_ratings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (268,'2024_02_07_192449_add_requirements_to_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (269,'2024_02_07_213520_add_visible_to_peers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (270,'2024_02_08_095758_add_last_post_id_to_topics',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2024_02_08_144018_add_system_required_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (272,'2024_02_09_190708_remove_show_forum_from_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (273,'2024_02_10_140207_create_forum_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (274,'2024_02_13_033340_create_topic_reads',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (275,'2024_02_14_022118_fix_subtitle_filepaths',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (276,'2024_02_19_100212_add_primary_keys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2024_02_19_100813_alter_year_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2024_02_19_102057_alter_floats_to_decimal',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (279,'2024_02_19_233644_add_permission_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (280,'2024_02_22_015442_create_post_tips_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2024_02_22_033718_create_gifts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (282,'2024_02_22_092555_create_torrent_tips_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (283,'2024_02_23_154435_remove_request_bon_transactions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2024_02_23_211021_create_internal_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (285,'2024_02_24_233030_rename_permissions_to_forum_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2024_02_26_000850_create_whitelisted_image_domains',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2024_03_06_062526_add_open_topics_to_forums',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (288,'2024_03_06_154000_add_user_indexes_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2024_03_19_211512_create_ticket_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2024_03_21_145139_add_group_description',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (291,'2024_04_30_063509_remove_casino_triva_bet_bot',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (292,'2024_05_06_212348_create_donations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (293,'2024_05_06_212410_create_donation_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2024_05_06_212446_create_donation_gateways_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2024_05_08_000014_add_min_uploads_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2024_05_23_184913_alter_wishes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (297,'2024_05_26_034811_create_user_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (298,'2024_05_26_043410_drop_user_stat_hidden_peer_hidden',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (299,'2024_05_29_075428_add_torrent_sort_field_to_user_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (300,'2024_06_04_115016_add_torrent_search_autofocus_to_user_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2024_06_06_042258_create_private_message_conversations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2024_06_09_052006_drop_useless_columns_from_bots',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (303,'2024_06_13_125854_add_is_uploader_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2024_06_13_185043_add_donation_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2024_06_14_005443_add_soft_deletes_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (306,'2024_06_23_202341_add_prewarned_at_to_history',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (307,'2024_07_02_082323_add_indexes_to_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (308,'2024_07_16_083832_add_composite_primary_key_to_peers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (309,'2024_07_19_143828_add_priority_to_topics',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (310,'2024_07_23_054141_add_composite_primary_key_to_history',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (311,'2024_07_23_054751_create_torrent_trumps_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (312,'2024_07_23_153202_add_poll_expiry',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (313,'2024_07_26_211112_fix_some_user_group_perms',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (314,'2024_07_28_231553_update_cat_type_res_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (315,'2024_08_14_012412_drop_release_year_from_torrents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (316,'2024_08_17_140412_create_password_reset_history',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (317,'2024_08_25_121227_drop_num_torrent_from_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (318,'2024_08_26_085452_fix_bot_systembot_nerdbot_flags',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (319,'2024_09_02_224259_alter_reports_add_snooze_until',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (320,'2024_09_27_072554_add_indexes_to_audits',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (321,'2024_09_29_041904_add_indexes_for_top10_performance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (322,'2024_10_10_140532_update_mediainfo_from_text_to_longtext',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (323,'2024_10_13_221353_create_events_claimed_prizes_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (324,'2024_10_29_180417_add_internal_note_to_invites_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (325,'2024_11_01_013426_add_soft_deletes_to_donation_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (326,'2024_11_13_044550_create_unregistered_info_hashes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (327,'2024_11_26_170256_add_is_torrent_modo_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (328,'2024_07_03_085223_htmlspecialchars_decode_bbcode',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (329,'2024_11_03_081202_add_timestamps_to_playlist_torrents',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (330,'2024_12_06_004504_add_soft_deletes_to_history_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (331,'2024_12_09_175613_add_index_on_created_at_for_comments',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (332,'2024_12_26_031647_bon_earnings_v2',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (333,'2025_02_10_164236_drop_featured_column_from_torrents_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (334,'2025_02_17_074140_update_columns_to_boolean',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (335,'2025_02_19_063416_create_index_on_reports_solved_snoozed_until',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (336,'2025_02_23_221840_move_public_files_to_private',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (337,'2025_03_06_143003_remove_sd_and_stream_from_torrents_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (338,'2025_03_11_131917_create_igdb_games_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (339,'2025_03_12_043518_split_torrents_tmdb_into_movie_id_and_tv_id',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (340,'2025_03_16_185628_update_torrents_table_to_int_igdb',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (341,'2025_03_17_122748_add_tmdb_prefix_to_metadata_tables',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (342,'2025_03_23_203227_add_automatically_unbookmark_torrents_user_setting',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (343,'2025_03_25_093436_update_metadata_id_default_to_null',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (344,'2025_03_29_215845_create_playlist_categories',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (345,'2025_04_03_085022_drop_season_and_episodes',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (346,'2025_04_07_152108_split_recommendations_into_movie_and_tv',8);
