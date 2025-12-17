<?php
/**
 * Minimal WordPress/WooCommerce stubs for IDE static analysis.
 * Purpose: suppress “Undefined function/class” in editors (e.g., Intelephense)
 * without requiring WordPress core to be present in this repo.
 */

declare(strict_types=1);

// Common WP core types
if (!class_exists('WP_Post')) {
    class WP_Post {
        /** @var int */
        public $ID = 0;
        /** @var string */
        public $post_type = '';
        /** @var string */
        public $post_content = '';
        /** @var string */
        public $post_name = '';
    }
}

if (!class_exists('WP_Term')) {
    class WP_Term {
        /** @var string */
        public $slug = '';
    }
}

if (!class_exists('WP_Error')) {
    class WP_Error {}
}

// Common WP functions used by this plugin
if (!function_exists('add_action')) {
    function add_action(string $hook_name, $callback, int $priority = 10, int $accepted_args = 1): void {}
}

if (!function_exists('add_editor_style')) {
    function add_editor_style($stylesheet): void {}
}

if (!function_exists('add_options_page')) {
    function add_options_page(string $page_title, string $menu_title, string $capability, string $menu_slug, $callback = null): void {}
}

if (!function_exists('plugins_url')) {
    function plugins_url(string $path = '', string $plugin = ''): string { return $path; }
}

if (!function_exists('is_singular')) {
    function is_singular($post_types = ''): bool { return false; }
}

if (!function_exists('get_the_terms')) {
    /** @return array<WP_Term>|false|WP_Error */
    function get_the_terms(int $post_id, string $taxonomy) { return false; }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing): bool { return $thing instanceof WP_Error; }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style(string $handle, string $src = '', array $deps = array(), $ver = false, string $media = 'all'): void {}
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script(string $handle, string $src = '', array $deps = array(), $ver = false, bool $in_footer = false): void {}
}

if (!function_exists('wp_add_inline_style')) {
    function wp_add_inline_style(string $handle, string $data): void {}
}

if (!function_exists('esc_html')) {
    function esc_html($text): string { return (string) $text; }
}

if (!function_exists('get_the_category')) {
    /** @return array<WP_Term> */
    function get_the_category(int $post_id = 0): array { return array(); }
}
