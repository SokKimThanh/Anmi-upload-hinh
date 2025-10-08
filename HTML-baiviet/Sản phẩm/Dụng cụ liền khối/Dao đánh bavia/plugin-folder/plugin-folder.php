<?php
/**
 * Plugin Name: AN MI Tools Carbide Burr Styles
 * Description: Enqueue CSS chuyên dụng cho bài viết Carbide Burr (chỉ tải khi cần).
 * Version: 1.0.0
 * Author: AN MI TOOLS
 * Text Domain: anmi-carbide-burr
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANMI_Carbide_Burr_Styles {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_styles' ], 20 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_preview' ], 20 );
    }

    public function maybe_enqueue_styles() {
        if ( is_singular() ) {
            global $post;
            if ( ! $post ) {
                return;
            }

            // Condition 1: nếu post type product (WooCommerce) hoặc post in category 'carbide-burr'
            $is_product_post = ( function_exists('is_product') && is_product() ) || ( get_post_type( $post ) === 'product' );
            $has_carbine_cat = has_term( 'carbide-burr', 'product_cat', $post ) || has_term( 'carbide-burr', 'category', $post );

            if ( $is_product_post || $has_carbine_cat ) {
                wp_enqueue_style(
                    'anmi-carbide-burr',
                    plugin_dir_url( __FILE__ ) . 'style-carbide-burr.css',
                    array(),
                    '1.0.0',
                    'all'
                );
            }
        }
    }

    // Tải CSS trong editor admin để preview (nếu cần)
    public function enqueue_admin_preview( $hook ) {
        // Chỉ tải trên post editor
        if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
            global $post;
            if ( $post && ( get_post_type( $post ) === 'product' || has_term( 'carbide-burr', 'product_cat', $post ) || has_term( 'carbide-burr', 'category', $post ) ) ) {
                wp_enqueue_style(
                    'anmi-carbide-burr-admin',
                    plugin_dir_url( __FILE__ ) . 'style-carbide-burr.css',
                    array(),
                    '1.0.0',
                    'all'
                );
            }
        }
    }
}

new ANMI_Carbide_Burr_Styles();
