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
		'enable'            => 'off',
		'sandbox_link_text' => 'Switch To The Sandbox Site',
		'sandbox_url'       => 'https://sandbox.example.com/',
		'live_link_text'    => 'Switch To The Live Site',
		'live_url'          => 'https://example.com/',
	);

	public function __construct () {

		add_action('admin_bar_menu', array( $this, 'admin_bar_link' ), 31);
	}

	public function admin_bar_link ( $wp_admin_bar ) {

		$option         = get_site_option( $this->option_key );
		$option         = array_merge( $this->default_option, $option );
		$sandbox_url    = preg_replace( '/\/$/', '', $option['sandbox_url'] );
		$live_url       = preg_replace( '/\/$/', '', $option['live_url'] );
		$domain         = $_SERVER['HTTP_HOST'];
		$uri            = $_SERVER['REQUEST_URI'];
		$protocol       = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$current_root   = $protocol . $domain;
		$switch_link    = '';
		$switch_title   = '';

		if ( $sandbox_url === $current_root ) {
			$switch_link  = $live_url . $uri;
			$switch_title = $option['live_link_text'];
		} elseif ( $live_url === $current_root ) {
			$switch_link  = $sandbox_url . $uri;
			$switch_title = $option['sandbox_link_text'];
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
			$icon_path = WP_USER_GOV_DIR_PATH . '/img/sandbox-icon.svg';
			$icon = file_get_contents($icon_path);
			$switch_title = $icon . $switch_title;
			$wp_admin_bar->add_node( array(
				'id'		=> 'wpug_network_sandbox_link',
				'title' => $switch_title,
				'href'  => $switch_link,
			) );

		}
	}
}
