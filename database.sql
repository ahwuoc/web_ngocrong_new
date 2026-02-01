-- ============================================
-- DATABASE SCHEMA FOR WEB_NRO
-- ============================================
-- Database: web_nro
-- Charset: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- ============================================

-- Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `web_nro` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `web_nro`;

-- ============================================
-- 1. BẢNG ACCOUNT (Tài khoản người dùng)
-- ============================================
CREATE TABLE IF NOT EXISTS `account` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
    `ban` TINYINT(1) NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 0,
    `danap` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Tổng số tiền đã nạp',
    `cash` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền hiện có',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    KEY `idx_active` (`active`),
    KEY `idx_is_admin` (`is_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. BẢNG PLAYER (Nhân vật trong game)
-- ============================================
CREATE TABLE IF NOT EXISTS `player` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `data_point` TEXT COMMENT 'JSON array chứa thông tin sức mạnh và các chỉ số',
    `data_task` TEXT COMMENT 'JSON array chứa thông tin nhiệm vụ',
    `head` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID avatar head',
    `gender` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Trái Đất, 1=Namec, 2=Xayda',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_id` (`account_id`),
    KEY `idx_name` (`name`),
    CONSTRAINT `fk_player_account` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. BẢNG CATEGORIES (Danh mục bài viết)
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. BẢNG POSTS (Bài viết)
-- ============================================
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `excerpt` TEXT COMMENT 'Tóm tắt bài viết',
    `featured_image` VARCHAR(500) DEFAULT NULL,
    `category_id` INT(11) UNSIGNED NOT NULL,
    `author_id` INT(11) UNSIGNED NOT NULL,
    `status` ENUM('published', 'draft', 'trash') NOT NULL DEFAULT 'draft',
    `views` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `published_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_author_id` (`author_id`),
    KEY `idx_status` (`status`),
    KEY `idx_published_at` (`published_at`),
    CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `account` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. BẢNG GIFTCODE (Mã quà tặng)
-- ============================================
CREATE TABLE IF NOT EXISTS `giftcode` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `count_left` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lượt còn lại',
    `detail` TEXT COMMENT 'JSON array chứa thông tin phần thưởng',
    `expired` DATETIME NOT NULL COMMENT 'Ngày hết hạn',
    `datecreate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    KEY `idx_expired` (`expired`),
    KEY `idx_count_left` (`count_left`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. BẢNG ITEM_TEMPLATE (Template vật phẩm)
-- ============================================
CREATE TABLE IF NOT EXISTS `item_template` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `icon_id` VARCHAR(50) DEFAULT NULL COMMENT 'ID icon vật phẩm',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. BẢNG ITEM_TEMPLATE_OPTION (Tùy chọn vật phẩm)
-- ============================================
CREATE TABLE IF NOT EXISTS `item_template_option` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. BẢNG TASK_MAIN_TEMPLATE (Template nhiệm vụ)
-- ============================================
CREATE TABLE IF NOT EXISTS `task_main_template` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. BẢNG HEAD_AVATAR (Avatar nhân vật)
-- ============================================
CREATE TABLE IF NOT EXISTS `head_avatar` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `head_id` INT(11) UNSIGNED NOT NULL,
    `avatar_id` VARCHAR(50) NOT NULL COMMENT 'ID file avatar (ví dụ: 12345.png)',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `head_id` (`head_id`),
    KEY `idx_avatar_id` (`avatar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. BẢNG SLIDES (Slide quảng cáo)
-- ============================================
CREATE TABLE IF NOT EXISTS `slides` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `image` VARCHAR(500) NOT NULL,
    `link` VARCHAR(500) DEFAULT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT(11) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. BẢNG SETTINGS (Cài đặt hệ thống)
-- ============================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key_name` VARCHAR(100) NOT NULL,
    `value` TEXT,
    `description` VARCHAR(255) DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. BẢNG USER_SESSIONS (Phiên đăng nhập)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `session_token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `session_token` (`session_token`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires_at` (`expires_at`),
    CONSTRAINT `fk_user_sessions_account` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DỮ LIỆU MẪU
-- ============================================

-- Insert categories mẫu
INSERT INTO `categories` (`name`, `slug`, `description`, `status`) VALUES
('Tin tức', 'tin-tuc', 'Các tin tức mới nhất về game', 'active'),
('Sự kiện', 'su-kien', 'Các sự kiện đặc biệt trong game', 'active'),
('Hướng dẫn', 'huong-dan', 'Hướng dẫn chơi game', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert settings mẫu
INSERT INTO `settings` (`key_name`, `value`, `description`) VALUES
('site_name', 'Web NRO', 'Tên website'),
('site_description', 'Website chính thức của game NRO', 'Mô tả website'),
('site_keywords', 'game, nro, dragon ball', 'Từ khóa SEO'),
('facebook_url', 'https://facebook.com', 'Link Facebook fanpage'),
('facebook_group_url', 'https://facebook.com/groups', 'Link Facebook group'),
('ios_download_url', '#', 'Link tải iOS'),
('android_download_url', '#', 'Link tải Android'),
('apk_download_url', '#', 'Link tải APK'),
('payment_url', '#', 'Link nạp tiền')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

-- Insert admin account mẫu (username: admin, password: admin123)
-- Lưu ý: Nên đổi mật khẩu sau khi import
INSERT INTO `account` (`username`, `email`, `password`, `is_admin`, `active`, `create_time`) VALUES
('admin', 'admin@example.com', 'admin123', 1, 1, NOW())
ON DUPLICATE KEY UPDATE `username`=VALUES(`username`);

-- ============================================
-- END OF SCRIPT
-- ============================================

