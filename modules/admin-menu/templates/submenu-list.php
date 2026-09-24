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
						<option value="2" {submenu_visibility_collapsed_selected}><?php esc_html_e( 'Optional', 'ats-dashboard' ); ?></option>
					</select>
				</div>
			</div>
			<div class="field ats-menu-builder--role-hide-field">
				<label for="submenu_role_hide_enabled_{role}_{default_menu_id}_{submenu_id}" class="label checkbox-label">
					<?php esc_html_e( 'Always hide for user role(s)', 'ats-dashboard' ); ?>
					<input
						type="checkbox"
						name="submenu_role_hide_enabled_{role}_{default_menu_id}_{submenu_id}"
						id="submenu_role_hide_enabled_{role}_{default_menu_id}_{submenu_id}"
						value="1"
						{submenu_role_hide_enabled_checked}
						class="ats-menu-builder--checkbox-field ats-menu-builder--role-hide-toggle"
						data-name="role_hide_enabled"
					>
					<div class="indicator"></div>
				</label>

				<div class="ats-menu-builder--role-hide-options {submenu_role_hide_options_hidden_class}">
					<div class="control ats-menu-builder--role-hide-mode">
						<label>
							<input type="radio" name="submenu_role_hide_mode_{role}_{default_menu_id}_{submenu_id}" value="except" data-name="role_hide_mode" {submenu_role_hide_mode_except_checked}>
							<?php esc_html_e( 'all roles except', 'ats-dashboard' ); ?>
						</label>
						<label>
							<input type="radio" name="submenu_role_hide_mode_{role}_{default_menu_id}_{submenu_id}" value="selected" data-name="role_hide_mode" {submenu_role_hide_mode_selected_checked}>
							<?php esc_html_e( 'selected roles', 'ats-dashboard' ); ?>
						</label>
					</div>

					<div class="ats-menu-builder--role-hide-roles {submenu_role_hide_roles_hidden_class}">
						{submenu_role_hide_roles_checkboxes}
					</div>

					<p class="description ats-menu-builder--role-hide-cap-note {submenu_role_hide_cap_note_hidden_class}">
						{submenu_role_hide_cap_note}
					</p>
				</div>
			</div>
			<div class="field">
				<label for="submenu_show_for_users_{role}_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
					<?php esc_html_e( 'Always show for the following users', 'ats-dashboard' ); ?>
				</label>
				<div class="control">
					<select
						name="submenu_show_for_users_{role}_{default_menu_id}_{submenu_id}[]"
						id="submenu_show_for_users_{role}_{default_menu_id}_{submenu_id}"
						class="ats-menu-builder--select-field ats-menu-builder--show-for-users"
						data-name="show_for_users"
						data-selected-users="{submenu_show_for_users_ids}"
						multiple
					></select>
				</div>
			</div>
		</div><!-- .ats-menu-builder--fields -->

	</div><!-- .ats-menu-builder--expanded-panel -->
</li>

<?php
return ob_get_clean();
