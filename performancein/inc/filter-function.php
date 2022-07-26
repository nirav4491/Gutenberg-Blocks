<?php
/**
 * File include all the filters callback functions.
 *
 * @package performancein
 */

/**
 * Register new category for custom block
 *
 * @param array $categories Category array.
 *
 * @return array
 */
function performancein_custom_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'performancein',
				'title' => __( 'PerformanceIN', 'performancein' ),
			),
		),
		$categories
	);
}

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 *
 * @return string (Maybe) modified "read more" excerpt string.
 */
function performancein_excerpt_more( $more ) {
	if ( ! is_single() ) {
		$more = sprintf( '<a class="read-more" href="%1$s">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			__( ' [...]', 'performancein' )
		);
	}

	return $more;
}

/**
 * Added Async to some javascript.
 *
 * @param $tag
 *
 * @return mixed
 */
function performancein_js_async_attr( $tag ) {

	// Add defer or async attribute to these scripts
	$scripts_to_include = array( 'js?id=UA-88365551-1' );

	foreach ( $scripts_to_include as $include_script ) {
		if ( true === strpos( $tag, $include_script ) ) # Async the scripts included above
		{
			return str_replace( ' src', ' async src', $tag );
		}
	}

	// Return original tag for all scripts not included
	return $tag;
}

/**
 * Function replace the placeholder text for the search input box.
 *
 * @param $search_html
 *
 * @return mixed
 */
function performancein_search_form( $search_html ) {
	$search_html = str_replace( 'placeholder="Search', 'placeholder="Type your keyword', $search_html );

	return $search_html;
}

function performancein_theme_prefix_block_category( $default_categories ) {
	$default_categories[] = array(
		'slug'  => 'postfilter',
		'title' => esc_html__( 'Post Filter', 'theme_prefix' ),
	);

	return $default_categories;
}



function pi_author_url_request( $query_vars ){
	if ( array_key_exists( 'author_name', $query_vars ) ) {
		global $wpdb;
		$author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='pi_user_slug' AND meta_value = %s", $query_vars['author_name'] ) );
		if ( $author_id ) {
			$query_vars['author'] = $author_id;
			unset( $query_vars['author_name'] );
		}
	}
	return $query_vars;
}

/**
 * Function to return change author link
 *
 * @param $link
 * @param $author_id
 * @param $author_nicename
 *
 * @return string
 */
function performancein_author_link( $link, $author_id, $author_nicename ) {
	$authorSlug = get_user_meta( $author_id, 'pi_user_slug', true );
	$authorSlug = ! empty($authorSlug) ? $authorSlug : $author_nicename;
	$link = site_url() . '/news/author/' . $authorSlug;
	return $link;
}

/**
 * Author title changed in yoast SEO
 * @param $title
 *
 * @return string|string[]
 */
function pi_filterAuthorTitle( $title ) {
//check if author page, if it's not return as it is
	if ( ! is_author() ) {
		return $title;
	}

//its author page, so let's pull current author name
	$current_display_name = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
	$authorobj = get_queried_object();
	$authorID = $authorobj->ID;
	$author_nicename = $authorobj->display_name ;
	$Username            = get_user_meta( $authorID, 'pi_user_name', true );
	$Username = ! empty($Username) ? $Username : $author_nicename;
	$title = str_replace(', Author at PerformanceIN','',$title);

	return str_replace( $current_display_name, $Username, $title );
}

/**
 * Resize the image.
 *
 * @param $image_data
 *
 * @return mixed
 */
function performancein_resize_uploaded_image( $image_data ) {
	// if there is no large image : return
	if ( isset( $image_data['sizes']['recruiter-logo'] ) ) {

		// paths to the uploaded image and the large image
		$upload_dir              = wp_upload_dir();
		$uploaded_image_location = $upload_dir['basedir'] . '/' . $image_data['file'];
		$large_image_location    = $upload_dir['path'] . '/' . $image_data['sizes']['recruiter-logo']['file'];

		// rename the large image
		rename( $large_image_location, $uploaded_image_location );
		// update image metadata and return them
		$image_data['width']  = 218;
		$image_data['height'] = 97;

		return $image_data;
	}
	if ( isset( $image_data['sizes']['company-logo'] ) ) {

		// paths to the uploaded image and the large image
		$upload_dir              = wp_upload_dir();
		$uploaded_image_location = $upload_dir['basedir'] . '/' . $image_data['file'];
		$large_image_location    = $upload_dir['path'] . '/' . $image_data['sizes']['company-logo']['file'];

		// rename the large image
		rename( $large_image_location, $uploaded_image_location );
		// update image metadata and return them
		$image_data['width']  = 380;
		$image_data['height'] = 185;

		return $image_data;
	}
	if ( isset( $image_data['sizes']['company-header'] ) ) {

		// paths to the uploaded image and the large image
		$upload_dir              = wp_upload_dir();
		$uploaded_image_location = $upload_dir['basedir'] . '/' . $image_data['file'];
		$large_image_location    = $upload_dir['path'] . '/' . $image_data['sizes']['company-header']['file'];

		// rename the large image
		rename( $large_image_location, $uploaded_image_location );
		// update image metadata and return them
		$image_data['width']  = 750;
		$image_data['height'] = 250;

		return $image_data;
	}

	return $image_data;
}


