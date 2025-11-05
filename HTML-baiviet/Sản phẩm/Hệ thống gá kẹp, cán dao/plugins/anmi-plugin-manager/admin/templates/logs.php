<?php
defined('ABSPATH') || exit;

$filters    = $view['filters'];
$logs       = $view['logs'];
$pagination = $view['pagination'];
$actions    = $view['actions'];
$plugins    = $view['plugins'];

$action_filter = $filters['action'];
$plugin_filter = $filters['plugin'];
$search_filter = $filters['search'];

$query_args = [
	'page'          => 'anmi-plugins-logs',
	'filter_action' => $action_filter ?: null,
	'filter_plugin' => $plugin_filter ?: null,
	's'             => $search_filter ?: null,
];

$base_url = add_query_arg(
	array_filter($query_args, static function ($value) {
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
	<h1><?php esc_html_e('Plugin History Logs', 'anmi-plugin-manager'); ?></h1>

	<div class="anmi-toolbar">
		<form method="get" class="anmi-form">
			<input type="hidden" name="page" value="anmi-plugins-logs">

			<label for="anmi-filter-action" class="screen-reader-text"><?php esc_html_e('Filter by action', 'anmi-plugin-manager'); ?></label>
			<select name="filter_action" id="anmi-filter-action">
				<option value=""><?php esc_html_e('All actions', 'anmi-plugin-manager'); ?></option>
				<?php foreach ($actions as $action): ?>
					<option value="<?php echo esc_attr($action); ?>" <?php selected($action_filter, $action); ?>>
						<?php echo esc_html($action); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label for="anmi-filter-plugin" class="screen-reader-text"><?php esc_html_e('Filter by plugin', 'anmi-plugin-manager'); ?></label>
			<select name="filter_plugin" id="anmi-filter-plugin">
				<option value=""><?php esc_html_e('All plugins', 'anmi-plugin-manager'); ?></option>
				<?php foreach ($plugins as $plugin): ?>
					<option value="<?php echo esc_attr($plugin); ?>" <?php selected($plugin_filter, $plugin); ?>>
						<?php echo esc_html($plugin); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label for="anmi-log-search" class="screen-reader-text"><?php esc_html_e('Search logs', 'anmi-plugin-manager'); ?></label>
			<input type="search" name="s" id="anmi-log-search" value="<?php echo esc_attr($search_filter); ?>" placeholder="<?php esc_attr_e('Search log payload…', 'anmi-plugin-manager'); ?>">

			<button type="submit" class="button button-secondary"><?php esc_html_e('Filter', 'anmi-plugin-manager'); ?></button>
		</form>
	</div>

	<?php if (empty($logs)): ?>
		<div class="anmi-empty-state">
			<p><?php esc_html_e('No logs found for the selected filters.', 'anmi-plugin-manager'); ?></p>
		</div>
	<?php else: ?>
		<div class="anmi-panel anmi-table-panel">
		<table class="wp-list-table widefat fixed striped anmi-table anmi-logs-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Timestamp', 'anmi-plugin-manager'); ?></th>
					<th><?php esc_html_e('Action', 'anmi-plugin-manager'); ?></th>
					<th><?php esc_html_e('Plugin', 'anmi-plugin-manager'); ?></th>
					<th><?php esc_html_e('User', 'anmi-plugin-manager'); ?></th>
					<th style="width: 140px; text-align: right;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($logs as $log): ?>
				<tr>
					<td><?php echo esc_html($log['timestamp']); ?></td>
					<td><span class="anmi-badge <?php echo esc_attr($log['action_style']); ?>"><?php echo esc_html($log['action']); ?></span></td>
					<td>
						<?php if (!empty($log['plugin_file'])): ?>
							<code><?php echo esc_html($log['plugin_file']); ?></code>
						<?php else: ?>
							<span class="anmi-subtle"><?php esc_html_e('N/A', 'anmi-plugin-manager'); ?></span>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html($log['user']); ?></td>
					<td style="text-align: right;">
						<button type="button" class="button button-small anmi-view-json" data-json="<?php echo esc_attr($log['json']); ?>">
							<?php esc_html_e('View JSON', 'anmi-plugin-manager'); ?>
						</button>
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

<div id="anmi-json-modal" class="anmi-modal" role="dialog" aria-modal="true" aria-labelledby="anmi-json-title">
	<div id="anmi-json-backdrop" class="anmi-modal-backdrop" tabindex="-1"></div>
	<div class="anmi-modal-dialog" style="width: 600px; max-width: 95%;">
		<div class="anmi-modal-header">
			<h2 id="anmi-json-title"><?php esc_html_e('Log payload', 'anmi-plugin-manager'); ?></h2>
		</div>
		<div class="anmi-modal-body">
			<pre id="anmi-json-content" class="anmi-json-content">{}</pre>
		</div>
		<div class="anmi-modal-footer">
			<button id="anmi-json-close" class="button button-primary"><?php esc_html_e('Close', 'anmi-plugin-manager'); ?></button>
		</div>
	</div>
</div>
