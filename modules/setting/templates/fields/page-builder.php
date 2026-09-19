<?php
/**
 * Page builder field.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use ATSDash\Helpers\Content_Helper;

return function ( $role_key ) {

	$settings       = get_option( 'ats_settings' );
	$setting_roles  = isset( $settings['page_builder_template'] ) ? $settings['page_builder_template'] : array();
	$selected       = '';
	$content_helper = new Content_Helper();

	if ( ! empty( $setting_roles ) ) {
		$selected = isset( $setting_roles[ $role_key ] ) ? $setting_roles[ $role_key ] : '';
	}

	$builders = $content_helper->get_active_page_builders();

	$saved_templates = array();
	$has_saved_templates = false;

	foreach ( $builders as $builder ) {
		$saved_templates[ $builder ] = $content_helper->get_page_builder_templates( $builder );
		if ( ! empty( $saved_templates[ $builder ] ) ) {
			$has_saved_templates = true;
		}
	}
	?>

	<select name="ats_settings[page_builder_template][<?php echo esc_attr( $role_key ); ?>]" id="ats_settings[page_builder_template][<?php echo esc_attr( $role_key ); ?>]" class="widefat">
		<?php if ( empty( $builders ) || ! $has_saved_templates ) : ?>
			<option value="" selected>
		<?php else : ?>
			<option value="">
		<?php endif; ?>

		<?php
		if ( empty( $builders ) ) {
			_e( 'No page builder activated.', 'ats-dashboard' );
		} elseif ( ! $has_saved_templates ) {
			_e( 'No templates available', 'ats-dashboard' );
		} else {
			_e( 'Select...', 'ats-dashboard' );
		}
		?>

		</option>

		<?php
		foreach ( $saved_templates as $builder => $templates ) {

			foreach ( $templates as $template ) {
				$suffix = 'beaver' === $builder ? ' Builder' : '';
				$value  = $builder . '_' . $template['id'];
				?>

				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>>
					<?php echo esc_attr( ucwords( $builder ) ) . $suffix; ?> &mdash;
					<?php echo esc_attr( $template['title'] ); ?>
				</option>

				<?php
			}
			?>

			<?php
		}
		?>
	</select>

	<?php

};
