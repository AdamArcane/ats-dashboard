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
use ATSDash\Setting\Site_Owner_Role;

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
		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'add_site_overview_widget' ), 20 );
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
	 * Replace WordPress core's "At a Glance" widget with a "Site Overview"
	 * widget geared towards a managed-client audience: content counts,
	 * storage used, SSL status, and last content update — no WordPress
	 * version, theme name, or other details a managed client wouldn't
	 * act on.
	 */
	public function add_site_overview_widget() {

		$settings = get_option( 'ats_settings' );

		if ( isset( $settings['disable_site_overview_widget'] ) ) {
			return;
		}

		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );

		wp_add_dashboard_widget( 'ats_site_overview', __( 'Site Overview', 'ats-dashboard' ), array( $this, 'render_site_overview_widget' ) );

		$this->custom_css[] = $this->get_site_overview_styles();

	}

	/**
	 * Render the Site Overview widget.
	 */
	public function render_site_overview_widget() {

		$post_counts      = wp_count_posts( 'post' );
		$page_counts      = wp_count_posts( 'page' );
		$comment_counts   = wp_count_comments();
		$user_count       = count_users();

		$published_posts  = isset( $post_counts->publish ) ? (int) $post_counts->publish : 0;
		$published_pages  = isset( $page_counts->publish ) ? (int) $page_counts->publish : 0;
		$pending_comments = isset( $comment_counts->moderated ) ? (int) $comment_counts->moderated : 0;
		$total_users      = isset( $user_count['total_users'] ) ? (int) $user_count['total_users'] : 0;

		$site_owner_count = 0;

		if ( Site_Owner_Role::is_enabled() && isset( $user_count['avail_roles'][ Site_Owner_Role::ROLE_KEY ] ) ) {
			$site_owner_count = (int) $user_count['avail_roles'][ Site_Owner_Role::ROLE_KEY ];
		}

		$storage_used  = $this->get_uploads_size_human();
		$last_change   = $this->get_last_content_change();
		$is_secure     = is_ssl();
		$is_up_to_date = $this->is_maintenance_up_to_date();
		?>

		<div class="ats-site-overview-widget">

			<ul class="ats-site-overview-counts">
				<li>
					<a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">
						<span class="ats-count"><?php echo esc_html( number_format_i18n( $published_posts ) ); ?></span>
						<span class="ats-label"><?php echo esc_html( _n( 'Post', 'Posts', $published_posts, 'ats-dashboard' ) ); ?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
						<span class="ats-count"><?php echo esc_html( number_format_i18n( $published_pages ) ); ?></span>
						<span class="ats-label"><?php echo esc_html( _n( 'Page', 'Pages', $published_pages, 'ats-dashboard' ) ); ?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>">
						<span class="ats-count"><?php echo esc_html( number_format_i18n( $total_users ) ); ?></span>
						<span class="ats-label">
							<?php echo esc_html( _n( 'User', 'Users', $total_users, 'ats-dashboard' ) ); ?>
							<?php if ( $site_owner_count > 0 ) : ?>
								· <?php echo esc_html( number_format_i18n( $site_owner_count ) ); ?> <?php echo esc_html( $this->pluralize_role_name( Site_Owner_Role::role_name(), $site_owner_count ) ); ?>
							<?php endif; ?>
						</span>
					</a>
				</li>
				<?php if ( $pending_comments > 0 ) : ?>
					<li class="ats-needs-attention">
						<a href="<?php echo esc_url( admin_url( 'edit-comments.php?comment_status=moderated' ) ); ?>">
							<span class="ats-count"><?php echo esc_html( number_format_i18n( $pending_comments ) ); ?></span>
							<span class="ats-label"><?php echo esc_html( _n( 'Comment awaiting moderation', 'Comments awaiting moderation', $pending_comments, 'ats-dashboard' ) ); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>

			<ul class="ats-site-overview-details">
				<li>
					<span class="ats-detail-label"><?php esc_html_e( 'Maintenance', 'ats-dashboard' ); ?></span>
					<span class="ats-detail-value <?php echo $is_up_to_date ? 'ats-status-good' : 'ats-status-pending'; ?>">
						<?php echo $is_up_to_date ? esc_html__( 'Up to date', 'ats-dashboard' ) : esc_html__( 'Updates scheduled', 'ats-dashboard' ); ?>
					</span>
				</li>
				<li>
					<span class="ats-detail-label"><?php esc_html_e( 'Security', 'ats-dashboard' ); ?></span>
					<span class="ats-detail-value <?php echo $is_secure ? 'ats-status-good' : 'ats-status-bad'; ?>">
						<?php echo $is_secure ? esc_html__( 'Secure (SSL)', 'ats-dashboard' ) : esc_html__( 'Not Secure', 'ats-dashboard' ); ?>
					</span>
				</li>
				<li>
					<span class="ats-detail-label"><?php esc_html_e( 'Storage Used', 'ats-dashboard' ); ?></span>
					<span class="ats-detail-value"><?php echo esc_html( $storage_used ); ?></span>
				</li>
				<li>
					<span class="ats-detail-label"><?php esc_html_e( 'Last Content Change', 'ats-dashboard' ); ?></span>
					<span class="ats-detail-value"><?php echo esc_html( $last_change ? $last_change['date'] : __( 'No content yet', 'ats-dashboard' ) ); ?></span>
				</li>
				<?php if ( $last_change ) : ?>
					<li class="ats-last-change-link">
						<a href="<?php echo esc_url( $last_change['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<?php
							echo esc_html(
								$last_change['was_added']
									/* translators: %s: Post/page title. */
									? sprintf( __( 'Added: %s', 'ats-dashboard' ), $last_change['title'] )
									/* translators: %s: Post/page title. */
									: sprintf( __( 'Modified: %s', 'ats-dashboard' ), $last_change['title'] )
							);
							?>
						</a>
					</li>
				<?php endif; ?>
			</ul>

		</div>

		<?php

	}

	/**
	 * Get the uploads directory size, formatted for display.
	 *
	 * Recursive directory scans are expensive, so the result is cached
	 * for a few hours rather than recalculated on every dashboard load.
	 *
	 * @return string
	 */
	private function get_uploads_size_human() {

		$cached = get_transient( 'ats_site_overview_storage' );

		if ( false !== $cached ) {
			return $cached;
		}

		if ( ! function_exists( 'get_dirsize' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$uploads = wp_upload_dir();
		$bytes   = get_dirsize( $uploads['basedir'] );

		$human = false !== $bytes ? size_format( $bytes, 1 ) : __( 'Unknown', 'ats-dashboard' );

		set_transient( 'ats_site_overview_storage', $human, 12 * HOUR_IN_SECONDS );

		return $human;

	}

	/**
	 * Get the most recently changed published post/page/custom post type
	 * entry — a concrete "content last changed on" date rather than a
	 * vague "X hours ago" that resets on every unrelated save, plus what
	 * that entry was and whether it was newly added or an edit to
	 * existing content.
	 *
	 * @return array|false {
	 *     @type string $date      The formatted modification date.
	 *     @type string $title     The entry's title.
	 *     @type string $url       The entry's public URL.
	 *     @type bool   $was_added True if this was a new entry, false if an edit to existing content.
	 * }
	 */
	private function get_last_content_change() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );
		unset( $post_types['attachment'] );

		if ( empty( $post_types ) ) {
			return false;
		}

		$latest = get_posts(
			array(
				'post_type'      => array_values( $post_types ),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		if ( empty( $latest ) ) {
			return false;
		}

		$post_id = $latest[0];

		$published_ts = strtotime( get_post_field( 'post_date', $post_id ) );
		$modified_ts  = strtotime( get_post_field( 'post_modified', $post_id ) );

		// A first publish can shift a few seconds between post_date and
		// post_modified (revision save, scheduled publish, etc.), so treat
		// anything within 2 minutes of its publish time as "added" rather
		// than a later "modified" edit.
		$was_added = abs( $modified_ts - $published_ts ) <= 2 * MINUTE_IN_SECONDS;

		$title = get_the_title( $post_id );

		return array(
			'date'      => date_i18n( get_option( 'date_format' ), $modified_ts ),
			'title'     => '' !== $title ? $title : __( '(no title)', 'ats-dashboard' ),
			'url'       => get_permalink( $post_id ),
			'was_added' => $was_added,
		);

	}

	/**
	 * Whether the site has no pending core/plugin/theme updates.
	 *
	 * Deliberately reported as a binary "Up to date" / "Updates scheduled"
	 * status rather than surfacing a raw update count — a managed client
	 * seeing "7 plugin updates available" reads as neglect, not as
	 * information they can act on; the maintenance provider is the one
	 * who acts on it.
	 *
	 * @return bool
	 */
	private function is_maintenance_up_to_date() {

		if ( ! function_exists( 'wp_get_update_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/update.php';
		}

		$update_data = wp_get_update_data();

		return empty( $update_data['counts']['total'] );

	}

	/**
	 * Naively pluralize a (possibly admin-configured) role display name for
	 * the "N {Role Name}" sublabel.
	 *
	 * @param string $name  The role display name, e.g. "Site Owner".
	 * @param int    $count The count it's being used with.
	 * @return string
	 */
	private function pluralize_role_name( $name, $count ) {

		if ( 1 === $count || preg_match( '/s$/i', $name ) ) {
			return $name;
		}

		return $name . 's';

	}

	/**
	 * Site Overview widget styles.
	 *
	 * @return string
	 */
	private function get_site_overview_styles() {

		return '
			.ats-site-overview-widget .ats-site-overview-counts {
				display: flex;
				flex-wrap: wrap;
				gap: 12px;
				margin: 0 0 16px;
				padding: 0;
				list-style: none;
			}
			.ats-site-overview-widget .ats-site-overview-counts li {
				flex: 1 1 calc(50% - 12px);
				min-width: 100px;
			}
			.ats-site-overview-widget .ats-site-overview-counts a {
				display: block;
				padding: 10px 12px;
				border-radius: 4px;
				background: #f6f7f7;
				text-decoration: none;
			}
			.ats-site-overview-widget .ats-count {
				display: block;
				font-size: 20px;
				font-weight: 600;
				line-height: 1.3;
				color: #1d2327;
			}
			.ats-site-overview-widget .ats-label {
				display: block;
				font-size: 13px;
				color: #646970;
			}
			.ats-site-overview-widget .ats-needs-attention a {
				background: #fcf0f1;
			}
			.ats-site-overview-widget .ats-needs-attention .ats-count {
				color: #d63638;
			}
			.ats-site-overview-widget .ats-site-overview-details {
				margin: 0;
				padding: 12px 0 0;
				border-top: 1px solid #dcdcde;
				list-style: none;
			}
			.ats-site-overview-widget .ats-site-overview-details li {
				display: flex;
				justify-content: space-between;
				padding: 4px 0;
				font-size: 13px;
			}
			.ats-site-overview-widget .ats-detail-label {
				color: #646970;
			}
			.ats-site-overview-widget .ats-detail-value {
				font-weight: 600;
				color: #1d2327;
			}
			.ats-site-overview-widget .ats-status-good {
				color: #00a32a;
			}
			.ats-site-overview-widget .ats-status-bad {
				color: #d63638;
			}
			.ats-site-overview-widget .ats-status-pending {
				color: #2271b1;
			}
			.ats-site-overview-widget .ats-site-overview-details li.ats-last-change-link {
				justify-content: flex-end;
				padding-top: 0;
			}
			.ats-site-overview-widget .ats-last-change-link a {
				font-size: 12px;
				color: #646970;
				text-decoration: none;
			}
			.ats-site-overview-widget .ats-last-change-link a:hover {
				color: #2271b1;
				text-decoration: underline;
			}
		';

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
