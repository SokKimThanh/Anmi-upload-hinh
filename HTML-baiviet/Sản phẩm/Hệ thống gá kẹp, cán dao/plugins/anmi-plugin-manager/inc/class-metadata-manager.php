<?php
/**
 * Metadata Manager - Quản lý metadata của plugins
 */

defined('ABSPATH') || exit;

class Anmi_PM_Metadata_Manager {
    
    /**
     * Get plugin metadata by plugin file
     */
    public static function get_plugin_meta($plugin_file) {
        $posts = get_posts([
            'post_type' => 'anmi_plugin',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_plugin_file',
                    'value' => $plugin_file,
                    'compare' => '='
                ]
            ]
        ]);
        
        if (empty($posts)) {
            return null;
        }
        
        $post = $posts[0];
        return [
            'id' => $post->ID,
            'plugin_file' => get_post_meta($post->ID, '_plugin_file', true),
            'plugin_dir' => get_post_meta($post->ID, '_plugin_dir', true),
            'name' => get_post_meta($post->ID, '_name', true),
            'version' => get_post_meta($post->ID, '_version', true),
            'checksum' => get_post_meta($post->ID, '_checksum', true),
            'installed_date' => get_post_meta($post->ID, '_installed_date', true),
            'active_status' => get_post_meta($post->ID, '_active_status', true),
            'managed' => get_post_meta($post->ID, '_managed', true) === '1',
            'backup_zip' => get_post_meta($post->ID, '_backup_zip', true),
            'author' => get_post_meta($post->ID, '_author', true)
        ];
    }
    
    /**
     * Get all managed plugins
     */
    public static function get_all_plugins() {
        $posts = get_posts([
            'post_type' => 'anmi_plugin',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        
        $plugins = [];
        foreach ($posts as $post) {
            $plugin_file = get_post_meta($post->ID, '_plugin_file', true);
            $plugins[$plugin_file] = [
                'id' => $post->ID,
                'plugin_file' => $plugin_file,
                'plugin_dir' => get_post_meta($post->ID, '_plugin_dir', true),
                'name' => get_post_meta($post->ID, '_name', true),
                'version' => get_post_meta($post->ID, '_version', true),
                'checksum' => get_post_meta($post->ID, '_checksum', true),
                'installed_date' => get_post_meta($post->ID, '_installed_date', true),
                'active_status' => get_post_meta($post->ID, '_active_status', true),
                'managed' => get_post_meta($post->ID, '_managed', true) === '1',
                'backup_zip' => get_post_meta($post->ID, '_backup_zip', true),
                'author' => get_post_meta($post->ID, '_author', true)
            ];
        }
        
        return $plugins;
    }
    
    /**
     * Save or update plugin metadata
     */
    public static function save_plugin_meta($data) {
        $plugin_file = isset($data['plugin_file']) ? $data['plugin_file'] : '';
        if (empty($plugin_file)) {
            return false;
        }
        
        // Check if exists
        $existing = self::get_plugin_meta($plugin_file);
        
        if ($existing) {
            $post_id = $existing['id'];
        } else {
            $post_id = wp_insert_post([
                'post_type' => 'anmi_plugin',
                'post_title' => isset($data['name']) ? $data['name'] : $plugin_file,
                'post_status' => 'publish'
            ]);
            
            if (is_wp_error($post_id)) {
                return false;
            }
        }
        
        // Update metadata
        $meta_fields = [
            '_plugin_file', '_plugin_dir', '_name', '_version', 
            '_checksum', '_installed_date', '_active_status', 
            '_managed', '_backup_zip', '_author'
        ];
        
        foreach ($meta_fields as $field) {
            $key = ltrim($field, '_');
            if (isset($data[$key])) {
                $value = $data[$key];
                if ($key === 'managed') {
                    $value = $value ? '1' : '0';
                }
                update_post_meta($post_id, $field, $value);
            }
        }
        
        return $post_id;
    }
    
    /**
     * Mark plugin as managed
     */
    public static function mark_managed($plugin_file, $managed = true) {
        $meta = self::get_plugin_meta($plugin_file);
        if (!$meta) {
            return false;
        }
        
        update_post_meta($meta['id'], '_managed', $managed ? '1' : '0');
        return true;
    }
    
    /**
     * Update active status
     */
    public static function update_active_status($plugin_file, $active) {
        $meta = self::get_plugin_meta($plugin_file);
        if (!$meta) {
            return false;
        }
        
        update_post_meta($meta['id'], '_active_status', $active ? '1' : '0');
        return true;
    }
    
    /**
     * Delete plugin metadata
     */
    public static function delete_plugin_meta($plugin_file) {
        $meta = self::get_plugin_meta($plugin_file);
        if (!$meta) {
            return false;
        }
        
        wp_delete_post($meta['id'], true);
        return true;
    }
}
