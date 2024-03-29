<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.tamu.edu/liberalarts-web/wp-user-governance/blob/master/src/class-user-governance.php
 * @since      1.0.0
 * @package    user-governance
 * @subpackage user-governance/src
 */

/**
 * The core plugin class
 *
 * @since 1.0.0
 * @return void
 */
class User_Governance {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		// Public asset files.
		require_once WP_USER_GOV_DIR_PATH . 'src/class-assets.php';
		new \User_Governance\Assets();

		// User page access feature.
		require_once WP_USER_GOV_DIR_PATH . 'src/class-user-page-access.php';
		new \User_Governance\User_Page_Access();

		// New user email settings.
		require_once WP_USER_GOV_DIR_PATH . 'src/class-menu-user-onboarding.php';
		new \User_Governance\Menu_User_Onboarding();
		require_once WP_USER_GOV_DIR_PATH . 'src/class-user-onboarding.php';
		new \User_Governance\User_Onboarding();

		// Network Sandbox feature.
		require_once WP_USER_GOV_DIR_PATH . 'src/class-menu-network-sandbox.php';
		new \User_Governance\Menu_Network_Sandbox();
		require_once WP_USER_GOV_DIR_PATH . 'src/class-network-sandbox.php';
		new \User_Governance\Network_Sandbox();

		// User Sandbox feature.
		// require_once WP_USER_GOV_DIR_PATH . 'src/class-user-sandbox.php';
		// new \User_Governance\User_Sandbox();

		// New user email settings.
		require_once WP_USER_GOV_DIR_PATH . 'src/class-menu-policy.php';
		new \User_Governance\Menu_Policy();

		// Disallow users from self-registering on multisite.
		add_filter( 'option_users_can_register', '__return_false' );

		// Hide the Advanced Custom Fields menu item for users who cannot edit options.
		add_filter('acf/settings/show_admin', array( $this, 'user_manages_options' ) );

	}

	/**
	 * Return whether or not the current user can manage options.
	 *
	 * @param bool $show Whether or not to show the menu item.
	 *
	 * @return bool
	 */
	public function user_manages_options( $show ) {

		return current_user_can('manage_options');

	}
}
