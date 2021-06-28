<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.tamu.edu/liberalarts-web/cla-user-governance/blob/master/src/class-cla-user-governance.php
 * @since      1.0.0
 * @package    cla-user-governance
 * @subpackage cla-user-governance/src
 */

/**
 * The core plugin class
 *
 * @since 1.0.0
 * @return void
 */
class CLA_User_Governance {

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
		require_once CLA_USER_GOV_DIR_PATH . 'src/class-assets.php';
		new \CLA_User_Governance\Assets();

		// User page access feature.
		require_once CLA_USER_GOV_DIR_PATH . 'src/class-user-page-access.php';
		new \CLA_User_Governance\User_Page_Access();

	}
}
