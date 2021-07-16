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
	public function __construct () {

		// Add the admin bar link.
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_link' ), 31 );

		// Add a sandbox class to the body.
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );

		// Hooks for the sandbox network only.
		$option       = get_site_option( $this->option_key );
		$option       = array_merge( $this->default_option, $option );
		$base_url     = $this->get_base_url();
		$sandbox_url  = $option['sandbox_url'];
		$sandbox_show = $option['sandbox_show_link'];
		if ( $sandbox_url === $base_url && 'on' === $sandbox_show ) {
			add_action( 'admin_init', array( 'PAnD', 'init' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice_sandbox_site' ) );
		}

	}

	/**
	 * Get the current page's base URL.
	 *
	 * @return string
	 */
	private function get_base_url() {

		$domain   = $_SERVER['HTTP_HOST'];
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

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
	public function body_class( $classes ){

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
			$classes[] = 'wpug-network-is-sandbox';
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
	public function admin_bar_link ( $wp_admin_bar ) {

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
			$switch_title = $option['live_link_text'];
			$switch_icon  = WP_USER_GOV_DIR_PATH . '/img/' . $option['live_icon'];
		} elseif ( $live_url === $base_url ) {
			if ( 'on' !== $sandbox_show ) {
				return;
			}
			$switch_link  = $sandbox_url . $uri;
			$switch_title = $option['sandbox_link_text'];
			$switch_icon  = WP_USER_GOV_DIR_PATH . '/img/' . $option['sandbox_icon'];
		}

		/**
		 * Adds a node to the menu.
		 *
		 * @since 3.1.0
		 * @since 4.5.0 Added the ability to pass 'lang' and 'dir' meta data.
		 *
		 * @param array $args {
		 *     Arguments for adding a node.
		 *
		 *     @type string $id     ID of the item.
		 *     @type string $title  Title of the node.
		 *     @type string $parent Optional. ID of the parent node.
		 *     @type string $href   Optional. Link for the item.
		 *     @type bool   $group  Optional. Whether or not the node is a group. Default false.
		 *     @type array  $meta   Meta data including the following keys: 'html', 'class', 'rel', 'lang', 'dir',
		 *                          'onclick', 'target', 'title', 'tabindex'. Default empty.
		 * }
		 */
		if ( $switch_link ) {
			$icon = file_get_contents( $switch_icon );
			$switch_title = $icon . $switch_title;
			$wp_admin_bar->add_node( array(
				'id'		=> 'wpug_network_sandbox_link',
				'title' => $switch_title,
				'href'  => $switch_link,
			) );

		}
	}
}
