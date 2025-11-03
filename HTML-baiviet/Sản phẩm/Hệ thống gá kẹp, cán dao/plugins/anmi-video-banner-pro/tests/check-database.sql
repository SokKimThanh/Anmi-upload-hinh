-- Check if new columns exist in database
-- Run this query in phpMyAdmin or WordPress database

SHOW COLUMNS FROM `wp_anmi_video_banners` LIKE 'video_%';

-- Expected output: 6 rows
-- video_autoplay
-- video_muted
-- video_loop
-- video_controls
-- video_modestbranding
-- video_rel

-- If columns are missing, run this to add them:
ALTER TABLE `wp_anmi_video_banners` 
ADD COLUMN IF NOT EXISTS `video_autoplay` tinyint(1) DEFAULT 1 AFTER `status`,
ADD COLUMN IF NOT EXISTS `video_muted` tinyint(1) DEFAULT 1 AFTER `video_autoplay`,
ADD COLUMN IF NOT EXISTS `video_loop` tinyint(1) DEFAULT 1 AFTER `video_muted`,
ADD COLUMN IF NOT EXISTS `video_controls` tinyint(1) DEFAULT 1 AFTER `video_loop`,
ADD COLUMN IF NOT EXISTS `video_modestbranding` tinyint(1) DEFAULT 1 AFTER `video_controls`,
ADD COLUMN IF NOT EXISTS `video_rel` tinyint(1) DEFAULT 0 AFTER `video_modestbranding`;

-- Verify table structure
DESCRIBE `wp_anmi_video_banners`;
