<?php
/**
 * The file that handles new user emails and notices.
 *
 * @link       https://github.com/zachwatkins/wp-user-governance/blob/master/src/class-menu-user-onboarding.php
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
class Menu_User_Onboarding {

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
	private $page_slug = 'new-user-onboarding';

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

		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option = $default_site_options['wpug_user_onboarding_option'];

		// Admin menus.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		if ( is_multisite() ) {

			// Advanced Custom Fields cannot add a network-level admin menu.
			add_action( 'network_admin_menu', array( $this, 'add_menu' ) );
			add_action( 'network_admin_edit_wpug_user_onboarding_options', array( $this, 'save_site_option' ) );

		} else {

			// Todo: Confirm single-site support.
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_edit_wpug_user_onboarding_options', array( $this, 'save_site_option' ) );

		}
	}

	/**
	 * Add the Policy and Guidelines menu item.
	 *
	 * @return void
	 */
	public function add_menu() {

		$role = is_multisite() ? 'superadmin' : 'administrator';
		add_submenu_page( 'users.php', 'User Onboarding', 'Onboarding', $role, $this->page_slug, array( $this, 'create_admin_page' ), 3 );

	}

	/**
	 * Options page callback
	 *
	 * @return void
	 */
	public function create_admin_page() {

		?>
	<div class="wrap">
	  <h1>New User Onboarding</h1>
	  <form id="wpug_user_onboarding_form" method="post" action="edit.php?action=wpug_user_onboarding_options">
		<?php
		// This prints out all hidden setting fields.
		settings_fields( 'wpug_user_onboarding' );
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
		 * Register the User Onboarding settings.
		 */
		register_setting(
			'wpug_user_onboarding',
			'wpug_user_onboarding_option',
			array( $this, 'sanitize_option' )
		);

		// New User Email Options.
		add_settings_section(
			'wpug_new_user_setting_section',
			'New User Email',
			array( $this, 'print_new_user_section_info' ),
			$this->page_slug
		);

		add_settings_field(
			'email_override',
			'Override Default Email',
			array( $this, 'checkbox_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_override',
			)
		);

		add_settings_field(
			'email_subject',
			'Email Subject',
			array( $this, 'text_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'class'       => 'wpug-onboarding-email-subject',
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_subject',
			)
		);

		add_settings_field(
			'email_message',
			'Email Message',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'class'       => 'wpug-onboarding-email-message',
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_message',
				'editor_args' => array(
					'textarea_rows' => '30',
				),
			)
		);

		add_settings_field(
			'email_headers',
			'Email Headers',
			array( $this, 'text_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'class'       => 'wpug-onboarding-email-headers',
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_headers',
			)
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize_option( $input ) {

		$output = array();

		if ( isset( $input['email_override'] ) ) {
			$output['email_override'] = 'on' === $input['email_override'] ? 'on' : 'off';
		}
		if ( isset( $input['email_subject'] ) ) {
			$output['email_subject'] = sanitize_text_field( $input['email_subject'] );
		}
		if ( isset( $input['email_message'] ) ) {
			$output['email_message'] = wp_kses_post( $input['email_message'] );
		}

		return $output;

	}

	/**
	 * Print the Section text
	 */
	public function print_new_user_section_info() {

		$output = '<p>You may wish to inform new users of policy, guidelines, resources, and contact information.</p><p>You can use template tags in the Subject and Message fields for dynamic content</p><table><tbody><tr><td><strong>{{login_name}}</strong></td></tr><tr><td><strong>{{first_name}}</strong></td></tr><tr><td><strong>{{last_name}}</strong></td></tr><tr><td><strong>{{user_email}}</strong></td></tr><tr><td><strong>{{login_link}}</strong></td></tr><tr><td><strong>{{site_url}}</strong></td></tr><tr><td><strong>{{site_link}}</strong></td></tr><tr><td><strong>{{site_title}}</strong></td></tr><tr><td><strong>{{network_title}}</strong></td></tr><tr><td><strong>{{network_domain}}</strong></td></tr></tbody></table>';
		echo wp_kses_post( $output );

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
				'content_css' => '',
			),
		);
		if ( isset( $args['editor_args'] ) ) {
			$editor_args = array_merge( $editor_args, $args['editor_args'] );
		}

		$option  = get_site_option( $option_name );
		$content = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		$content = stripslashes( $content );

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

		$option_name   = $args['option_name'];
		$field_name    = $args['field_name'];
		$default_value = $this->default_option[ $field_name ];
		$option        = get_site_option( $option_name );
		$is_checked    = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		$checked       = 'on' === $is_checked ? ' checked' : '';
		echo "<input type=\"checkbox\" name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-checkbox\"{$checked} />";

	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function text_field( $args ) {

		$option_name   = $args['option_name'];
		$field_name    = $args['field_name'];
		$default_value = $this->default_option[ $field_name ];
		$option        = get_site_option( $option_name );
		$value         = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		echo "<input type=\"text\" name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-text\" value=\"{$value}\" data-lpignore=\"true\" />";

	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function textarea_field( $args ) {

		$option_name   = $args['option_name'];
		$field_name    = $args['field_name'];
		$default_value = $this->default_option[ $field_name ];
		$option        = get_site_option( $option_name );
		$value         = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		echo "<textarea name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-textarea\" rows=\"5\">{$value}</textarea>";

	}

	public function save_site_option() {

		// Verify nonce.
		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		// Save the new user option.
		$option = $_POST['wpug_user_onboarding_option'];
		$option = $this->sanitize_option( $option );
		update_site_option( 'wpug_user_onboarding_option', $option );

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
}
