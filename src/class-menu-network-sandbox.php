<?php
/**
 * The file that handles the network sandbox settings.
 *
 * @link       https://github.com/zachwatkins/wp-user-governance/blob/master/src/class-menu-network-sandbox.php
 * @since      1.0.0
 * @package    wp-user-governance
 * @subpackage wp-user-governance/src
 */

namespace User_Governance;

/**
 * The network sandbox settings page class.
 *
 * @since 1.0.0
 * @return void
 */
class Menu_Network_Sandbox {

	/**
	 * Default Option Values
	 *
	 * @var default_option
	 */
	private $default_option = array(
		'enable'            => 'off',
		'sandbox_link_text' => 'Switch To The Sandbox Site',
		'sandbox_url'       => 'https://sandbox.example.com/',
		'live_link_text'    => 'Switch To The Live Site',
		'live_url'          => 'https://example.com/',
	);

	private $submenu_parent_slug = 'settings.php';

	/**
	 * Page Slug
	 *
	 * @var page_slug
	 */
	private $page_slug = 'wpug-network-sandbox';

	/**
	 * Settings Group Slug
	 *
	 * @var settings_group_slug
	 */
	private $settings_group_slug = 'wpug_network_sandbox';

	/**
	 * Option Key
	 *
	 * @var option_key
	 */
	private $option_key = 'wpug_network_sandbox_option';

	public function __construct(){

		if ( is_admin() ) {

			require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
			$this->default_option = $default_site_options[ $this->option_key ];

		}

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		if ( is_multisite() ) {

			// Advanced Custom Fields cannot add a network-level admin menu.
			add_action( 'network_admin_menu', array( $this, 'add_menu' ) );
			add_action( 'network_admin_edit_' . $this->settings_group_slug, array( $this, 'save_site_option' ) );

		} else {

			// Todo: Confirm single-site support.
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_edit_' . $this->settings_group_slug, array( $this, 'save_site_option' ) );

		}

	}

	public function add_menu() {

		$permission = is_multisite() ? 'manage_network_options' : 'manage_options';
	  add_submenu_page(
       $this->submenu_parent_slug,
       'Sandbox Settings',
       'Sandbox',
       $permission,
       $this->page_slug,
       array( $this, 'create_admin_page' )
	  );
	}

	/**
	 * Options page callback
	 *
	 * @return void
	 */
	public function create_admin_page() {

		?>
	<div class="wrap">
	  <h1>Network Sandbox Settings</h1>
	  <form method="post" action="edit.php?action=<?php echo $this->settings_group_slug; ?>">
		<?php
		// This prints out all hidden setting fields.
		settings_fields( $this->settings_group_slug );
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
			$this->settings_group_slug,
			$this->option_key,
			array( $this, 'sanitize_option' )
		);

		add_settings_section(
			$this->settings_group_slug . '_setting_section',
			'',
			array( $this, 'print_section_info' ),
			$this->page_slug
		);

		add_settings_field(
			'enable',
			'Enable Admin Bar Link',
			array( $this, 'checkbox_field' ),
			$this->page_slug,
			$this->settings_group_slug . '_setting_section',
			array(
				'option_name' => $this->option_key,
				'field_name'  => 'enable',
			)
		);

		add_settings_field(
			'sandbox_link_text',
			'Sandbox Link Text',
			array( $this, 'text_field' ),
			$this->page_slug,
			$this->settings_group_slug . '_setting_section',
			array(
				'option_name' => $this->option_key,
				'field_name'  => 'sandbox_link_text',
			)
		);

		add_settings_field(
			'sandbox_url',
			'Sandbox URL',
			array( $this, 'text_field' ),
			$this->page_slug,
			$this->settings_group_slug . '_setting_section',
			array(
				'option_name' => $this->option_key,
				'field_name'  => 'sandbox_url',
			)
		);

		add_settings_field(
			'live_link_text',
			'Live Link Text',
			array( $this, 'text_field' ),
			$this->page_slug,
			$this->settings_group_slug . '_setting_section',
			array(
				'option_name' => $this->option_key,
				'field_name'  => 'live_link_text',
			)
		);

		add_settings_field(
			'live_url',
			'Live URL',
			array( $this, 'text_field' ),
			$this->page_slug,
			$this->settings_group_slug . '_setting_section',
			array(
				'option_name' => $this->option_key,
				'field_name'  => 'live_url',
			)
		);

	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {

		print 'The network sandbox is a place where features, fixes, and changes to the network and its sites can be tested before introducing them into the production environment.';

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
		echo "<input type=\"text\" name=\"{$option_name}[{$field_name}]\" id=\"{$option_name}[{$field_name}]\" class=\"settings-text\" value=\"{$value}\" data-lpignore=\"true\" size=\"40\" />";
		if ( isset( $args['after'] ) ) {
			echo $args['after'];
		}

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize_option( $input ) {

		$output = array();

		if ( isset( $input['enable'] ) ) {
			$output['enable'] = 'on' === $input['enable'] ? 'on' : 'off';
		}
		if ( isset( $input['sandbox_link_text'] ) ) {
			$output['sandbox_link_text'] = sanitize_text_field( $input['sandbox_link_text'] );
		}
		if ( isset( $input['sandbox_url'] ) ) {
			$output['sandbox_url'] = sanitize_text_field( $input['sandbox_url'] );
		}
		if ( isset( $input['live_link_text'] ) ) {
			$output['live_link_text'] = sanitize_text_field( $input['live_link_text'] );
		}
		if ( isset( $input['live_url'] ) ) {
			$output['live_url'] = sanitize_text_field( $input['live_url'] );
		}

		return $output;

	}

	public function save_site_option() {

		// Verify nonce.
		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		// Save the option.
		$option = $_POST[ $this->option_key ];
		$option = $this->sanitize_option( $option );
		update_site_option( $this->option_key, $option );

		// Redirect to settings page.
		wp_redirect(
			add_query_arg(
				array(
					'page'    => $this->page_slug,
					'updated' => 'true',
				),
				( is_multisite() ? network_admin_url( $this->submenu_parent_slug ) : admin_url( $this->submenu_parent_slug ) )
			)
		);
		exit;

	}
}
