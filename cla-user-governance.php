<?php
/**
 * TAMU CLA User Governance
 *
 * @package      TAMU CLA User Governance
 * @author       Zachary Watkins
 * @license      GPL-2.0+
 *
 * @cla-user-governance
 * Plugin Name:  TAMU CLA User Governance
 * Plugin URI:   https://github.tamu.edu/liberalarts-web/cla-user-governance
 * Description:  This plugin adds features the College of Liberal Arts at Texas A&M University uses to manage, educate, and communicate with its users.
 * Version:      1.0.0
 * Author:       Zachary Watkins
 * Author URI:   https://github.com/ZachWatkins
 * Author Email: zwatkins2@tamu.edu
 * Text Domain:  cla-user-governance
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/* Define some useful constants */
define( 'CLA_USER_GOV_DIRNAME', 'cla-user-governance' );
define( 'CLA_USER_GOV_TEXTDOMAIN', 'cla-user-governance' );
define( 'CLA_USER_GOV_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CLA_USER_GOV_DIR_FILE', __FILE__ );
define( 'CLA_USER_GOV_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to initialize the plugin.
 */
require CLA_USER_GOV_DIR_PATH . 'src/class-cla-user-governance.php';
new CLA_User_Governance();

/**
 * The Masquerade third party plugin which was abandoned.
 * We monitor its security with the WPCS Composer module.
 */
require CLA_USER_GOV_DIR_PATH . 'wp-masquerade/masquerade.php';
