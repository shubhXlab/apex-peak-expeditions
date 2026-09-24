-- ==============================================================================
-- APEX EXPEDITIONS PRO & DATABASE ENGINE - CORE SCHEMA
-- Description: Custom MySQL database architecture for high-altitude expedition
--              fleet operations, lead management, gear rental, and climber reviews.
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- Table 1: wp_apex_expeditions
-- Description: Master catalog for mountain departures, guide ratios, and pricing.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wp_apex_expeditions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elevation_m` int(11) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `difficulty` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `guide_ratio` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_slots` int(11) DEFAULT 6,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 2: wp_apex_bookings
-- Description: Real-time transactional booking submissions & expedition leads.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wp_apex_bookings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `expedition_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `climber_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `climber_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `climber_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure_season` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience_level` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Confirmed Lead',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 3: wp_apex_gear_inventory
-- Description: Certified high-altitude hardgoods and technical gear locker.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wp_apex_gear_inventory` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `daily_price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 10,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 4: wp_apex_reviews
-- Description: Verified climber summit accounts, dispatches, and ratings.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wp_apex_reviews` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `climber_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int(2) DEFAULT 5,
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summit_year` int(4) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Data: Expeditions
-- ------------------------------------------------------------------------------
INSERT INTO `wp_apex_expeditions` (`name`, `region`, `elevation_m`, `duration_days`, `difficulty`, `price_usd`, `guide_ratio`, `image_url`, `available_slots`, `status`) VALUES
('Mont Blanc Classic Traverse', 'French Alps (Chamonix)', 4809, 6, 'Level 1: Alpine Skills', 2850.00, '2:1 Max', '/wp-content/uploads/2026/09/mont-blanc-climb.jpg', 4, 'active'),
('Matterhorn Hörnli Ridge', 'Pennine Alps (Switzerland)', 4478, 5, 'Level 2: Classic Technical', 4650.00, '1:1 Strict', '/wp-content/uploads/2026/09/matterhorn-peak-ascent.jpg', 2, 'active'),
('Ama Dablam Technical Expedition', 'Khumbu Himalayas (Nepal)', 6812, 28, 'Level 3: Himalayan Giant', 9400.00, '1:1 High Altitude Sherpa', '/wp-content/uploads/2026/09/ama-dablam-peak.jpg', 3, 'active');

-- ------------------------------------------------------------------------------
-- Seed Data: Gear Inventory
-- ------------------------------------------------------------------------------
INSERT INTO `wp_apex_gear_inventory` (`item_name`, `category`, `daily_price`, `stock`) VALUES
('La Sportiva Olympus Mons Double Boots', 'Footwear', 15.00, 12),
('Petzl Lynx Modular Technical Crampons', 'Hardgoods', 8.00, 20),
('Petzl Quark Technical Ice Axe (Pair)', 'Hardgoods', 12.00, 16),
('Western Mountaineering -30°C Down Sleeping Bag', 'Sleep Systems', 14.00, 8),
('Garmin inReach Explorer+ Satellite Tracker', 'Electronics', 10.00, 15),
('Black Diamond Vapor Lightweight Climbing Helmet', 'Protection', 5.00, 25);

-- ------------------------------------------------------------------------------
-- Seed Data: Verified Reviews
-- ------------------------------------------------------------------------------
INSERT INTO `wp_apex_reviews` (`climber_name`, `origin`, `route_name`, `rating`, `review_text`, `summit_year`) VALUES
('Marcus Vance', 'Zurich, Switzerland', 'Ama Dablam Southwest Ridge', 5, 'The medical supervision and acclimatization discipline were unmatched. Our Sherpa guide was flawless on the yellow tower.', 2025),
('Dr. Clara Morales', 'Madrid, Spain', 'Mont Blanc Goûter Traverse', 5, 'The 2:1 guide ratio made all the difference when predawn winds picked up above the Dôme du Goûter. Pure professionals.', 2025),
('David Sterling', 'London, UK', 'Matterhorn Hörnli Ridge', 5, 'Strict 1:1 guiding ensured total precision on the Solvay slabs. Apex Peak delivered an unforgettable, safe summit.', 2024);

SET FOREIGN_KEY_CHECKS = 1;
