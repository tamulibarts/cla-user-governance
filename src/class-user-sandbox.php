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
	private $sandbox_table_prefix;
	private $sandbox_site_tables = array();

	public function __construct() {

		require WP_USER_GOV_DIR_PATH . 'fields/options-default.php';
		$this->default_option    = $default_site_options['wpug_user_onboarding_option'];
		$this->onboarding_option = get_site_option( 'wpug_user_onboarding_option' );
		$this->sandbox_site_id   = $this->default_option['sandbox_id'];
		if ( $this->onboarding_option && isset( $this->onboarding_option['sandbox_id'] ) && $this->onboarding_option['sandbox_id'] ) {
			$this->sandbox_site_id = intval( $this->onboarding_option['sandbox_id'] );
		}

		// Store all tables for the sandbox site in a variable to use later in the table select field.
		global $wpdb;
		global $table_prefix;
		$this->sandbox_table_prefix = $table_prefix . $this->sandbox_site_id . '_';
		$this->backup_file_name     = $this->sandbox_table_prefix . 'sandbox_backup.sql';
		$command                    = "SHOW TABLES LIKE 'wp%{$this->sandbox_site_id}%'";
		$table_classes              = $wpdb->get_results( $command );
		foreach ( $table_classes as $table ) {
			foreach ( $table as $t ) {
				$this->sandbox_site_tables[] = $t;
			}
		}

		add_action( 'network_admin_edit_wpug_save_sandbox_state', array( $this, 'sandbox_set' ) );
		add_action( 'network_admin_edit_wpug_create_sandbox_site', array( $this, 'create_sandbox_clone' ) );
		add_action( 'network_admin_edit_wpug_delete_sandbox_site', array( $this, 'delete_sandbox_clone' ) );
		add_action( 'sandbox_new', array( $this, 'sandbox_new' ) );

		// Schedule cron task for resetting the sandbox.
		// add_action( 'wpug_sandbox_reset', array( $this, 'sandbox_reset' ) );
		// wp_clear_scheduled_hook( 'wpug_sandbox_reset' );
		// if ( ! wp_next_scheduled( 'wpug_sandbox_reset' ) ) {
		// wp_schedule_event( strtotime('midnight'), 'daily', array( $this, 'sandbox_reset') );
		// wp_schedule_event( time(), 'daily', 'wpug_sandbox_reset' );
		// }
		// add_action( 'admin_init', array( $this, 'sandbox_get' ) );
		// add_action( 'admin_init', array( $this, 'create_sandbox_clone' ) );
	}

	public function create_sandbox_clone() {

		$current_user = wp_get_current_user();
		$user_slug    = preg_replace( '/[^a-z]+/', '', $current_user->user_login );
		$user_id      = $current_user->ID;
		$user_sandbox = get_user_meta( $user_id, 'wpug_sandbox_id', true );
		$subsite      = $user_slug . 'sandbox';
		$domain       = true !== SUBDOMAIN_INSTALL ? DOMAIN_CURRENT_SITE : $subsite . '.' . DOMAIN_CURRENT_SITE;
		$path         = true !== SUBDOMAIN_INSTALL ? "/{$subsite}/" : '/';
		$title        = 'Sandbox Site';
		$args         = array(
			'domain'  => $domain,
			'path'    => $path,
			'public'  => 0,
			'user_id' => $user_id,
			'title'   => $title,
		);
		// Reuse user sandbox site ID.
		if ( $user_sandbox ) {
			$args['blog_id'] = $user_sandbox;
		}

		$new_sandbox_site_id = wp_insert_site( $args );
		if ( $new_sandbox_site_id ) {
			do_action( 'sandbox_new', $new_sandbox_site_id );
		}

		// Verify nonce.
		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		wp_redirect(
			add_query_arg(
				array(
					'page'    => 'new-user-onboarding',
					'updated' => 'true',
				),
				( is_multisite() ? network_admin_url( 'users.php' ) : admin_url( 'users.php' ) )
			)
		);
		exit;

	}

	public function delete_sandbox_clone() {
		error_log( 'delete clone' );
		wp_delete_site( 4 );

		// Verify nonce.
		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		wp_redirect(
			add_query_arg(
				array(
					'page'    => 'new-user-onboarding',
					'updated' => 'true',
				),
				( is_multisite() ? network_admin_url( 'users.php' ) : admin_url( 'users.php' ) )
			)
		);
		exit;
	}

	public function sandbox_set() {

		global $wpdb;
		global $table_prefix;
		$int_types  = array( 'int', 'bigint' );
		$sql_output = '';

		foreach ( $this->sandbox_site_tables as $table ) {
			// Drop the table statement.
			$sql_output .= "DROP TABLE IF EXISTS `$table`;\n\n";
			// Get the table creation statement.
			$method      = 'Create Table';
			$command     = "SHOW CREATE TABLE $table";
			$results     = $wpdb->get_results( $command );
			$sql_output .= $results[0]->$method . ";\n\n";

			// Get SQL data types for each column.
			$columns = $wpdb->get_results( "SELECT column_name,data_type FROM information_schema.columns WHERE table_name = '$table'", OBJECT );
			$types   = array();
			foreach ( $columns as $column ) {
				$types[ $column->COLUMN_NAME ] = $column->DATA_TYPE;
			}

			// Get the table row statements.
			$method        = '';
			$command       = "SELECT * FROM $table";
			$rows          = $wpdb->get_results( $command, ARRAY_A );
			$compiled_rows = array();
			if ( is_array( $rows ) && $rows ) {
				$sql_output .= "INSERT INTO `$table` VALUES\n";
				foreach ( $rows as $key => $row ) {
					// Wrap string typed column values in single quotes.
					foreach ( $row as $key => $column ) {
						$column_type = $types[ $key ];
						if ( ! in_array( $column_type, $int_types, true ) ) {
							$row[ $key ] = "'" . $column . "'";
						}
					}
					$row             = '(' . implode( ', ', $row ) . ')';
					$row             = str_replace( "\n", '\n', $row );
					$compiled_rows[] = $row;
				}
				$sql_output .= implode( ",\n", $compiled_rows ) . ";\n\n";
			}
		}

		// Output SQL statements to file.
		if ( ! empty( $sql_output ) ) {
			$fileHandler     = fopen( WP_USER_GOV_DIR_PATH . 'sandbox/' . $this->backup_file_name, 'w+' );
			$number_of_lines = fwrite( $fileHandler, $sql_output );
			fclose( $fileHandler );
		}

		// Verify nonce.
		wp_verify_nonce( $_POST['_wpnonce'], 'update' );

		wp_redirect(
			add_query_arg(
				array(
					'page'    => 'new-user-onboarding',
					'updated' => 'true',
				),
				( is_multisite() ? network_admin_url( 'users.php' ) : admin_url( 'users.php' ) )
			)
		);
		exit;
	}

	/**
	 * If I use wpdb->insert it will handle variable type conversion.
	 */
	public function sandbox_new( $new_site_id ) {

		global $wpdb;
		global $table_prefix;

		// Create a copy of the sandbox database file using the new site prefix.
		$filepath    = WP_USER_GOV_DIR_PATH . 'sandbox/' . $this->backup_file_name;
		$sandbox_sql = file_get_contents( $filepath );

		$new_table_prefix = $table_prefix . $new_site_id . '_';
		$new_filepath     = WP_USER_GOV_DIR_PATH . 'sandbox/' . $new_table_prefix . 'sandbox_backup.sql';
		$new_sql_output   = str_replace( "`$this->sandbox_table_prefix", "`$new_table_prefix", $sandbox_sql );

		if ( ! empty( $new_sql_output ) ) {
			$fileHandler     = fopen( $new_filepath, 'w+' );
			$number_of_lines = fwrite( $fileHandler, $new_sql_output );
			fclose( $fileHandler );
		}

		// Execute the SQL file.
		$command = 'mysql --user=' . DB_USER . " --password='" . DB_PASSWORD . "' -h " . DB_HOST . ' -D ' . DB_NAME . " < \"{$new_filepath}\"";
		shell_exec( $command . '/shellexec.sql' );

		// Delete the new SQL file.
		// unlink( $new_filepath );
	}
}
