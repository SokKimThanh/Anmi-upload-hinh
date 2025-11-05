<?php
defined('ABSPATH') || exit;

$settings      = $view['settings'];
$purge_options = $view['purge_options'];
$intervals     = $view['intervals'];
$messages      = $view['messages'];

$alert_styles = [
	'success' => 'anmi-alert anmi-alert--success',
	'error'   => 'anmi-alert anmi-alert--danger',
	'warning' => 'anmi-alert anmi-alert--warning',
	'info'    => 'anmi-alert',
];

$kill_switch_active = !empty($settings['kill_switch_enabled']);
?>

<div class="wrap anmi-pm-wrap">
	<h1><?php esc_html_e('Anmi Plugin Manager Settings', 'anmi-plugin-manager'); ?></h1>

	<?php if (!empty($messages)): ?>
		<?php foreach ($messages as $message):
			$class = $alert_styles[$message['type']] ?? 'anmi-alert';
		?>
			<div class="<?php echo esc_attr($class); ?>" role="status">
				<p><?php echo esc_html($message['text']); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="anmi-form anmi-form--stacked">
		<?php wp_nonce_field('anmi_pm_save_settings', 'anmi_pm_settings_nonce'); ?>
		<input type="hidden" name="action" value="anmi_pm_save_settings">

		<div class="anmi-panel">
			<h2 class="anmi-panel__title"><?php esc_html_e('Health check', 'anmi-plugin-manager'); ?></h2>
			
			<div class="anmi-form__field">
				<label for="health_check_endpoint"><?php esc_html_e('Endpoint URL', 'anmi-plugin-manager'); ?></label>
				<input type="url" class="regular-text" name="health_check_endpoint" id="health_check_endpoint" value="<?php echo esc_attr($settings['health_check_endpoint']); ?>" placeholder="https://example.com/wp-json/anmi/v1/health">
				<span class="anmi-form__help"><?php esc_html_e('Optional: If provided, the watchdog will ping this URL after deployments to verify site health.', 'anmi-plugin-manager'); ?></span>
			</div>

			<div class="anmi-form__field">
				<label for="health_check_interval"><?php esc_html_e('Polling interval', 'anmi-plugin-manager'); ?></label>
				<select name="health_check_interval" id="health_check_interval">
					<?php foreach ($intervals as $value => $label): ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected((int) $settings['health_check_interval'], (int) $value); ?>>
							<?php echo esc_html($label); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span class="anmi-form__help"><?php esc_html_e('Defines how often automated health checks run after a plugin change.', 'anmi-plugin-manager'); ?></span>
			</div>
		</div>

		<div class="anmi-panel">
			<h2 class="anmi-panel__title"><?php esc_html_e('Activation pending window', 'anmi-plugin-manager'); ?></h2>
			
			<div class="anmi-form__field">
				<label for="pending_window_minutes"><?php esc_html_e('Pending duration (minutes)', 'anmi-plugin-manager'); ?></label>
				<input type="number" min="1" step="1" name="pending_window_minutes" id="pending_window_minutes" value="<?php echo esc_attr($settings['pending_window_minutes']); ?>">
				<span class="anmi-form__help"><?php esc_html_e('Safe activate keeps the previous plugin snapshot for this duration before pruning temporary backups.', 'anmi-plugin-manager'); ?></span>
			</div>
		</div>

		<div class="anmi-panel">
			<h2 class="anmi-panel__title"><?php esc_html_e('Log & backup purge policy', 'anmi-plugin-manager'); ?></h2>
			
			<div class="anmi-form__field">
				<label for="purge_policy"><?php esc_html_e('Retention', 'anmi-plugin-manager'); ?></label>
				<select name="purge_policy" id="purge_policy">
					<?php foreach ($purge_options as $key => $label): ?>
						<option value="<?php echo esc_attr($key); ?>" <?php selected($settings['purge_policy'], $key); ?>>
							<?php echo esc_html($label); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span class="anmi-form__help"><?php esc_html_e('Controls how many historical entries are retained before automatic cleanup.', 'anmi-plugin-manager'); ?></span>
			</div>
		</div>

		<div class="anmi-panel">
			<h2 class="anmi-panel__title"><?php esc_html_e('Kill-switch', 'anmi-plugin-manager'); ?></h2>
			
			<div class="anmi-form__field">
				<label>
					<input type="checkbox" name="kill_switch_enabled" value="1" <?php checked($kill_switch_active); ?>>
					<?php esc_html_e('Disable all plugin actions (uploads, activations, deletions) until re-enabled.', 'anmi-plugin-manager'); ?>
				</label>
				<span class="anmi-form__help"><?php esc_html_e('Use during maintenance or incident response. Administrators can still review logs and settings.', 'anmi-plugin-manager'); ?></span>
			</div>

			<div class="anmi-form__field">
				<label for="kill_switch_note"><?php esc_html_e('Kill-switch note', 'anmi-plugin-manager'); ?></label>
				<textarea name="kill_switch_note" id="kill_switch_note" rows="4" class="large-text"><?php echo esc_textarea($settings['kill_switch_note']); ?></textarea>
				<span class="anmi-form__help"><?php esc_html_e('Optional: document the reason for enabling the kill-switch. Displayed to future administrators.', 'anmi-plugin-manager'); ?></span>
			</div>

			<?php if ($kill_switch_active && !empty($settings['kill_switch_note'])): ?>
				<div class="anmi-alert anmi-alert--warning">
					<strong><?php esc_html_e('Kill-switch is currently active', 'anmi-plugin-manager'); ?></strong>
					<span><?php echo esc_html($settings['kill_switch_note']); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<div class="anmi-form__actions">
			<button type="submit" class="button button-primary button-large"><?php esc_html_e('Save Settings', 'anmi-plugin-manager'); ?></button>
		</div>
	</form>
</div>
