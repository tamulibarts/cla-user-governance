<?php
namespace User_Governance;

class User_Sandbox {
	/**
	 * Default Option Values
	 *
	 * @var default_option
	 */
	private $default_option = array();

	/**
	 * Default wpdb tables
	 *
	 * @var default_tables
	 */
	private $tables_to_reset = array( 'blc_filters', 'blc_instances', 'blc_links', 'blc_synch', 'commentmeta', 'comments', 'gf_draft_submissions', 'gf_entry', 'gf_entry_meta', 'gf_entry_notes', 'gf_form', 'gf_form_meta', 'gf_form_revisions', 'gf_form_view', 'gf_rest_api_keys', 'links', 'options', 'realmedialibrary', 'realmedialibrary_debug', 'realmedialibrary_posts', 'realmedialibrary_tmp', 'redirection_404', 'redirection_groups', 'redirection_items', 'redirection_logs', 'term_relationships', 'term_taxonomy', 'termmeta', 'terms', 'wpgmza', 'wpgmza_categories', 'wpgmza_category_maps', 'wpgmza_circles', 'wpgmza_datasets', 'wpgmza_maps', 'wpgmza_polygon', 'wpgmza_polylines', 'wpgmza_rectangles' );

	private $post_types_to_preserve = array( 'attachment' );

	public function __construct() {

		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option = $default_site_options['wpug_user_onboarding_option'];

		add_action( 'wpug_sandbox_reset', array( $this, 'sandbox_reset' ) );

		wp_clear_scheduled_hook( 'wpug_sandbox_reset' );
		if ( ! wp_next_scheduled( 'wpug_sandbox_reset' ) ) {
			// wp_schedule_event( strtotime('midnight'), 'daily', array( $this, 'sandbox_reset') );
			wp_schedule_event( time(), 'daily', 'wpug_sandbox_reset' );
		}
	}

	public function sandbox_reset() {

		global $wpdb;

		// Get the sandbox site ID.
		$option          = $this->default_option;
		$sandbox_site_id = $option['sandbox_id'];
		$site_option     = get_site_option( 'wpug_user_onboarding_option' );
		if ( $site_option && isset( $site_option['sandbox_id'] ) && $site_option['sandbox_id'] ) {
			$sandbox_site_id = intval( $site_option['sandbox_id'] );
		}

		$sandbox_reset_daily = $option['sandbox_reset_daily'];
		if ( $site_option && isset( $site_option['sandbox_reset_daily'] ) && $site_option['sandbox_reset_daily'] ) {
			$sandbox_reset_daily = $site_option['sandbox_reset_daily'];
		}

		if ( 'on' === $sandbox_reset_daily ) {

			// Get the sandbox template SQL file.
			$script_path = WP_USER_GOV_DIR_PATH . 'fields/sandbox-template.sql';

			// Empty the tables.
			// foreach ($this->tables_to_reset as $table ) {
			// global $table_prefix;
			// $table_name = $table_prefix . $sandbox_site_id . $table;
			// $wpdb->query( "TRUNCATE TABLE $table_name" );
			// }
		}

	}
}
