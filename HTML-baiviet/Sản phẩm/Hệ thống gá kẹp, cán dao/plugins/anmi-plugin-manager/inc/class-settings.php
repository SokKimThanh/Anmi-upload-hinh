<?php
/**
 * Settings management for Anmi Plugin Manager
 */

defined('ABSPATH') || exit;

class Anmi_PM_Settings {

	const OPTION_KEY = 'anmi_pm_settings';

	public function __construct() {
		add_action('admin_post_anmi_pm_save_settings', [$this, 'handle_save']);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		$settings = self::get_settings();
		$view = [
			'settings'      => $settings,
			'purge_options' => $this->get_purge_options(),
			'intervals'     => $this->get_health_check_intervals(),
			'messages'      => $this->collect_messages(),
		];

		include ANMI_PM_DIR . 'admin/templates/settings.php';
	}

	/**
	 * Handle settings form submission
	 */
	public function handle_save() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Unauthorized', 'anmi-plugin-manager'));
		}

		check_admin_referer('anmi_pm_save_settings', 'anmi_pm_settings_nonce');

		$settings = $this->sanitize_settings($_POST);
		update_option(self::OPTION_KEY, $settings);

		$redirect = add_query_arg([
			'page'   => 'anmi-plugins-settings',
			'notice' => 'success',
			'code'   => 'settings_saved',
		], admin_url('admin.php'));

		wp_safe_redirect($redirect);
		exit;
	}

	/**
	 * Retrieve settings merged with defaults
	 */
	public static function get_settings() {
		$defaults = self::get_defaults();
		$stored   = get_option(self::OPTION_KEY, []);

		return wp_parse_args($stored, $defaults);
	}

	/**
	 * Determine whether kill-switch is active
	 */
	public static function is_kill_switch_enabled() {
		$settings = self::get_settings();
		return !empty($settings['kill_switch_enabled']);
	}

	/**
	 * Default settings
	 */
	private static function get_defaults() {
		return [
			'health_check_endpoint'  => '',
			'health_check_interval'  => 15,
			'pending_window_minutes' => 30,
			'purge_policy'           => 'keep_100',
			'kill_switch_enabled'    => false,
			'kill_switch_note'       => '',
		];
	}

	/**
	 * Sanitize settings payload
	 */
	private function sanitize_settings(array $input) {
		$defaults = self::get_defaults();

		$endpoint = isset($input['health_check_endpoint'])
			? esc_url_raw(trim($input['health_check_endpoint']))
			: $defaults['health_check_endpoint'];

		$interval = isset($input['health_check_interval'])
			? (int) $input['health_check_interval']
			: $defaults['health_check_interval'];

		$pending = isset($input['pending_window_minutes'])
			? (int) $input['pending_window_minutes']
			: $defaults['pending_window_minutes'];

		$purge  = isset($input['purge_policy']) ? sanitize_text_field($input['purge_policy']) : $defaults['purge_policy'];
		$purge_options = $this->get_purge_options();
		if (!isset($purge_options[$purge])) {
			$purge = $defaults['purge_policy'];
		}

		$kill_switch_enabled = isset($input['kill_switch_enabled']) && $input['kill_switch_enabled'] === '1';
		$kill_switch_note    = isset($input['kill_switch_note']) ? sanitize_textarea_field($input['kill_switch_note']) : '';

		if ($interval <= 0) {
			$interval = $defaults['health_check_interval'];
		}

		if ($pending <= 0) {
			$pending = $defaults['pending_window_minutes'];
		}

		return [
			'health_check_endpoint'  => $endpoint,
			'health_check_interval'  => $interval,
			'pending_window_minutes' => $pending,
			'purge_policy'           => $purge,
			'kill_switch_enabled'    => $kill_switch_enabled,
			'kill_switch_note'       => $kill_switch_note,
		];
	}

	/**
	 * Purge options for logs/backups retention
	 */
	private function get_purge_options() {
		return [
			'keep_50'  => __('Keep last 50 entries', 'anmi-plugin-manager'),
			'keep_100' => __('Keep last 100 entries', 'anmi-plugin-manager'),
			'keep_250' => __('Keep last 250 entries', 'anmi-plugin-manager'),
			'keep_all' => __('Keep everything (no purge)', 'anmi-plugin-manager'),
		];
	}

	/**
	 * Available health check intervals (minutes)
	 */
	private function get_health_check_intervals() {
		return [
			5  => __('Every 5 minutes', 'anmi-plugin-manager'),
			15 => __('Every 15 minutes', 'anmi-plugin-manager'),
			30 => __('Every 30 minutes', 'anmi-plugin-manager'),
			60 => __('Every hour', 'anmi-plugin-manager'),
		];
	}

	/**
	 * Collect notices for settings page
	 */
	private function collect_messages() {
		if (!isset($_GET['notice'])) {
			return [];
		}

		$type = sanitize_text_field($_GET['notice']);
		$code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';

		$map = [
			'settings_saved' => __('Settings updated successfully.', 'anmi-plugin-manager'),
		];

		$text = $map[$code] ?? __('Settings saved.', 'anmi-plugin-manager');

		return [[
			'type' => $type,
			'text' => $text,
		]];
	}
}
