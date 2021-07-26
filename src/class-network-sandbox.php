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
			$switch_title = '<div class="switch-a">
		<fieldset aria-label="switch between the live website and the sandbox website" role="radiogroup">
			<!-- 	<legend><h1>Live and Sandbox Website Switching Toggle</h1></legend> -->
			<div class="c-toggle">
				<label for="wpug_env_live3" title="Click to go back to the Live Site">Go Back to the Live Site</label><span class="c-toggle__wrapper"><input type="radio" name="environment3" id="wpug_env_live3"><input type="radio" name="environment3" id="wpug_env_sandbox3" checked><span aria-hidden="true" class="c-toggle__background"></span><span aria-hidden="true" class="c-toggle__switcher"></span><span class="c-toggle__tooltip">
						<div>What is a Sandbox Site?</div>
						<div>It\'s a <span class="big">classroom</span> version of the website where</div>
						<div>you can learn and experiment. Don\'t worry, we</div>
						<div>reset it weekly. <a class="text" href="#moreinfo">Click for more information</a></div>
						<div style="text-align:right;"><a class="text gotit" href="#gotit">Got it! <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
									<path fill="#FFF" d="M12.72 2c0.15-0.020 0.26 0.020 0.41 0.070 0.56 0.19 0.83 0.79 0.66 1.35-0.17 0.55-1 3.040-1 3.58 0 0.53 0.75 1 1.35 1h3c0.6 0 1 0.4 1 1s-2 7-2 7c-0.17 0.39-0.55 1-1 1h-9.14v-9h2.14c0.41-0.41 3.3-4.71 3.58-5.27 0.21-0.41 0.6-0.68 1-0.73zM2 8h2v9h-2v-9z"></path>
								</svg></a>
						</div>
					</span></span><label for="wpug_env_sandbox3" title="">Sandbox Site</label>
			</div>
		</fieldset>
	</div>';
		} elseif ( $live_url === $base_url ) {
			if ( 'on' !== $sandbox_show ) {
				return;
			}
			$switch_link  = $sandbox_url . $uri;
			$switch_class = 'wpug-network-sandbox-link-to-sandbox';
			$switch_title = '<div class="switch-a">
		<fieldset aria-label="switch between the live website and the sandbox website" role="radiogroup">
			<!-- 	<legend><h1>Live and Sandbox Website Switching Toggle</h1></legend> -->
			<div class="c-toggle">
				<label for="wpug_env_live3" title="">Live Site</label><span class="c-toggle__wrapper"><input type="radio" name="environment3" id="wpug_env_live3" checked><input type="radio" name="environment3" id="wpug_env_sandbox3"><span aria-hidden="true" class="c-toggle__background"></span><span aria-hidden="true" class="c-toggle__switcher"></span><span class="c-toggle__tooltip">
						<div>What is a Sandbox Site?</div>
						<div>It\'s a <span class="big">classroom</span> version of the website where</div>
						<div>you can learn and experiment. Don\'t worry, we</div>
						<div>reset it weekly. <a class="text" href="#moreinfo">Click for more information</a></div>
						<div style="text-align:right;"><a class="text gotit" href="#gotit">Got it! <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
									<path fill="#FFF" d="M12.72 2c0.15-0.020 0.26 0.020 0.41 0.070 0.56 0.19 0.83 0.79 0.66 1.35-0.17 0.55-1 3.040-1 3.58 0 0.53 0.75 1 1.35 1h3c0.6 0 1 0.4 1 1s-2 7-2 7c-0.17 0.39-0.55 1-1 1h-9.14v-9h2.14c0.41-0.41 3.3-4.71 3.58-5.27 0.21-0.41 0.6-0.68 1-0.73zM2 8h2v9h-2v-9z"></path>
								</svg></a>
						</div>
					</span></span><label for="wpug_env_sandbox3" title="Click to go to the Sandbox Site">Go to Sandbox Site</label>
			</div>
		</fieldset>
	</div>';
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
