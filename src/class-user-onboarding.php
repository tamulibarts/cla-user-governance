<?php

namespace WP_User_Governance;

class User_Onboarding {

	public function __construct() {

		add_filter( 'wp_new_user_notification_email', array( $this, 'customize_new_user_email' ) );

	}

	/**
	 * Filters the contents of the new user notification email sent to the new user.
	 *
	 * @since 4.9.0
	 *
	 * @param array   $wp_new_user_notification_email {
	 *     Used to build wp_mail().
	 *
	 *     @type string $to      The intended recipient - New user email address.
	 *     @type string $subject The subject of the email.
	 *     @type string $message The body of the email.
	 *     @type string $headers The headers of the email.
	 * }
	 * @param WP_User $user     User object for new user.
	 * @param string  $blogname The site title.
	 */
	function customize_new_user_email( $wp_new_user_notification_email, $user, $blogname ) {

		$translate = array(
			'{{user_name}}'      => $user->user_login,
			'{{first_name}}'     => '',
			'{{last_name}}'      => '',
			'{{user_email}}'     => $user->user_email,
			'{{login_link}}'     => wp_login_url(),
			'{{site_url}}'       => site_url(),
			'{{site_link}}'      => '<a href="' . site_url() . '">' . site_url() . '</a>',
			'{{site_title}}'     => $blogname,
			'{{network_title}}'  => get_network()->site_name,
			'{{network_domain}}' => get_network()->domain,
		);

		$option = get_site_option( 'wpug_user_onboarding_option' );

		if ( is_array( $option ) && array_key_exists( 'email_override', $option ) && 'on' === $option['email_override'] ) {

			if ( array_key_exists( 'email_message', $option ) && $option['email_message'] ) {
				$wp_new_user_notification_email['message'] = strtr( $option['email_message'], $translate );
			}

			if ( array_key_exists( 'email_subject', $option ) && $option['email_subject'] ) {
				$wp_new_user_notification_email['subject'] = strtr( $option['email_subject'], $translate );
			}

			if ( array_key_exists( 'email_headers', $option ) && $option['email_headers'] ) {
				$wp_new_user_notification_email['headers'] = strtr( $option['email_headers'], $translate );
			}
		}

		return $wp_new_user_notification_email;

	}

}
