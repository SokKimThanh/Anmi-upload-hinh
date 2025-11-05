<?php
defined('ABSPATH') || exit;

$stats          = $view['stats'];
$filters        = $view['filters'];
$plugins        = $view['plugins'];
$messages       = $view['messages'];
$pagination     = $view['pagination'];
$resync_url     = $view['resync_url'];
$kill_switch    = $view['kill_switch'];
$confirm_phrase = $view['confirm_phrase'];

$filter_status = $filters['status'];
$search_value  = $filters['search'];
$kill_switch_note = $view['kill_switch_note'];

$alert_styles = [
	'success' => 'anmi-alert anmi-alert--success',
	'error'   => 'anmi-alert anmi-alert--danger',
	'warning' => 'anmi-alert anmi-alert--warning',
	'info'    => 'anmi-alert anmi-alert--info',
];

$status_options = [
	'all'       => __('All statuses', 'anmi-plugin-manager'),
	'active'    => __('Active only', 'anmi-plugin-manager'),
	'inactive'  => __('Inactive only', 'anmi-plugin-manager'),
	'managed'   => __('Managed only', 'anmi-plugin-manager'),
	'unmanaged' => __('Not managed', 'anmi-plugin-manager'),
];

$pagination_args = [
	'page'          => 'anmi-plugins',
	'filter_status' => $filter_status !== 'all' ? $filter_status : null,
	's'             => $search_value ?: null,
];

$base_url = add_query_arg(
	array_filter($pagination_args, static function ($value) {
		return !empty($value);
	}),
	admin_url('admin.php')
);

$page_links = paginate_links([
	'base'      => esc_url_raw(add_query_arg('paged', '%#%', $base_url)),
	'format'    => '',
	'current'   => $pagination['current_page'],
	'total'     => $pagination['total_pages'],
	'prev_text' => __('« Previous', 'anmi-plugin-manager'),
	'next_text' => __('Next »', 'anmi-plugin-manager'),
]);
?>

