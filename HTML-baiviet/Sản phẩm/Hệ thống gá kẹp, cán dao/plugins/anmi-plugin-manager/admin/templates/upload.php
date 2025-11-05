<?php
defined('ABSPATH') || exit;

$messages     = $view['messages'];
$scan_session = $view['scan_session'];
$scan_token   = $view['scan_token'];
$max_upload   = $view['max_upload'];

$alert_styles = [
	'success' => 'anmi-alert anmi-alert--success',
	'error'   => 'anmi-alert anmi-alert--danger',
	'warning' => 'anmi-alert anmi-alert--warning',
	'info'    => 'anmi-alert',
];

$has_session = !empty($scan_session);
$metadata    = $has_session ? ($scan_session['metadata'] ?? []) : [];
$scan_result = $has_session ? ($scan_session['scan'] ?? []) : [];
$auto_activate = $has_session && !empty($scan_session['auto_activate']);

$file_name = $has_session ? ($scan_session['file_name'] ?? '') : '';
$file_size = $has_session ? size_format($scan_session['file_size'] ?? 0) : '';
?>

<div class="wrap anmi-pm-wrap">
	<h1><?php esc_html_e('Upload Plugin Package', 'anmi-plugin-manager'); ?></h1>

	<?php if (!empty($messages)): ?>
		<?php foreach ($messages as $message):
			$class = $alert_styles[$message['type']] ?? 'anmi-alert';
		?>
			<div class="<?php echo esc_attr($class); ?>" role="status">
				<p><?php echo esc_html($message['text']); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="anmi-panel">
		<h2 class="anmi-panel__title"><?php esc_html_e('Upload a plugin package', 'anmi-plugin-manager'); ?></h2>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="anmi-form anmi-form--stacked">
			<?php wp_nonce_field('anmi_pm_scan_zip', 'anmi_pm_scan_nonce'); ?>
			<input type="hidden" name="action" value="anmi_pm_scan_zip">

			<div class="anmi-form__field">
				<label for="plugin_zip"><?php esc_html_e('Select Plugin ZIP File', 'anmi-plugin-manager'); ?></label>
				<input type="file" name="plugin_zip" id="plugin_zip" accept=".zip" required>
				<span class="anmi-form__help"><?php printf(esc_html__('Maximum file size: %s', 'anmi-plugin-manager'), esc_html($max_upload)); ?></span>
			</div>

			<div class="anmi-form__field">
				<label>
					<input type="checkbox" name="auto_activate" value="1">
					<?php esc_html_e('Auto activate safely after upload (not recommended for production).', 'anmi-plugin-manager'); ?>
				</label>
				<span class="anmi-form__help"><?php esc_html_e('Leave unchecked to review the plugin before activation.', 'anmi-plugin-manager'); ?></span>
			</div>

			<div class="anmi-form__actions">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e('Upload & Run Security Scan', 'anmi-plugin-manager'); ?></button>
				<a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins')); ?>" class="button button-large"><?php esc_html_e('Back to list', 'anmi-plugin-manager'); ?></a>
			</div>
		</form>
	</div>

	<div class="anmi-panel anmi-panel--subtle">
		<h2 class="anmi-panel__title"><?php esc_html_e('Staging workflow overview', 'anmi-plugin-manager'); ?></h2>
		<ol class="anmi-subtle">
			<li><?php esc_html_e('Upload ZIP → stored in a temporary sandbox', 'anmi-plugin-manager'); ?></li>
			<li><?php esc_html_e('Security scan → detect dangerous PHP patterns', 'anmi-plugin-manager'); ?></li>
			<li><?php esc_html_e('Extract to staging → review extracted plugin safely', 'anmi-plugin-manager'); ?></li>
			<li><?php esc_html_e('Automatic backup → existing plugin archived with timestamp', 'anmi-plugin-manager'); ?></li>
			<li><?php esc_html_e('Deploy to wp-content/plugins → metadata stored for manager', 'anmi-plugin-manager'); ?></li>
		</ol>
	</div>

	<?php if ($has_session): ?>
		<div class="anmi-panel anmi-upload-session">
			<h2 class="anmi-panel__title"><?php esc_html_e('Scan Results Ready for Staging', 'anmi-plugin-manager'); ?></h2>

			<div class="anmi-meta-grid">
				<div class="anmi-meta-card">
					<span class="anmi-meta-label"><?php esc_html_e('Uploaded file', 'anmi-plugin-manager'); ?></span>
					<strong><?php echo esc_html($file_name); ?></strong>
					<div><?php echo esc_html($file_size); ?></div>
				</div>

				<?php if (!empty($metadata)): ?>
					<div class="anmi-meta-card">
						<span class="anmi-meta-label"><?php esc_html_e('Plugin name', 'anmi-plugin-manager'); ?></span>
						<strong><?php echo esc_html($metadata['name'] ?? ''); ?></strong>
						<div><?php esc_html_e('Version', 'anmi-plugin-manager'); ?>: <?php echo esc_html($metadata['version'] ?? __('Unknown', 'anmi-plugin-manager')); ?></div>
						<div><?php esc_html_e('Author', 'anmi-plugin-manager'); ?>: <?php echo esc_html($metadata['author'] ?? __('Unknown', 'anmi-plugin-manager')); ?></div>
					</div>
				<?php endif; ?>

				<?php if (!empty($metadata['plugin_file'])): ?>
					<div class="anmi-meta-card">
						<span class="anmi-meta-label"><?php esc_html_e('Plugin file', 'anmi-plugin-manager'); ?></span>
						<code><?php echo esc_html($metadata['plugin_file']); ?></code>
					</div>
				<?php endif; ?>

				<div class="anmi-meta-card">
					<span class="anmi-meta-label"><?php esc_html_e('Scan status', 'anmi-plugin-manager'); ?></span>
					<?php if (!empty($scan_result['safe'])): ?>
						<span class="anmi-badge anmi-badge--success"><?php esc_html_e('No threats detected', 'anmi-plugin-manager'); ?></span>
					<?php else: ?>
						<span class="anmi-badge anmi-badge--danger"><?php esc_html_e('Review required', 'anmi-plugin-manager'); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="anmi-upload-actions">
				<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="anmi-form anmi-form--stacked">
					<?php wp_nonce_field('anmi_pm_extract', 'anmi_pm_extract_nonce'); ?>
					<input type="hidden" name="action" value="anmi_pm_extract_staging">
					<input type="hidden" name="scan_token" value="<?php echo esc_attr($scan_session['token'] ?? $scan_token); ?>">

					<div class="anmi-form__field">
						<label>
							<input type="checkbox" name="auto_activate" value="1" <?php checked($auto_activate); ?>>
							<?php esc_html_e('Safe activate immediately after deployment', 'anmi-plugin-manager'); ?>
						</label>
						<span class="anmi-form__help"><?php esc_html_e('Safe activate performs rollback checks and will revert automatically if errors are detected.', 'anmi-plugin-manager'); ?></span>
						<span class="anmi-form__help"><?php esc_html_e('Extracted files are placed into wp-content/anmi-staging-plugins before being copied into wp-content/plugins.', 'anmi-plugin-manager'); ?></span>
					</div>

					<div class="anmi-form__actions">
						<button type="submit" class="button button-primary">
							<?php esc_html_e('Extract to staging & deploy', 'anmi-plugin-manager'); ?>
						</button>
					</div>
				</form>

				<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="anmi-form anmi-form--stacked">
					<?php wp_nonce_field('anmi_pm_discard', 'anmi_pm_discard_nonce'); ?>
					<input type="hidden" name="action" value="anmi_pm_discard_scan">
					<input type="hidden" name="scan_token" value="<?php echo esc_attr($scan_session['token'] ?? $scan_token); ?>">
					<div class="anmi-form__actions">
						<button type="submit" class="button button-secondary"><?php esc_html_e('Discard session', 'anmi-plugin-manager'); ?></button>
					</div>
				</form>
			</div>

			<?php if (!empty($metadata['description'])): ?>
				<p class="anmi-subtle"><?php echo esc_html(wp_trim_words($metadata['description'], 40)); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
