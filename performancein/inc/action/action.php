<?php
/**
 * File include all the actions.
 *
 * @package performancein
 */

/**
 * Action for the theme setup.
 */
add_action( 'after_setup_theme', 'performancein_setup' );

/**
 * Action for content width defining in the theme setup.
 */
add_action( 'after_setup_theme', 'performancein_content_width', 0 );

/**
 * Action for the frontend script enqueueing.
 */
add_action( 'wp_enqueue_scripts', 'performancein_scripts' );

/**
 * Action for Remove emoji
 */
add_action( 'get_header', 'performancein_remove_wp_emoji' );

/**
 * Action for Load JS in footer
 */
add_action( 'get_header', 'performancein_move_scripts_to_footer' );

/**
 * Action for add gutenberg custom block.
 */
add_action( 'enqueue_block_editor_assets', 'performancein_add_block_editor_assets' );

/**
 * Action for Change default post type object name.
 */
/*add_action( 'init', 'performancein_change_post_object' );*/

/**
 * Action to add SVG support in file uploads.
 */
add_action( 'upload_mimes', 'performancein_add_file_types_to_uploads' );

/**
 * action to register dynamic slider block.
 */
add_action( 'init', 'performancein_register_dynamic_blocks' );

/**
 * Register new rest route to fetch all terms
 */
add_action( 'rest_api_init', 'performancein_register_api_endpoints' );
/**
 * Register new rest route to fetch all posts
 */
add_action( 'rest_api_init', 'performancein_theme_prefix_post_filter_register_rest_route' );

/**
 * Hook into acf initialization.
 */
add_action( 'acf/init', 'performancein_register_acf_options_pages' );

/**
 * Action to add custom recent post widget.
 */
add_action( 'widgets_init', 'performancein_register_recent_post_widget' );

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
add_action( 'admin_enqueue_scripts', 'performancein_enqueue_custom_admin_style' );

/**
 * Register Partner Network Post Type
 */
add_action( 'init', 'performancein_partner_networks', 0 );
/**
 * Register Partner Network Post Type Category
 */
add_action( 'init', 'performancein_category_partner_networks', 0 );

/**
 * Register Partner Network Post Type Tags
 */
add_action( 'init', 'performancein_tag_partner_networks', 0 );

/**
 * Register Events Post Type
 */
add_action( 'init', 'performancein_events', 0 );
/**
 * Register Events Post Type
 */
add_action( 'init', 'performancein_resources', 0 );

/**
 * Register Events Post Type Tags
 */
/*add_action( 'init', 'performancein_tag_resources', 0 );*/

/**
 * Register Job Post Post Type
 */
add_action( 'init', 'performancein_job_post', 0 );

/**
 * Register job taxonomy.
 */
add_action( 'init', 'performancein_category_job_post', 0 );

add_action('init', 'performancein_change_role_name');

/**
 * Add Job fields in product page admin side hook.
 */
add_action( 'woocommerce_product_options_pricing', 'performancein_wc_product_job_options_html' );

/**
 * Saving Job fields data of products metabox hook.
 */
add_action( 'woocommerce_process_product_meta', 'performancein_wc_save_product_job_options_fields' );

/**
 * Redirect to specific page hook.
 */
add_action( 'template_redirect', 'performancein_redirect_to_specific_page' );

/**
 * The setup recruiter image size add hook.
 */
add_action( 'after_setup_theme', 'performancein_theme_setup' );


/**
 * Remove the job package transient with product update hook.
 */
add_action( 'before_delete_post', 'performancein_remove_transient' );
add_action( 'save_post_product', 'performancein_remove_transient' );
add_action( 'wp_trash_post', 'performancein_job_revert_credit' );
add_action( 'untrash_post', 'performancein_job_decrease_credit_manage' );
add_action( 'transition_post_status', 'performancein_job_revert_credit_draft', 10, 3 );


/**
 * Add user profile custom fields html.
 */
add_action( 'show_user_profile', 'performancein_user_profile_fields_html', 10, 1 );
add_action( 'edit_user_profile', 'performancein_user_profile_fields_html', 10, 1 );


/**
 * Remove the job package transient with product update hook.
 *
 */
add_action( 'personal_options_update', 'performancein_update_profile_fields' );
add_action( 'edit_user_profile_update', 'performancein_update_profile_fields' );

/**
 * Checkout order review section in add credit logo.
 */
add_action( 'woocommerce_checkout_order_review', 'performancein_wc_credit_card_logo', 15 );
/**
 * Remove the action and add action.
 */
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_before_order_review_heading', 'woocommerce_checkout_payment', 10 );

/**
 * Thank you page add text description.
 */
add_action( 'woocommerce_thankyou', 'performancein_wc_thankyou', 10, 1 );

/**
 * Payment complete the add credit.
 */
add_action( 'woocommerce_payment_complete', 'performancein_wc_payment_complete', 10, 1 );

/**
 * Add meta box.
 */
add_action( 'add_meta_boxes', 'performancein_register_meta_boxes' );

add_action( 'wp_ajax_pi_partner_search_ajax', 'pi_get_search_partner_result' );
add_action( 'wp_ajax_nopriv_pi_partner_search_ajax', 'pi_get_search_partner_result' );


add_action( 'wp', 'pi_redirect' );

add_action( 'admin_menu', 'performancein_change_post_label' );
add_action( 'init', 'performancein_change_post_object' );

/**
 *Action to add select2 in author selection
 */
add_action( 'admin_head', 'pi_select2jquery_inline' );
