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
		'live_show_link'    => 'off',
		'live_link_text'    => 'Switch To The Live Site',
		'live_url'          => 'https://example.com/',
	);

	private $labels = array(
		'live'    => array(
			array('wpug_env_live', 'You are on the Live Site', 'Live Site'),
			array('wpug_env_live', 'Click to go back to the Live Site', 'Go back to the Live Site'),
		),
		'sandbox' => array(
			array('wpug_env_sandbox', 'You are on the Sandbox Site', 'Sandbox Site'),
			array('wpug_env_sandbox', 'Click to go back to the Sandbox Site', 'Go back to the Sandbox Site'),
		),
	);

	/**
	 * Class constructor function.
	 *
	 * @return void
	 */
	public function __construct() {

		// Get the status of the current site's network sandbox switch.
		$network_switch_context = $this->network_switch_context();

		if ( 'on' === $network_switch_context['status'] ) {

			// Add the admin bar link.
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_link' ), 51 );

			// Add a sandbox class to the body.
			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_filter( 'admin_body_class', array( $this, 'body_class' ) );

			if ( 'sandbox' === $network_switch_context['type'] ) {

				// Admin notices for the Sandbox site.
				add_action( 'admin_init', array( 'PAnD', 'init' ) );
				add_action( 'admin_notices', array( $this, 'admin_notice_sandbox_site' ) );

			}

		}

		// Register the Network Sandbox assets.

		if ( $network_switch_context && $network_switch_context['status'] === 'on' ) {

			if ( 'sandbox' === $network_switch_context['type'] ) {

				// Register global styles used in the theme.
				add_action( 'admin_enqueue_scripts', array( $this, 'register_network_sb_sandbox_assets' ) );

				// Enqueue extension styles.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_network_sb_sandbox_assets' ) );

				// Register global styles used in the theme.
				add_action( 'wp_enqueue_scripts', array( $this, 'register_network_sb_sandbox_assets' ) );

				// Enqueue extension styles.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_network_sb_sandbox_assets' ) );

			} elseif ( 'live' === $network_switch_context['type'] ) {

				// Register global styles used in the theme.
				add_action( 'admin_enqueue_scripts', array( $this, 'register_network_sb_live_assets' ) );

				// Enqueue extension styles.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_network_sb_live_assets' ) );

				// Register global styles used in the theme.
				add_action( 'wp_enqueue_scripts', array( $this, 'register_network_sb_live_assets' ) );

				// Enqueue extension styles.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_network_sb_live_assets' ) );

			}

			// Register global styles used in the theme.
			add_action( 'admin_enqueue_scripts', array( $this, 'register_network_sb_js' ) );

			// Enqueue extension styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_network_sb_js' ) );

			// Register global styles used in the theme.
			add_action( 'wp_enqueue_scripts', array( $this, 'register_network_sb_js' ) );

			// Enqueue extension styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_network_sb_js' ) );

		}

	}

	private function network_switch_context() {

		$option       = get_site_option( $this->option_key );
		$option       = array_merge( $this->default_option, $option );
		$base_url     = $this->get_base_url();
		$sandbox_show = $option['sandbox_show_link'];
		$sandbox_url  = $option['sandbox_url'];
		$live_show    = $option['live_show_link'];
		$live_url     = $option['live_url'];
		$site         = array(
			'type'        => '',
			'status'      => '',
			'destination' => '',
		);

		if ( $sandbox_url === $base_url ) {

			$site['type'] = 'sandbox';
			$site['status'] = $live_show;
			$site['destination'] = $live_url . preg_replace( '/^\/?/', '', $_SERVER['REQUEST_URI'] );

		} elseif ( $live_url === $base_url ) {

			$site['type'] = 'live';
			$site['status'] = $sandbox_show;
			$site['destination'] = $sandbox_url . preg_replace( '/^\/?/', '', $_SERVER['REQUEST_URI'] );

		}

		return $site;

	}

	/**
	 * Registers sandbox assets specific to the sandbox site.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_network_sb_sandbox_assets() {

		wp_register_style(
			'wp-user-governance-network-sb-sandbox-styles',
			WP_USER_GOV_DIR_URL . 'css/network-sb-sandbox.css',
			false,
			filemtime( WP_USER_GOV_DIR_PATH . 'css/network-sb-sandbox.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues sandbox assets specific to the sandbox site.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_network_sb_sandbox_assets() {

		wp_enqueue_style( 'wp-user-governance-network-sb-sandbox-styles' );

	}

	/**
	 * Registers sandbox assets specific to the live site.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_network_sb_live_assets() {

		wp_register_style(
			'wp-user-governance-network-sb-live-styles',
			WP_USER_GOV_DIR_URL . 'css/network-sb-live.css',
			false,
			filemtime( WP_USER_GOV_DIR_PATH . 'css/network-sb-live.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues sandbox assets specific to the live site.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_network_sb_live_assets() {

		wp_enqueue_style( 'wp-user-governance-network-sb-live-styles' );

  	global $_wp_admin_css_colors;
  	$user_color = get_user_option( 'admin_color' );
  	$user_color_theme = $_wp_admin_css_colors[$user_color];

		$css = "#wpadminbar>#wp-toolbar #wp-admin-bar-wpug_network_sandbox_link .active {\n";
		$css .= "  color: {$user_color_theme->colors[3]};";
		$css .= "\n}\n";
		$css .= "#wp-admin-bar-wpug_network_sandbox_link .info-icon {\n";
		$css .= "  color: {$user_color_theme->icon_colors['base']};";
		$css .= "\n}\n";
		$css .= "#wp-admin-bar-wpug_network_sandbox_link .info-icon:focus,\n";
		$css .= "#wp-admin-bar-wpug_network_sandbox_link .info-icon:hover,\n";
		$css .= "#wp-admin-bar-wpug_network_sandbox_link .info-icon.active {\n";
		$css .= "  color: {$user_color_theme->icon_colors['focus']};";
		$css .= "\n}\n";

		wp_add_inline_style( 'wp-user-governance-network-sb-live-styles', $css );

	}

	/**
	 * Registers sandbox JS assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_network_sb_js() {

		wp_register_script(
			'wp-user-governance-network-sandbox-scripts',
			WP_USER_GOV_DIR_URL . 'js/network-sandbox.js',
			'jquery',
			filemtime( WP_USER_GOV_DIR_PATH . 'js/network-sandbox.js' ),
			true
		);

	}

	/**
	 * Enqueues sandbox JS assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_network_sb_js() {

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		wp_enqueue_script( 'wp-user-governance-network-sandbox-scripts' );

		// Include destination URL for network sandbox switch.
		$network_switch_context = $this->network_switch_context();
		$destination_url = $this->network_switch_context()['destination'];
		$script_variables = 'var wpugnsbdest = "' . $destination_url . '";';

		wp_add_inline_script( 'wp-user-governance-network-sandbox-scripts', $script_variables, 'before' );

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

		$network_switch_context = $this->network_switch_context();

		if ( 'on' === $network_switch_context['status'] ) {

			// Create switch group.
			$type         = $network_switch_context['type'];
			$switch_class = "wpug-network-sandbox-link-to-{$type}";

			// Get site-type-specific switch markup.
			ob_start();
			include WP_USER_GOV_TEMPLATE_PATH . "network-sb-switch-{$type}site.php";
			$switch = ob_get_clean();
			$switch = preg_replace( '/[\s\n]*$/', '', $switch );

			// Get the help panel.
			ob_start();
			include WP_USER_GOV_TEMPLATE_PATH . "network-sb-switch-help.php";
			$help_panel = ob_get_clean();
			$help_panel = preg_replace( '/[\s\n]*$/', '', $help_panel );

			$wp_admin_bar->add_node(
				array(
					'id' => 'wpug_network_sandbox_link',
					'title' => $switch . $help_panel,
					'meta' => array(
						'class' => $switch_class,
					),
				)
			);
		}
	}
}
