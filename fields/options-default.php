<?php
/**
 * The file that defines the default site option values for this plugin.
 *
 * @link       https://github.tamu.edu/liberalarts-web/wp-user-governance/blob/master/fields/options-default.php
 * @since      1.0.0
 * @package    wp-user-governance
 * @subpackage wp-user-governance/fields
 */

$default_site_options = array(
	'wpug_policy_option'          => array(
		'pii_body'       => '<p>Personally Identifiable Information (PII) is information that can be used to distinguish or trace an individual’s identity, either alone or when combined with other personal or identifying information that is linkable to a specific individual.</p><p>PII includes, but is not limited to:<ul><li>Social Security Numbers (SSNs)</li><li>Bank account numbers, Credit card numbers, or any financial information</li><li>Home telephone numbers & mobile telephone numbers</li><li>Ages & birth dates</li><li>Marital status & spouse names</li><li>Medical History</li><li>Educational history</li><li>Biometric identifiers (for example, fingerprints, voiceprints, and iris scans)</li>Computer passwords</li></ul></p><p>PII and other sensitive information must be stored in a manner that protects the confidentiality of the information and is designed to prevent unauthorized individuals from retrieving it by computer, remote access, or any other means.</p><p>PII, and other sensitive information obtained through a request, must not be disclosed to anyone other than an individual or entity authorized by law to receive the information. Examples of Authorized Individuals include, but are not limited to:<ul><li>program staff with a need to know;</li><li>auditors;</li><li>state and fiscal monitors; and</li><li>individuals or entities identified in a signed release from the participant.</li></ul></p><p>Report to the Office of Risk, Ethics, and Compliance or Division of Information Technology any incidents if you suspect that Protected Health Information (PHI), Personally Identifiable Information (PII) or information covered by the Health Insurance Portability and Accountability Act (HIPAA) have been disclosed. <a href="https://it.tamu.edu/star/star-system/reporting-data-breaches.php">https://it.tamu.edu/star/star-system/reporting-data-breaches.php</a></p>',
		'user_body'      => '<p>You must complete accessibility training before writing content for your website. Any unauthorized use is strictly prohibited.</p>',
		'visitor_body'   => '<p>This site uses cookies and analytics to inform and improve our operations. Any unauthorized use is strictly prohibited.</p>',
		'copyright_body' => '<p>You must have the legal right to use an image, video, or other file type before uploading it here.</p>',
	),
	'wpug_user_onboarding_option' => array(
		'email_override'   => 'off',
		'email_subject'    => 'Your Login Details for {{site_title}}',
		'email_headers'    => 'Content-type: text/html; charset=UTF-8',
		'email_message'    => '<p>Howdy,</p><p>You now have access to {{site_url}}!</p><p>Before you begin creating or editing web content, you must complete an accessibility course. To sign up for this course, or for additional support, email helpdesk@{{network_domain}}. Files uploaded to your website are public and some content may not be suitable for storage there.</p><p>To log in using your NetID email address, password, and Duo for two-factor authentication (<a href="https://it.tamu.edu/duo/">enroll here</a>), go to {{login_url}}.</p><p>Thanks and Gig\'em!</p><p>- The web team at {{network_title}}</p>',
		'sandbox_id'       => 2,
		'sandbox_lifetime' => DAY_IN_SECONDS,
	),
);
