<?php
/**
 * Submenu list template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<li class="ats-menu-builder--menu-item ats-menu-builder--submenu-item" data-hidden="{submenu_is_hidden}" data-added="{submenu_was_added}" data-default-url="{default_submenu_url}" data-submenu-id="{submenu_id}">
	<div class="ats-menu-builder--control-panel">
		<div class="ats-menu-builder--menu-drag">
			<span></span>
		</div>
		<div class="ats-menu-builder--menu-name">
			{parsed_submenu_title}
		</div>
		<div class="ats-menu-builder--menu-actions">
			{trash_icon}
			{override_indicator}
			<span class="dashicons dashicons-{hidden_icon} ats-menu-builder--visibility-indicator {visibility_indicator_class}" title="{visibility_label}"></span>
			<span class="dashicons dashicons-arrow-down-alt2 expand-menu"></span>
		</div>
	</div><!-- .ats-menu-builder--control-panel -->

	<div class="ats-menu-builder--expanded-panel">

		<div class="ats-menu-builder--fields ats-menu-builder--submenu-fields">
			<div class="field">
				<label for="submenu_title_{role}_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
					<?php esc_html_e( 'Submenu Title', 'ats-dashboard' ); ?>
				</label>
				<div class="control">
					<input 
						type="text" 
						name="submenu_title_{role}_{default_menu_id}_{submenu_id}" 
						id="submenu_title_{role}_{default_menu_id}_{submenu_id}" 
						value="{submenu_title}" 
						placeholder="{default_submenu_title}" 
						class="ats-menu-builder--text-field"
						data-name="submenu_title"
					>
				</div>
			</div>
			<div class="field">
				<label for="submenu_url_{role}_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
					<?php esc_html_e( 'Submenu URL', 'ats-dashboard' ); ?>
				</label>
				<div class="control">
					<input 
						type="text" 
						name="submenu_url_{role}_{default_menu_id}_{submenu_id}" 
						id="submenu_url_{role}_{default_menu_id}_{submenu_id}" 
						value="{submenu_url}" 
						placeholder="{default_submenu_url}" 
						class="ats-menu-builder--text-field"
						data-name="submenu_url"
					>
				</div>
			</div>
			<div class="field">
				<label for="submenu_open_new_tab_{role}_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
					<?php esc_html_e( 'Open in New Tab', 'ats-dashboard' ); ?>
				</label>
				<div class="control">
					<input
						type="checkbox"
						name="submenu_open_new_tab_{role}_{default_menu_id}_{submenu_id}"
						id="submenu_open_new_tab_{role}_{default_menu_id}_{submenu_id}"
						value="1"
						{submenu_open_new_tab_checked}
						class="ats-menu-builder--checkbox-field"
						data-name="submenu_open_new_tab"
					>
				</div>
			</div>
			<div class="field">
				<label for="submenu_visibility_{role}_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
					<?php esc_html_e( 'Visibility', 'ats-dashboard' ); ?>
				</label>
				<div class="control">
					<select
						name="submenu_visibility_{role}_{default_menu_id}_{submenu_id}"
						id="submenu_visibility_{role}_{default_menu_id}_{submenu_id}"
						class="ats-menu-builder--select-field"
						data-name="submenu_visibility"
					>
						<option value="0" {submenu_visibility_normal_selected}><?php esc_html_e( 'Normal', 'ats-dashboard' ); ?></option>
						<option value="1" {submenu_visibility_hidden_selected}><?php esc_html_e( 'Hidden', 'ats-dashboard' ); ?></option>
						<option value="2" {submenu_visibility_collapsed_selected}><?php esc_html_e( 'Collapsed', 'ats-dashboard' ); ?></option>
					</select>
				</div>
			</div>
		</div><!-- .ats-menu-builder--fields -->

	</div><!-- .ats-menu-builder--expanded-panel -->
</li>

<?php
return ob_get_clean();
