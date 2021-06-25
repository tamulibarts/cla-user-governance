<?php
/**
 * The file that generates admin menus.
 * Example: https://developer.wordpress.org/plugins/settings/custom-settings-page/
 *
 * @link       https://github.tamu.edu/liberalarts-web/cla-user-governance/blob/master/src/class-menu-page-access.php
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
class Menu_Page_Access {

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		$user_role      = ( is_multisite() ) ? 'superadmin' : 'administrator';
		$page_args      = array(
			'method'    => 'add_submenu_page',
			'title'     => 'User Page Access',
			'slug'      => 'user-page-access',
			'opt_group' => 'user_page_access_option_group',
			'opt_name'  => 'user_page_access_option_name',
		);
		$method_args    = array(
			'parent_slug' => 'users.php',
			'page_title'  => 'Page Access',
			'menu_title'  => 'Page Access',
			'menu_slug'   => 'user-page-access',
			'position'    => 2,
			'capability'  => $user_role,
		);
		$setting_args   = array(
			'opt_group' => 'cla_page_access_group',
			'opt_name'  => 'cla_page_access',
			'args'      => array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_page_access' ),
				'default'           => array(),
			),
		);
		$field_sections = array(
			'cla_page_access_section' => array(
				'title'  => 'My Custom Settings',
				'desc'   => 'Enter your settings below:',
				'fields' => array(
					array(
						'id'    => 'title',
						'title' => 'Title',
						'type'  => 'text',
					),
				),
			),
		);

		add_action( 'admin_menu', array( $this, 'register_menu_item' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );

	}

	public function register_menu_item() {
		$user_role = ( is_multisite() ) ? 'superadmin' : 'administrator';
		add_submenu_page( 'users.php', 'User Page Access', 'Page Access', $user_role, 'user-page-access', array( $this, 'form' ), 2 );
	}

	public function form() {
		?><form>
		<?php
		settings_fields( 'user-page-access' );
		// output setting sections and their fields
		// (sections are registered for "wporg", each field is registered to a specific section)
		do_settings_sections( 'user-page-access' );
		// output save settings button
		submit_button( 'Save Settings' );
		?>
		</form>
		<?php
	}

	public function settings_init() {

		register_setting(
			'user-page-access',
			'user_page_access_option',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_user_page_access_option' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'user_page_access_section',
			'User Page Access',
			array( $this, 'description' ),
			'user-page-access'
		);

		add_settings_field(
			'wporg_field_pill', // As of WP 4.6 this value is used only internally.
			// Use $args' label_for to populate the id inside the callback.
			__( 'Pill', 'user-page-access' ),
			'wporg_field_pill_cb',
			'user-page-access_group',
			'user_page_access_section',
			array(
				'label_for'         => 'wporg_field_pill',
				'class'             => 'wporg_row',
				'wporg_custom_data' => 'custom',
			)
		);
	}

	/**
	 * Filters an option value following sanitization.
	 *
	 * @since 2.3.0
	 * @since 4.3.0 Added the `$original_value` parameter.
	 *
	 * @param string $value          The sanitized option value.
	 * @param string $option         The option name.
	 * @param string $original_value The original value passed to the function.
	 */
	public function sanitize_user_page_access_option( $value, $option, $original_value ) {
		return $value;
	}

	public function description() {
		esc_html_e( 'description' );
	}

}
