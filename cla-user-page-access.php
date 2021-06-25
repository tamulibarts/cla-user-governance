<?php
/**
 * TAMU CLA User Page Access
 *
 * @package      TAMU CLA User Page Access
 * @author       Zachary Watkins
 * @license      GPL-2.0+
 *
 * @cla-user-page-access
 * Plugin Name:  TAMU CLA User Page Access
 * Plugin URI:   https://github.com/zachwatkins/cla-user-page-access
 * Description:  This plugin adds a settings page which allows a user (defined as a constant in wp-config.php) to restrict other users' access to posts and pages individually. The user can also define other users who can use the settings page as well.
 * Version:      1.0.0
 * Author:       Zachary Watkins
 * Author URI:   https://github.com/ZachWatkins
 * Author Email: watkinza@gmail.com
 * Text Domain:  cla-user-page-access
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/* Define some useful constants */
define( 'CLA_USER_PAGE_ACCESS_DIRNAME', 'cla-user-page-access' );
define( 'CLA_USER_PAGE_ACCESS_TEXTDOMAIN', 'cla-user-page-access' );
define( 'CLA_USER_PAGE_ACCESS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CLA_USER_PAGE_ACCESS_DIR_FILE', __FILE__ );
define( 'CLA_USER_PAGE_ACCESS_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to initialize the plugin.
 */
require CLA_USER_PAGE_ACCESS_DIR_PATH . 'src/class-cla-user-page-access.php';
new CLA_User_Page_Access();
