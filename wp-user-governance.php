<?php
/**
 * WP User Governance
 *
 * @package      WP User Governance
 * @author       Zachary Watkins
 * @license      GPL-2.0+
 *
 * @wp-user-governance
 * Plugin Name:  WP User Governance
 * Plugin URI:   https://github.tamu.edu/liberalarts-web/wp-user-governance
 * Description:  This plugin adds features the College of Liberal Arts at Texas A&M University uses to manage, educate, and communicate with its users.
 * Version:      1.0.0
 * Author:       Zachary Watkins
 * Author URI:   https://github.com/ZachWatkins
 * Author Email: zwatkins2@tamu.edu
 * Text Domain:  wp-user-governance
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/* Define some useful constants */
define( 'WP_USER_GOV_DIRNAME', 'wp-user-governance' );
define( 'WP_USER_GOV_TEXTDOMAIN', 'wp-user-governance' );
define( 'WP_USER_GOV_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_USER_GOV_DIR_FILE', __FILE__ );
define( 'WP_USER_GOV_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to initialize the plugin.
 */
require WP_USER_GOV_DIR_PATH . 'src/class-user-governance.php';
new User_Governance();

/**
 * The Masquerade third party plugin which was abandoned.
 * We monitor its security with the WPCS Composer module.
 */
require WP_USER_GOV_DIR_PATH . 'cla-wp-masquerade/class-wpmasquerade.php';

register_activation_hook( WP_USER_GOV_DIR_FILE, 'wordpress_plugin_activation' );
function wordpress_plugin_activation() {

	// Check for missing dependencies.
	$plugin = is_plugin_active( 'advanced-custom-fields-pro/acf.php' );

	if ( true !== $plugin ) {

		$error = sprintf(
		/* translators: %s: URL for plugins dashboard page */
			__(
				'Plugin NOT activated: The <strong>User Governance</strong> plugin needs the <strong>Advanced Custom Fields Pro</strong> plugin to be installed and activated first. <a href="%s">Back to plugins page</a>',
				'wp-user-governance'
			),
			get_admin_url( null, '/plugins.php' )
		);
		wp_die( wp_kses_post( $error ) );

	} else {

		update_option( 'wordpress_plugin_permalinks_flushed', 0 );

		// Add default options.
		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';

		// Add default policy options.
		$current_policy_option = get_site_option( 'wpug_policy_option' );
		if ( ! is_array( $current_policy_option ) ) {
			update_site_option( 'wpug_policy_option', $default_site_options );
		} else {
			$changed = false;
			foreach ( $default_site_options as $key => $default_option ) {
				if ( ! isset( $current_policy_option[ $key ] ) || ! $current_policy_option[ $key ] ) {
					$current_policy_option[ $key ] = $default_option;
					$changed                       = true;
				}
			}
			if ( $changed ) {
				update_site_option( 'wpug_policy_option', $current_policy_option );
			}
		}
	}

}
