<?php
/**
 * Menu list template to be rendered via JS.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<li class="ats-menu-builder--menu-item" data-hidden="{menu_is_hidden}" data-added="{menu_was_added}" data-default-id="{default_menu_id}" data-default-url="{default_menu_url}">
	<div class="ats-menu-builder--control-panel">
		<div class="ats-menu-builder--menu-drag">
			<span></span>
		</div>
		<div class="ats-menu-builder--menu-icon">
			{menu_icon}
		</div>
		<div class="ats-menu-builder--menu-name">
			{parsed_menu_title}
		</div>
		<div class="ats-menu-builder--menu-actions">
			{trash_icon}
			<span class="dashicons dashicons-{hidden_icon} ats-menu-builder--visibility-indicator {visibility_indicator_class}" title="{visibility_label}"></span>
			<span class="dashicons dashicons-arrow-down-alt2 expand-menu"></span>
		</div>
	</div><!-- .ats-menu-builder--control-panel -->

	<div class="ats-menu-builder--expanded-panel">

		<div class="ats-menu-builder--tabs ats-menu-builder--menu-item-tabs">

			<ul class="ats-menu-builder--tab-menu">
				<li class="ats-menu-builder--tab-menu-item is-active" data-ats-tab-content="ats-menu-builder--settings-tab--{role}">
					<button type="button">
						<?php esc_html_e( 'Settings', 'ats-dashboard' ); ?>
					</button>
				</li>
				<li class="ats-menu-builder--tab-menu-item" data-ats-tab-content="ats-menu-builder--submenu-tab--{role}">
					<button type="button">
						<?php esc_html_e( 'Submenu', 'ats-dashboard' ); ?>
					</button>
				</li>
			</ul><!-- .ats-menu-builder--tab-menu -->

			<div class="ats-menu-builder--tab-content">
				<div id="ats-menu-builder--settings-tab--{role}" class="ats-menu-builder--tab-content-item is-active">
					<div class="ats-menu-builder--fields">
						<div class="field">
							<label for="menu_title_{role}_{default_menu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Menu Title', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<input 
									type="text" 
									name="menu_title_{role}_{default_menu_id}" 
									id="menu_title_{role}_{default_menu_id}" 
									value="{menu_title}" 
									placeholder="{default_menu_title}" 
									class="ats-menu-builder--text-field"
									data-name="menu_title"
								>
							</div>
						</div>
						<div class="field">
							<label for="menu_url_{role}_{default_menu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Menu URL', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<input
									type="text"
									name="menu_url_{role}_{default_menu_id}"
									id="menu_url_{role}_{default_menu_id}"
									value="{menu_url}"
									placeholder="{default_menu_url}"
									class="ats-menu-builder--text-field ats-menu-builder--url-field"
									data-name="menu_url"
									autocomplete="off"
								>
							</div>
						</div>
						<div class="field">
							<label for="menu_open_new_tab_{role}_{default_menu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Open in New Tab', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<label for="menu_open_new_tab_{role}_{default_menu_id}" class="toggle-switch">
									<input
										type="checkbox"
										name="menu_open_new_tab_{role}_{default_menu_id}"
										id="menu_open_new_tab_{role}_{default_menu_id}"
										value="1"
										{menu_open_new_tab_checked}
										class="ats-menu-builder--checkbox-field"
										data-name="menu_open_new_tab"
									>
									<div class="switch-track">
										<div class="switch-thumb"></div>
									</div>
								</label>
							</div>
						</div>
						<div class="field">
							<label for="menu_visibility_{role}_{default_menu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Visibility', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<select
									name="menu_visibility_{role}_{default_menu_id}"
									id="menu_visibility_{role}_{default_menu_id}"
									class="ats-menu-builder--select-field"
									data-name="menu_visibility"
								>
									<option value="0" {menu_visibility_normal_selected}><?php esc_html_e( 'Normal', 'ats-dashboard' ); ?></option>
									<option value="1" {menu_visibility_hidden_selected}><?php esc_html_e( 'Hidden', 'ats-dashboard' ); ?></option>
									<option value="2" {menu_visibility_collapsed_selected}><?php esc_html_e( 'Optional', 'ats-dashboard' ); ?></option>
								</select>
							</div>
						</div>
						<div class="field ats-menu-builder--role-hide-field">
							<div class="ats-menu-builder--toggle-row">
								<label for="menu_role_hide_enabled_{role}_{default_menu_id}" class="toggle-switch">
									<input
										type="checkbox"
										name="menu_role_hide_enabled_{role}_{default_menu_id}"
										id="menu_role_hide_enabled_{role}_{default_menu_id}"
										value="1"
										{menu_role_hide_enabled_checked}
										class="ats-menu-builder--checkbox-field ats-menu-builder--role-hide-toggle"
										data-name="role_hide_enabled"
									>
									<div class="switch-track">
										<div class="switch-thumb"></div>
									</div>
								</label>
								<label for="menu_role_hide_enabled_{role}_{default_menu_id}" class="ats-menu-builder--toggle-row-label">
									<?php esc_html_e( 'Always hide for user role(s)', 'ats-dashboard' ); ?>
								</label>
							</div>

							<div class="ats-menu-builder--role-hide-options {menu_role_hide_options_hidden_class}">
								<div class="control ats-menu-builder--role-hide-mode">
									<label>
										<input type="radio" name="menu_role_hide_mode_{role}_{default_menu_id}" value="except" data-name="role_hide_mode" {menu_role_hide_mode_except_checked}>
										<?php esc_html_e( 'all roles except', 'ats-dashboard' ); ?>
									</label>
									<label>
										<input type="radio" name="menu_role_hide_mode_{role}_{default_menu_id}" value="selected" data-name="role_hide_mode" {menu_role_hide_mode_selected_checked}>
										<?php esc_html_e( 'selected roles', 'ats-dashboard' ); ?>
									</label>
								</div>

								<div class="ats-menu-builder--role-hide-roles {menu_role_hide_roles_hidden_class}">
									{menu_role_hide_roles_checkboxes}
								</div>

								<p class="description ats-menu-builder--role-hide-cap-note {menu_role_hide_cap_note_hidden_class}">
									{menu_role_hide_cap_note}
								</p>
							</div>
						</div>
						<div class="field">
							<label for="menu_show_for_users_{role}_{default_menu_id}" class="label ats-menu-builder--label">
								<?php esc_html_e( 'Always show for the following users', 'ats-dashboard' ); ?>
							</label>
							<div class="control">
								<select
									name="menu_show_for_users_{role}_{default_menu_id}[]"
									id="menu_show_for_users_{role}_{default_menu_id}"
									class="ats-menu-builder--select-field ats-menu-builder--show-for-users"
									data-name="show_for_users"
									data-selected-users="{menu_show_for_users_ids}"
									multiple
								></select>
							</div>
						</div>
						<div class="is-nested">
							<div class="field">
								<label for="menu_icon_{role}_{default_menu_id}" class="label ats-menu-builder--label">
									<?php esc_html_e( 'Menu Icon', 'ats-dashboard' ); ?>
								</label>
							</div>
							<div class="ats-menu-builder--tabs ats-menu-builder--icon-switcher">
								<ul class="ats-menu-builder--tab-menu">
									<li class="ats-menu-builder--tab-menu-item {dashicon_tab_is_active}" data-ats-tab-content="ats-menu-builder--dashicon-tab--{role}" data-tab-name="dashicon">
										<button type="button">Dashicons</button>
									</li>
									<li class="ats-menu-builder--tab-menu-item {icon_svg_tab_is_active}" data-ats-tab-content="ats-menu-builder--icon-svg-tab--{role}" data-tab-name="icon_svg">
										<button type="button">SVG Code</button>
									</li>
								</ul>
								<div class="ats-menu-builder--tab-content">
									<div id="ats-menu-builder--dashicon-tab--{role}" class="ats-menu-builder--tab-content-item {dashicon_tab_is_active}">
										<div class="field">
											<div class="control">
												<input type="text" class="ats-menu-builder--text-field ats-menu-builder--icon-field dashicons-picker" data-width="100%" name="menu_dashicon_{role}_{default_menu_id}" id="menu_dashicon_{role}_{default_menu_id}" value="{menu_dashicon}" placeholder="{default_menu_dashicon}" data-name="dashicon" />
											</div>
										</div>
									</div>
									<div id="ats-menu-builder--icon-svg-tab--{role}" class="ats-menu-builder--tab-content-item {icon_svg_tab_is_active}">
										<textarea name="menu_icon_svg_{role}_{default_menu_id}" id="menu_icon_svg_{role}_{default_menu_id}" class="ats-menu-builder--textarea-field ats-menu-builder--icon-field" placeholder="{default_menu_icon_svg}" data-name="icon_svg">{menu_icon_svg}</textarea>
										<p class="description">
										<?php
										echo wp_kses_post(
											sprintf(
												/* translators: 1: data URI, 2: code example, 3: tool URL */
												__( 'Paste a base64-encoded SVG using a %1$s, which will be colored to match the color scheme. This should begin with %2$s. Or you can use this tool to generate it: %3$s', 'ats-dashboard' ),
												'<strong>data URI</strong>',
												'<code>data:image/svg+xml;base64,</code>',
												'<a href="https://iotools.cloud/tool/svg-to-base64-encode/" target="_blank">https://iotools.cloud/tool/svg-to-base64-encode/</a>'
											)
										);
										?>
									</p>
									</div>
								</div>
							</div>
						</div>
					</div><!-- .ats-menu-builder--fields -->
				</div><!-- #ats-menu-builder--settings-tab -->
				<div id="ats-menu-builder--submenu-tab--{role}" class="ats-menu-builder--tab-content-item ats-menu-builder--edit-area">
					<ul class="ats-menu-builder--menu-list ats-menu-builder--submenu-list ats-menu-builder-sortable">
						{submenu_template}
					</ul>

					<?php do_action( 'ats_admin_menu_add_submenu_button' ); ?>
				</div><!-- #ats-menu-builder--submenu-tab -->
			</div><!-- .ats-menu-builder--tab-content -->

		</div><!-- .ats-menu-builder--tabs -->

	</div><!-- .ats-menu-builder--expanded-panel -->
</li>

<?php
return ob_get_clean();
