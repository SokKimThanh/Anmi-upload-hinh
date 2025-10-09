<?php
/**
 * Plugin Name: AN MI Tools Carbide Burr Styles
 * Description: Enqueue CSS chuyên dụng cho bài viết Carbide Burr (chỉ tải khi cần).
 * Version: 1.1.0
 * Author: AN MI TOOLS
 * Text Domain: anmi-carbide-burr
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Post;

class ANMI_Carbide_Burr_Styles {

    private const STYLE_HANDLE   = 'anmi-carbide-burr';
    private const STYLE_VERSION  = '1.1.0';
    private const STYLE_FILENAME = 'style-carbide-burr.css';

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_styles' ], 20 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_preview' ], 20 );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
    }

    public function maybe_enqueue_styles() {
        if ( ! is_singular() ) {
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
        wp_enqueue_style(
            self::STYLE_HANDLE . $suffix,
            plugin_dir_url( __FILE__ ) . self::STYLE_FILENAME,
            array(),
            self::STYLE_VERSION,
            'all'
        );
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

        $slug             = (string) $post->post_name;
        $permalink        = get_permalink( $post );
        $slug_contains    = strpos( $slug, 'carbide-burr' ) !== false;
        $url_contains     = is_string( $permalink ) && strpos( $permalink, '/carbide-burr/' ) !== false;
        $meta_flagged     = get_post_meta( $post->ID, '_anmi_enable_carbide_burr_css', true );
        $meta_is_enabled  = in_array( $meta_flagged, array( '1', 1, true, 'true', 'yes' ), true );

        if ( $slug_contains || $url_contains || $meta_is_enabled ) {
            return true;
        }

        return (bool) apply_filters( 'anmi_carbide_burr_should_enqueue', false, $post );
    }
}

new ANMI_Carbide_Burr_Styles();