/**
 * Our hooked in function - $fields is passed via the filter!
 *
 * @param $fields
 *
 * @return mixed
 */
function performancein_wc_checkout_fields( $fields ) {

	$fields['billing']['billing_email']['priority']      = 10;
	$fields['billing']['billing_company']['priority']    = 20;
	$fields['billing']['billing_first_name']['priority'] = 30;
	$fields['billing']['billing_last_name']['priority']  = 40;
	$fields['billing']['billing_country']['priority']    = 50;
	$fields['billing']['billing_address_1']['priority']  = 60;
	$fields['billing']['billing_address_2']['priority']  = 70;
	$fields['billing']['billing_city']['priority']       = 80;
	$fields['billing']['billing_state']['priority']      = 90;
	$fields['billing']['billing_postcode']['priority']   = 100;
	$fields['billing']['billing_phone']['priority']      = 110;

	$fields['billing']['billing_company']['required'] = true;

	$fields['billing']['billing_first_name']['class'] = array( 'form-row-wide' );
	$fields['billing']['billing_last_name']['class']  = array( 'form-row-wide' );

	$fields['billing']['billing_email']['label']      = __( 'Invoice Email Address', 'performancein' );
	$fields['billing']['billing_first_name']['label'] = __( 'Cardholder Forename', 'performancein' );
	$fields['billing']['billing_last_name']['label']  = __( 'Cardholder Surname', 'performancein' );

	return $fields;
}


/**
 * Lost password url change.
 * @return string|void
 */
function performancein_lost_password_url() {
	return site_url( '/account/iforgot/' );
}


/**
 *  Remove the h1 tag from the WordPress editor.
 *
 * @param array $settings The array of editor settings
 *
 * @return  array             The modified edit settings
 */
function remove_h1_from_editor( $settings ) {

	if ( isset( $settings['selector'] ) ) {
		if ( '#id_job_description' === $settings['selector'] || '#id_cover_description' === $settings['selector'] ) {
			$settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;';
			$toolbar1                  = $settings['toolbar1'];

			$toolbar1_old   = explode( ',', $toolbar1 );
			$remove_buttons = array(
				'blockquote',
				'hr', // horizontal line
				'alignleft',
				'aligncenter',
				'alignright',
				'link',
				'unlink',
				'wp_more', // read more link
				'spellchecker',
				'dfw', // distraction free writing mode
				'wp_adv', // kitchen sink toggle (if removed, kitchen sink will always display)
			);
			foreach ( $toolbar1_old as $button_key => $button_value ) {
				if ( in_array( $button_value, $remove_buttons, true ) ) {
					unset( $toolbar1_old[ $button_key ] );
				}
			}

			$settings['toolbar1'] = implode( ',', $toolbar1_old );
		}
	}

	return $settings;
}

/**
 *
 * Function to return only post data
 *
 * @param $query
 *
 * @return mixed
 */
function pi_searchfilter( $query ) {

	if ( $query->is_search && ! is_admin() ) {
		$query->set( 'post_type', array( 'post', 'pi_partner_networks' ) );
	}

	return $query;
}


function pagenum_link( $link ) {
	return preg_replace( '~/page/(\d+)/?~', '?paged=\1', $link );
}

/**
 * Function to return modified resources URL as per Landingpage URL
 * @param $post_url
 * @param $post
 *
 * @return mixed|void|null
 */
function pi_resources_modify_the_link($post_url,$post) {
	if( get_post_type() === 'pi_resources' ) {
		$pi_resources                     = get_field( 'pi_landing_page_url', $post->ID );
		$post_url                     = ! empty( $pi_resources ) ? $pi_resources : $post_url;
	}
	return $post_url;
}
