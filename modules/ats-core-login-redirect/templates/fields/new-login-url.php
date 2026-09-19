<?php
/**
 * New login url field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	$settings = get_option( 'ats_login_redirect' );
	$slug     = isset( $settings['login_url_slug'] ) ? trim( $settings['login_url_slug'], '/' ) : '';
	?>

	<div class="ats-url-prefix-suffix-field">
		<div class="ats-url-prefix-field">
			<code>
				<?php echo esc_url( site_url() ); ?>/
			</code>
		</div>

		<input type="text" name="ats_login_redirect[login_url_slug]" class="all-options" value="<?php echo esc_attr( $slug ); ?>" placeholder="login" />

		<div class="ats-url-suffix-field">
			<code>/</code>
		</div>
	</div>


	<p class="description">
		<?php
		/* translators: %1$s: Site URL */
		echo wp_kses_post( sprintf( __( 'Change the login URL and prevent users from accessing <code>%1$s/wp-login.php</code>.', 'ats-dashboard' ), esc_url( site_url() ) ) );
		?>
	</p>

	<?php

};
