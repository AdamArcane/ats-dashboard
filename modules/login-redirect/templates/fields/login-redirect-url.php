<?php
/**
 * Login redirect url field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ats\Vars;

return function ( $site_type = 'blueprint' ) {

	$site_type_prefix = 'subsites' === $site_type ? 'subsites_' : '';

	$settings       = get_option( 'ats_login_redirect', array() );
	$redirect_slugs = isset( $settings[ $site_type_prefix . 'login_redirect_slugs' ] ) ? $settings[ $site_type_prefix . 'login_redirect_slugs' ] : array();

	$wp_roles   = wp_roles();
	$role_names = $wp_roles->role_names;

	$multisite_supported = apply_filters( 'ats_ms_supported', false );
	$is_blueprint        = apply_filters( 'ats_ms_is_blueprint', false );

	$field_prefix = esc_url( site_url( '/' ) );

	if ( $multisite_supported ) {
		if ( $is_blueprint ) {
			if ( 'blueprint' !== $site_type ) {
				$field_prefix = '{subsite_url}/';
			}
		}
	}
	?>

	<div class="ats-login-redirect--wrapper ats-login-redirect--<?php echo esc_attr( $site_type ); ?>-wrapper">

		<select class="ats-login-redirect--role-selector" data-placeholder="<?php esc_attr_e( 'Select a Role', 'ats-dashboard' ); ?>" data-width="200px" data-ats-site-type="<?php echo esc_attr( $site_type ); ?>" data-ats-field-prefix="<?php echo esc_attr( $field_prefix ); ?>">

			<option value="" readonly>
				<?php esc_html_e( 'Select a User', 'ats-dashboard' ); ?>
			</option>

			<?php
			if ( $multisite_supported && is_super_admin() ) {
				$super_admin_value       = ! empty( $redirect_slugs ) && isset( $redirect_slugs['all'] ) ? $redirect_slugs['all'] : '';
				$super_admin_is_disabled = $super_admin_value ? true : false;
				?>

				<option value="super_admin"  data-ats-default-slug="<?php echo esc_attr( $super_admin_value ); ?>" <?php disabled( $super_admin_is_disabled, true ); ?>>
					<?php esc_html_e( 'Super Admin', 'ats-dashboard' ); ?>
				</option>

				<?php
			}

			foreach ( $role_names as $role_key => $role_name ) {
				$value       = ! empty( $redirect_slugs ) && isset( $redirect_slugs[ $role_key ] ) ? $redirect_slugs[ $role_key ] : '';
				$is_disabled = $value ? true : false;
				?>

				<option value="<?php echo esc_attr( $role_key ); ?>" data-ats-default-slug="<?php echo esc_attr( $value ); ?>" <?php disabled( $is_disabled, true ); ?>>
					<?php echo esc_html( $role_name ); ?>
				</option>

			<?php } ?>

		</select>

		<div class="ats-login-redirect--repeater">

			<?php
			foreach ( $redirect_slugs as $role_key => $redirect_slug ) {
				if ( ! empty( $redirect_slug ) ) {
					$role_name     = isset( $role_names[ $role_key ] ) ? $role_names[ $role_key ] : '';
					$role_name     = 'super_admin' === $role_key ? __( 'Super Admin', 'ats-dashboard' ) : $role_name;
					$readonly_attr = '';

					if ( 'super_admin' === $role_key ) {
						if ( ! $multisite_supported || ( $multisite_supported && ! is_super_admin() ) ) {
							$readonly_attr = ' readonly';
						}
					}
					?>

					<div class="ats-login-redirect--repeater-item" data-ats-role-key="<?php echo esc_attr( $role_key ); ?>" data-ats-role-name="<?php echo esc_attr( $role_name ); ?>">
						<label class="ats-login-redirect--field-label">
							<?php echo esc_html( $role_name ); ?>
						</label>
						<div class="ats-login-redirect--field-control">

							<div class="ats-url-prefix-suffix-field">
								<div class="ats-url-prefix-field">
									<code>
										<?php echo esc_html( $field_prefix ); ?>
									</code>
								</div>
								<input type="text" name="ats_login_redirect[<?php echo esc_attr( $site_type_prefix ); ?>login_redirect_slugs][<?php echo esc_attr( $role_key ); ?>]" value="<?php echo esc_attr( $redirect_slug ); ?>" placeholder="wp-admin/"<?php echo wp_kses_post( $readonly_attr ); ?>>
								<div class="ats-url-prefix-field">
									<?php if ( 'super_admin' === $role_key ) : ?>
										<?php if ( $multisite_supported && is_super_admin() ) : ?>
											<button type="button" class="ats-login-redirect--remove-field">
												<span class="ats-login-redirect--close-icon"></span>
											</button>
										<?php endif; ?>
									<?php else : ?>
										<button type="button" class="ats-login-redirect--remove-field">
											<span class="ats-login-redirect--close-icon"></span>
										</button>
									<?php endif; ?>
								</div>
							</div>

						</div>
					</div>

					<?php
				}
			}
			?>

		</div>

	</div>

	<?php

};
