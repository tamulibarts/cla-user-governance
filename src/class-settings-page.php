<?php
/**
 * The file that registers an admin dashboard settings page.
 *
 * @link       https://github.com/zachwatkins/cla-user-page-access/blob/master/src/class-settings-page.php
 * @since      1.0.0
 * @package    cla-user-page-access
 * @subpackage cla-user-page-access/src
 */

namespace CLA_User_Page_Access;

/**
 * The post type registration class
 *
 * @since 1.0.0
 * @return void
 */
class Settings_Page {

	/**
	 * Page registration arguments.
	 *
	 * @var page_args
	 */
	private $page_args;

	/**
	 * File name
	 *
	 * @var method_args
	 */
	private $method_args;

	/**
	 * File name
	 *
	 * @var field_sections
	 */
	private $field_sections;

	/**
	 * File name
	 *
	 * @var fields
	 */
	private $fields = array();

	/**
	 * Advanced Custom Fields
	 *
	 * @var acfields
	 */
	private $acfields = array();

	/**
	 * Builds and registers the settings page.
	 *
	 * @since 1.0.1
	 *
	 * @param array $page_args {
	 *     Various registration function parameter values.
	 *
	 *     @type string $method    add_options_page|add_menu_page; Function to register the settings page;
	 *     @type string $title     Settings page title for h1 element.
	 *     @type string $slug      Slug for the settings page.
	 *     @type string $opt_group Option group slug.
	 *     @type string $opt_name  Option name slug.
	 * }
	 * @param array $method_args {
	 *     Required. An array of arguments for the WordPress add_menu_page function.
	 *
	 *     @type string   $page_title The text to be displayed in the title tags of the page
	 *                                when the menu is selected.
	 *     @type string   $menu_title The text to be used for the menu.
	 *     @type string   $capability The capability required for this menu to be displayed to the
	 *                                user.
	 *     @type string   $menu_slug  The slug name to refer to this menu by. Should be unique for
	 *                                this menu page and only include lowercase alphanumeric, dashes,
	 *                                and underscores characters to be compatible with sanitize_key().
	 *     @type string   $icon_url   Optional if $page_args['method'] = 'add_menu_page'; The URL to
	 *                                the icon to be used for this menu.
	 *                                * Pass a base64-encoded SVG using a data URI, which will be
	 *                                  colored to match the color scheme. This should begin with
	 *                                  'data:image/svg+xml;base64,'.
	 *                                * Pass the name of a Dashicons helper class to use a font icon,
	 *                                  e.g. 'dashicons-chart-pie'.
	 *                                * Pass 'none' to leave div.wp-menu-image empty so an icon can be
	 *                                  added via CSS.
	 *     @type int      $position   The position in the menu order this item should appear.
	 * }
	 * @param array $field_sections {
	 *     Required. An array of arguments for registering settings fields.
	 *
	 *     @type string $key The field section slug.
	 *     @type array  $value {
	 *         Required. The field section attributes and fields.
	 *
	 *         @type string $title  The field section title h2 element text.
	 *         @type string $desc   Instructions to users for this section's fields.
	 *         @type array  $fields {
	 *             @type string $id    The field slug.
	 *             @type string $title The field label.
	 *             @type string $type  The field value type.
	 *         }
	 *     }
	 * }
	 *
	 * @return void
	 */
	public function __construct(
		$page_args = array(
			'method'    => 'add_options_page',
			'title'     => 'My Settings',
			'slug'      => 'plugin-name-settings',
			'opt_group' => 'my_option_group',
			'opt_name'  => 'my_option_name',
		),
		$method_args = array(
			'page_title' => 'Plugin Name',
			'menu_title' => 'Plugin Name',
			'capability' => 'manage_options',
			'menu_slug'  => 'plugin-name-settings',
			'icon_url'   => 'dashicons-portfolio',
			'position'   => 0,
		),
		$field_sections = array(
			'setting_section_id' => array(
				'title'  => 'My Custom Settings',
				'desc'   => 'Enter your settings below:',
				'fields' => array(
					array(
						'id'    => 'title',
						'title' => 'Title',
						'type'  => 'text',
					),
				),
				'acf_field_files' => array()
			),
		)
	) {

		// Validate arguments.
		if ( 'add_menu_page' !== $page_args['method'] ) {
			$page_args['method'] = 'add_options_page';
		}

		// Store arguments.
		$this->page_args      = $page_args;
		$this->method_args    = $method_args;
		$this->field_sections = $field_sections;

		foreach ( $field_sections as $section ) {
			$this->fields = array_merge( $this->fields, $section['fields'] );
		}

		// Register hooks.
		add_action( 'admin_menu', array( $this, $page_args['method'] ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

	}

	/**
	 * Add menu page.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function add_menu_page() {

		$args = $this->method_args;

		add_menu_page(
			$args['page_title'],
			$args['menu_title'],
			$args['capability'],
			$args['menu_slug'],
			array( $this, 'settings_page_content' ),
			$args['icon_url'],
			$args['position']
		);

	}

	/**
	 * Add options page.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function add_options_page() {

		$args = $this->method_args;

		add_options_page(
			$args['page_title'],
			$args['menu_title'],
			$args['capability'],
			$args['menu_slug'],
			array( $this, 'settings_page_content' ),
			$args['position']
		);

	}

	/**
	 * Register and add settings
	 *
	 * @since 1.0.1
	 *
	 * @return void;
	 */
	public function page_init() {

		register_setting(
			$this->page_args['opt_group'],
			$this->page_args['opt_name'],
			array( $this, 'sanitize' )
		);

		foreach ( $this->field_sections as $id => $section ) {

			add_settings_section(
				$id,
				$section['title'],
				array( $this, 'section_description' ),
				$this->page_args['slug']
			);

			foreach ( $section['fields'] as $field ) {

				add_settings_field(
					$field['id'],
					$field['title'],
					array( $this, "{$field['type']}_field_callback" ),
					$this->page_args['slug'],
					$id,
					$field
				);

			}
		}

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @since 1.0.1
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize( $input ) {

		$new_input = array();

		foreach ( $this->fields as $field ) {

			$id = $field['id'];

			if ( isset( $input[ $id ] ) ) {

				switch ( $field['type'] ) {
					case 'int':
						$new_input[ $id ] = absint( $input[ $id ] );
						break;
					default:
						$new_input[ $id ] = sanitize_text_field( $input[ $id ] );
						break;
				}
			}
		}

		return $new_input;

	}

	/**
	 * Show the settings section description.
	 *
	 * @since 1.0.1
	 *
	 * @param array $arg The settings section's properties.
	 *
	 * @return void
	 */
	public function section_description( $arg ) {

		echo wp_kses_post( $this->field_sections[ $arg['id'] ]['desc'] );

	}

	/**
	 * Display the int option as a form field.
	 *
	 * @since 1.0.1
	 *
	 * @param array $field The registered field properties.
	 *
	 * @return void
	 */
	public function int_field_callback( $field ) {

		$id           = $field['id'];
		$allowed_html = array(
			'input' => array(
				'type'  => 1,
				'id'    => 1,
				'name'  => 1,
				'value' => 1,
			),
		);
		$output       = sprintf(
			'<input type="text" id="%s" name="%s[%s]" value="%s" />',
			$id,
			$this->page_args['opt_name'],
			$id,
			isset( $this->options[ $id ] ) ? esc_attr( $this->options[ $id ] ) : ''
		);

		echo wp_kses( $output, $allowed_html );

	}

	/**
	 * Display the text option as a form field.
	 *
	 * @since 1.0.1
	 *
	 * @param array $field The registered field identifiers.
	 *
	 * @return void
	 */
	public function text_field_callback( $field ) {

		$id           = $field['id'];
		$allowed_html = array(
			'input' => array(
				'type'  => 1,
				'id'    => 1,
				'name'  => 1,
				'value' => 1,
			),
		);
		$output       = sprintf(
			'<input type="text" id="%s" name="%s[%s]" value="%s" />',
			$id,
			$this->page_args['opt_name'],
			$id,
			isset( $this->options[ $id ] ) ? esc_attr( $this->options[ $id ] ) : ''
		);

		echo wp_kses( $output, $allowed_html );

	}

	/**
	 * Generate the settings page form fields.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function settings_page_content() {

		// Retrieve settings values.
		$this->options = get_option( $this->page_args['opt_name'] );

		?>
	<div class="wrap">
		<h1><?php echo esc_html( $this->page_args['title'] ); ?></h1>
		<form method="post" action="options.php">
		<?php
			// This prints out all hidden setting fields.
			settings_fields( $this->page_args['opt_group'] );
			do_settings_sections( 'plugin-name-settings' );
			submit_button();
		?>
		</form>
	</div>
		<?php

	}

}
