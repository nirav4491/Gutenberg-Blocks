<?php
/**
 * File include all the shortcodes.
 *
 * @package performancein
 */

//Shortcode for filter options
add_shortcode( 'performancein_browse_filter', 'performancein_browse_filter_callback' );

/**
 * Shortcode for Check inbox html.
 */
add_shortcode( 'performancein_check_inbox', 'performancein_check_inbox_html' );
/**
 * Shortcode for Complete profile html.
 */
add_shortcode( 'performancein_complete_profile', 'performancein_complete_profile_form_html' );
/**
 * Shortcode for Registration html.
 */
add_shortcode( 'performancein_registration', 'performancein_registration_form_html' );
/**
 * Shortcode for account forgot html.
 */
add_shortcode( 'performancein_forgot', 'performancein_account_iforgot_html' );

/**
 * Shortcode for login html.
 */
add_shortcode( 'performancein_login', 'performancein_account_login_html' );
/**
 * Shortcode for account.
 */
add_shortcode( 'performancein_account', 'performancein_account_html' );
/**
 * Shrotcode for job form html.
 */
add_shortcode( 'performancein_job_package', 'performancein_job_package_html' );

/**
 * Shortcode for job save/edit form html.
 */
add_shortcode( 'performancein_job_form', 'performancein_job_form_html' );

/**
 * Shortcode for password reset form html.
 */
add_shortcode( 'performancein_password_reset', 'performancein_account_password_reset_html' );

/**
 * Shortcode for partner banner.
 */
add_shortcode( 'performancein_partner_banner', 'performancein_partner_banner_html' );

/**
 * Shortcode for partner banner.
 */
add_shortcode( 'performancein_partner_login_status', 'performancein_partner_login_html' );

add_shortcode( 'performancein_company_profile_form', 'performancein_company_profile_form_html' );

add_shortcode( 'performancein_remove_social', 'performancein_remove_social_html' );

add_shortcode('pi_partner_account_request_form','performancein_partner_account_request_form_html');

add_shortcode('performancein_advertisingForm', 'performancein_advertising_form_content_html');

add_shortcode('performancein_selectPackage', 'performancein_select_package_content_html');
