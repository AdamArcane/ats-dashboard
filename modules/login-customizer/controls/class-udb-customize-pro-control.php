<?php
/**
 * Custom PRO control.
 *
 * @package ATS_Dashboard
 */

namespace ats;

/**
 * Custom PRO control.
 */
class ats_Customize_Pro_Control extends \WP_Customize_Control {
	/**
	 * Control's type.
	 *
	 * @var string
	 */
	public $type = 'pro';

	/**
	 * Control's button text.
	 *
	 * @var string
	 */
	public $button_text = '';

	/**
	 * Control's button link.
	 *
	 * @var string
	 */
	public $button_url = '';

	/**
	 * Renders the range control wrapper and calls $this->render_content() for the internals.
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		$class = 'customize-control customize-control-' . $this->type . ' ats-customize-control ats-customize-control-' . $this->type;

		if ( ! isset( $this->input_attrs['class'] ) ) {
			$this->input_attrs['class'] = '';
		}

		$this->input_attrs['class'] .= ' ats-customize-field ats-customize-' . $this->type . '-field';

		printf( '<li id="%s" class="%s" data-control-name="%s">', esc_attr( $id ), esc_attr( $class ), esc_attr( $this->id ) );
		$this->render_content();
		echo '</li>';
	}

	/**
	 * Render the range control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
	 * Control content can alternately be rendered in JS. See WP_Customize_Control::print_template().
	 */
	public function render_content() {
		?>

		<header class="ats-customize-control-header">
			<?php if ( ! empty( $this->label ) ) : ?>
				<label class="customize-control-title ats-customize-control-label ats-customize-control-title">
					<?php echo esc_html( $this->label ); ?>
				</label>
			<?php endif; ?>
		</header>

		<p class="ats-pro-customizer-control-notice">
			<?php echo wp_kses_post( $this->description ); ?>
		</p>

		<a href="<?php echo esc_url( $this->button_url ); ?>" class="button button-primary" target="_blank">
			<?php echo esc_html( $this->button_text ); ?>
		</a>

		<?php
	}
}
