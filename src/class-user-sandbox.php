<?php
/**
 * The User Sandbox class.
 * The purpose of the sandbox is to allow users hands-on experience with the WordPress content management system.
 * This class helps facilitate that experience by resetting the sandbox from time to time.
 */
namespace User_Governance;

class User_Sandbox {

	private $default_content = array(
		'comments' => "(1, 'A WordPress Commenter', 'wapuu@wordpress.example', 'https://wordpress.org/', '', '2020-06-26 16:58:20', '2020-06-26 16:58:20', 'Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com\">Gravatar</a>.', 0, '1', '', 'comment', 0, 0)",
	);

	private $post_types_to_preserve = array( 'attachment' );
	private $options_to_preserve    = array();
	private $onboarding_option      = array();
	private $sandbox_site_id;

	public function __construct() {

		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option    = $default_site_options['wpug_user_onboarding_option'];
		$this->onboarding_option = get_site_option( 'wpug_user_onboarding_option' );
		$this->sandbox_site_id   = $this->default_option['sandbox_id'];
		if ( $this->onboarding_option && isset( $this->onboarding_option['sandbox_id'] ) && $this->onboarding_option['sandbox_id'] ) {
			$this->sandbox_site_id = intval( $this->onboarding_option['sandbox_id'] );
		}

		// Schedule cron task for resetting the sandbox.
		add_action( 'wpug_sandbox_reset', array( $this, 'sandbox_reset' ) );
		wp_clear_scheduled_hook( 'wpug_sandbox_reset' );
		if ( ! wp_next_scheduled( 'wpug_sandbox_reset' ) ) {
			// wp_schedule_event( strtotime('midnight'), 'daily', array( $this, 'sandbox_reset') );
			wp_schedule_event( time(), 'daily', 'wpug_sandbox_reset' );
		}

	}

	/**
	 */
	public function sandbox_reset() {

		global $wpdb;
		global $table_prefix;

		$subsite_prefix = $table_prefix . $this->sandbox_site_id . '_';
		$sandbox_prefix = $table_prefix . $this->sandbox_site_id . '_wpug_';

		$sandbox_reset_daily = $this->default_option['sandbox_reset_daily'];
		if ( $this->onboarding_option && isset( $this->onboarding_option['sandbox_reset_daily'] ) && $this->onboarding_option['sandbox_reset_daily'] ) {
			$sandbox_reset_daily = $this->onboarding_option['sandbox_reset_daily'];
		}

		$sandbox_tables_to_empty = $this->default_option['sandbox_tables_empty'];
		if ( $this->onboarding_option && isset( $this->onboarding_option['sandbox_tables_empty'] ) && $this->onboarding_option['sandbox_tables_empty'] ) {
			$sandbox_tables_to_empty = $this->onboarding_option['sandbox_tables_empty'];
		}

		$sandbox_tables_to_clone = $this->default_option['sandbox_tables_clone'];
		if ( $this->onboarding_option && isset( $this->onboarding_option['sandbox_tables_clone'] ) && $this->onboarding_option['sandbox_tables_clone'] ) {
			$sandbox_tables_to_clone = $this->onboarding_option['sandbox_tables_clone'];
		}

		if ( 'on' === $sandbox_reset_daily ) {

			// Empty tables.
			foreach ( $sandbox_tables_to_empty as $table ) {
				$find_table   = "SHOW TABLES LIKE '$table'";
				$table_exists = $wpdb->query( $find_table );
				if ( $table_exists ) {
					$wpdb->query( "TRUNCATE TABLE $table" );
				}
			}

			// Clone tables from backup.
			foreach ( $sandbox_tables_to_clone as $table ) {
				$sandbox_table = str_replace( $subsite_prefix, $sandbox_prefix, $table );
				$find_table    = "SHOW TABLES LIKE '$sandbox_table'";
				$table_exists  = $wpdb->query( $find_table );
				if ( $table_exists ) {
					$command = "INSERT INTO {$table} SELECT * FROM {$sandbox_table}";
					error_log( $command );
					// $wpdb->query( $command );
				}
			}

			// Delete all posts and post meta except posts by Marcy or myself and attachments.
			// How the heck do we restore these posts to the previous state and delete all of the others?
		}
	}

	public function sandbox_set() {
		global $wpdb;
		global $table_prefix;
	}
}
