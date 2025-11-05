<?php
/**
 * Plugin Uploader - Placeholder cho Batch 2
 */

defined('ABSPATH') || exit;

class Anmi_PM_Plugin_Uploader {
    
    public function render() {
        ?>
        <div class="wrap">
            <h1><?php _e('Upload Plugin', 'anmi-plugin-manager'); ?></h1>
            <p><?php _e('Upload functionality sẽ được implement ở Batch 2.', 'anmi-plugin-manager'); ?></p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins')); ?>" class="button">
                <?php _e('Back to Plugin List', 'anmi-plugin-manager'); ?>
            </a>
        </div>
        <?php
    }
}
