<?php
/**
 * The file that handles the required user notices settings.
 * Todo:
 *   Accessibility Policy
 *   Texas A&M University Brand Guidelines
 *   Writing for the Web Guidelines
 *
 * @link       https://github.com/zachwatkins/wp-user-governance/blob/master/src/class-required-user-notices.php
 * @since      1.0.0
 * @package    wp-user-governance
 * @subpackage wp-user-governance/src
 */

namespace User_Governance;

/**
 * The new user email settings page class.
 *
 * @since 1.0.0
 * @return void
 */
class Menu_Policy {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * New User Option
	 *
	 * @var new_user_option
	 */
	private $new_user_option;

	/**
	 * Page Slug
	 *
	 * @var page_slug
	 */
	private $page_slug = 'wpug-site-policy';

	/**
	 * Default Option Values
	 *
	 * @var default_option
	 */
	private $default_option = array();

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		if ( is_admin() ) {
		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option = $default_site_options['wpug_policy_option'];

			// Admin menus.
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			if ( is_multisite() ) {

				// Advanced Custom Fields cannot add a network-level admin menu.
				add_action( 'network_admin_menu', array( $this, 'add_menu' ) );
				add_action( 'network_admin_edit_wpug_site_policy', array( $this, 'save_site_option' ) );

			} else {

				// Todo: Confirm single-site support.
				add_action( 'admin_menu', array( $this, 'add_menu' ) );
				add_action( 'admin_edit_wpug_site_policy', array( $this, 'save_site_option' ) );

			}
		}
	}

	/**
	 * Add the Policy and Guidelines menu item.
	 *
	 * @return void
	 */
	public function add_menu() {

		$role = is_multisite() ? 'superadmin' : 'administrator';
		add_submenu_page( 'users.php', 'Web Governance Policy', 'Manage Policy', $role, $this->page_slug, array( $this, 'create_admin_page' ), 3 );

	}

	/**
	 * Options page callback
	 *
	 * @return void
	 */
	public function create_admin_page() {

		?>
	<div class="wrap">
	  <h1>Web Governance Policy</h1>
	  <form method="post" action="edit.php?action=wpug_site_policy">
		<?php
		// This prints out all hidden setting fields.
		settings_fields( 'wpug_site_policy' );
		do_settings_sections( $this->page_slug );
		submit_button();
		?>
	  </form>
	</div>
		<?php

	}

	/**
	 * Initialize the admin settings.
	 *
	 * @return void
	 */
	public function register_settings() {

		/**
		 * Register the Policy fields.
		 */
		register_setting(
			'wpug_site_policy',
			'wpug_policy_option',
			array( $this, 'sanitize_policy_option' )
		);

		add_settings_section(
			'wpug_policy_setting_section',
			'',
			array( $this, 'print_policy_section_info' ),
			$this->page_slug
		);

		add_settings_field(
			'user_body',
			'Users',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_policy_setting_section',
			array(
				'class'       => 'wpug-policy-user-body',
				'option_name' => 'wpug_policy_option',
				'field_name'  => 'user_body',
				'editor_args' => array(
					'textarea_rows' => '10',
				),
			)
		);

		add_settings_field(
			'visitor_body',
			'Visitors',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_policy_setting_section',
			array(
				'class'       => 'wpug-policy-visitor-body',
				'option_name' => 'wpug_policy_option',
				'field_name'  => 'visitor_body',
				'editor_args' => array(
					'textarea_rows' => '10',
				),
			)
		);

		add_settings_field(
			'copyright_body',
			'Copyright',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_policy_setting_section',
			array(
				'class'       => 'wpug-policy-copyright-body',
				'option_name' => 'wpug_policy_option',
				'field_name'  => 'copyright_body',
				'editor_args' => array(
					'textarea_rows' => '5',
				),
			)
		);

		add_settings_field(
			'pii_body',
			'Personally Identifying Information',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_policy_setting_section',
			array(
				'class'       => 'wpug-policy-pii-body',
				'option_name' => 'wpug_policy_option',
				'field_name'  => 'pii_body',
				'editor_args' => array(
					'textarea_rows' => '10',
				),
			)
		);

	}

	/**
	 * Print the Section text
	 */
	public function print_policy_section_info() {

		print 'There are different places throughout the network where we may find it necessary to inform users or visitors of certain policies. Define those policy statements here.';

	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function wp_editor_field( $args ) {

		$option_name   = $args['option_name'];
		$field_name    = $args['field_name'];
		$default_value = $this->default_option[ $field_name ];
		$editor_args   = array(
			'textarea_name' => "{$option_name}[{$field_name}]",
			'tinymce'       => array(
				'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,hr,separator,alignleft,aligncenter,alignright,alignjustify,indent,outdent,charmap,link,unlink,undo,redo,fullscreen,wp_help',
				'toolbar2' => '',
				'paste_remove_styles' => true,
				'paste_remove_spans' => true,
				'paste_strip_class_attributes' => 'all',
				'content_css' => '',
			),
			'default_editor' => '',
		);
		if ( isset( $args['editor_args'] ) ) {
			$editor_args = array_merge( $editor_args, $args['editor_args'] );
		}

		$option  = get_site_option( $option_name );
		$content = isset( $option[ $field_name ] ) && $option[ $field_name ] ? $option[ $field_name ] : $default_value;
		$content = stripslashes( $content );

		add_filter( 'quicktags_settings', function( $qtInit ){ $qtInit['buttons'] = ','; return $qtInit; });
		wp_editor( $content, $field_name, $editor_args );

	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function checkbox_field( $args ) {

		$option_name = $args['option_name'];
		$field_name  = $args['field_name'];
		$option      = get_site_option( $option_name );
		$is_checked  = isset( $option[ $field_name ] ) ? $option[ $field_name ] : 'off';
		$checked     = 'on' === $is_checked ? ' checked' : '';
		echo "<input type=\"checkbox\" name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\"{$checked} />";

	}

	public function save_site_option() {

		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		$policy_option = $_POST['wpug_policy_option'];
		$policy_option = $this->sanitize_policy_option( $policy_option );
		update_site_option( 'wpug_policy_option', $policy_option );

		wp_redirect(
			add_query_arg(
				array(
					'page'    => $this->page_slug,
					'updated' => 'true',
				),
				( is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) )
			)
		);
		exit;

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize_policy_option( $input ) {

		$output = array();

		if ( isset( $input['pii_body'] ) ) {
			$output['pii_body'] = wp_kses_post( $input['pii_body'] );
		}
		if ( isset( $input['user_body'] ) ) {
			$output['user_body'] = wp_kses_post( $input['user_body'] );
		}
		if ( isset( $input['visitor_body'] ) ) {
			$output['visitor_body'] = wp_kses_post( $input['visitor_body'] );
		}
		if ( isset( $input['copyright_body'] ) ) {
			$output['copyright_body'] = wp_kses_post( $input['copyright_body'] );
		}

		return $output;

	}
}
