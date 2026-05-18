CREATE DATABASE IF NOT EXISTS `blood_donation`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `blood_donation`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blood_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `requester_user_id` INT UNSIGNED NOT NULL,
  `blood_type` VARCHAR(3) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `units` INT UNSIGNED NOT NULL DEFAULT 1,
  `status` ENUM('open', 'fulfilled', 'cancelled') NOT NULL DEFAULT 'open',
  `notes` TEXT NULL,
  `contact_name` VARCHAR(100) NOT NULL,
  `contact_phone` VARCHAR(30) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `blood_requests_requester_user_id_index` (`requester_user_id`),
  CONSTRAINT `blood_requests_requester_user_id_foreign`
    FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blood_request_responses` (
  `request_id` INT UNSIGNED NOT NULL,
  `donor_user_id` INT UNSIGNED NOT NULL,
  `decision` ENUM('accept', 'decline') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`, `donor_user_id`),
  KEY `blood_request_responses_donor_user_id_index` (`donor_user_id`),
  CONSTRAINT `blood_request_responses_request_id_foreign`
    FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blood_request_responses_donor_user_id_foreign`
    FOREIGN KEY (`donor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing databases: add request contact columns if missing.
-- ALTER TABLE `blood_requests`
--   ADD COLUMN `contact_name` VARCHAR(100) NOT NULL DEFAULT '' AFTER `notes`,
--   ADD COLUMN `contact_phone` VARCHAR(30) NOT NULL DEFAULT '' AFTER `contact_name`;
