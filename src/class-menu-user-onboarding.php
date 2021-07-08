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

	private $all_tables        = array();
	private $tables_to_exclude = array( 'posts', 'postmeta' );
	private $sandbox_id;

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		// Get the default and site option values for wpug_user_onboarding_option.
		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option        = $default_site_options['wpug_user_onboarding_option'];
		$wpug_user_onboarding_option = get_site_option( 'wpug_user_onboarding_option' );
		$this->sandbox_id            = $this->default_option['sandbox_id'];
		if ( isset( $wpug_user_onboarding_option['sandbox_id'] ) && $wpug_user_onboarding_option['sandbox_id'] ) {
			$this->sandbox_id = $wpug_user_onboarding_option['sandbox_id'];
		}

		// Set up the $tables_to_exclude variable with the correct prefixes.
		global $table_prefix;
		foreach ( $this->tables_to_exclude as $key => $value ) {
			$this->tables_to_exclude[ $key ] = $table_prefix . $this->sandbox_id . '_' . $value;
		}

		// Store all tables for the sandbox site in a variable to use later in the table select field.
		global $wpdb;
		$command = 'SHOW TABLES';
		if ( $sandbox_id ) {
			$command .= " LIKE 'wp%{$this->sandbox_id}%'";
		}
		$this->all_tables = $wpdb->get_results( $command );

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
	  <h2 id="template_tags">Template Tags</h2>
	  <table id="template_tags_table">
		  <tbody>
			  <tr><td><strong>{{user_name}}</strong></td><td>The user's username</td></tr>
			  <tr><td><strong>{{first_name}}</strong></td><td>The user's first name</td></tr>
			  <tr><td><strong>{{last_name}}</strong></td><td>The user's last name</td></tr>
			  <tr><td><strong>{{user_email}}</strong></td><td>The user's email address</td></tr>
			  <tr><td><strong>{{login_link}}</strong></td><td>A link to the site login page with the URL as the link text</td></tr>
			  <tr><td><strong>{{site_url}}</strong></td><td>The site URL</td></tr>
			  <tr><td><strong>{{site_link}}</strong></td><td>A link to the site with the URL as the link text</td></tr>
			  <tr><td><strong>{{site_title}}</strong></td><td>The title of the site</td></tr>
			  <tr><td><strong>{{network_title}}</strong></td><td>The title of the network</td></tr>
			  <tr><td><strong>{{network_domain}}</strong></td><td>The domain of the network</td></tr>
		  </tbody>
	  </table>
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
			'Subject',
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
			'Message',
			array( $this, 'wp_editor_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'class'       => 'wpug-onboarding-email-message',
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_message',
				'editor_args' => array(
					'textarea_rows' => '20',
				),
			)
		);

		add_settings_field(
			'email_headers',
			'Headers',
			array( $this, 'text_field' ),
			$this->page_slug,
			'wpug_new_user_setting_section',
			array(
				'class'       => 'wpug-onboarding-email-headers',
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'email_headers',
				'after'       => '<span class="wpug-form-field-footnote">Separate each header statement with a semicolon ; character.</span>',
			)
		);

		/**
		 * Register the User Sandbox Site fields.
		 */
		add_settings_section(
			'wpug_sandbox_setting_section',
			'Sandbox Site',
			array( $this, 'print_sandbox_section_info' ),
			$this->page_slug
		);

		add_settings_field(
			'sandbox_id',
			'Site ID',
			array( $this, 'number_field' ),
			$this->page_slug,
			'wpug_sandbox_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'sandbox_id',
			)
		);

		add_settings_field(
			'sandbox_auto_add_user',
			'Add New Users',
			array( $this, 'checkbox_field' ),
			$this->page_slug,
			'wpug_sandbox_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'sandbox_auto_add_user',
			)
		);

		add_settings_field(
			'sandbox_reset_daily',
			'Reset Sandbox Daily',
			array( $this, 'checkbox_field' ),
			$this->page_slug,
			'wpug_sandbox_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'sandbox_reset_daily',
			)
		);

		add_settings_field(
			'sandbox_tables_empty',
			'Tables to Empty',
			array( $this, 'db_table_field' ),
			$this->page_slug,
			'wpug_sandbox_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'sandbox_tables_empty',
			)
		);

		add_settings_field(
			'sandbox_tables_clone',
			'Tables to Clone',
			array( $this, 'db_table_field' ),
			$this->page_slug,
			'wpug_sandbox_setting_section',
			array(
				'option_name' => 'wpug_user_onboarding_option',
				'field_name'  => 'sandbox_tables_clone',
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
		if ( isset( $input['email_headers'] ) ) {
			$output['email_headers'] = wp_check_invalid_utf8( $input['email_headers'], true );
		}
		if ( isset( $input['sandbox_id'] ) ) {
			$output['sandbox_id'] = intval( $input['sandbox_id'] );
		}
		if ( isset( $input['sandbox_auto_add_user'] ) ) {
			$output['sandbox_auto_add_user'] = 'on' === $input['sandbox_auto_add_user'] ? 'on' : 'off';
		}
		if ( isset( $input['sandbox_reset_daily'] ) ) {
			$output['sandbox_reset_daily'] = 'on' === $input['sandbox_reset_daily'] ? 'on' : 'off';
		}
		if ( isset( $input['sandbox_tables_empty'] ) ) {
			$output['sandbox_tables_empty'] = is_array( $input['sandbox_tables_empty'] ) ? $input['sandbox_tables_empty'] : array( $input['sandbox_tables_empty'] );
		}
		if ( isset( $input['sandbox_tables_clone'] ) ) {
			$output['sandbox_tables_clone'] = is_array( $input['sandbox_tables_clone'] ) ? $input['sandbox_tables_clone'] : array( $input['sandbox_tables_clone'] );
		}

		return $output;

	}

	/**
	 * Print the Section text
	 */
	public function print_new_user_section_info() {

		$output = '<p>Consider new user emails an opportunity to introduce your new users to the standard operating procedures they will be using in their day to day work. You may wish to inform them of policy, guidelines, resources, and contact information.</p><p>You can use <a href="#template_tags">template tags</a> in the Subject and Message fields for dynamic content.</p>';
		echo wp_kses_post( $output );

	}

	/**
	 * Print the Section text
	 */
	public function print_sandbox_section_info() {

		$output = '<p>The sandbox site is a place where users can learn how to use WordPress and the theme and plugin features they will have access to on their public site.</p>';
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
			'textarea_name'  => "{$option_name}[{$field_name}]",
			'tinymce'        => array(
				'toolbar1'                     => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,hr,separator,alignleft,aligncenter,alignright,alignjustify,indent,outdent,charmap,link,unlink,undo,redo,fullscreen,wp_help',
				'toolbar2'                     => '',
				'paste_remove_styles'          => true,
				'paste_remove_spans'           => true,
				'paste_strip_class_attributes' => 'all',
				'content_css'                  => '',
			),
			'default_editor' => '',
			'wpautop'        => false,
		);
		if ( isset( $args['editor_args'] ) ) {
			$editor_args = array_merge( $editor_args, $args['editor_args'] );
		}

		$option  = get_site_option( $option_name );
		$content = isset( $option[ $field_name ] ) && $option[ $field_name ] ? $option[ $field_name ] : $default_value;
		$content = stripslashes( $content );

		add_filter(
			'quicktags_settings',
			function( $qtInit ) {
				$qtInit['buttons'] = ',';
				return $qtInit;
			}
		);
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
		if ( isset( $args['after'] ) ) {
			echo $args['after'];
		}

	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function number_field( $args ) {

		$option_name   = $args['option_name'];
		$field_name    = $args['field_name'];
		$default_value = $this->default_option[ $field_name ];
		$option        = get_site_option( $option_name );
		$value         = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		echo "<input type=\"number\" min=\"1\" name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-number\" value=\"{$value}\" data-lpignore=\"true\" />";
		if ( isset( $args['after'] ) ) {
			echo $args['after'];
		}

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

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param array $args The arguments needed to render the setting field.
	 *
	 * @return void
	 */
	public function db_table_field( $args ) {

		global $table_prefix;
		$option_name    = $args['option_name'];
		$field_name     = $args['field_name'];
		$default_value  = $this->default_option[ $field_name ];
		$option         = get_site_option( $option_name );
		$value          = isset( $option[ $field_name ] ) ? $option[ $field_name ] : $default_value;
		$select_options = array();
		foreach ( $this->all_tables as $table ) {
			foreach ( $table as $t ) {
				if ( ! in_array( $t, $this->tables_to_exclude ) ) {
					$selected = '';
					if ( in_array( $t, $value, true ) ) {
						$selected = ' selected';
					}
					$select_options[] = "<option value=\"{$t}\"{$selected}>{$t}</option>";
				}
			}
		}
		$select_options = implode( '', $select_options );
		echo "<select style=\"width:50%;\" multiple size=\"10\" name=\"{$option_name}[{$field_name}][]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-select\">{$select_options}</select>";

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
				( is_multisite() ? network_admin_url( 'users.php' ) : admin_url( 'users.php' ) )
			)
		);
		exit;

	}
}
