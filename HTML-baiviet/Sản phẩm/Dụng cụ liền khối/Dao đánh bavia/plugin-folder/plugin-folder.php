<?php
/**
 * Plugin Name: AN MI Tools Carbide Burr Styles
 * Description: Enqueue CSS chuyên dụng cho bài viết Carbide Burr (chỉ tải khi cần).
 * Version: 1.1.1
 * Author: AN MI TOOLS
 * Text Domain: anmi-carbide-burr
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Post;

class ANMI_Carbide_Burr_Styles {

    private const STYLE_HANDLE   = 'anmi-carbide-burr';
    private const STYLE_FILENAME = 'style-carbide-burr.css';

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_styles' ], 20 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_preview' ], 20 );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
    }

    public function maybe_enqueue_styles() {
        if ( ! is_singular() || is_feed() || is_embed() ) {
            return;
        }

        $post = get_queried_object();
        if ( ! $post instanceof WP_Post ) {
            return;
        }

        if ( $this->should_enqueue_for_post( $post ) ) {
            $this->enqueue_style();
        }
    }

    // Tải CSS trong editor admin để preview (Classic + Block editor)
    public function enqueue_admin_preview( $hook ) {
        if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
            return;
        }

        global $post;
        if ( $post instanceof WP_Post && $this->should_enqueue_for_post( $post ) ) {
            $this->enqueue_style( '-admin' );
        }
    }

    public function enqueue_block_editor_assets() {
        global $post;
        if ( $post instanceof WP_Post && $this->should_enqueue_for_post( $post ) ) {
            $this->enqueue_style( '-editor' );
        }
    }

    private function enqueue_style( string $suffix = '' ) : void {
        $src      = plugins_url( self::STYLE_FILENAME, __FILE__ );
        $src      = apply_filters( 'anmi_carbide_burr_style_url', $src, $suffix );
        $handle   = apply_filters( 'anmi_carbide_burr_style_handle', self::STYLE_HANDLE . $suffix, $suffix );
        $ver_file = plugin_dir_path( __FILE__ ) . self::STYLE_FILENAME;
        $version  = file_exists( $ver_file ) ? (string) filemtime( $ver_file ) : '1.1.1';

        // Đăng ký rồi enqueue để tránh trùng lặp và cho phép phụ thuộc nếu cần
        wp_register_style( $handle, $src, array(), $version, 'all' );
        wp_enqueue_style( $handle );
    }

    private function should_enqueue_for_post( WP_Post $post ) : bool {
        $post_type = get_post_type( $post );
        if ( 'product' === $post_type ) {
            return true;
        }

        $taxonomy_slugs = apply_filters(
            'anmi_carbide_burr_taxonomies',
            array( 'category', 'product_cat' )
        );

        foreach ( (array) $taxonomy_slugs as $taxonomy ) {
            if ( taxonomy_exists( $taxonomy ) && has_term( 'carbide-burr', $taxonomy, $post ) ) {
                return true;
            }
        }

        $slug        = (string) $post->post_name;
        $permalink   = (string) get_permalink( $post );
        $slug_hit    = strpos( $slug, 'carbide-burr' ) !== false;
        $url_hit     = strpos( $permalink, 'carbide-burr' ) !== false;

        $meta_flag   = get_post_meta( $post->ID, '_anmi_enable_carbide_burr_css', true );
        $meta_on     = in_array( $meta_flag, array( '1', 1, true, 'true', 'yes' ), true );

        if ( $slug_hit || $url_hit || $meta_on ) {
            return true;
        }

        return (bool) apply_filters( 'anmi_carbide_burr_should_enqueue', false, $post );
    }
}

new ANMI_Carbide_Burr_Styles();