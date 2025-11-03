<?php
/**
 * Manual Database Migration Script for v1.6.13
 * 
 * Run this file ONCE by accessing:
 * http://your-site.com/wp-content/plugins/anmi-video-banner/migrate-v1.6.13.php
 * 
 * Then DELETE this file after successful migration.
 */

// Security check - must be run from WordPress context
define('WP_USE_THEMES', false);
require_once('../../../../../wp-load.php');

// Only allow admin users
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

global $wpdb;
$table_name = $wpdb->prefix . 'anmi_video_banners';

echo '<h1>An Mi Video Banner - Database Migration v1.6.13</h1>';
echo '<p>Adding 6 new columns for video settings...</p>';
echo '<hr>';

// Check if columns already exist
$columns = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE 'video_%'");

echo '<h2>Current video_* columns:</h2>';
echo '<ul>';
foreach ($columns as $column) {
    echo '<li>' . esc_html($column->Field) . ' - ' . esc_html($column->Type) . '</li>';
}
echo '</ul>';
echo '<p>Found ' . count($columns) . ' columns starting with "video_"</p>';
echo '<hr>';

// Add missing columns
$migrations = array(
    'video_autoplay' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_autoplay` tinyint(1) DEFAULT 1 AFTER `status`",
    'video_muted' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_muted` tinyint(1) DEFAULT 1 AFTER `video_autoplay`",
    'video_loop' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_loop` tinyint(1) DEFAULT 1 AFTER `video_muted`",
    'video_controls' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_controls` tinyint(1) DEFAULT 1 AFTER `video_loop`",
    'video_modestbranding' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_modestbranding` tinyint(1) DEFAULT 1 AFTER `video_controls`",
    'video_rel' => "ALTER TABLE `{$table_name}` ADD COLUMN `video_rel` tinyint(1) DEFAULT 0 AFTER `video_modestbranding`"
);

echo '<h2>Migration Results:</h2>';
echo '<ul>';

$success_count = 0;
$skip_count = 0;
$error_count = 0;

foreach ($migrations as $column => $sql) {
    // Check if column exists
    $exists = $wpdb->get_var("SHOW COLUMNS FROM `{$table_name}` LIKE '{$column}'");
    
    if ($exists) {
        echo '<li style="color: orange;">⚠️ <strong>' . esc_html($column) . '</strong>: Already exists (skipped)</li>';
        $skip_count++;
    } else {
        // Run migration
        $result = $wpdb->query($sql);
        
        if ($result !== false) {
            echo '<li style="color: green;">✅ <strong>' . esc_html($column) . '</strong>: Added successfully</li>';
            $success_count++;
        } else {
            echo '<li style="color: red;">❌ <strong>' . esc_html($column) . '</strong>: Failed to add - ' . esc_html($wpdb->last_error) . '</li>';
            $error_count++;
        }
    }
}

echo '</ul>';
echo '<hr>';

// Summary
echo '<h2>Migration Summary:</h2>';
echo '<ul>';
echo '<li>✅ Successfully added: ' . $success_count . ' columns</li>';
echo '<li>⚠️ Already existed: ' . $skip_count . ' columns</li>';
echo '<li>❌ Errors: ' . $error_count . ' columns</li>';
echo '</ul>';

// Verify final structure
$final_columns = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE 'video_%'");

echo '<h2>Final Structure (video_* columns):</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
echo '<tr><th>Column</th><th>Type</th><th>Null</th><th>Default</th></tr>';
foreach ($final_columns as $column) {
    echo '<tr>';
    echo '<td>' . esc_html($column->Field) . '</td>';
    echo '<td>' . esc_html($column->Type) . '</td>';
    echo '<td>' . esc_html($column->Null) . '</td>';
    echo '<td>' . esc_html($column->Default) . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr>';

// Check all banners
$banners = $wpdb->get_results("SELECT id, name, video_autoplay, video_muted, video_loop, video_controls FROM `{$table_name}`");

echo '<h2>Existing Banners (with new settings):</h2>';
if ($banners) {
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    echo '<tr><th>ID</th><th>Name</th><th>Autoplay</th><th>Muted</th><th>Loop</th><th>Controls</th></tr>';
    foreach ($banners as $banner) {
        echo '<tr>';
        echo '<td>' . esc_html($banner->id) . '</td>';
        echo '<td>' . esc_html($banner->name) . '</td>';
        echo '<td>' . ($banner->video_autoplay ? '✅' : '❌') . '</td>';
        echo '<td>' . ($banner->video_muted ? '✅' : '❌') . '</td>';
        echo '<td>' . ($banner->video_loop ? '✅' : '❌') . '</td>';
        echo '<td>' . ($banner->video_controls ? '✅' : '❌') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No banners found.</p>';
}

echo '<hr>';

if ($error_count > 0) {
    echo '<div style="background: #ffcccc; padding: 20px; border: 2px solid #cc0000;">';
    echo '<h2 style="color: #cc0000;">⚠️ MIGRATION FAILED</h2>';
    echo '<p>Some columns could not be added. Please check the error messages above.</p>';
    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ol>';
    echo '<li>Check WordPress debug.log for detailed errors</li>';
    echo '<li>Verify database user has ALTER TABLE permissions</li>';
    echo '<li>Try running SQL manually in phpMyAdmin</li>';
    echo '</ol>';
    echo '</div>';
} else {
    echo '<div style="background: #ccffcc; padding: 20px; border: 2px solid #00cc00;">';
    echo '<h2 style="color: #00cc00;">✅ MIGRATION SUCCESSFUL!</h2>';
    echo '<p>All columns were added successfully. You can now:</p>';
    echo '<ol>';
    echo '<li>Go to <a href="' . admin_url('admin.php?page=anmi-video-banners') . '">Video Banners</a></li>';
    echo '<li>Edit any banner and see the new "Video Settings" section</li>';
    echo '<li><strong style="color: red;">DELETE THIS FILE (migrate-v1.6.13.php)</strong> for security</li>';
    echo '</ol>';
    echo '</div>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=anmi-video-banners') . '">← Back to Video Banners</a></p>';
?>
