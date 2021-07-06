<?php
/**
 * We generate our own email for new users because we are not given full control
 * over WordPress's automatic new user emails, particularly headers.
 */

namespace User_Governance;

class User_Onboarding {

	public function __construct() {

		add_action( 'add_user_to_blog', array( $this, 'new_user_email' ), 11, 3 );
		add_action( 'wp_header', array( $this, 'wp_header' ) );

		$option = get_site_option( 'wpug_user_onboarding_option' );
		if ( is_array( $option ) && array_key_exists( 'email_override', $option ) && 'on' === $option['email_override'] ) {

			// Disable default new user emails.
		  remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
		  remove_action( 'network_site_new_created_user', 'wp_send_new_user_notifications' );
		  remove_action( 'network_site_users_created_user', 'wp_send_new_user_notifications' );
		  remove_action( 'network_user_new_created_user', 'wp_send_new_user_notifications' );

		}

	}

	/**
	 * Fires immediately after a new user is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 *
	 * @return void
	 */
	function new_user_email( $user_id, $role, $blog_id ) {

		$option = get_site_option( 'wpug_user_onboarding_option' );

		if ( is_array( $option ) && array_key_exists( 'email_override', $option ) && 'on' === $option['email_override'] ) {

			switch_to_blog( $blog_id );
			$user      = get_user_by( 'ID', $user_id );
			$blogname  = get_blog_option( $blog_id, 'blogname' );
			$translate = array(
				'{{user_name}}'      => $user->user_login,
				'{{first_name}}'     => '',
				'{{last_name}}'      => '',
				'{{user_email}}'     => $user->user_email,
				'{{login_link}}'     => '<a href="' . wp_login_url() . '">' . wp_login_url() . '</a>',
				'{{site_url}}'       => site_url(),
				'{{site_link}}'      => '<a href="' . site_url() . '">' . site_url() . '</a>',
				'{{site_title}}'     => $blogname,
				'{{network_title}}'  => get_network()->site_name,
				'{{network_domain}}' => get_network()->domain,
			);
			$message   = '';
			$subject   = '';
			$headers   = '';
			restore_current_blog();

			if ( array_key_exists( 'email_subject', $option ) && $option['email_subject'] ) {
				$subject = strtr( $option['email_subject'], $translate );
			}

			if ( array_key_exists( 'email_message', $option ) && $option['email_message'] ) {
				$message = stripslashes( strtr( $option['email_message'], $translate ) );
				$message = "<html><head><title>{$subject}</title><body>{$message}</body></html>";
			}

			if ( array_key_exists( 'email_headers', $option ) && $option['email_headers'] ) {
				$headers = wp_check_invalid_utf8( $option['email_headers'] );
				$headers = preg_split('/;\s?/', $headers);
			}

			if ( $message && $subject && $headers ) {
				wp_mail( $user->user_email, $subject, $message, $headers );
			}
		}

	}

	/**
	 * Hide the password field on the activation page since we use NetID authentication.
	 *
	 * @param string $name Name of the specific header file to use.
	 *
	 * @return void
	 */
	public function wp_header( $name ) {
		// if ( 'wp-activate' === $name ) {
			echo "<style id=\"wpug\">#signup-welcome p + p { display: none; }</style>";
		// }
	}

}
