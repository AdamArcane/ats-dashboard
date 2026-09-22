<?php
/**
 * Submenu list template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<li class="ats-menu-builder--menu-item ats-menu-builder--submenu-item ats-menu-builder--submenu-item-level-{submenu_level}" data-default-parent="{default_submenu_parent}" data-added="{submenu_was_added}" data-hidden="{submenu_is_hidden}"  data-default-id="{default_submenu_id}" data-default-href="{default_submenu_href}" data-default-group="{default_submenu_group}" data-menu-id="{submenu_id}">
	<div class="ats-menu-builder--control-panel">
		<div class="ats-menu-builder--menu-drag">
			<span></span>
		</div>
		<div class="ats-menu-builder--menu-name">
			{parsed_submenu_title}
		</div>
		<div class="ats-menu-builder--menu-actions">
			{frontend_only_indicator}
			{group_indicator}
			{trash_icon}
			<span class="dashicons dashicons-{hidden_icon} hide-menu"></span>
			<span class="dashicons dashicons-arrow-down-alt2 expand-menu"></span>
		</div>
	</div><!-- .ats-menu-builder--control-panel -->

	<div class="ats-menu-builder--expanded-panel">

		<div class="ats-menu-builder--tabs ats-menu-builder--submenu-item-tabs">

			<ul class="ats-menu-builder--tab-menu">
				<li class="ats-menu-builder--tab-menu-item is-active" data-ats-tab-content="ats-menu-builder--settings-tab--{default_submenu_id}">
					<button type="button">
						<?php esc_html_e( 'Settings', 'ats-dashboard' ); ?>
					</button>
				</li>
				<li class="ats-menu-builder--tab-menu-item {submenu_tab_is_hidden}" data-ats-tab-content="ats-menu-builder--submenu-tab--{default_submenu_id}">
					<button type="button">
						<?php esc_html_e( 'Submenu', 'ats-dashboard' ); ?>
					</button>
				</li>
			</ul><!-- .ats-menu-builder--tab-menu -->

			<div class="ats-menu-builder--tab-content">
				<div id="ats-menu-builder--settings-tab--{default_submenu_id}" class="ats-menu-builder--tab-content-item is-active">
					<div class="ats-menu-builder--fields ats-menu-builder--submenu-fields">
						<div class="field {submenu_title_field_is_hidden}">
							<label for="submenu_title_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Submenu Title', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<textarea 
									row="1" 
									name="submenu_title_{default_menu_id}_{submenu_id}" 
									id="submenu_title_{default_menu_id}_{submenu_id}" 
									placeholder="{encoded_default_submenu_title}" 
									class="ats-menu-builder--text-field"
									data-name="submenu_title"
									{submenu_title_is_disabled}
								>{submenu_title}</textarea>
							</div>
						</div>
						<div class="field {submenu_href_field_is_hidden}">
							<label for="submenu_href_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Submenu URL', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<input 
									type="text" 
									name="submenu_href_{default_menu_id}_{submenu_id}" 
									id="submenu_href_{default_menu_id}_{submenu_id}" 
									value="{submenu_href}" 
									placeholder="{default_submenu_href}" 
									class="ats-menu-builder--text-field"
									data-name="submenu_href"
									{submenu_href_is_disabled}
								>
							</div>
						</div>
						<div class="field {submenu_href_field_is_hidden}">
							<label for="submenu_open_new_tab_{default_menu_id}_{submenu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Open in New Tab', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<input
									type="checkbox"
									name="submenu_open_new_tab_{default_menu_id}_{submenu_id}"
									id="submenu_open_new_tab_{default_menu_id}_{submenu_id}"
									value="1"
									{submenu_open_new_tab_checked}
									class="ats-menu-builder--checkbox-field"
									data-name="submenu_open_new_tab"
								>
							</div>
						</div>

						<!--
						These fields are not being used currently.
						But leave it here because in the future, if requested, it would be used for
						"hide menu item for specific role(s) / user(s)" functionality.
						-->
						<!--
						<div class="field">
							<label for="disallowed_roles_{default_submenu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Hide from specific role(s):', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<select
									name="disallowed_roles_{default_submenu_id}" 
									id="disallowed_roles_{default_submenu_id}" 
									class="ats-menu-builder--select-field ats-menu-builder--select2-field ats-menu-builder--roles-select2-field"
									data-placeholder="<?php esc_attr_e( 'Select a role', 'ats-dashboard' ); ?>"
									data-name="disallowed_roles"
									data-disallowed-roles="{disallowed_roles}"
									multiple
								></select>
							</div>
						</div>
						<div class="field">
							<label for="disallowed_users_{default_submenu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Hide from specific user(s):', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<select
									name="disallowed_users_{default_submenu_id}" 
									id="disallowed_users_{default_submenu_id}" 
									class="ats-menu-builder--select-field ats-menu-builder--select2-field ats-menu-builder--users-select2-field"
									data-placeholder="<?php esc_attr_e( 'Select a user', 'ats-dashboard' ); ?>"
									data-name="disallowed_users"
									data-disallowed-users="{disallowed_users}"
									multiple
								></select>
							</div>
						</div>
						-->

						{empty_submenu_settings_text}

					</div><!-- .ats-menu-builder--fields -->
				</div><!-- #ats-menu-builder--settings-tab -->
				<div id="ats-menu-builder--submenu-tab--{default_submenu_id}" class="ats-menu-builder--tab-content-item ats-menu-builder--edit-area {submenu_tab_is_hidden}">
					<ul class="ats-menu-builder--menu-list ats-menu-builder--submenu-list" data-menu-type="submenu" data-submenu-level="{submenu_next_level}">
						{submenu_template}
					</ul>

					<?php do_action( 'ats_admin_bar_add_submenu_button' ); ?>
				</div><!-- #ats-menu-builder--submenu-tab -->
			</div><!-- .ats-menu-builder--tab-content -->

		</div>

	</div><!-- .ats-menu-builder--expanded-panel -->
</li>

<?php
return ob_get_clean();
