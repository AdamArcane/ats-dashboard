<?php
/**
 * Branding output.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Widget;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use WP_Query;
use ATSDash\Base\Base_Output;
use ATSDash\Helpers\Widget_Helper;

/**
 * Class to setup widgets output.
 */
class Widget_Base_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * The default placeholder tags.
	 *
	 * @var array
	 */
	public $placeholder_tags;

	/**
	 * The default placeholder tag values.
	 *
	 * @var array
	 */
	public $placeholder_values;

	/**
	 * Per-widget custom CSS, collected while adding dashboard widgets and
	 * printed as part of the aggregated dashboard stylesheet.
	 *
	 * @var array
	 */
	public $custom_css = array();

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ATS_DASHBOARD_PLUGIN_URL . '/modules/widget';

		$this->placeholder_tags = [
			'{first_name}',
			'{last_name}',
			'{display_name}',
			'{user_email}',

			'{admin_email}',
			'{site_name}',
			'{site_url}',
		];

		$current_user = wp_get_current_user();

		$this->placeholder_values = [
			$current_user->first_name,
			$current_user->last_name,
			$current_user->display_name,
			$current_user->user_email,

			get_option( 'admin_email' ),
			get_bloginfo( 'name' ),
			site_url(),
		];

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup widgets output.
	 */
	public function setup() {

		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'add_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'remove_default_dashboard_widgets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_styles' ), 100 );

	}

	/**
	 * Add dashboard widgets.
	 *
	 * @param array $user_roles Current user roles.
	 */
	public function add_dashboard_widgets( $user_roles = array() ) {

		$current_user = wp_get_current_user();

		/**
		 * We pass the current user role here to check against in the PRO add-on.
		 * Note: a better variable for $user_roles would be $current_roles as that's really what it actually is.
		 * Shouldn't be changed as parameter passed is called $user_roles.
		 */
		if ( empty( $user_roles ) ) {
			$user_roles = $current_user->roles;
		}

		// Currently only used to add the super admin to current roles.
		$user_roles = apply_filters( 'ats_widget_user_roles', $user_roles );

		$args = array(
			'post_type'      => 'ats_widgets',
			'posts_per_page' => 100,
			'post_status'    => 'publish',
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) :

			$loop->the_post();

			$post_id     = get_the_ID();
			$title       = get_the_title();
			$title       = do_shortcode( $title );
			$title       = $this->convert_placeholder_tags( $title );
			$icon        = get_post_meta( $post_id, 'ats_icon_key', true );
			$link        = get_post_meta( $post_id, 'ats_link', true );
			$target      = get_post_meta( $post_id, 'ats_link_target', true );
			$tooltip     = get_post_meta( $post_id, 'ats_tooltip', true );
			$position    = get_post_meta( $post_id, 'ats_position_key', true );
			$priority    = get_post_meta( $post_id, 'ats_priority_key', true );
			$widget_type = get_post_meta( $post_id, 'ats_widget_type', true );
			$output      = '';

			// Preventing edge case when widget_type is empty.
			if ( ! $widget_type ) {

				$widget_type = apply_filters( 'ats_compat_widget_type', $widget_type, $post_id );

			}

			$allow_access = apply_filters( 'ats_allow_widget_access', true, $post_id, $user_roles );

			if ( ! $allow_access ) {
				continue;
			}

			if ( 'html' === $widget_type ) {

				$content = get_post_meta( $post_id, 'ats_html', true );

				$output = sprintf(
					'<div class="ats-html-wrapper">%1s</div>',
					do_shortcode( $content )
				);

				$output = $this->convert_placeholder_tags( $output );

			} elseif ( 'text' === $widget_type ) {

				$content = get_post_meta( $post_id, 'ats_content', true );

				$content_height = get_post_meta( $post_id, 'ats_content_height', true );
				$content_height = $content_height ? $content_height : '';

				$output = sprintf(
					'<div class="ats-content-wrapper"%1s>%2s</div>',
					$content_height ? ' data-ats-content-height="' . esc_attr( $content_height ) . '"' : '',
					wp_kses_post( wpautop( $content ) )
				);

				$output = do_shortcode( $output );
				$output = $this->convert_placeholder_tags( $output );

			} elseif ( 'icon' === $widget_type ) {

				$link = is_string( $link ) ? $link : '';

				if ( 0 === strpos( $link, './wp-admin/' ) ) {
					// Prevent double wp-admin string ('/wp-admin/wp-admin/') when rendering the link.
					$link = str_replace( './wp-admin/', './', $link );
				}

				$icon_color = get_post_meta( $post_id, 'ats_icon_color', true );
				$icon_style = $icon_color ? ' style="color: ' . esc_attr( $icon_color ) . ';"' : '';

				$output = sprintf(
					'<a href="%1$s" target="%2$s"><i class="%3$s"%4$s></i></a>',
					// We don't use esc_url() here since $link can be a relative path.
					esc_attr( $link ),
					esc_attr( $target ),
					esc_attr( $icon ),
					$icon_style
				);

				if ( $tooltip ) {
					$tooltip = $this->convert_placeholder_tags( $tooltip );

					$output .= sprintf(
						'<i class="ats-info"></i><div class="ats-tooltip"><span>%1s</span></div>',
						esc_html( $tooltip )
					);
				}
			} elseif ( 'rss' === $widget_type ) {

				$output = $this->render_rss_widget( $post_id );

			}

			$custom_css = get_post_meta( $post_id, 'ats_custom_css', true );

			if ( $custom_css ) {
				// {{WRAPPER}} lets widget authors scope rules to just this
				// widget; anything else they write applies dashboard-wide.
				$this->custom_css[] = str_replace( '{{WRAPPER}}', '#ms-ats' . $post_id, $custom_css );
			}

			$output_args = array(
				'id'          => $post_id,
				'title'       => $title,
				'position'    => $position,
				'priority'    => $priority,
				'widget_type' => $widget_type,
			);

			$output = apply_filters( 'ats_widget_output', $output, $output_args );

			$output_callback = function () use ( $output ) {
				$widget_helper = new Widget_Helper();
				echo wp_kses( $output, $widget_helper->get_allowed_tags() );
			};

			// Add metabox.
			add_meta_box( 'ms-ats' . $post_id, $title, $output_callback, 'dashboard', $position, $priority );

		endwhile;

	}

	/**
	 * Remove default WordPress dashboard widgets.
	 */
	public function remove_default_dashboard_widgets() {

		$saved_widgets   = $this->widget()->get_saved_default();
		$default_widgets = $this->widget()->get_default();
		$settings        = get_option( 'ats_settings' );

		if ( isset( $settings['remove-all'] ) ) {

			remove_action( 'welcome_panel', 'wp_welcome_panel' );

			foreach ( $default_widgets as $id => $widget ) {
				if ( false !== $widget ) {
					remove_meta_box( $id, 'dashboard', $widget['context'] );
				}
			}
		} else {

			if ( isset( $settings['welcome_panel'] ) ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
			}

			foreach ( $saved_widgets as $id => $widget ) {
				if ( false !== $widget ) {
					remove_meta_box( $id, 'dashboard', $widget['context'] );
				}
			}
		}

	}

	/**
	 * Add dashboard styles.
	 */
	public function dashboard_styles() {

		$css = '';

		ob_start();
		require __DIR__ . '/inc/widget-styles.css.php';
		$css = ob_get_clean();

		if ( ! empty( $this->custom_css ) ) {
			$css .= "\n" . implode( "\n", $this->custom_css );
		}

		wp_add_inline_style( 'ats-dashboard', $css );

	}

	/**
	 * Convert placeholder tags with their values.
	 *
	 * @param string $str The string to replace the tags in.
	 * @return string The modified string.
	 */
	public function convert_placeholder_tags( $str ) {

		$str = str_replace( $this->placeholder_tags, $this->placeholder_values, $str );
		$str = apply_filters( 'ats_widgets_convert_placeholder_tags', $str );

		return $str;

	}

	/**
	 * Render the RSS feed widget's dashboard output.
	 *
	 * @param int $post_id The widget's post id.
	 *
	 * @return string The rendered HTML.
	 */
	public function render_rss_widget( $post_id ) {

		$feed_url = get_post_meta( $post_id, 'ats_rss_feed_url', true );

		if ( ! $feed_url ) {
			return '<p>' . esc_html__( 'No feed URL configured.', 'ats-dashboard' ) . '</p>';
		}

		if ( ! function_exists( 'fetch_feed' ) ) {
			require_once ABSPATH . WPINC . '/feed.php';
		}

		$feed = fetch_feed( $feed_url );

		if ( is_wp_error( $feed ) ) {
			return '<p>' . esc_html__( 'Unable to load this feed right now.', 'ats-dashboard' ) . '</p>';
		}

		$max_items      = (int) get_post_meta( $post_id, 'ats_rss_max_items', true );
		$max_items      = $max_items ? $max_items : 5;
		$show_images    = (bool) get_post_meta( $post_id, 'ats_rss_show_images', true );
		$show_excerpt   = (bool) get_post_meta( $post_id, 'ats_rss_show_excerpt', true );
		$excerpt_length = (int) get_post_meta( $post_id, 'ats_rss_excerpt_length', true );
		$excerpt_length = $excerpt_length ? $excerpt_length : 20;
		$show_author    = (bool) get_post_meta( $post_id, 'ats_rss_show_author', true );
		$show_date      = (bool) get_post_meta( $post_id, 'ats_rss_show_date', true );
		$target         = get_post_meta( $post_id, 'ats_rss_new_tab', true ) ? ' target="_blank" rel="noopener noreferrer"' : '';

		$items = $feed->get_items( 0, $max_items );

		if ( empty( $items ) ) {
			return '<p>' . esc_html__( 'This feed has no items.', 'ats-dashboard' ) . '</p>';
		}

		$output = '<ul class="ats-rss-wrapper">';

		foreach ( $items as $item ) {

			$title = $item->get_title();
			$title = $title ? $title : __( '(no title)', 'ats-dashboard' );
			$link  = $item->get_permalink();

			$output .= '<li class="ats-rss-item">';

			if ( $show_images ) {
				$image_url = $this->get_rss_item_image( $item );

				if ( $image_url ) {
					$output .= '<div class="ats-rss-item-image"><img src="' . esc_url( $image_url ) . '" alt=""></div>';
				}
			}

			$output .= '<div class="ats-rss-item-content">';
			$output .= '<a class="ats-rss-item-title" href="' . esc_url( $link ) . '"' . $target . '>' . esc_html( $title ) . '</a>';

			$meta = array();

			if ( $show_author && $item->get_author() ) {
				$meta[] = esc_html( $item->get_author()->get_name() );
			}

			if ( $show_date && $item->get_date( 'U' ) ) {
				$meta[] = esc_html( date_i18n( get_option( 'date_format' ), $item->get_date( 'U' ) ) );
			}

			if ( ! empty( $meta ) ) {
				$output .= '<div class="ats-rss-item-meta">' . implode( ' &middot; ', $meta ) . '</div>';
			}

			if ( $show_excerpt ) {
				$excerpt = wp_strip_all_tags( $item->get_description() );
				$excerpt = wp_trim_words( $excerpt, $excerpt_length );

				if ( $excerpt ) {
					$output .= '<div class="ats-rss-item-excerpt">' . esc_html( $excerpt ) . '</div>';
				}
			}

			$output .= '</div>'; // .ats-rss-item-content
			$output .= '</li>';

		}

		$output .= '</ul>';

		return $output;

	}

	/**
	 * Try to resolve a feed item's image, checking its enclosure first and
	 * falling back to the first <img> found in its content.
	 *
	 * @param SimplePie_Item $item The feed item.
	 *
	 * @return string The image url, or an empty string if none was found.
	 */
	public function get_rss_item_image( $item ) {

		$enclosure = $item->get_enclosure();

		if ( $enclosure ) {

			$thumbnail = $enclosure->get_thumbnail();

			if ( $thumbnail ) {
				return $thumbnail;
			}

			if ( $enclosure->get_link() && $enclosure->get_type() && 0 === strpos( $enclosure->get_type(), 'image/' ) ) {
				return $enclosure->get_link();
			}
		}

		$content = $item->get_content();

		if ( $content && preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches ) ) {
			return $matches[1];
		}

		return '';

	}

}
