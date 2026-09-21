<?php
/**
 * Integrations page template.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Integrations\Integrations_Module;

return function () {

	$tab_menus = array(
		array(
			'id'     => 'mainwp',
			'text'   => __( 'MainWP', 'ats-dashboard' ),
			'active' => true,
		),
		array(
			'id'   => 'ploi',
			'text' => __( 'Ploi Hosting', 'ats-dashboard' ),
		),
		array(
			'id'   => 'suitedash',
			'text' => __( 'SuiteDash', 'ats-dashboard' ),
		),
		array(
			'id'   => 'postmark',
			'text' => __( 'Postmark / Email', 'ats-dashboard' ),
		),
	);

	$tab_menus = apply_filters( 'ats_integrations_tab_menus', $tab_menus );
	$pages     = Integrations_Module::tab_pages();
	?>

	<div class="wrap atsui-wrap ats-integrations-page">

		<div class="atsui-header atsui-has-tab-nav atsui-margin-bottom">

			<div class="atsui-container atsui-container-center">

				<div class="logo-container">

					<div>
						<span class="title">
							<?php echo esc_html( get_admin_page_title() ); ?>
						</span>
						<p class="subtitle"><?php esc_html_e( 'View data pushed from MainWP and other third-party integrations, with manual override options.', 'ats-dashboard' ); ?></p>
					</div>

					<div>
						<img src="<?php echo esc_url( ATS_DASHBOARD_PLUGIN_URL ); ?>/assets/img/logo.png">
					</div>

				</div>

				<nav>
					<ul class="atsui-tab-nav">
						<?php foreach ( $tab_menus as $tab ) : ?>
							<li class="atsui-tab-nav-item <?php echo esc_attr( $tab['id'] ); ?>-panel">
								<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['text'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

			</div>

		</div>

		<form method="post" action="options.php" class="ats-integrations-form">

			<div class="atsui-container atsui-container-center">

				<h1 style="display: none;"></h1>

				<?php settings_fields( 'ats-integrations-group' ); ?>

				<div>
					<?php foreach ( $pages as $tab_id => $page_slug ) : ?>
						<div class="atsui-admin-panel ats-<?php echo esc_attr( $tab_id ); ?>-panel">
							<div class="atsui">
								<?php do_settings_sections( $page_slug ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php submit_button( '', 'button button-primary button-larger' ); ?>

			</div>

		</form>

	</div>

	<?php
};
