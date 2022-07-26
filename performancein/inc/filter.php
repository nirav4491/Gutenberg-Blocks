<?php
/**
 * File include all the filters.
 *
 * @package performancein
 */

/**
 * Filter for register new categories for custom block.
 */
add_filter( 'block_categories', 'performancein_custom_block_category', 10, 2 );

/**
 * Filter for excerpt Read more link.
 */
add_filter( 'excerpt_more', 'performancein_excerpt_more' );

/**
 * Added Async to some javascript.
 */
add_filter( 'script_loader_tag', 'performancein_js_async_attr', 10 );

/**
 * Filter for change search form as per need.
 */
add_filter( 'get_search_form', 'performancein_search_form' );
/**
 * Filyer for category dropdown in article block
 */
add_filter( 'block_categories', 'performancein_theme_prefix_block_category', 10, 2 );
/**
 * Filter to change author URL
 */
add_filter( 'request', 'pi_author_url_request' );
add_filter( 'author_link', 'performancein_author_link', 10, 3 );
add_filter( 'wpseo_title', 'pi_filterAuthorTitle' );



/**
 * Filter to checkout fields update.
 */
add_filter( 'woocommerce_checkout_fields', 'performancein_wc_checkout_fields' );

/**
 * Filter to lost password url
 */
add_filter( 'lostpassword_url', 'performancein_lost_password_url', 10, 0 );

/**
 * Filter to remove wp_editor fields.
 */
add_filter( 'tiny_mce_before_init', 'remove_h1_from_editor' );
/**
 * Filter to search result only post data
 * Filter to search result only post data
 * Filter to search result only post data
 */
add_filter('pre_get_posts','pi_searchfilter');


/**
 * Filter to modified resources view URL
 */
add_filter('post_type_link',"pi_resources_modify_the_link",10,2);

/**
 * filter for pagination.
 */
//add_filter( 'get_pagenum_link', 'pagenum_link' );