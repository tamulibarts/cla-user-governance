<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/zachwatkins/cla-user-page-access/blob/master/src/class-cla-user-page-access.php
 * @since      1.0.0
 * @package    cla-user-page-access
 * @subpackage cla-user-page-access/src
 */

namespace CLA_User_Governance;

/**
 * The user page access restriction class.
 *
 * @since 1.0.0
 * @return void
 */
class User_Page_Access {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		if ( is_admin() ) {
			if ( class_exists( 'acf' ) ) {
				require_once CLA_USER_GOV_DIR_PATH . 'fields/user-page-access-fields.php';
			}

			// Admin menus.
			add_action( 'acf/init', array( $this, 'user_menu_init' ) );
			add_action( 'acf/prepare_field/key=field_5fd29a782bd03', array( $this, 'acf_who_can_restrict' ) );

			// Access Restriction Logic.
			add_filter( 'map_meta_cap', array( $this, 'unmap_caps_by_post_id' ), 11, 4 );
			add_action( 'parse_query', array( $this, 'exclude_pages_from_admin' ), 999 );
			add_action( 'acf/save_post', array( $this, 'update_option_user_page_access' ) );
			// Action hooks specific to the Nested Pages plugin.
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			if (
				( strpos( $request_uri, '/wp-admin/admin.php' ) !== false && strpos( $request_uri, 'page=nestedpages' ) !== false )
				|| ( strpos( $request_uri, '/wp-admin/edit.php' ) !== false && strpos( $request_uri, 'page=nestedpages' ) !== false )
			) {
				add_action( 'plugins_loaded', array( $this, 'np_init' ) );
			}
		}
	}

	/**
	 * Hide the ACF settings page field that defines who can access it.
	 * Only super admins should be able to see this field, or admins if the network is not multisite.
	 *
	 * @param array $field The field settings.
	 *
	 * @return array
	 */
	public function acf_who_can_restrict( $field ) {

		$user_role = ( is_multisite() ) ? 'superadmin' : 'administrator';

		if ( ! current_user_can( $user_role ) ) {
			$field = false;
		}

		return $field;

	}

	/**
	 * Initialize the user page access admin menu.
	 *
	 * @return void
	 */
	public static function user_menu_init() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		// Lock down page for master user and other authorized users only.
		if ( function_exists( 'acf_add_options_page' ) && defined( 'CLA_USER_GOV_MASTER_USER' ) ) {

			$current_user      = wp_get_current_user();
			$current_user_data = $current_user->data;
			$current_user_name = $current_user_data->user_login;
			$authorized_users  = array( CLA_USER_GOV_MASTER_USER );
			$auth_user_field   = get_field( 'ugov_who_can_see_settings_page', 'option' );
			if ( $auth_user_field ) {

				// Get usernames as array.
				foreach ( $auth_user_field as $user ) {

					$authorized_users[] = $user['user_nicename'];

				}
			}

			// Show the settings page if the current username is authorized.
			if ( in_array( $current_user_name, $authorized_users, true ) ) {

				acf_add_options_page(
					array(
						'page_title'  => 'User Page Access',
						'menu_title'  => 'User Page Access',
						'menu_slug'   => 'cla-user-page-access',
						'parent_slug' => 'users.php',
						'position'    => 2,
						'redirect'    => false,
					)
				);

			}
		}
	}

	/**
	 * Disable editing options on the nested pages view.
	 *
	 * @return void
	 */
	public function np_init() {
		if ( $this->is_user_limited() ) {
			add_filter( 'nestedpages_menu_autosync_enabled', '__return_false' );
			add_filter( 'nestedpages_menus_disabled', '__return_true' );
			add_filter( 'nestedpages_post_sortable', '__return_false' );
			add_filter( 'nestedpages_row_action_wpml', '__return_false' );
			add_filter( 'nestedpages_row_action_comments', '__return_false' );
			add_filter( 'nestedpages_row_action_insert_before', '__return_false' );
			add_filter( 'nestedpages_row_action_insert_after', '__return_false' );
			add_filter( 'nestedpages_row_action_push_to_top', '__return_false' );
			add_filter( 'nestedpages_row_action_push_to_bottom', '__return_false' );
			add_filter( 'nestedpages_row_action_clone', '__return_false' );
			add_filter( 'nestedpages_row_action_quickedit', '__return_false' );
			add_filter( 'nestedpages_row_action_trash', '__return_false' );
			add_filter( 'nestedpages_row_action_add_child_link', '__return_false' );
			add_filter( 'nestedpages_row_action_add_child_page', '__return_false' );
			add_filter( 'nestedpages_edit_link_text', array( $this, 'np_remove_edit_link_text' ), 11, 2 );
			add_filter( 'nestedpages_quickedit', array( $this, 'np_disable_quickedit' ), 11, 2 );
			add_filter( 'option_nestedpages_allowsorting', '__return_false' );
			add_filter( 'get_edit_post_link', array( $this, 'np_empty_post_link' ), 11, 3 );
			add_filter( 'get_delete_post_link', array( $this, 'np_empty_post_link' ), 11, 3 );
			add_filter( 'post_row_actions', array( $this, 'np_restricted_id_empty_string' ), 11, 2 );
			add_filter( 'page_row_actions', array( $this, 'np_restricted_id_empty_string' ), 11, 2 );
			add_filter( 'user_has_cap', array( $this, 'np_disable_caps' ), 11, 4 );
		}
	}

	/**
	 * Determine if the user is limited with optional parameters.
	 *
	 * @param mixed $post_type The post type.
	 * @param int   $post_id   The post ID.
	 * @param int   $user_id   The user ID.
	 *
	 * @return boolean
	 */
	private function is_user_limited( $post_type = '', $post_id = 0, $user_id = 0 ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$limited          = false;
		$user_id          = ! $user_id ? get_current_user_id() : $user_id;
		$user_page_access = get_option( 'cla_user_page_access' );

		if ( is_array( $user_page_access ) ) {

			$limited = array_key_exists( strval( $user_id ), $user_page_access );

			if ( $limited ) {

				if ( $post_type ) {

					$limited_post_types = array( 'page', 'post', array( 'post' ), array( 'page' ), array( 'page', 'np-redirect' ), array( 'post', 'np-redirect' ) );

					if ( in_array( $post_type, $limited_post_types, true ) ) {

						$limited = true;

					} else {

						$limited = false;

					}
				}

				if ( $post_id ) {

					$user_key           = strval( $user_id );
					$exclusive_page_ids = $user_page_access[ $user_key ];
					$limited            = ! in_array( $post_id, $exclusive_page_ids, true );

				}
			}
		}

		return $limited;

	}

	/**
	 * Exclude posts from query not in a restricted user's list of allowed posts.
	 * Note: The post type array check using 'np-redirect' is part of the Nested Pages WordPress plugin.
	 *
	 * @param WP_Query $query The current page query.
	 *
	 * @return void;
	 */
	public function exclude_pages_from_admin( $query ) {

		global $pagenow;

		$post_type = $query->query_vars['post_type'];
		$limited   = $this->is_user_limited( $post_type );

		if ( $limited && in_array( $pagenow, array( 'edit.php', 'admin.php' ), true ) ) {

			$user_page_access    = get_option( 'cla_user_page_access' );
			$user_id             = get_current_user_id();
			$user_key            = strval( $user_id );
			$exclusive_page_ids  = $user_page_access[ $user_key ];
			$current_post_in_var = $query->query_vars['post__in'];

			if ( $current_post_in_var ) {

				$new_post_in_var = array_merge( $current_post_in_var, $exclusive_page_ids );

			} else {

				$new_post_in_var = $exclusive_page_ids;

			}

			// Nested Pages handling.
			// If the post type is for its Nested Pages admin page, and the post type is hierarchical, and the post type is handled by Nested Pages, and the custom indentation is active, then add ancestors of allowed posts to the query.
			$true_post_type        = is_array( $post_type ) ? $post_type[0] : $post_type;
			$nestedpages_page_slug = 'page' === $true_post_type ? 'nestedpages' : 'nestedpages-' . $true_post_type;
			$query                 = isset( $_SERVER['QUERY_STRING'] ) ? '' : sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) );
			if ( $query ) {
				parse_str( $query, $params );
				$get_page = $params['page'];
			} else {
				$get_page = '';
			}
			if ( $nestedpages_page_slug === $get_page ) {

				// Is the Classic (non-indented) display option enabled.
				$np_ui_option    = get_option( 'nestedpages_ui', false );
				$np_notindented  = $np_ui_option && isset( $np_ui_option['non_indent'] ) && $np_ui_option['non_indent'] == 'true' ? true : false;
				$np_types        = get_option( 'nestedpages_posttypes' );
				$is_hierarchical = is_post_type_hierarchical( $true_post_type );

				if ( ! $np_notindented && array_key_exists( $true_post_type, $np_types ) && $is_hierarchical ) {
					// Add all parent pages of the restricted page IDs to the post__in list so that the Nested View page shows the posts we want them to edit.
					$ancestors = array();
					foreach ( $exclusive_page_ids as $key => $post_id ) {
						$a = get_post_ancestors( $post_id );
						if ( $a ) {
							$ancestors = array_merge( $ancestors, $a );
						}
					}
					$new_post_in_var = array_merge( $exclusive_page_ids, $ancestors );
				}
			}

			$query->query_vars['post__in'] = $new_post_in_var;

		}
	}

	/**
	 * Hook that saves user page access variables to a custom options table.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return void
	 */
	public function update_option_user_page_access( $post_id ) {

		if ( isset( $_POST['acf'] ) && isset( $_POST['acf']['field_5fd299302bcff'] ) && ( isset( $_POST['_acf_post_id'] ) && 'options' === $_POST['_acf_post_id'] ) ) {
			$users       = $_POST['acf']['field_5fd299302bcff'];
			$user_access = array();
			foreach ( $users as $user ) {
				$user_id                 = strval( $user['field_5fd299652bd00'] );
				$page_ids                = $user['field_5fd2996d2bd01'];
				$user_access[ $user_id ] = $page_ids;
			}
			update_option( 'cla_user_page_access', $user_access );
		}

	}

	public function np_disable_row_actions( $enabled, $post_type ) {

		$limited = $this->is_user_limited( $post_type );
		if ( $limited ) {
			$enabled = false;
		}

		return $enabled;

	}

	public function np_disable_quickedit( $enabled, $post ) {

		$limited = $this->is_user_limited( $post->post_type, $post->ID );
		if ( $limited ) {
			$enabled = false;
		}

		return $enabled;

	}

	public function np_remove_edit_link_text( $text, $post ) {

		$limited = $this->is_user_limited( $post->post_type, $post->ID );
		if ( $limited ) {
			$text = 'Private';
		}

		return $text;

	}

	public function np_restricted_id_empty_string( $actions, $post ) {

		$limited = $this->is_user_limited( $post->post_type, $post->ID );
		if ( $limited ) {
			$actions = '';
		}

		return $actions;

	}

	public function np_empty_post_link( $link, $post_id, $context ) {

		$limited = $this->is_user_limited( '', $post_id );

		if ( $limited ) {

			$link = '#';

		}

		return $link;

	}

	public function np_disable_caps( $allcaps, $caps, $args, $user ) {

		$limited = $this->is_user_limited();

		if ( $limited ) {
			unset( $allcaps['delete_pages'] );
			unset( $allcaps['delete_page'] );
			unset( $allcaps['delete_posts'] );
			unset( $allcaps['delete_post'] );
			unset( $allcaps['nestedpages_sorting_page'] );
			unset( $allcaps['nestedpages_sorting_post'] );
		}

		return $allcaps;

	}

	/**
	 * Filters the primitive capabilities required of the given user to satisfy the
	 * capability being checked.
	 *
	 * @param string[] $caps    Primitive capabilities required of the user.
	 * @param string   $cap     Capability being checked.
	 * @param int      $user_id The user ID.
	 * @param array    $args    Adds context to the capability check, typically
	 *                          starting with an object ID.
	 */
	public function unmap_caps_by_post_id( $caps, $cap, $user_id, $args ) {

		global $post, $post_type;
		// List the capabilities a Post ID would be checked against that we want to disallow when the Post ID isn't in the list of the user's allowed Post IDs.
		$disallowed_capabilities = array( 'edit_post', 'edit_page', 'delete_posts', 'delete_pages' );
		$post_id                 = $args && is_int( $args[0] ) ? $args[0] : 0;
		if ( ! $post_id && is_object( $post ) ) {
			$post_id = $post->ID;
		}
		if ( ! $post_type ) {
			$post_type = get_post_type( $post_id );
		}
		if ( $post_id && $this->is_user_limited( $post_type, $post_id, $user_id ) && in_array( $cap, $disallowed_capabilities, true ) ) {
			$caps = array();
		}
		return $caps;

	}

}
