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

		// add_action( 'in_admin_header', array( $this, 'add_profile_theme_color_picker' ) );
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

	public function add_profile_theme_color_picker() {
		?><div id="sandbox-ui">
			<div><input type="color" id="base-color" name="base-color" value="#23282d" /> <label for="base-color">Base color</label></div>
			<div><input type="color" id="highlight-color" name="highlight-color" value="#0073aa" /> <label for="highlight-color">Highlight color</label></div>
			<div><input type="color" id="notification-color" name="notification-color" value="#d54e21" /> <label for="notification-color">Notification color</label></div>
			<div><input type="color" id="menu-submenu-text" name="menu-submenu-text" value="#0073aa" /> <label for="menu-submenu-text">Submenu Text color</label></div>
		<script type="text/javascript">
			// Function for lightening or darkening colors according to how they are done in the wp-admin sass file.
			function LightenDarkenColor(col, amt) {

				var usePound = false;

				if (col[0] == "#") {
					col = col.slice(1);
					usePound = true;
				}

				var num = parseInt(col,16);

				var r = (num >> 16) + amt;

				if (r > 255) r = 255;
				else if  (r < 0) r = 0;

				var b = ((num >> 8) & 0x00FF) + amt;

				if (b > 255) b = 255;
				else if  (b < 0) b = 0;

				var g = (num & 0x0000FF) + amt;

				if (g > 255) g = 255;
				else if (g < 0) g = 0;

				return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);

			}

			// https://css-tricks.com/converting-color-spaces-in-javascript/
			function hexToHSL(H) {
			  // Convert hex to RGB first
			  let r = 0, g = 0, b = 0;
			  if (H.length == 4) {
				r = "0x" + H[1] + H[1];
				g = "0x" + H[2] + H[2];
				b = "0x" + H[3] + H[3];
			  } else if (H.length == 7) {
				r = "0x" + H[1] + H[2];
				g = "0x" + H[3] + H[4];
				b = "0x" + H[5] + H[6];
			  }
			  // Then to HSL
			  r /= 255;
			  g /= 255;
			  b /= 255;
			  let cmin = Math.min(r,g,b),
				  cmax = Math.max(r,g,b),
				  delta = cmax - cmin,
				  h = 0,
				  s = 0,
				  l = 0;

			  if (delta == 0)
				h = 0;
			  else if (cmax == r)
				h = ((g - b) / delta) % 6;
			  else if (cmax == g)
				h = (b - r) / delta + 2;
			  else
				h = (r - g) / delta + 4;

			  h = Math.round(h * 60);

			  if (h < 0)
				h += 360;

			  l = (cmax + cmin) / 2;
			  s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
			  s = +(s * 100).toFixed(1);
			  l = +(l * 100).toFixed(1);

			  return {
				  css: "hsl(" + h + "," + s + "%," + l + "%)",
				  hue: h,
				  saturation: s,
				  lightness: l
			  };
			}

			// https://css-tricks.com/converting-color-spaces-in-javascript/
			function HSLToHex(h,s,l) {
			  s /= 100;
			  l /= 100;

			  let c = (1 - Math.abs(2 * l - 1)) * s,
				  x = c * (1 - Math.abs((h / 60) % 2 - 1)),
				  m = l - c/2,
				  r = 0,
				  g = 0,
				  b = 0;

			  if (0 <= h && h < 60) {
				r = c; g = x; b = 0;
			  } else if (60 <= h && h < 120) {
				r = x; g = c; b = 0;
			  } else if (120 <= h && h < 180) {
				r = 0; g = c; b = x;
			  } else if (180 <= h && h < 240) {
				r = 0; g = x; b = c;
			  } else if (240 <= h && h < 300) {
				r = x; g = 0; b = c;
			  } else if (300 <= h && h < 360) {
				r = c; g = 0; b = x;
			  }
			  // Having obtained RGB, convert channels to hex
			  r = Math.round((r + m) * 255).toString(16);
			  g = Math.round((g + m) * 255).toString(16);
			  b = Math.round((b + m) * 255).toString(16);

			  // Prepend 0s, if necessary
			  if (r.length == 1)
				r = "0" + r;
			  if (g.length == 1)
				g = "0" + g;
			  if (b.length == 1)
				b = "0" + b;

			  return "#" + r + g + b;
			}

			var base_color = '#23282d'; // (210°, 12.5, 15.7)
			var base_color_hsl = hexToHSL(base_color);
			var highlight_hsl = {
				hue: parseFloat(base_color_hsl.hue) - 10.6,
				saturation: 100,
				lightness: parseFloat(base_color_hsl.lightness) + 17.6
			};
			var highlight_color = HSLToHex(highlight_hsl.hue, highlight_hsl.saturation, highlight_hsl.lightness);
			var highlight_variations = {
				'--highlight-color': highlight_color,
				'--highlight-color-darken-five': -5,
				'--highlight-color-darken-ten': -10,
				'--highlight-color-darken-twenty': -20,
				'--highlight-color-lighten-ten': 10
			};
			function makeHighlightVariationsString() {
				var color = highlight_variations;
				var value = '';
				for ( var i in highlight_variations ) {
					if ( highlight_variations.hasOwnProperty( i ) ) {
						if ( typeof highlight_variations[i] === 'string' ) {
							value += i + ':' + highlight_variations[i] + ';';
						} else {
							value += i + ':' + LightenDarkenColor( color, highlight_variations[i] ) + ';';
						}
					}
				}
				return value;
			}

			var icon_color = 'hsl(' + base_color_hsl.hue + ', 7%, 95%)';

			// Set up initial values for base variables in base theme.
			document.body.style.cssText = '--base-color:' + base_color + ';--menu-background:var(--base-color);--icon-color:' + icon_color + ';--menu-icon:var(--icon-color);--notification-color:#E1A948;--button-color:#E1A948;--menu-submenu-text:#E2ECF1;--menu-submenu-background:' + LightenDarkenColor( base_color, -7 ) + ';' + makeHighlightVariationsString() + '--menu-submenu-focus-text:var(--highlight-color);';

			// Add event handler for changing inline CSS variables in body style attribute.
			jQuery('#sandbox-ui').on('change', 'input[type="color"]', function(e){

				// Update the color variable.
				var value = e.target.value;
				var prop = '--' + e.target.name;
				jQuery('body').css(prop, value);

				// Update other variables dependent on this variable, if applicable.
				if ( 'base-color' === e.target.name ) {
					var base_color_hsl = hexToHSL(value);
					var highlight_hsl = {
						hue: parseFloat(base_color_hsl.hue) - 10.6,
						saturation: 100,
						lightness: parseFloat(base_color_hsl.lightness) + 17.6
					};
					var highlight_color = HSLToHex(highlight_hsl.hue, highlight_hsl.saturation, highlight_hsl.lightness);
					console.log(highlight_color);
					for ( var i in highlight_variations ) {
						if ( highlight_variations.hasOwnProperty( i ) ) {
							if ( i === '--highlight-color' ) {
								jQuery('body').css( '--highlight-color', highlight_color );
							} else {
								jQuery('body').css( i, LightenDarkenColor( highlight_color, highlight_variations[i] ) );
							}
						}
					}
					jQuery('body').css( '--menu-submenu-background', LightenDarkenColor( value, -7 ) );
					var base_color_hsl = hexToHSL(value);
					jQuery('body').css( '--icon-color', 'hsl(' + base_color_hsl.hue + ', 7%, 95%)' );
				}

			});

		</script></div>
		<?php
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

	}

	/**
	 * Enqueues sandbox assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_sandbox_assets() {

		wp_enqueue_style( 'wp-user-governance-network-sandbox-styles' );

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
			$icon         = file_get_contents( $switch_icon );
			$switch_title = $icon . $switch_title;
			$wp_admin_bar->add_node(
				array(
					'id'    => 'wpug_network_sandbox_link',
					'title' => $switch_title,
					'href'  => $switch_link,
				)
			);

		}
	}
}
