-- ============================================================
-- Game Center Store v2 - Full E-Commerce Schema
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- Database
-- ============================================================
CREATE DATABASE IF NOT EXISTS `game_center`;
USE `game_center`;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin account  (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@gamecenter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- NOTE: Replace the hash above with: password_hash('admin123', PASSWORD_DEFAULT)

-- ============================================================
-- Table: games  (extended from original)
-- ============================================================
CREATE TABLE IF NOT EXISTS `games` (
  `id`          INT(11)        NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(100)   NOT NULL,
  `genre`       VARCHAR(255)   DEFAULT NULL,
  `price`       DECIMAL(10,2)  DEFAULT 0.00,
  `image_url`   TEXT           DEFAULT NULL,
  `image_path`  VARCHAR(255)   DEFAULT NULL,
  `description` TEXT           DEFAULT NULL,
  `created_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: cart
-- ============================================================
CREATE TABLE `cart` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11) NOT NULL,
  `game_id`    INT(11) NOT NULL,
  `quantity`   INT(11) NOT NULL DEFAULT 1,
  `added_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_game` (`user_id`, `game_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`game_id`) REFERENCES `games`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: orders
-- ============================================================
CREATE TABLE `orders` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11)       NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status`      ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: order_items
-- ============================================================
CREATE TABLE `order_items` (
  `id`       INT(11)       NOT NULL AUTO_INCREMENT,
  `order_id` INT(11)       NOT NULL,
  `game_id`  INT(11)       NOT NULL,
  `quantity` INT(11)       NOT NULL DEFAULT 1,
  `price`    DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`game_id`)  REFERENCES `games`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
