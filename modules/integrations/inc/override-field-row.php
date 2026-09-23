<?php
/**
 * Renders one row of the Label | Auto Value | Override table shared by the
 * integrations settings fields (MainWP, Ploi, SuiteDash).
 *
 * The override cell shows the current override value (if any) with an edit
 * icon; clicking it — or the auto value — reveals the input (see
 * assets/js/integrations.js). When an override is active, the auto value
 * cell gets a `has-override` class on the row so CSS can mute it, making it
 * visually obvious it isn't the value currently in effect.
 *
 * Passing `note` instead of `label`/`auto_value` renders a description row
 * instead of a field row — indented to align under the auto value/override
 * columns (skipping the label column) so it reads as a note attached to the
 * row(s) directly above it, inside the same table, rather than a floating
 * paragraph disconnected from the field it explains.
 *
 * @package ATS_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( array $args ) {

	if ( isset( $args['note'] ) ) {
		?>
		<tr class="ats-override-note-row">
			<th scope="row"></th>
			<td colspan="2" class="ats-override-note"><?php echo esc_html( $args['note'] ); ?></td>
		</tr>
		<?php
		return;
	}

	$args = array_merge(
		array(
			'label'          => '',
			'auto_value'     => '',
			'input_name'     => null, // Null renders a read-only row with no override column.
			'override_value' => '',
			'placeholder'    => '',
			'type'           => 'text',
		),
		$args
	);

	$has_override = null !== $args['input_name'] && '' !== trim( (string) $args['override_value'] );
	?>

	<tr class="ats-override-row<?php echo $has_override ? ' has-override' : ''; ?>">
		<th scope="row"><?php echo esc_html( $args['label'] ); ?></th>
		<td class="ats-override-auto-cell">
			<span class="ats-override-auto-value"><?php echo esc_html( '' !== $args['auto_value'] ? $args['auto_value'] : '—' ); ?></span>
		</td>
		<td class="ats-override-cell">
			<?php if ( null === $args['input_name'] ) : ?>
				<span class="ats-override-na">&mdash;</span>
			<?php else : ?>
				<span class="ats-override-display">
					<?php if ( $has_override ) : ?>
						<span class="ats-override-display-value"><?php echo esc_html( $args['override_value'] ); ?></span>
					<?php endif; ?>
					<button type="button" class="ats-override-edit-toggle" aria-label="<?php esc_attr_e( 'Edit override', 'ats-dashboard' ); ?>">
						<span class="dashicons dashicons-edit"></span>
					</button>
				</span>
				<span class="ats-override-edit"<?php echo $has_override ? '' : ' style="display:none;"'; ?>>
					<input
						type="<?php echo esc_attr( $args['type'] ); ?>"
						name="<?php echo esc_attr( $args['input_name'] ); ?>"
						value="<?php echo esc_attr( $args['override_value'] ); ?>"
						data-original="<?php echo esc_attr( $args['override_value'] ); ?>"
						class="regular-text"
						placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					/>
					<button type="button" class="ats-override-cancel button-link"><?php esc_html_e( 'Cancel', 'ats-dashboard' ); ?></button>
				</span>
			<?php endif; ?>
		</td>
	</tr>

	<?php

};