<div class="wrap anmi-pm-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e('Anmi Plugin Manager', 'anmi-plugin-manager'); ?></h1>

	<a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins-upload')); ?>" class="page-title-action">
		<?php esc_html_e('Upload New Plugin', 'anmi-plugin-manager'); ?>
	</a>

	<a href="<?php echo esc_url($resync_url); ?>" class="page-title-action <?php echo $kill_switch ? 'anmi-action-disabled' : ''; ?>">
		<?php esc_html_e('Resync Renamed Plugins', 'anmi-plugin-manager'); ?>
	</a>

	<hr class="wp-header-end">

	<?php if (!empty($messages)): ?>
		<?php foreach ($messages as $message):
			$class = $alert_styles[$message['type']] ?? 'anmi-alert anmi-alert--info';
		?>
			<div class="<?php echo esc_attr($class); ?>" role="status">
				<p><?php echo esc_html($message['text']); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ($kill_switch): ?>
		<div class="anmi-alert anmi-alert--warning" role="status">
			<strong><?php esc_html_e('Kill-switch active', 'anmi-plugin-manager'); ?></strong>
			<span><?php esc_html_e('Actions that modify plugins are temporarily disabled until the kill-switch is turned off in Settings.', 'anmi-plugin-manager'); ?></span>
			<?php if (!empty($kill_switch_note)): ?>
				<p class="anmi-subtle"><?php echo esc_html($kill_switch_note); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="anmi-toolbar">
		<form method="get" class="anmi-form">
			<input type="hidden" name="page" value="anmi-plugins">

			<label for="anmi-filter-status" class="screen-reader-text"><?php esc_html_e('Filter by status', 'anmi-plugin-manager'); ?></label>
			<select name="filter_status" id="anmi-filter-status">
				<?php foreach ($status_options as $value => $label): ?>
					<option value="<?php echo esc_attr($value); ?>" <?php selected($filter_status, $value); ?>>
						<?php echo esc_html($label); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label for="anmi-search" class="screen-reader-text"><?php esc_html_e('Search plugins', 'anmi-plugin-manager'); ?></label>
			<input type="search" name="s" id="anmi-search" value="<?php echo esc_attr($search_value); ?>" placeholder="<?php esc_attr_e('Search name, author, or file…', 'anmi-plugin-manager'); ?>">

			<button type="submit" class="button button-secondary"><?php esc_html_e('Apply', 'anmi-plugin-manager'); ?></button>
		</form>

		<div class="description">
			<?php esc_html_e('Filters and safe actions apply only to plugins authored by or managed for Anmi.', 'anmi-plugin-manager'); ?>
		</div>
	</div>

	<div class="anmi-stat-grid">
		<div class="anmi-stat-card">
			<span class="anmi-stat-value"><?php echo intval($stats['total']); ?></span>
			<span class="anmi-stat-label"><?php esc_html_e('Total Plugins', 'anmi-plugin-manager'); ?></span>
		</div>
		<div class="anmi-stat-card">
			<span class="anmi-stat-value"><?php echo intval($stats['active']); ?></span>
			<span class="anmi-stat-label"><?php esc_html_e('Active', 'anmi-plugin-manager'); ?></span>
		</div>
		<div class="anmi-stat-card">
			<span class="anmi-stat-value"><?php echo intval($stats['managed']); ?></span>
			<span class="anmi-stat-label"><?php esc_html_e('Managed', 'anmi-plugin-manager'); ?></span>
		</div>
	</div>

	<?php if (empty($plugins)): ?>
		<div class="anmi-empty-state">
			<p><?php esc_html_e('No Anmi plugins found. Upload a new plugin or mark an existing one as managed.', 'anmi-plugin-manager'); ?></p>
		</div>
	<?php else: ?>
		<div class="anmi-panel anmi-table-panel">
		<table class="wp-list-table widefat fixed striped anmi-table">
			<thead>
				<tr>
					<th class="column-name"><?php esc_html_e('Plugin Name', 'anmi-plugin-manager'); ?></th>
					<th class="column-version"><?php esc_html_e('Version', 'anmi-plugin-manager'); ?></th>
					<th class="column-author"><?php esc_html_e('Author', 'anmi-plugin-manager'); ?></th>
					<th class="column-status"><?php esc_html_e('Status', 'anmi-plugin-manager'); ?></th>
					<th class="column-managed"><?php esc_html_e('Managed', 'anmi-plugin-manager'); ?></th>
					<th class="column-actions"><?php esc_html_e('Actions', 'anmi-plugin-manager'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($plugins as $plugin):
				$plugin_file = $plugin['plugin_file'];
				$slug        = sanitize_title(str_replace('/', '-', $plugin_file));
				$actions     = $plugin['actions'];
			?>
				<tr>
					<td class="plugin-title">
						<strong><?php echo esc_html($plugin['name']); ?></strong>
						<div class="anmi-meta-text"><?php echo esc_html($plugin_file); ?></div>
						<?php if (!empty($plugin['description'])): ?>
							<div class="description anmi-subtle"><?php echo esc_html(wp_trim_words($plugin['description'], 24)); ?></div>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html($plugin['version']); ?></td>
					<td><?php echo esc_html($plugin['author']); ?></td>
					<td>
						<?php if ($plugin['is_active']): ?>
							<span class="anmi-badge anmi-badge--success"><?php esc_html_e('Active', 'anmi-plugin-manager'); ?></span>
						<?php else: ?>
							<span class="anmi-badge anmi-badge--neutral"><?php esc_html_e('Inactive', 'anmi-plugin-manager'); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($plugin['managed']): ?>
							<span class="anmi-badge anmi-badge--info">&check; <?php esc_html_e('Managed', 'anmi-plugin-manager'); ?></span>
						<?php else: ?>
							<span class="anmi-badge anmi-badge--warning"><?php esc_html_e('Not Managed', 'anmi-plugin-manager'); ?></span>
						<?php endif; ?>
					</td>
					<td class="anmi-table__actions">
						<?php if (!empty($actions['mark'])): ?>
							<a href="<?php echo esc_url($actions['mark']); ?>" class="button button-small <?php echo $kill_switch ? 'anmi-action-disabled' : 'button-primary'; ?>">
								<?php esc_html_e('Mark', 'anmi-plugin-manager'); ?>
							</a>
						<?php endif; ?>

						<?php if (!empty($actions['unmark'])): ?>
							<a href="<?php echo esc_url($actions['unmark']); ?>" class="button button-small <?php echo $kill_switch ? 'anmi-action-disabled' : ''; ?>">
								<?php esc_html_e('Unmark', 'anmi-plugin-manager'); ?>
							</a>
						<?php endif; ?>

						<?php if (!empty($actions['activate'])): ?>
							<a href="<?php echo esc_url($actions['activate']); ?>" class="button button-small <?php echo $kill_switch ? 'anmi-action-disabled' : ''; ?>">
								<?php esc_html_e('Safe Activate', 'anmi-plugin-manager'); ?>
							</a>
						<?php endif; ?>

						<?php if (!empty($actions['deactivate'])): ?>
							<a href="<?php echo esc_url($actions['deactivate']); ?>" class="button button-small <?php echo $kill_switch ? 'anmi-action-disabled' : ''; ?>">
								<?php esc_html_e('Safe Deactivate', 'anmi-plugin-manager'); ?>
							</a>
						<?php endif; ?>

						<?php if (!empty($actions['delete'])): ?>
							<a href="<?php echo esc_url($actions['delete']); ?>"
							   class="button button-small button-link-delete anmi-trigger-confirm <?php echo $kill_switch ? 'anmi-action-disabled' : ''; ?>"
							   data-plugin-slug="<?php echo esc_attr($slug); ?>">
								<?php esc_html_e('Delete', 'anmi-plugin-manager'); ?>
							</a>
						<?php endif; ?>

						<a href="<?php echo esc_url($actions['history']); ?>" class="button button-link">
							<?php esc_html_e('View History', 'anmi-plugin-manager'); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ($page_links): ?>
		<div class="anmi-table__footer">
			<div class="tablenav-pages">
				<?php echo wp_kses_post($page_links); ?>
			</div>
		</div>
		<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<div id="anmi-confirm-modal" class="anmi-modal" role="dialog" aria-modal="true" aria-labelledby="anmi-confirm-title">
	<div id="anmi-confirm-backdrop" class="anmi-modal-backdrop" tabindex="-1"></div>
	<div class="anmi-modal-dialog">
		<div class="anmi-modal-header">
			<h2 id="anmi-confirm-title"><?php esc_html_e('Confirm destructive action', 'anmi-plugin-manager'); ?></h2>
		</div>
		<div class="anmi-modal-body">
			<p><?php esc_html_e('This action cannot be undone. To continue, type the confirmation phrase below.', 'anmi-plugin-manager'); ?></p>
			<p class="anmi-confirm-instructions">
				<?php esc_html_e('Confirmation phrase:', 'anmi-plugin-manager'); ?>
				<code><?php echo esc_html($confirm_phrase); ?></code>
			</p>
			<label for="anmi-confirm-input" class="screen-reader-text"><?php esc_html_e('Confirmation input', 'anmi-plugin-manager'); ?></label>
			<input type="text" id="anmi-confirm-input" class="anmi-confirm-input" placeholder="<?php echo esc_attr($confirm_phrase); ?>">
			<div id="anmi-confirm-error" class="anmi-confirm-error" aria-live="polite"></div>
			<p><strong><?php esc_html_e('Target plugin slug:', 'anmi-plugin-manager'); ?></strong> <span id="anmi-confirm-plugin"></span></p>
		</div>
		<div class="anmi-modal-footer">
			<button id="anmi-confirm-cancel" class="button button-secondary"><?php esc_html_e('Cancel', 'anmi-plugin-manager'); ?></button>
			<button id="anmi-confirm-submit" class="button button-primary"><?php esc_html_e('Confirm', 'anmi-plugin-manager'); ?></button>
		</div>
	</div>
</div>
