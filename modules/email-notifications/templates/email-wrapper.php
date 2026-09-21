<?php
/**
 * Shared branded HTML wrapper used by every editable notification email.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $args ) {

	$args = wp_parse_args(
		$args,
		array(
			'logo_url'      => '',
			'accent_color'  => '#850b1a',
			'header_color'  => '#1a1a1a',
			'heading'       => '',
			'body'          => '',
			'button_text'   => '',
			'button_url'    => '',
			'support_email' => '',
			'support_url'   => '',
			'footer_text'   => '',
		)
	);

	ob_start();
	?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f0f0;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f0;padding:40px 20px;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;">

	<!-- Header -->
	<tr><td style="background:<?php echo esc_attr( $args['header_color'] ); ?>;border-radius:4px 4px 0 0;padding:28px 40px;text-align:center;">
		<?php if ( $args['logo_url'] ) : ?>
			<img src="<?php echo esc_url( $args['logo_url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="180" style="display:block;margin:0 auto;max-width:180px;height:auto;" />
		<?php endif; ?>
	</td></tr>

	<!-- Body -->
	<tr><td style="background:#ffffff;padding:40px;border-radius:0 0 4px 4px;">

		<?php if ( $args['heading'] ) : ?>
			<p style="margin:0 0 18px;font-size:17px;font-weight:bold;color:#1d2327;">
				<?php echo wp_kses_post( $args['heading'] ); ?>
			</p>
		<?php endif; ?>

		<div style="margin:0 0 14px;font-size:14px;color:#444;line-height:1.6;">
			<?php echo wp_kses_post( $args['body'] ); ?>
		</div>

		<?php if ( $args['button_text'] && $args['button_url'] ) : ?>
			<!-- CTA Button -->
			<table cellpadding="0" cellspacing="0" style="margin:0 0 32px;">
			<tr><td style="background:<?php echo esc_attr( $args['accent_color'] ); ?>;border-radius:3px;">
				<a href="<?php echo esc_url( $args['button_url'] ); ?>" style="display:inline-block;padding:13px 28px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">
					<?php echo esc_html( $args['button_text'] ); ?> &rarr;
				</a>
			</td></tr>
			</table>

			<p style="margin:0 0 6px;font-size:13px;color:#888;line-height:1.6;">
				<?php esc_html_e( "If the button doesn't work, copy and paste this link into your browser:", 'ats-dashboard' ); ?>
			</p>
			<p style="margin:0 0 32px;font-size:12px;word-break:break-all;">
				<a href="<?php echo esc_url( $args['button_url'] ); ?>" style="color:<?php echo esc_attr( $args['accent_color'] ); ?>;"><?php echo esc_url( $args['button_url'] ); ?></a>
			</p>
		<?php endif; ?>

		<?php if ( $args['support_email'] || $args['support_url'] ) : ?>
			<hr style="border:none;border-top:1px solid #eee;margin:0 0 24px;" />

			<p style="margin:0;font-size:13px;color:#888;line-height:1.6;">
				<?php esc_html_e( "Questions? We're always here to help.", 'ats-dashboard' ); ?><br>
				<?php if ( $args['support_email'] ) : ?>
					<?php
					/* translators: %s: Support email address */
					echo wp_kses_post( sprintf( __( 'Reach us at %s', 'ats-dashboard' ), '<a href="mailto:' . esc_attr( $args['support_email'] ) . '" style="color:' . esc_attr( $args['accent_color'] ) . ';text-decoration:none;">' . esc_html( $args['support_email'] ) . '</a>' ) );
					?>
				<?php endif; ?>
				<?php if ( $args['support_email'] && $args['support_url'] ) : ?>
					<?php esc_html_e( 'or visit our', 'ats-dashboard' ); ?>
				<?php endif; ?>
				<?php if ( $args['support_url'] ) : ?>
					<a href="<?php echo esc_url( $args['support_url'] ); ?>" style="color:<?php echo esc_attr( $args['accent_color'] ); ?>;text-decoration:none;"><?php esc_html_e( 'support portal', 'ats-dashboard' ); ?></a>.
				<?php endif; ?>
			</p>
		<?php endif; ?>

	</td></tr>

	<!-- Footer -->
	<tr><td style="padding:20px 40px;text-align:center;">
		<p style="margin:0;font-size:11px;color:#aaa;">
			<?php echo wp_kses_post( $args['footer_text'] ); ?>
		</p>
	</td></tr>

</table>
</td></tr>
</table>

</body>
</html>
	<?php
	return ob_get_clean();
};
