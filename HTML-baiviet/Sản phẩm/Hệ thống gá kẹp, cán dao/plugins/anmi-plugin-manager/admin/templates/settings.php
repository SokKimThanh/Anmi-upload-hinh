<?php
defined('ABSPATH') || exit;

$settings      = $view['settings'];
$purge_options = $view['purge_options'];
$intervals     = $view['intervals'];
$messages      = $view['messages'];

$notice_classes = [
	'success' => 'notice-success',
	'error'   => 'notice-error',
	'warning' => 'notice-warning',
	'info'    => 'notice-info',
];

$kill_switch_active = !empty($settings['kill_switch_enabled']);
?>

<div class="wrap anmi-pm-wrap">
	<h1><?php esc_html_e('Anmi Plugin Manager Settings', 'anmi-plugin-manager'); ?></h1>

	<?php if (!empty($messages)): ?>
		<?php foreach ($messages as $message):
			$class = $notice_classes[$message['type']] ?? 'notice-info';
		?>
			<div class="notice <?php echo esc_attr($class); ?> is-dismissible">
				<p><?php echo esc_html($message['text']); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="anmi-settings-form">
		<?php wp_nonce_field('anmi_pm_save_settings', 'anmi_pm_settings_nonce'); ?>
		<input type="hidden" name="action" value="anmi_pm_save_settings">

		<h2><?php esc_html_e('Health check', 'anmi-plugin-manager'); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="health_check_endpoint"><?php esc_html_e('Endpoint URL', 'anmi-plugin-manager'); ?></label></th>
					<td>
						<input type="url" class="regular-text" name="health_check_endpoint" id="health_check_endpoint" value="<?php echo esc_attr($settings['health_check_endpoint']); ?>" placeholder="https://example.com/wp-json/anmi/v1/health">
						<p class="description"><?php esc_html_e('Optional: If provided, the watchdog will ping this URL after deployments to verify site health.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="health_check_interval"><?php esc_html_e('Polling interval', 'anmi-plugin-manager'); ?></label></th>
					<td>
						<select name="health_check_interval" id="health_check_interval">
							<?php foreach ($intervals as $value => $label): ?>
								<option value="<?php echo esc_attr($value); ?>" <?php selected((int) $settings['health_check_interval'], (int) $value); ?>>
									<?php echo esc_html($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e('Defines how often automated health checks run after a plugin change.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e('Activation pending window', 'anmi-plugin-manager'); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="pending_window_minutes"><?php esc_html_e('Pending duration (minutes)', 'anmi-plugin-manager'); ?></label></th>
					<td>
						<input type="number" min="1" step="1" name="pending_window_minutes" id="pending_window_minutes" value="<?php echo esc_attr($settings['pending_window_minutes']); ?>">
						<p class="description"><?php esc_html_e('Safe activate keeps the previous plugin snapshot for this duration before pruning temporary backups.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e('Log & backup purge policy', 'anmi-plugin-manager'); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="purge_policy"><?php esc_html_e('Retention', 'anmi-plugin-manager'); ?></label></th>
					<td>
						<select name="purge_policy" id="purge_policy">
							<?php foreach ($purge_options as $key => $label): ?>
								<option value="<?php echo esc_attr($key); ?>" <?php selected($settings['purge_policy'], $key); ?>>
									<?php echo esc_html($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e('Controls how many historical entries are retained before automatic cleanup.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e('Kill-switch', 'anmi-plugin-manager'); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e('Global kill-switch', 'anmi-plugin-manager'); ?></th>
					<td>
						<label>
							<input type="checkbox" name="kill_switch_enabled" value="1" <?php checked($kill_switch_active); ?>>
							<?php esc_html_e('Disable all plugin actions (uploads, activations, deletions) until re-enabled.', 'anmi-plugin-manager'); ?>
						</label>
						<p class="description"><?php esc_html_e('Use during maintenance or incident response. Administrators can still review logs and settings.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="kill_switch_note"><?php esc_html_e('Kill-switch note', 'anmi-plugin-manager'); ?></label></th>
					<td>
						<textarea name="kill_switch_note" id="kill_switch_note" rows="4" cols="50" class="large-text"><?php echo esc_textarea($settings['kill_switch_note']); ?></textarea>
						<p class="description"><?php esc_html_e('Optional: document the reason for enabling the kill-switch. Displayed to future administrators.', 'anmi-plugin-manager'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<?php if ($kill_switch_active && !empty($settings['kill_switch_note'])): ?>
			<div class="anmi-kill-switch-banner">
				<strong><?php esc_html_e('Kill-switch is currently active', 'anmi-plugin-manager'); ?></strong>
				<span><?php echo esc_html($settings['kill_switch_note']); ?></span>
			</div>
		<?php endif; ?>

		<p class="submit">
			<button type="submit" class="button button-primary button-large"><?php esc_html_e('Save Settings', 'anmi-plugin-manager'); ?></button>
		</p>
	</form>
</div>
