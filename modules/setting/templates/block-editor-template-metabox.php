<?php
/**
 * Block editor template metabox.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$manage_url = admin_url( 'edit.php?post_type=ats_block_template' );
$new_url    = admin_url( 'post-new.php?post_type=ats_block_template' );
?>

<div class="heatbox ats-block-editor-template-metabox">

	<h2><?php esc_html_e( 'Block Editor Templates', 'ats-dashboard' ); ?></h2>

	<div class="heatbox-content">
		<p>
			<?php esc_html_e( 'Create a Block Editor Template to use Gutenberg for your Page Builder Dashboard.', 'ats-dashboard' ); ?>
		</p>
		<p>
			<a href="<?php echo esc_url( $manage_url ); ?>" class="button button-larger">
				<?php esc_html_e( 'Manage Block Editor Templates', 'ats-dashboard' ); ?>
			</a>
			&nbsp;
			<a href="<?php echo esc_url( $new_url ); ?>" class="button button-primary button-larger">
				<?php esc_html_e( 'Add New Template', 'ats-dashboard' ); ?>
			</a>
		</p>
	</div>

</div>
