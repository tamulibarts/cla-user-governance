<?php
/**
 * The file that defines css and js files loaded for the plugin
 *
 * A class definition that includes css and js files used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.tamu.edu/liberalarts-web/cla-user-governance/blob/master/src/class-assets.php
 * @since      1.0.0
 * @package    cla-user-governance
 * @subpackage cla-user-governance/src
 */

namespace CLA_User_Governance;

/**
 * Add assets
 *
 * @since 1.0.0
 */
class Assets {

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		// Register global styles used in the theme.
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );

		// Enqueue extension styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Registers all styles used within the plugin
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_styles() {

		wp_register_style(
			'cla-user-governance-admin-styles',
			CLA_USER_GOV_DIR_URL . 'css/admin.css',
			false,
			filemtime( CLA_USER_GOV_DIR_PATH . 'css/admin.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues extension styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_styles() {

		wp_enqueue_style( 'cla-user-governance-admin-styles' );

	}

}
