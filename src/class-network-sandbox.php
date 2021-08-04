<?php

namespace User_Governance;

class Network_Sandbox {

	/**
	 * Option Key
	 *
	 * @var option_key
	 */
	private $option_key = 'wpug_network_sandbox_option';

	private $default_option = array(
		'sandbox_show_link' => 'off',
		'sandbox_link_text' => 'Switch To The Sandbox Site',
		'sandbox_url'       => 'https://sandbox.example.com/',
		'sandbox_icon'      => 'sandbox-icon.svg',
		'live_show_link'    => 'off',
		'live_link_text'    => 'Switch To The Live Site',
		'live_url'          => 'https://example.com/',
		'live_icon'         => 'live-icon.svg',
	);

	/**
	 * Class constructor function.
	 *
	 * @return void
	 */
	public function __construct() {

		// Add the admin bar link.
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_link' ), 31 );

		// Add a sandbox class to the body.
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );

		// Get options.
		$option   = get_site_option( $this->option_key );
		$option   = array_merge( $this->default_option, $option );
		$base_url = $this->get_base_url();

		// Hooks for the sandbox network only.
		$sandbox_url  = $option['sandbox_url'];
		$sandbox_show = $option['sandbox_show_link'];
		if ( $sandbox_url === $base_url && 'on' === $sandbox_show ) {
			add_action( 'admin_init', array( 'PAnD', 'init' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice_sandbox_site' ) );
		}

		if (
			( $sandbox_url === $base_url && 'on' === $sandbox_show )
			|| ( $option['live_url'] === $base_url && 'on' === $option['live_show_link'] )
		) {

			// Register global styles used in the theme.
			add_action( 'admin_enqueue_scripts', array( $this, 'register_sandbox_assets' ) );

			// Enqueue extension styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_sandbox_assets' ) );

			// Register global styles used in the theme.
			add_action( 'wp_enqueue_scripts', array( $this, 'register_sandbox_assets' ) );

			// Enqueue extension styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_sandbox_assets' ) );

		}

	}

	/**
	 * Registers sandbox assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_sandbox_assets() {

		wp_register_style(
			'wp-user-governance-network-sandbox-styles',
			WP_USER_GOV_DIR_URL . 'css/network-sandbox.css',
			false,
			filemtime( WP_USER_GOV_DIR_PATH . 'css/network-sandbox.css' ),
			'screen'
		);

		wp_register_script(
			'wp-user-governance-network-sandbox-scripts',
			WP_USER_GOV_DIR_URL . 'js/network-sandbox.js',
			'jquery',
			filemtime( WP_USER_GOV_DIR_PATH . 'js/network-sandbox.js' ),
			true
		);

	}

	/**
	 * Enqueues sandbox assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_sandbox_assets() {

		wp_enqueue_style( 'wp-user-governance-network-sandbox-styles' );
		wp_enqueue_script( 'wp-user-governance-network-sandbox-scripts' );

	}

	/**
	 * Get the current page's base URL.
	 *
	 * @return string
	 */
	private function get_base_url() {

		$domain   = $_SERVER['HTTP_HOST'];
		$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://';

		return $protocol . $domain . '/';

	}

	/**
	 * Show a message on the sandbox site notifying users they are on the sandbox site.
	 *
	 * @return void
	 */
	public function admin_notice_sandbox_site() {

		if ( ! \PAnD::is_admin_notice_active( 'disable-wpug-network-sandbox-notice-forever' ) ) {
			return;
		}

		$option        = get_site_option( $this->option_key );
		$option        = array_merge( $this->default_option, $option );
		$live_url      = $option['live_url'];
		$uri           = preg_replace( '/^\/?/', '', $_SERVER['REQUEST_URI'] );
		$live_page_url = $live_url . $uri;
		?>
		<div data-dismissible="disable-wpug-network-sandbox-notice-forever" class="notice wpug-network-sandbox-notice notice-error is-dismissible">
			<p><?php _e( "You are now editing the Sandbox site! <a href=\"$live_page_url\">Click here to go back to the live site.</a>", 'wp-user-governance' ); ?></p>
		</div>
		<?php

	}

	/**
	 * Add a sandbox class to the body.
	 *
	 * @param array|string $classes The current body classes.
	 *
	 * @return array|string
	 */
	public function body_class( $classes ) {

		// If $classes is a string convert to an array and remember it was a string.
		$type = gettype( $classes );
		if ( 'string' === $type ) {
			if ( ! $classes ) {
				$classes = array();
			} else {
				$classes = explode( ' ', $classes );
			}
		}

		// Get URLs for comparison.
		$option      = get_site_option( $this->option_key );
		$option      = array_merge( $this->default_option, $option );
		$base_url    = $this->get_base_url();
		$sandbox_url = $option['sandbox_url'];
		$live_url    = $option['live_url'];

		// Add class name if this is a sandbox site.
		if ( $sandbox_url === $base_url ) {
			$classes[] = 'wpug-network-is-sandbox sandbox-a';
		} elseif ( $live_url === $base_url ) {
			$classes[] = 'wpug-network-is-live';
		}

		// Return $classes to a string if it was a string before.
		if ( 'string' === $type ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;

	}

	/**
	 * Add the Switch to Site link to the admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 *
	 * @return void
	 */
	public function admin_bar_link( $wp_admin_bar ) {

		$option       = get_site_option( $this->option_key );
		$option       = array_merge( $this->default_option, $option );
		$base_url     = $this->get_base_url();
		$sandbox_show = $option['sandbox_show_link'];
		$sandbox_url  = $option['sandbox_url'];
		$live_show    = $option['live_show_link'];
		$live_url     = $option['live_url'];
		$switch_link  = '';
		$switch_title = '';
		$uri          = preg_replace( '/^\/?/', '', $_SERVER['REQUEST_URI'] );

		if ( $sandbox_url === $base_url ) {

			if ( 'on' !== $live_show ) {
				return;
			}
			$switch_link  = $live_url . $uri;
			$switch_class = 'wpug-network-sandbox-link-to-live';
			ob_start();
			include WP_USER_GOV_DIR_PATH . 'templates/network-sb-switch-sandboxsite.php';
			$switch_title = ob_get_clean();

		} elseif ( $live_url === $base_url ) {

			if ( 'on' !== $sandbox_show ) {
				return;
			}
			$switch_link  = $sandbox_url . $uri;
			$switch_class = 'wpug-network-sandbox-link-to-sandbox';
			ob_start();
			include WP_USER_GOV_DIR_PATH . 'templates/network-sb-switch-livesite.php';
			$switch_title = ob_get_clean();

		}

		if ( $switch_link ) {
			$wp_admin_bar->add_node(
				array(
					'id' => 'wpug_network_sandbox_link',
					'title' => $switch_title,
					'meta' => array(
						'class' => $switch_class,
					)
				)
			);
		}
	}
}
