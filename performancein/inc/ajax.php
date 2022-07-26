<?php
/**
 * This file contains all the ajax actions and the callback functions.
 *
 * @package performancein
 */


/**
 * Ajax for masonry block filters.
 * @since 1.0
 */

add_action( 'wp_ajax_masonry_list_browse_filter', 'performancein_masonry_list_browse_filter_callback' );
add_action( 'wp_ajax_nopriv_masonry_list_browse_filter', 'performancein_masonry_list_browse_filter_callback' );

/**
 * Return posts according to filters.
 * @return string
 * @since 1.0.
 */
function performancein_masonry_list_browse_filter_callback() {

	check_ajax_referer( 'browse_filter_nonce', 'browse_filter_nonce' );

	$result_post  = array();
	$final_result = array();

	$post_type       = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
	$page_number     = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_STRING );
	$post_limit      = filter_input( INPUT_GET, 'post_limit', FILTER_SANITIZE_STRING );
	$post_start      = filter_input( INPUT_GET, 'post_start', FILTER_SANITIZE_STRING );
	$post_search     = filter_input( INPUT_GET, 'post_search', FILTER_SANITIZE_STRING );
	$select_category = filter_input( INPUT_GET, 'select_category', FILTER_SANITIZE_STRING );
	$select_date     = filter_input( INPUT_GET, 'select_date', FILTER_SANITIZE_STRING );
	$post_mini_title = filter_input( INPUT_GET, 'post_mini_title', FILTER_SANITIZE_STRING );

	$session_query = get_transient( 'performancein-ajax-masonry-list-browse-filter-cache' . $post_type . $page_number . $post_limit . $post_start . $post_search . $select_category . $select_date );

	if ( false === $session_query || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {

		$query_arg = array(
			'post_type'      => $post_type,
			'posts_per_page' => $post_limit,
			'paged'          => $page_number,
			'post_status'    => 'publish'
		);

		if ( ! empty( $post_start ) ) {
			$query_arg['starts_with'] = $post_start;
		}

		if ( ! empty( $post_search ) ) {
			$query_arg['s'] = $post_search;
		}

		if ( ! empty( $select_date ) ) {
			$query_arg['year']     = date( 'Y', strtotime( $select_date ) );
			$query_arg['monthnum'] = date( 'm', strtotime( $select_date ) );
		}

		$tax_query_args = array( 'relation' => 'AND' );

		$post_taxonomy_array = array(
			'post'               => 'category',
			'podcasts'           => 'cat_podcasts',
			'thought_leadership' => 'cat_tl',
			'case_studies'       => 'cat_cases',
			'clients'            => 'cat_clients',
		);

		if ( ! empty( $select_category ) && 'All' !== $select_category ) {
			$tax_query_args[] = array(
				'taxonomy' => $post_taxonomy_array[ $post_type ],
				'field'    => 'slug',
				'terms'    => $select_category,
			);
		}

		if ( count( $tax_query_args ) > 1 ) {
			$query_arg['tax_query'] = $tax_query_args;
		}

		$session_query = new WP_Query( $query_arg );

		set_transient( 'performancein-ajax-masonry-list-browse-filter-cache' . $post_type . $page_number . $post_limit . $post_start . $post_search . $select_category . $select_date, $session_query, 20 * MINUTE_IN_SECONDS + wp_rand( 1, 60 ) );

	}

	$total_pages = $session_query->max_num_pages;

	if ( $session_query->have_posts() ) {

		$i = 0;

		while ( $session_query->have_posts() ) {

			$session_query->the_post();

			$session_id                          = get_the_ID();
			$result_post[ $i ]["post_id"]        = $session_id;
			$result_post[ $i ]["post_title"]     = html_entity_decode( get_the_title() );
			$result_post[ $i ]["post_link"]      = get_permalink();
			$result_post[ $i ]["post_thumbnail"] = get_the_post_thumbnail_url();
			$postType                            = get_post_type_object( get_post_type() );
			$result_post[ $i ]["post_type"]      = ( $post_mini_title ? $post_mini_title : $postType->labels->singular_name );
			$result_post[ $i ]["post_excerpt"]   = get_the_excerpt();

			$i ++;
		}
	}

	if ( $total_pages > 1 ) {

		$current_page = max( 1, $page_number );

		$allowed_tags = [
			'span' => [
				'class' => [],
			],
			'i'    => [
				'class' => [],
			],
			'a'    => [
				'class' => [],
				'href'  => [],
			],
		];

		$pagination = wp_kses( paginate_links( array(
			'base'      => '#%#%',
			'current'   => $current_page,
			'total'     => $total_pages,
			'add_args'  => false,
			'prev_text' => __( '<i class="fa fa-arrow-left"></i> Previous' ),
			'next_text' => __( 'Next <i class="fa fa-arrow-right"></i>' ),
		) ), $allowed_tags );
	}

	wp_reset_postdata();

	$final_result["next_page_number"]  = $page_number + 1;
	$final_result["total_page"]        = $total_pages;
	$final_result["result_post"]       = $result_post;
	$final_result["result_pagination"] = ( $pagination ) ? $pagination : '';

	echo wp_json_encode( $final_result );

	wp_die();
}


/**
 * Job credit selection.
 */
function performancein_job_package_form_callback() {

	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );
	$result['success'] = false;
	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'job_package_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$filter            = array(
		'_wp_http_referer' => FILTER_SANITIZE_STRING,
		'product_quantity' => array(
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'flags'  => FILTER_REQUIRE_ARRAY
		),

	);
	$_job_package_data = filter_input_array( INPUT_POST, $filter );
	if ( is_array( $_job_package_data['product_quantity'] ) && ! array_filter( $_job_package_data['product_quantity'] ) ) {
		$result['msg']     = esc_html__( 'Please Choose credit.', 'performancein' );
		$result['success'] = false;
		echo wp_json_encode( $result );
		wp_die();
	}

	$product_quantity = $_job_package_data['product_quantity'];
	if ( isset( $product_quantity ) && is_array( $product_quantity ) && array_filter( $product_quantity ) ) {
		WC()->cart->empty_cart();
		$product_quantity     = array_filter( $product_quantity );
		$redirect_to_checkout = false;
		foreach ( $product_quantity as $product_id => $quantity ) {
			if ( ! is_user_logged_in() ) {
				setcookie( 'performancein_order', wp_json_encode( $product_quantity ), time() + 62208000, '/', $_SERVER['HTTP_HOST'] );
				$result['url']     = add_query_arg( array( 'referer' => rawurlencode( wp_get_raw_referer() ) ), site_url( 'account/login/' ) );
				$result['msg']     = esc_html__( 'Please Login first.', 'performancein' );
				$result['success'] = false;
				echo wp_json_encode( $result );
				wp_die();
			} else {
				WC()->cart->add_to_cart( $product_id, $quantity );
				$redirect_to_checkout = true;
			}
		}
		if ( true === $redirect_to_checkout ) {
			$result['url']     = wc_get_checkout_url();
			$result['msg']     = esc_html__( 'Package added successfully. ', 'performancein' );
			$result['success'] = true;
		}
	}
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_job_package_form', 'performancein_job_package_form_callback' );
add_action( 'wp_ajax_job_package_form', 'performancein_job_package_form_callback' );

/**
 * Recruiter form ajax callback.
 */
function performancein_recruiter_form_callback() {

	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'recruiter_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( is_user_logged_in() ) {
		$recruiter_name = filter_input( INPUT_POST, 'recruiter_name', FILTER_SANITIZE_STRING );
		/**
		 * Filter to resize the uploaded recruiter logo.
		 */
		add_filter( 'wp_generate_attachment_metadata', 'performancein_resize_uploaded_image' );
		$attachment_id = pi_process_image( 'image' );
		/**
		 * Filter to resize the uploaded recruiter logo.
		 */
		remove_filter( 'wp_generate_attachment_metadata', 'performancein_resize_uploaded_image' );
		$user_id = get_current_user_id();
		update_field( 'pi_recruiter_company_name', $recruiter_name, "user_{$user_id}" );
		update_field( 'pi_recruiter_logo', $attachment_id, "user_{$user_id}" );
		$result['msg']     = esc_html__( 'Data successfully saved.', 'performancein' );
		$result['success'] = true;
	} else {
		$result['msg'] = esc_html__( 'Security check failed. please login', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	echo wp_json_encode( $result );
	wp_die();

}

add_action( 'wp_ajax_nopriv_recruiter_form', 'performancein_recruiter_form_callback' );
add_action( 'wp_ajax_recruiter_form', 'performancein_recruiter_form_callback' );

function performancein_login_form_callback() {

	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'login_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$email    = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
	$password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$referer  = filter_input( INPUT_POST, 'referer', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Sanitize user input.

	if ( empty( $email ) || ! is_email( $email ) ) {
		$result['msg'] = esc_html__( 'Please provide a valid email address.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( ! email_exists( $email ) ) {
		$result['msg'] = esc_html__( 'Please enter valid email id.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $password ) ) {
		$result['msg'] = esc_html__( 'Please enter an account password.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$user = get_user_by( 'email', $email );
	if ( is_wp_error( $user ) ) {
		$result['msg'] = $user->get_error_message();
		echo wp_json_encode( $result );
		wp_die();
	}

	$info                  = array();
	$info['user_login']    = $email;
	$info['user_password'] = $password;
	$info['remember']      = true;

	$is_confirm        = get_the_author_meta( 'is_confirm', $user->ID );
	$user_roles        = $user->roles;
	$allow_login_roles = array( 'administrator', 'editor' );
	if ( true === (bool) $is_confirm || in_array( $user_roles[0], $allow_login_roles, true ) ) {
		remove_action( 'authenticate', 'gglcptch_login_check', 21 );
		$user_signon = wp_signon( $info );
		add_action( 'authenticate', 'gglcptch_login_check', 21, 1 );
		setcookie( "performancein_cookie", "", time() - 3600 );
		if ( is_wp_error( $user_signon ) ) {
			$result['msg'] = $user_signon->get_error_message();
			echo wp_json_encode( $result );
			wp_die();
		}
		if ( isset( $referer ) && ! empty( $referer ) ) {
			$url = site_url( $referer );
		} else {
			$url = site_url( '/account/details/' );
		}
		$result['msg']     = esc_html__( 'Login successfully.', 'performancein' );
		$result['url']     = $url;
		$result['success'] = true;

	} else {
		$result['msg'] = esc_html__( 'Your account is not confirmed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	echo wp_json_encode( $result );
	wp_die();

}

add_action( 'wp_ajax_nopriv_login_form', 'performancein_login_form_callback' );
add_action( 'wp_ajax_login_form', 'performancein_login_form_callback' );

/**
 * Registration form callback.
 */
function performancein_registration_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'registration_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$filter             = array(
		'email'               => FILTER_SANITIZE_EMAIL,
		'password'            => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'confirm_password'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'first_name'          => FILTER_SANITIZE_STRING,
		'last_name'           => FILTER_SANITIZE_STRING,
		'referer'             => FILTER_SANITIZE_STRING,
		'_wp_http_referer'    => FILTER_SANITIZE_STRING,
		'performancein_token' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,

	);
	$_registration_data = filter_input_array( INPUT_POST, $filter );
	$email              = $_registration_data['email'];
	$password           = $_registration_data['password'];
	$confirm_password   = $_registration_data['confirm_password'];
	$first_name         = $_registration_data['first_name'];
	$last_name          = $_registration_data['last_name'];


	if ( empty( $email ) || ! is_email( $email ) ) {
		$result['msg'] = esc_html__( 'Please provide a valid email address.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $email ) || email_exists( $email ) ) {
		$allow_html    = array( 'a' => array( 'href' => array() ) );
		$error_msg     = wp_kses( sprintf( '%s<a href="%s">%s</a>',
			esc_html__( 'An account with this email address already exists! You can reset your password.', 'performancein' ),
			esc_url( add_query_arg( array( 'email' => $email ), site_url( '/account/iforgot/' ) ) ),
			esc_html__( 'here', 'performancein' )
		), $allow_html );
		$result['msg'] = $error_msg;
		echo wp_json_encode( $result );
		wp_die();
	}

	$username = wc_create_new_customer_username( $email );
	$username = sanitize_user( $username );

	if ( empty( $username ) || ! validate_username( $username ) ) {
		$result['msg'] = esc_html__( 'Please enter a valid account username.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( username_exists( $username ) ) {
		$result['msg'] = esc_html__( 'An account is already registered with that username. Please choose another.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $password ) ) {
		$result['msg'] = esc_html__( 'Please enter an account password.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $confirm_password ) ) {
		$result['msg'] = esc_html__( 'Please fill the confirm password textbox.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( $password !== $confirm_password ) {
		$result['msg'] = esc_html__( 'The passwords do not match.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $first_name ) ) {
		$result['msg'] = esc_html__( 'Please enter first name.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $last_name ) ) {
		$result['msg'] = esc_html__( 'Please enter last name..', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( $password !== $confirm_password ) {
		$result['msg'] = esc_html__( 'Password dose not match.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$user_data           = array(
		'user_login' => $username,
		'user_email' => $email,
		'user_pass'  => $password,
		'first_name' => $first_name,
		'last_name'  => $last_name,
		'nickname'   => $first_name,
		'role'       => 'customer',
	);
	$new_customer_id     = wp_insert_user( $user_data );
	$performancein_order = json_decode( stripslashes( $_COOKIE["performancein_order"] ), true );
	update_user_meta( $new_customer_id, 'performancein_user_order', wp_json_encode( $performancein_order ) );
	setcookie( "performancein_order", "", time(), '/', $_SERVER['HTTP_HOST'] );
	setcookie( 'performancein_cookie', $email . '+' . $first_name, time() + 62208000, '/', $_SERVER['HTTP_HOST'] );

	if ( ! is_wp_error( $new_customer_id ) ) {
		$user_email_status = pi_wp_new_user_notification( $new_customer_id );
		update_user_meta( $new_customer_id, 'activation_email_status', $user_email_status );
		$result['msg']     = esc_html__( 'Register successfully.', 'performancein' );
		$result['url']     = site_url( 'account/complete-profile/' );
		$result['success'] = true;

	} else {
		$result['msg'] = $new_customer_id->get_error_message();
		echo wp_json_encode( $result );
		wp_die();
	}
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_registration_form', 'performancein_registration_form_callback' );
add_action( 'wp_ajax_registration_form', 'performancein_registration_form_callback' );

/**
 * Registration form callback.
 */
function performancein_complete_profile_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'complete_profile_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$filter                 = array(
		'company_name' => FILTER_SANITIZE_STRING,
		'job_title'    => FILTER_SANITIZE_STRING,
		'demographic'  => FILTER_SANITIZE_STRING,
		'user_email'   => FILTER_SANITIZE_STRING,
		'regions'      => array(
			'filter' => FILTER_SANITIZE_STRING,
			'flags'  => FILTER_REQUIRE_ARRAY
		),
		'verticals'    => array(
			'filter' => FILTER_SANITIZE_STRING,
			'flags'  => FILTER_REQUIRE_ARRAY
		),
		'topics'       => array(
			'filter' => FILTER_SANITIZE_STRING,
			'flags'  => FILTER_REQUIRE_ARRAY
		),
	);
	$_complete_profile_data = filter_input_array( INPUT_POST, $filter );
	$company_name           = $_complete_profile_data['company_name'];
	$demographic            = $_complete_profile_data['demographic'];
	$job_title              = $_complete_profile_data['job_title'];
	$user_email             = $_complete_profile_data['user_email'];
	$regions                = $_complete_profile_data['regions'];
	$verticals              = $_complete_profile_data['verticals'];
	$topics                 = $_complete_profile_data['topics'];

	if ( empty( $company_name ) ) {
		$result['msg'] = esc_html__( 'Please Enter Company name.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $demographic ) ) {
		$result['msg'] = esc_html__( 'Please select the Demographic.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $user_email ) ) {
		$result['msg'] = esc_html__( 'Something went wrong.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$user = get_user_by( 'email', $user_email );
	if ( is_wp_error( $user ) ) {
		$result['msg'] = $user->get_error_message();
		echo wp_json_encode( $result );
		wp_die();
	}
	update_user_meta( $user->ID, 'pi_company_name', $company_name );
	update_user_meta( $user->ID, 'pi_job_title', $job_title );
	update_user_meta( $user->ID, 'pi_demographic', $demographic );
	update_user_meta( $user->ID, 'pi_regions_of_interest', $regions );
	update_user_meta( $user->ID, 'pi_verticals', $verticals );
	update_user_meta( $user->ID, 'pi_topics', $topics );

	$is_link_with_google = get_user_meta( $user->ID, 'is_link_with_google', true );
	if ( true === (bool) $is_link_with_google ) {
		wp_set_auth_cookie( $user->ID );
		$result['msg']     = esc_html__( 'Profile successfully saved.', 'performancein' );
		$result['url']     = site_url( 'account/details/' );
		$result['success'] = true;
	}
	$result['msg']     = esc_html__( 'Profile successfully saved.', 'performancein' );
	$result['url']     = site_url( 'account/register/check-inbox/' );
	$result['success'] = true;

	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_complete_profile_form', 'performancein_complete_profile_form_callback' );
add_action( 'wp_ajax_complete_profile_form', 'performancein_complete_profile_form_callback' );


/**
 * Job save/edited job save and preview job callback.
 */
function performancein_job_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce      = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$job_id     = filter_input( INPUT_POST, 'job_id', FILTER_SANITIZE_NUMBER_INT );
	$is_preview = filter_input( INPUT_POST, 'is_preview', FILTER_VALIDATE_BOOLEAN );
	$is_edit    = false;
	if ( isset( $job_id ) && ! empty( $job_id ) ) {
		$is_edit      = true;
		$nonce_action = 'save_edited_job_form_nonce';
	} elseif ( isset( $is_preview ) && ! empty( $is_preview ) && true === $is_preview ) {
		$nonce        = filter_input( INPUT_POST, 'security_preview', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$nonce_action = 'preview_job_form_nonce';
	} else {
		$nonce_action = 'save_job_form_nonce';
	}
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, $nonce_action ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$product_id      = filter_input( INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT );
	$job_title       = filter_input( INPUT_POST, 'job_title', FILTER_SANITIZE_STRING );
	$job_type        = filter_input( INPUT_POST, 'job_type', FILTER_SANITIZE_STRING );
	$job_length      = filter_input( INPUT_POST, 'job_length', FILTER_SANITIZE_STRING );
	$job_area        = filter_input( INPUT_POST, 'job_area', FILTER_SANITIZE_STRING );
	$job_description = filter_input( INPUT_POST, 'job_description', FILTER_UNSAFE_RAW );
	$minimum_salary  = filter_input( INPUT_POST, 'minimum_salary', FILTER_SANITIZE_NUMBER_INT );
	$maximum_salary  = filter_input( INPUT_POST, 'maximum_salary', FILTER_SANITIZE_NUMBER_INT );
	$closing_date    = filter_input( INPUT_POST, 'closing_date', FILTER_SANITIZE_STRING );
	$contact_phone   = filter_input( INPUT_POST, 'contact_phone', FILTER_SANITIZE_STRING );
	$contact_email   = filter_input( INPUT_POST, 'contact_email', FILTER_SANITIZE_EMAIL );
	$street_address  = filter_input( INPUT_POST, 'street_address', FILTER_UNSAFE_RAW );
	$post_code       = filter_input( INPUT_POST, 'post_code', FILTER_SANITIZE_STRING );
	$address_region  = filter_input( INPUT_POST, 'address_region', FILTER_SANITIZE_STRING );
	$address_country = filter_input( INPUT_POST, 'address_country', FILTER_SANITIZE_STRING );


	$filter             = array(
		'categories' => array(
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'flags'  => FILTER_REQUIRE_ARRAY
		),
	);
	$_save_new_job_data = filter_input_array( INPUT_POST, $filter );

	$job_categories = $_save_new_job_data['categories'];
	if ( empty( get_current_user_id() ) ) {
		$result['msg'] = esc_html__( 'Please login first.', 'performancein' );
		$result['url'] = site_url( 'account/login/' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $product_id ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $job_title ) ) {
		$result['msg'] = esc_html__( 'Please enter job title.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $job_type ) ) {
		$result['msg'] = esc_html__( 'Please select job type.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $job_length ) ) {
		$result['msg'] = esc_html__( 'Please select job length.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $job_area ) ) {
		$result['msg'] = esc_html__( 'Please select job area.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $job_description ) ) {
		$result['msg'] = esc_html__( 'Please enter description.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
//	if ( empty( $minimum_salary ) || ! is_numeric( $minimum_salary ) ) {
//		$result['msg'] = esc_html__( 'Please enter valid salary amount.', 'performancein' );
//		echo wp_json_encode( $result );
//		wp_die();
//	}
//	if ( empty( $maximum_salary ) || ! is_numeric( $maximum_salary ) ) {
//		$result['msg'] = esc_html__( 'Please enter valid salary amount.', 'performancein' );
//		echo wp_json_encode( $result );
//		wp_die();
//	}
	if ( $minimum_salary > $maximum_salary ) {
		$result['msg'] = esc_html__( 'The maximum salary must be larger than the minimum salary.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( count( $job_categories ) > 2 ) {
		$result['msg'] = esc_html__( 'You must select 1 or 2 categories!', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $closing_date ) ) {
		$result['msg'] = esc_html__( 'Please select closing date.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$today = date( "Y-m-d" );
	if ( $today > $closing_date ) {
		$result['msg'] = esc_html__( 'The closing date cannot be set in the past!', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$_duration_days = get_post_meta( $product_id, '_duration_days', true );

	if ( true === $is_edit ) {
		if ( ! empty( $closing_date ) ) {
			$created_date_human_day_diff = pi_get_since_added_days( get_the_date( 'd-m-Y', $job_id ) );
			$job_max_limit_date          = $_duration_days - $created_date_human_day_diff;
			$job_duration_date           = Date( 'Y/m/d', strtotime( "+{$job_max_limit_date} days" ) );

			if ( $closing_date > $job_duration_date ) {
				$result['msg'] = esc_html__( "The closing date must on or before {$job_duration_date}", 'performancein' );
				echo wp_json_encode( $result );
				wp_die();
			}
		}
	} else {
		$job_duration_date = Date( 'Y/m/d', strtotime( "+{$_duration_days} days" ) );
		if ( $closing_date > $job_duration_date ) {
			$result['msg'] = esc_html__( "The closing date must on or before {$job_duration_date}", 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		}
	}


	if ( empty( $contact_phone ) ) {
		$result['msg'] = esc_html__( 'Please enter phone number', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( ! preg_match( "/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/", $contact_phone ) ) {
		$result['msg'] = esc_html__( 'Please valid phone number.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $contact_email ) || ! is_email( $contact_email ) ) {
		$result['msg'] = esc_html__( 'Please provide a valid email address.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( true !== $is_preview ) {
		if ( false === $is_edit ) {
			$pi_credit_package = pi_get_credit_package( get_current_user_id() );
			$user_credit       = pi_get_credit( $pi_credit_package, $product_id );

			if ( 1 > $user_credit ) {
				$result['msg'] = esc_html__( 'Please buy a credit.', 'performancein' );
				$result['url'] = site_url( '/order/jobs/' );
				echo wp_json_encode( $result );
				wp_die();
			}
		}
	}
	if ( is_user_logged_in() ) {
		if ( isset( $is_preview ) && ! empty( $is_preview ) && true === $is_preview ) {
			ob_start();
			get_header();
			$author_id              = get_current_user_id();
			$recruiter_company_name = get_field( 'pi_recruiter_company_name', "user_{$author_id}" );
			$recruiter_company_name = ( isset( $recruiter_company_name ) && ! empty( $recruiter_company_name ) ) ? $recruiter_company_name : '';
			$recruiter_logo_url     = pi_get_recruiter_logo( $author_id );
			$salary                 = pi_get_salary( $minimum_salary, $maximum_salary );
			$is_featured            = pi_is_featured_package( $product_id );
			?>
            <div class="grid mainContent clearfix" id="js-mainContent" role="main">
                <section class="content contentWithSidebar">
                    <article class="articlefull job ">
                        <header class="jobFull-header">
							<span
                                    class="jobPreview animated infinite flash"><?php esc_html_e( 'PREVIEW', 'performancein' ); ?></span>
							<?php
							if ( true === $is_featured ) {
								if ( isset( $recruiter_logo_url ) && ! empty( $recruiter_logo_url ) ) { ?>
                                    <a href="<?php the_permalink(); ?>" class="job-recruiter-logo">
                                        <img src="<?php echo esc_url( $recruiter_logo_url ); ?>"
                                             alt="<?php echo esc_attr( $recruiter_company_name ); ?>">
                                    </a>
									<?php
								}
							}
							?>
                            <h1>
								<?php echo $job_title; ?><br>

								<?php if ( isset( $salary ) && '' !== $salary ) { ?>
                                    <span class="jobsalary"><?php echo esc_html( $salary ); ?></span>
                                    <span class="jobDivider">/</span>
								<?php } ?>
								<?php if ( isset( $job_type ) && ! empty( $job_type ) ) { ?>
                                    <span class="jobtype"><?php echo esc_html( $job_type ); ?></span>
								<?php } ?>
                            </h1>

                            <h2>
								<?php if ( isset( $job_area ) ) {
									?>
                                    <span data-icon="&#xe013;"></span><?php echo esc_html( $job_area ); ?> /
									<?php if ( isset( $job_length ) ) {
										echo esc_html( $job_length );
									} ?>
								<?php } ?>
                            </h2>

                            <div class="meta">
								<?php if ( isset( $recruiter_company_name ) && ! empty( $recruiter_company_name ) ) { ?>
                                    <span class="recruiter">
                                <span data-icon="&#xe029;"></span>
							<?php echo esc_html( $recruiter_company_name ); ?>
                            </span>
								<?php } ?>
                                <span class="date"><span data-icon="&#xf073;"></span>
	                            <?php
	                            esc_html_e( 'Closes: ', 'performancein' );
	                            echo esc_html( date_format( date_create( $closing_date ), "d/m/y" ) );
	                            ?>
                            </span>
                            </div>
                        </header>
                        <section class="articlecont">
							<?php echo wp_kses_post( $job_description ); ?>
                        </section>
                    </article>
                </section>
				<?php get_sidebar(); ?>
            </div>
			<?php
			get_footer();
			$html              = ob_get_clean();
			$result['msg']     = esc_html__( 'Preview success.', 'performancein' );
			$result['preview'] = true;
			$result['html']    = $html;
		} else {
			$user_data = get_user_meta( get_current_user_id(), 'pi_recruiter_company_name', true );
			$job_arg   = array(
				'post_title'   => $job_title,
				'post_content' => $job_description,
				'post_type'    => 'pi_jobs',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'tax_input'    => array(
					"pi_cat_jobs" => $job_categories //Video Cateogry is Taxnmony Name and being used as key of array.
				),
			);
			if ( true === $is_edit ) {
				$job_arg['ID'] = $job_id;
				$post_id       = wp_update_post( $job_arg );
			} else {
				$post_id = wp_insert_post( $job_arg );
				if ( isset( $post_id ) && ! empty( $post_id ) ) {
					if ( isset( $user_data ) && ! empty( $user_data ) ) {
						update_field( 'pi_jobs_employer', $user_data, $post_id );
					}
				}
			}

			if ( is_wp_error( $post_id ) ) {
				wp_send_json_error( $post_id->get_error_message() );
				$result['msg'] = $post_id->get_error_message();
				echo wp_json_encode( $result );
				wp_die();
			}

			wp_set_post_terms( $post_id, $job_categories, 'pi_cat_jobs' );
			if ( false === $is_edit ) {

				$update_credit_package = pi_update_credit( $pi_credit_package, ( $user_credit - 1 ), $product_id );
				update_field( 'pi_credit_package', $update_credit_package, 'user_' . get_current_user_id() );
			}

			update_field( 'pi_job_type', $job_type, $post_id );
			update_field( 'pi_contract_length', $job_length, $post_id );
			update_field( 'pi_geographic_location', $job_area, $post_id );
			update_field( 'pi_description', $job_description, $post_id );
			update_field( 'pi_minimum_salary', $minimum_salary, $post_id );
			update_field( 'pi_maximum_salary', $maximum_salary, $post_id );
			$closing_date = str_replace( '/', '', $closing_date );
			update_field( 'pi_closing_date', $closing_date, $post_id );
			update_field( 'pi_contact_phone', $contact_phone, $post_id );
			update_field( 'pi_contact_email', $contact_email, $post_id );
			update_field( 'pi_jobs_schema_streetaddress', $street_address, $post_id );
			update_field( 'pi_jobs_schema_postalcode', $post_code, $post_id );
			update_field( 'pi_jobs_schema_addressregion', $address_region, $post_id );
			update_field( 'pi_jobs_schema_addresscountry', $address_country, $post_id );

			update_field( 'pi_jobs_packages', $product_id, $post_id ); // job Packages
			if ( isset( $job_id ) && ! empty( $job_id ) ) {
				$result['msg'] = esc_html__( 'Job successfully updated.', 'performancein' );
			} else {
				$result['msg'] = esc_html__( 'Job successfully published.', 'performancein' );
			}
			$result['url'] = get_the_permalink( $post_id );
		}
		$result['success'] = true;
	}
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_save_job_form', 'performancein_job_form_callback' );
add_action( 'wp_ajax_save_job_form', 'performancein_job_form_callback' );

/**
 * Iforgot callback ajax function.
 */
function performancein_iforgot_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'iforgot_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( is_user_logged_in() ) {
		$result['msg'] = esc_html__( 'Already logged in.', 'performancein' );
		$result['url'] = site_url( '/account/details/' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$email = filter_input( INPUT_POST, 'id_email', FILTER_SANITIZE_EMAIL );
	if ( empty( $email ) || ! is_email( $email ) ) {
		$result['msg'] = esc_html__( 'Please provide a valid email address.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}


	if ( ! email_exists( $email ) ) {
		$result['msg'] = esc_html__( 'Please provide a valid user email.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$status = pi_retrieve_password( $email );
	if ( true === $status ) {
		$result['msg']     = esc_html__( 'A password reset link has been sent to your email inbox.', 'performancein' );
		$result['success'] = true;
	} else {
		$result['msg']     = esc_html__( 'Something went wrong in sending email.', 'performancein' );
		$result['success'] = false;
	}
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_iforgot_form', 'performancein_iforgot_form_callback' );
add_action( 'wp_ajax_iforgot_form', 'performancein_iforgot_form_callback' );

/**
 * Password reset ajax function.
 */
function performancein_password_reset_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'password_reset_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( is_user_logged_in() ) {
		$result['msg'] = esc_html__( 'Already logged in.', 'performancein' );
		$result['url'] = site_url( '/account/details/' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$email            = filter_input( INPUT_POST, 'id_email', FILTER_SANITIZE_EMAIL );
	$code             = filter_input( INPUT_POST, 'id_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$password         = filter_input( INPUT_POST, 'id_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$confirm_password = filter_input( INPUT_POST, 'id_confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	if ( empty( $email ) || ! is_email( $email ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( ! email_exists( $email ) ) {
		$result['msg'] = esc_html__( 'Please open valid reset url.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $code ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $password ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $confirm_password ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( $password !== $confirm_password ) {
		$result['msg'] = esc_html__( 'The passwords do not match.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$user = get_user_by( 'email', $email );
	if ( '' === $user->user_activation_key ) {
		$allow_html    = array( 'a' => array( 'href' => array() ) );
		$error_msg     = wp_kses( sprintf( '%s <a href="%s">%s</a>',
			esc_html__( 'Password already already update! You can reset your password again.', 'performancein' ),
			esc_url( add_query_arg( array( 'email' => $email ), site_url( '/account/iforgot/' ) ) ),
			esc_html__( 'here', 'performancein' )
		), $allow_html );
		$result['msg'] = $error_msg;
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( $code !== $user->user_activation_key ) {
		$allow_html    = array( 'a' => array( 'href' => array() ) );
		$error_msg     = wp_kses( sprintf( '%s <a href="%s">%s</a>',
			esc_html__( 'Password token is mismatch! please reset your password again.', 'performancein' ),
			esc_url( add_query_arg( array( 'email' => $email ), site_url( '/account/iforgot/' ) ) ),
			esc_html__( 'here', 'performancein' )
		), $allow_html );
		$result['msg'] = $error_msg;
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( ! is_wp_error( $user ) ) {
		wp_set_password( $password, $user->ID );
		$result['msg']     = esc_html__( 'Password successfully updated.', 'performancein' );
		$result['url']     = site_url( '/account/login' );
		$result['success'] = true;
	} else {
		$result['msg'] = $user->get_error_message();
		echo wp_json_encode( $result );
		wp_die();
	}
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_password_reset_form', 'performancein_password_reset_form_callback' );
add_action( 'wp_ajax_password_reset_form', 'performancein_password_reset_form_callback' );

/**
 * Apply job form ajax callback function.
 */
function performancein_apply_job_form_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'job_apply_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$job_id            = filter_input( INPUT_POST, 'job_id', FILTER_SANITIZE_NUMBER_INT );
	$email_id          = filter_input( INPUT_POST, 'email_id', FILTER_SANITIZE_EMAIL );
	$product_id        = filter_input( INPUT_POST, 'id_product', FILTER_SANITIZE_NUMBER_INT );
	$cover_description = filter_input( INPUT_POST, 'cover_description', FILTER_UNSAFE_RAW );
	$email             = filter_input( INPUT_POST, 'email', FILTER_UNSAFE_RAW );

	if ( empty( $email ) ) {
		$result['msg'] = esc_html__( 'Enter your Email.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $job_id ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $cover_description ) ) {
		$result['msg'] = esc_html__( 'Please enter cover descirption.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( ! isset( $_FILES ) || empty( $_FILES['resume'] ) ) {
		$result['msg'] = esc_html__( 'Please select your resume/CV.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( ! empty( $_FILES['resume'] ) ) {
		$acceptable = array(
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);
		if ( ( ! in_array( $_FILES['resume']['type'], $acceptable ) ) && ( ! empty( $_FILES["resume"]["type"] ) ) ) {
			$result['msg'] = esc_html__( 'This file is not allowed. Try again with PDF, DOC, DOCX file.', 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		}
	}

	$attachment_id = pi_process_image( 'resume' );

	$post_name = $email . ' - ' . get_the_title( $job_id );

	$apply_arg = array(
		'post_title'   => $post_name,
		'post_content' => $cover_description,
		'post_type'    => 'pi_applied_jobs',
		'post_status'  => 'publish',
		'post_parent'  => $job_id,
	);

	$post_id = wp_insert_post( $apply_arg );
	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( $post_id->get_error_message() );
		$result['msg'] = $post_id->get_error_message();
		echo wp_json_encode( $result );
		wp_die();
	}
	$email_body_content = array(
		'email'             => $email,
		'cover_description' => $cover_description,
		'attachment_id'     => $attachment_id,
		'job_id'            => $job_id,
		'to_email'          => $email_id,
		'job_type'          => $product_id,
	);
	$email_status       = pi_job_applied_send_email( $email_body_content );
	update_post_meta( $email_status, 'applied_job_email_status', $email_status );
	update_field( 'pi_resumecv', $attachment_id, $post_id );
	update_field( 'pi_jobs', $job_id, $post_id );

	$result['msg']          = esc_html__( 'Your job application has been sent!', 'performancein' );
	$result['url']          = get_the_permalink( $post_id );
	$result['email_status'] = $email_status;
	$result['success']      = true;

	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_apply_job_form', 'performancein_apply_job_form_callback' );
add_action( 'wp_ajax_apply_job_form', 'performancein_apply_job_form_callback' );


function performancein_company_profile_form_submit() {

	global $wpdb;

	$result            = array();
	$result['success'] = 0;
	$result['msg']     = esc_html__( 'Something went wrong.', 'performancein' );

	// Sanitize user input.
	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'company_profile_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$account   = filter_input( INPUT_POST, 'account', FILTER_SANITIZE_STRING );
	$is_active = filter_input( INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN );


	$company_id           = filter_input( INPUT_POST, 'company_id', FILTER_SANITIZE_STRING );
	$product_id           = filter_input( INPUT_POST, 'product', FILTER_SANITIZE_STRING );
	$company_name         = filter_input( INPUT_POST, 'company_name', FILTER_SANITIZE_STRING );
	$company_email        = filter_input( INPUT_POST, 'company_email', FILTER_SANITIZE_EMAIL );
	$logo                 = filter_input( INPUT_POST, 'logo', FILTER_SANITIZE_NUMBER_INT );
	$is_logo_uploaded     = filter_input( INPUT_POST, 'is_logo_uploaded', FILTER_SANITIZE_STRING );
	$company_description  = filter_input( INPUT_POST, 'company_description', FILTER_UNSAFE_RAW );
	$website_url          = filter_input( INPUT_POST, 'website_url', FILTER_SANITIZE_URL );
	$address1             = filter_input( INPUT_POST, 'address1', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$address2             = filter_input( INPUT_POST, 'address2', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$city                 = filter_input( INPUT_POST, 'city', FILTER_SANITIZE_STRING );
	$postcode             = filter_input( INPUT_POST, 'postcode', FILTER_SANITIZE_STRING );
	$tags                 = filter_input( INPUT_POST, 'tags', FILTER_SANITIZE_STRING );
	$tags                 = explode( ',', $tags );
	$country              = filter_input( INPUT_POST, 'country', FILTER_SANITIZE_STRING );
	$telephone_number     = filter_input( INPUT_POST, 'telephone_number', FILTER_SANITIZE_STRING );
	$facebook_profile     = filter_input( INPUT_POST, 'facebook_profile', FILTER_SANITIZE_URL );
	$twitter_profile      = filter_input( INPUT_POST, 'twitter_profile', FILTER_SANITIZE_URL );
	$linkedin_profile     = filter_input( INPUT_POST, 'linkedin_profile', FILTER_SANITIZE_URL );
	$founded_year         = filter_input( INPUT_POST, 'founded_year', FILTER_SANITIZE_NUMBER_INT );
	$number_of_staff      = filter_input( INPUT_POST, 'number_of_staff', FILTER_SANITIZE_URL );
	$client_testimonial_1 = filter_input( INPUT_POST, 'client_testimonial_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$client_testimonial_2 = filter_input( INPUT_POST, 'client_testimonial_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$client_testimonial_3 = filter_input( INPUT_POST, 'client_testimonial_3', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( false === pi_is_partner_account( wp_get_current_user() ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( 0 === $company_id ) {
		$result['msg'] = esc_html__( 'Please contact to admin because your company is not created.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $product_id ) ) {
		$result['msg'] = esc_html__( 'Something went wrong in hidden fields.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $company_name ) ) {
		$result['msg'] = esc_html__( 'Please enter company name.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $is_logo_uploaded ) ) {
		$result['msg'] = esc_html__( 'Please upload company logo.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	/*if ( ! isset( $_FILES ) || empty( $_FILES['logo'] ) ) {
		$result['msg'] = esc_html__( 'Please upload company logo.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}*/

	if ( ! empty( $_FILES['logo'] ) ) {
		$acceptable = array(
			'image/jpeg',
			'image/jpg',
			'image/png'
		);
		if ( ( ! in_array( $_FILES['logo']['type'], $acceptable ) ) && ( ! empty( $_FILES["logo"]["type"] ) ) ) {
			$result['msg'] = esc_html__( 'Logo: Invalid file type. Only JPG and PNG types are accepted.', 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		}
	}
	$custom_header_id = '';
	if ( ! empty( $_FILES['custom_header'] ) ) {
		$acceptable = array(
			'image/jpeg',
			'image/jpg',
		);
		if ( ( ! in_array( $_FILES['custom_header']['type'], $acceptable ) ) && ( ! empty( $_FILES["custom_header"]["type"] ) ) ) {
			$result['msg'] = esc_html__( 'Custom Header: Invalid file type. Only JPG types are accepted.', 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		}
		$custom_header_id = pi_process_image( 'custom_header' );
	}

	$partner_post = array(
		'ID'         => $company_id,
		'post_title' => $company_name,

	);

	remove_action( 'save_post_pi_partner_networks', 'save_partner_post' );

	if ( ! $is_active ) {
		$partner_post['post_status'] = 'draft';
		$partner_id                  = wp_update_post( $partner_post );
	} else {
		$partner_id = wp_update_post( $partner_post );
		$wpdb->query( "UPDATE wp_posts SET post_status='publish' WHERE ID=" . $company_id );
	}

	add_action( 'save_post_pi_partner_networks', 'save_partner_post', 10, 3 );

	$logo_id = pi_process_image( 'logo' );
	set_post_thumbnail( $company_id, $logo_id );

	update_field( 'pi_partner_sidebar_pi_contact_info_pi_email_id', $company_email, $partner_id );

	update_field( 'pi_partner_description_pi_partner_description_title', 'Profile', $partner_id );
	update_field( 'pi_partner_description_pi_partner_description', $company_description, $partner_id );
	if ( isset( $custom_header_id ) && ! empty( $custom_header_id ) ) {
		update_field( 'pi_partner_network_banner_image', $custom_header_id, $partner_id );
	}

	if ( $client_testimonial_1 || $client_testimonial_2 || $client_testimonial_3 ) {

		$cli_menu = get_field( 'pi_partner_page_sub_menu', $partner_id );
		array_push( $cli_menu, 'client_testimonials' );
		update_field( 'pi_partner_page_sub_menu', $cli_menu, $partner_id );
		update_field( 'pi_client_testimonials_pi_client_testimonial1', $client_testimonial_1, $partner_id );
		update_field( 'pi_client_testimonials_pi_client_testimonial2', $client_testimonial_2, $partner_id );
		update_field( 'pi_client_testimonials_pi_client_testimonial3', $client_testimonial_3, $partner_id );
	}

	update_field( 'pi_partner_sidebar_pi_contact_info_pi_website_url', $website_url, $partner_id );
	update_field( 'pi_partner_sidebar_pi_head_office_info_pi_address1', $address1, $partner_id );
	update_field( 'pi_partner_sidebar_pi_head_office_info_pi_address2', $address2, $partner_id );
	update_field( 'pi_partner_sidebar_pi_head_office_info_pi_city', $city, $partner_id );
	update_field( 'pi_partner_sidebar_pi_head_office_info_pi_postcode', $postcode, $partner_id );

	update_field( 'pi_partner_key_services_pi_partner_key_services_title', 'Key Services', $partner_id );
	update_field( 'pi_partner_key_services_pi_partner_tags', $tags, $partner_id );

	update_field( 'pi_partner_sidebar_pi_head_office_info_pi_country', $country, $partner_id );
	update_field( 'pi_partner_sidebar_pi_contact_info_pi_telephone_number', $telephone_number, $partner_id );
	update_field( 'pi_facebook_link', $facebook_profile, $partner_id );
	update_field( 'pi_twitter_link', $twitter_profile, $partner_id );
	update_field( 'pi_linkedin_link', $linkedin_profile, $partner_id );
	update_field( 'pi_partner_sidebar_pi_further_info_pi_founded_year', $founded_year, $partner_id );
	update_field( 'pi_partner_sidebar_pi_further_info_pi_number_of_staff', $number_of_staff, $partner_id );

	$result['success'] = true;
	$result['msg']     = esc_html__( 'Company data successfully updated.', 'performancein' );
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_company_profile_form', 'performancein_company_profile_form_submit' );
add_action( 'wp_ajax_nopriv_company_profile_form', 'performancein_company_profile_form_submit' );


function pi_resources_category_listing() {
	$result            = array();
	$result['success'] = true;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$postPerPage = get_field( 'pi_category_post_per_page', 'option' );
	$page_number = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$piTermID    = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );
	/*$args        = array(
		'post_type'           => array( 'post', 'pi_resources' ),
		'fields'              => 'ids',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $postPerPage,
		'paged'               => $page_number,
		'tax_query'           => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $piTermID
			)
		)
	);*/
	$args      = array(
		'post_type'           => 'pi_resources',
		'fields'              => 'ids',
		'ignore_sticky_posts' => 1,
		'post_status'         => 'publish',
		'posts_per_page'      => $postPerPage,
		'paged'               => $page_number,
	);
	$the_query = new WP_Query( $args );
	ob_start();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$pi_the_image_shown_on_article_lists_ids = get_field( 'pi_the_image_shown_on_article_lists', get_the_ID() );
			$post_image                              = wp_get_attachment_image_src( $pi_the_image_shown_on_article_lists_ids, 'full' );
			$placeHolderImageID                      = get_field( 'pi_article_placeholder_image', 'option' );
			$placeHolderImageSrc                     = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
			//$post_image                              = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
			$image_alt           = get_post_meta( $pi_the_image_shown_on_article_lists_ids, '_wp_attachment_image_alt', true );
			$pi_landing_page_url = get_field( 'pi_landing_page_url', get_the_ID() );
			$post_title          = get_the_title();
			$post_terms          = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
			$DocumentsSrc        = get_field( 'pi_resource_document', get_the_ID() );
			$DocumentsSrc        = ! empty( $DocumentsSrc ) ? $DocumentsSrc : '#';
			$pi_landing_page_url = ! empty( $pi_landing_page_url ) ? $pi_landing_page_url : $DocumentsSrc;
			$pi_img_attri_data   = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $pi_the_image_shown_on_article_lists_ids ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );


			if ( 1 > count( $post_terms ) ) {
				if ( ! empty( $post_terms ) && is_array( $post_terms ) ) {
					$pi_term_name = array();
					$pi_term_link = array();

					foreach ( $post_terms as $post_term ) {
						$pi_term_slug = $post_term->slug;
						if ( 'reports' !== $pi_term_slug ) {
							$pi_term_name[] = $post_term->name;
							$term_id        = $post_term->term_id;
							$pi_term_link[] = get_term_link( $term_id );
						}

					}
					$pi_term_name = $pi_term_name[0];
					$pi_term_link = $pi_term_link[0];
				}
			} else {
				if ( ! empty( $post_terms ) && is_array( $post_terms ) ) {
					$pi_term_name = array();
					$pi_term_link = array();

					foreach ( $post_terms as $post_term ) {
						$pi_term_name[] = $post_term->name;
						$term_id        = $post_term->term_id;
						$pi_term_link[] = get_term_link( $term_id );
					}
					$pi_term_name = $pi_term_name[0];
					$pi_term_link = $pi_term_link[0];
				}
			}
			?>
            <article class="pi-articleListItem">
                <div class="pi-article-item-inner">
                    <div class="pi-post-thumbnail">
                        <a href="<?php echo esc_url( $pi_landing_page_url ); ?>" class="articleListItem-link responsively-lazy">
                            <img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" class="articleListItem-image responsively-lazy-loaded"/>
                        </a>
                    </div>
                    <div class="pi-news-details">
                        <a href="<?php echo esc_url( $pi_landing_page_url ); ?>" class="pi-articleListItem-link">
                            <h2 class="title"><?php esc_html_e( $post_title, 'performancein' ); ?></h2>
                        </a>
						<?php
						if ( ! empty( $post_terms ) ) { ?>
                            <ul class="pi-category-list">
                                <li class="pi-listCategories-item">
                                    <a href="<?php echo $pi_term_link; ?>"><?php esc_html_e( $pi_term_name, 'performancein' ); ?></a>
                                </li>
                            </ul>
						<?php }
						?>
                        <time class="pi-articleListItem-date"
                              datetime="<?php echo get_the_date( 'F j, Y' ); ?>"><?php echo get_the_date( 'd M y' ); ?></time>
                    </div>
                </div>
            </article>
			<?php
			?>


		<?php }
	} else {
		echo '';
	}
	$result['html'] = ob_get_clean();

	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_pi_resources_category_listing', 'pi_resources_category_listing' );
add_action( 'wp_ajax_pi_resources_category_listing', 'pi_resources_category_listing' );
/**
 * Ajax to serve pagination for category page
 */
function performancein_post_category_listing_callback() {
	$result            = array();
	$result['success'] = true;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$postPerPage = get_field( 'pi_category_post_per_page', 'option' );
	$page_number = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$piTermID    = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );
	$args        = array(
		'post_type'      => 'post',
		'fields'         => 'ids',
		'posts_per_page' => $postPerPage,
		'post_status'    => 'publish',
		'paged'          => $page_number,
		'tax_query'      => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $piTermID
			)
		)
	);
	$piTermobj   = get_term_by( 'id', $piTermID, 'category' );
	$piTermName  = $piTermobj->name;
	$the_query   = new WP_Query( $args );
	ob_start();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$postID                 = get_the_ID();
			$pi_primary_category_id = get_field( 'pi_primary_category', $postID );
			if ( empty( $pi_primary_category_id ) ) {
				$post_terms   = wp_get_post_terms( $postID, 'category', array( 'orderby' => 'term_order' ) );
				$pi_term_name = array();
				$pi_term_link = array();
				foreach ( $post_terms as $post_term ) {
					$pi_term_name[] = $post_term->name;
					$term_id        = $post_term->term_id;
					$pi_term_link[] = get_term_link( $term_id );
				}
				$pi_term_name = $pi_term_name[0];
				$pi_term_link = $pi_term_link[0];
			} else {
				$pi_term_link = get_term_link( $pi_primary_category_id );
				$pi_term      = get_term( $pi_primary_category_id );
				$pi_term_name = $pi_term->name;
			}
			$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', $postID );
			$post_image_id                     = get_field( 'pi_article_image', $postID );
			$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
			$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
			$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
			//$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
			$post_permalink           = get_the_permalink();
			$post_title               = get_the_title();
			$articleFlagCategory      = wp_get_post_terms( $postID, 'category', array( 'orderby' => 'term_order' ) );
			$pi_img_attri_data        = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
			$articleFlagCategoryArray = array();
			$flagTermName             = array();
			$flagTermLink             = array();
			foreach ( $articleFlagCategory as $articleFlagCategoryList ) {
				$parent_category = $articleFlagCategoryList->parent;
				if ( 0 !== $parent_category ) {
					$parent = get_term_by( 'id', $parent_category, 'category' );
					if ( ! empty( $parent ) ) {
						$articleFlagCategoryArray[] = $parent->name;
						$termArray                  = $articleFlagCategoryList;
						$flagTermName[]             = $termArray->name;
						$flagTermID                 = $termArray->term_id;
						$flagTermLink[]             = get_term_link( $flagTermID );
					}
				}
			}
			if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
				$flagTermLink = $flagTermLink[0];
				$flagTermName = $flagTermName[0];
			}

			$categories          = wp_get_post_categories( $postID );
			$pi_primary_category = array(
				get_field( 'pi_primary_category', $postID ),
			);
			if ( ! empty( $pi_primary_category ) ) {
				$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
				$categories = array_unique( $categories );
			}
			$CategorriesFindRegionalName = array();
			$CategorriesFindRegionalLink = array();
			foreach ( $categories as $category ) {
				$piCategoryObj          = get_term_by( 'id', $category, 'category' );
				$piParentCategoryParent = $piCategoryObj->parent;
				$parentIDObj            = get_term_by( 'id', $piParentCategoryParent, 'category' );
				if ( ! empty( $parentIDObj ) ) {
					$CategorriesParentRegional = $parentIDObj->slug;
					if ( 'regional' === $CategorriesParentRegional ) {
						$piCategoryObj                 = get_term_by( 'id', $category, 'category' );
						$CategorriesFindRegionalName[] = $piCategoryObj->name;
						$CategorriesFindRegionalLink[] = get_term_link( $piCategoryObj->term_id );
					}
				}


			}
			if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
				$CategorriesFindRegionalLink = $CategorriesFindRegionalLink[0];
				$CategorriesFindRegionalName = $CategorriesFindRegionalName[0];
			}

			?>
            <article class="pi-articleListItem">
                <div class="pi-article-item-inner">
					<?php if ( 'image' === $pi_article_banner_section_choices ) { ?>
                        <div class="pi-post-thumbnail">
                            <a href="<?php echo esc_url( $post_permalink ); ?>">
                                <img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"/>
                            </a>
                        </div>
						<?php
					} elseif ( 'video' === $pi_article_banner_section_choices ) {
						$post_image_id = get_field( 'pi_article_video_thumbnail', $postID );
						$post_image    = wp_get_attachment_image_src( $post_image_id, 'full' );
//						$post_image    = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
						$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
						?>
                        <div class="pi-post-thumbnail">
                            <a href="<?php echo esc_url( $post_permalink ); ?>">
                                <img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"/>
                                <div class="pi-videoIcon"></div>
                            </a>
                        </div>
					<?php } else {
						$post_image_id = get_field( 'pi_article_image_gallery_thumbnail', $postID );
						$post_image    = wp_get_attachment_image_src( $post_image_id, 'full' );
//						$post_image    = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
						$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
						?>
                        <div class="pi-post-thumbnail">
                            <a href="<?php echo esc_url( $post_permalink ); ?>">
                                <img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"/>
                            </a>
                        </div>
					<?php }
					?>
                    <div class="pi-news-details">
						<?php
						if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
							if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { ?>
                                <a href="<?php echo $CategorriesFindRegionalLink; ?>" class="articleRegionalFlag"><?php echo esc_html( $CategorriesFindRegionalName ); ?></a>
							<?php }

						}
						?>
						<?php
						if ( ! empty( $post_title ) ):?>
                            <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                                <h2 class="title"><?php echo esc_html( $post_title ); ?></h2></a>
						<?php endif; ?>
                        <ul class="pi-category-list">
                            <li class="pi-listCategories-item">
                                <a href="<?php echo $pi_term_link; ?>"><?php echo esc_html( $pi_term_name ); ?></a>
                            </li>
							<?php if ( ! empty( get_field( 'pi_partners', $postID ) ) ) {
								$partners = get_field( 'pi_partners', $postID );
								?>
                                <li class="pi-listCategories-item pi-listCategories-item-partner">
                                    <a href="<?php echo esc_url( get_the_permalink( $partners ) ) ?>"><?php esc_html_e( 'Partner Networks' ); ?></a>
                                </li>
							<?php } elseif ( ! empty( get_field( 'pi_sponsored', $postID ) ) ) { ?>

                                <li class="pi-listCategories-item pi-listCategories-item-sponsored">
									<?php esc_html_e( 'Sponsored', 'performacein' ); ?>
                                </li>
							<?php } ?>
                        </ul>
                        <time class="pi-articleListItem-date"
                              datetime="<?php echo get_the_date( 'F j, Y' ); ?>"><?php echo get_the_date( 'd M y' ); ?></time>
                    </div>
                </div>
            </article>
		<?php }
	} else {
		echo '';
	}
	$result['html'] = ob_get_clean();

	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();

}

add_action( 'wp_ajax_nopriv_pi_post_category_listing', 'performancein_post_category_listing_callback' );
add_action( 'wp_ajax_pi_post_category_listing', 'performancein_post_category_listing_callback' );


function performancein_pi_job_listing_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$extra_fields   = filter_input( INPUT_POST, 'extra_fields', FILTER_SANITIZE_STRING );
	$extra_fields   = html_entity_decode( $extra_fields );
	$extra_fields   = json_decode( $extra_fields, true );
	$posts_per_page = isset( $extra_fields['posts_per_page'] ) ? $extra_fields['posts_per_page'] : 10;
	$page_number    = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$job_ids        = pi_get_job_orderby();

	$args      = array(
		'post_type'      => 'pi_jobs',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'post__in'       => $job_ids,
		'orderby'        => 'post__in',
		'order'          => 'DESC',
		'paged'          => $page_number,
	);
	$the_query = new WP_Query( $args );
	ob_start();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$pi_closing_date        = get_field( 'pi_closing_date', get_the_ID() );
			$remaining_days         = pi_get_remaining_days( $pi_closing_date );
			$pi_product             = get_field( 'pi_jobs_packages', get_the_ID() );
			$is_featured            = pi_is_featured_package( $pi_product );
			$minimum_salary         = get_field( 'pi_minimum_salary', get_the_ID() );
			$maximum_salary         = get_field( 'pi_maximum_salary', get_the_ID() );
			$salary                 = pi_get_salary( $minimum_salary, $maximum_salary );
			$geographic_location    = get_field( 'pi_geographic_location', get_the_ID() );
			$author_id              = get_post_field( 'post_author', get_the_ID() );
			$recruiter_company_name = get_field( 'pi_recruiter_company_name', "user_{$author_id}" );
			$recruiter_company_name = ( isset( $recruiter_company_name ) && ! empty( $recruiter_company_name ) ) ? $recruiter_company_name : '';
			$description            = get_field( 'pi_description', get_the_ID() );
			$recruiter_logo_url     = pi_get_recruiter_logo( $author_id );
			$is_status              = esc_html__( 'Live', 'performancein' );
			$is_expired             = false;
			$is_class               = '';

			if ( true === pi_is_expired_job( $pi_closing_date ) ) {
				$is_status  = esc_html__( 'Expired', 'performancein' );
				$is_expired = true;
				$is_class   = 'job-expired';
			}
			if ( true === $is_featured && false === $is_expired ) {
				$is_class = 'job-featured';
			}
			?>
            <article class="jobList-item clearfix <?php echo esc_attr( $is_class ); ?> ">
				<?php if ( true === $is_featured ) { ?>
					<?php if ( isset( $recruiter_logo_url ) && ! empty( $recruiter_logo_url ) ) { ?>
                        <a href="<?php the_permalink(); ?>" class="job-recruiter-logo">
                            <img src="<?php echo esc_url( $recruiter_logo_url ); ?>" alt="<?php echo esc_attr( $recruiter_company_name ); ?>">
                        </a>
					<?php } ?>
				<?php } ?>
                <div class="job-flags">
					<?php if ( true === $is_featured && false === $is_expired ) { ?>
                        <span class="featjob"><?php esc_html_e( 'Featured', 'performancein' ); ?></span>
						<?php if ( 1 === $remaining_days ) { ?>
                            <span class="endingjob"><?php esc_html_e( '1 day left', 'performancein' ) ?></span>
						<?php } elseif ( $remaining_days > 1 and $remaining_days < 7 ) { ?>
                            <span class="endingjob"><?php printf( __( "%s days left", 'performancein' ), $remaining_days ); ?> </span>
						<?php } elseif ( 0 === $remaining_days ) { ?>
                            <span class="endingjob"><?php esc_html_e( 'Expires today', 'performancein' ) ?></span>
						<?php } elseif ( pi_get_since_added_days( get_the_date( 'd-m-Y' ) ) < 7 ) { ?>
                            <span class="newjob"><?php esc_html_e( 'New', 'performancein' ); ?></span>
						<?php } ?>
					<?php } ?>
                </div>

                <h2 class="jobList-itemTitle">
                    <a href="<?php echo esc_url( get_the_permalink() ) ?>">
						<?php echo esc_html( get_the_title() ); ?>
                    </a>
                </h2>
                <div class="meta">
                        <span class="jobList-itemRecruiter">
                            <span data-icon="&#xe029;"></span>
							<?php echo esc_html( $recruiter_company_name ); ?>
                        </span>
					<?php if ( '' !== $salary ) { ?>
                        <span class="jobList-itemSalary"><?php echo esc_html( $salary ); ?></span>
					<?php } ?>
					<?php if ( isset( $geographic_location ) ) { ?>
                        <span class="jobList-itemLocation">
                            <span data-icon="&#xe013;"></span>
                            <?php echo esc_html( $geographic_location ); ?>
                        </span>
					<?php } ?>
					<?php if ( true === $is_expired ) { ?>
                        <span class="jobList-itemDate">
                                <span data-icon="&#xf073;"></span>
							<?php echo esc_html( $is_status ); ?>
                            </span>
					<?php } ?>
                </div>
				<?php echo wp_trim_words( $description, 50, null ); ?>
            </article>
			<?php
		}
	} else {
		echo '';
	}
	$result['html'] = ob_get_clean();

	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();

}


add_action( 'wp_ajax_nopriv_pi_job_listing', 'performancein_pi_job_listing_callback' );
add_action( 'wp_ajax_pi_job_listing', 'performancein_pi_job_listing_callback' );

/**
 * Author listing ajax callback.
 */
function performancein_pi_author_listing_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$extra_fields   = filter_input( INPUT_POST, 'extra_fields', FILTER_SANITIZE_STRING );
	$extra_fields   = html_entity_decode( $extra_fields );
	$extra_fields   = json_decode( $extra_fields, true );
	$users_per_page = isset( $extra_fields['users_per_page'] ) ? $extra_fields['users_per_page'] : 10;
	$total_users    = isset( $extra_fields['total_users'] ) ? $extra_fields['total_users'] : 0;
	$page_number    = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );

	$offset      = $users_per_page * ( $page_number - 1 );
	$total_pages = ceil( $total_users / $users_per_page );

	// main user query
	$args = array(
		'role'   => 'author',
		'fields' => 'all_with_meta',
		'number' => $users_per_page,
		'offset' => $offset
	);

	// Create the WP_User_Query object
	$wp_user_query = new WP_User_Query( $args );

	// Get the results
	$user_query_data = $wp_user_query->get_results();
	ob_start();
	if ( isset( $user_query_data ) && ! empty( $user_query_data ) ) {
		foreach ( $user_query_data as $user_data ) {
			$userID              = $user_data->ID;
			$userID              = get_the_author_meta( 'ID', $userID );
			$user_meta           = get_userdata( $userID );
			$Username            = get_user_meta( $userID, 'pi_user_name', true );
			$Username            = ! empty( $Username ) ? $Username : $user_meta->display_name;
			$userFirstName       = get_the_author_meta( 'first_name', $userID );
			$userLastName        = get_the_author_meta( 'last_name', $userID );
			$userDescription     = $user_data->description;
			$get_author_gravatar = get_avatar_url( $userID );
			$userCustomAvtar     = get_user_meta( $userID, 'author_avtar_image', true );
			$userCustomAvtar     = wp_get_attachment_image_src( $userCustomAvtar, array( 245, 245 ) );
			$userAvtarImg        = ! empty( $userCustomAvtar ) ? $userCustomAvtar[0] : $get_author_gravatar;
			$userPermalink       = get_author_posts_url( $userID );
			$userLinkedinURL     = get_user_meta( $userID, 'pi_linkedin_url', true );
			$userTwitterURL      = get_user_meta( $userID, 'pi_twitter_url', true );
			?>
            <article class="pi-authorProfile">
                <img src="<?php echo esc_url( $userAvtarImg ); ?>" class="greyscale pi-greyscale">
                <div class="pi-authorProfile-content">
					<?php
					if ( empty( $userFirstName ) ) { ?>
                        <h3><?php echo esc_html( $Username ); ?></h3>
					<?php } else { ?>
                        <h3><?php echo esc_html( $userFirstName ) . ' ' . esc_html( $userLastName ); ?></h3>
					<?php }
					?>
                    <ul class="pi-authorfollow">
						<?php
						if ( '' !== $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo "http://twitter.com/" . $userTwitterURL; ?>" data-icon="" rel="nofollow">
                                    <span class="pi-visuallyhidden"></span></a>
                            </li>

                            <li>
                                <a href="<?php echo $userLinkedinURL; ?>" data-icon=""
                                   rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } elseif ( '' !== $userTwitterURL && '' === $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo "http://twitter.com/" . $userTwitterURL; ?>" data-icon="" rel="nofollow"><span
                                            class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } elseif ( '' === $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo $userLinkedinURL; ?>" data-icon=""
                                   rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } ?>
                    </ul>


                    <div>
                        <p><?php echo $userDescription; ?></p>
                    </div>
					<?php
					if ( empty( $userFirstName ) ) { ?>
                        <a href="<?php echo $userPermalink; ?>"><?php esc_html_e( sprintf( 'Read more from %1$s', $Username ) ); ?></a>
					<?php } else { ?>
                        <a href="<?php echo $userPermalink; ?>"><?php esc_html_e( sprintf( 'Read more from %1$s', $userFirstName ) ); ?></a>
					<?php }
					?>
                </div>
            </article>
			<?php
		}
	} else {
		echo '';
	}
	$result['html'] = ob_get_clean();

	ob_start();
	if ( $total_pages !== 0 ) {
		pi_user_pagination_html( $total_pages, $page_number );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_pi_author_listing', 'performancein_pi_author_listing_callback' );
add_action( 'wp_ajax_pi_author_listing', 'performancein_pi_author_listing_callback' );


function performancein_pi_partner_account_registration_form() {
	$result            = array();
	$result['success'] = false;
	$result['html']    = '';

	$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'partner_account_register_form_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$full_name            = filter_input( INPUT_POST, 'full_name', FILTER_SANITIZE_STRING );
	$email                = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
	$company_name         = filter_input( INPUT_POST, 'company_name', FILTER_SANITIZE_STRING );
	$website_url          = filter_input( INPUT_POST, 'website_url', FILTER_SANITIZE_URL );
	$partner_package_type = filter_input( INPUT_POST, 'partner_package_type', FILTER_SANITIZE_STRING );
	$company_biography    = filter_input( INPUT_POST, 'company_biography', FILTER_UNSAFE_RAW );



	if ( empty( $full_name ) ) {
		$result['msg'] = esc_html__( 'Please enter full name.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		$result['msg'] = esc_html__( 'Email id is invalid or empty.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( empty( $company_name ) ) {
		$result['msg'] = esc_html__( 'Please enter company name.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	if ( empty( $partner_package_type ) ) {
		$result['msg'] = esc_html__( 'Error occurred please try again...!', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	if ( email_exists( $email ) ) {
		$userID    = email_exists( $email );
		$userMeta  = get_userdata( $userID );
		$userRoles = $userMeta->roles;
		if ( 'account' === $userRoles[0] ) {
			$result['msg'] = esc_html__( 'A user with this email already exists – please contact sales@performancein.com', 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		} elseif ( 'customer' === $userRoles[0] ) {
			$result['msg'] = esc_html__( 'A user with this email already exists – please contact sales@performancein.com', 'performancein' );
			echo wp_json_encode( $result );
			wp_die();
		}
	}
	$username        = wc_create_new_customer_username( $email );
	$user_data       = array(
		'user_login'   => sanitize_user( $username ),
		'user_email'   => $email,
		'display_name' => $full_name,
		'role'         => 'account',
		'user_pass'    => null
	);
	$new_customer_id = wp_insert_user( $user_data );
	if ( ! is_wp_error( $new_customer_id ) ) {

		update_user_meta( $new_customer_id, 'pi_company_name', $company_name );

		$post_data  = array(
			'post_title'   => $company_name,
			'post_content' => '',
			'post_type'    => 'pi_partner_networks',
		);
		$partner_id = wp_insert_post( $post_data );

		if ( $partner_id ) {

			update_post_meta( $partner_id, 'pi_user_selection', $new_customer_id );
			$pakage_id = get_page_by_path( $partner_package_type, OBJECT, 'product' );
			update_post_meta( $partner_id, 'pi_package_selection', $pakage_id->ID );

			if ( $company_biography ) {
				$pi_partner_page_sub_menu['profile']             = 'profile';
				$pi_partner_page_sub_menu['client_testimonials'] = 'client_testimonials';
				update_field( 'pi_partner_page_sub_menu', $pi_partner_page_sub_menu, $partner_id );
				update_field( 'pi_partner_description_pi_partner_description_title', 'Profile', $partner_id );
				update_field( 'pi_partner_description_pi_partner_description', $company_biography, $partner_id );
			}

			update_field( 'pi_partner_sidebar_pi_contact_info_pi_contact_info_title', 'Contact', $partner_id );

			if ( $website_url ) {
				update_field( 'pi_partner_sidebar_pi_contact_info_pi_website_url', $website_url, $partner_id );
			}
			update_field( 'pi_partner_sidebar_pi_contact_info_pi_email_id', $email, $partner_id );

		}
		$emailEnable = get_field('pi_email_enable','option');
		if(! empty($emailEnable)){
			$email_array = get_field('pi_select_email_id','option');
			$email_array = preg_replace('/\s+/', '', $email_array);
			$email_array = explode(',', $email_array);
			if(! empty($email_array)) {
				foreach ($email_array as $user_email) {
				    ob_start(); ?>
					<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
						<title><?php echo __( 'PerformanceIN Profile Hub Confirmation' ); ?></title>
					</head>

					<body>
                    <?php
                    $emailTempate = get_field('pi_email_template','option');
                    $emailTempate = str_replace('{{partner_fullname}}',$full_name,$emailTempate);
                    $link = site_url()."/wp-admin/post.php?post=".$partner_id."&action=edit";
                    $emailTempate = str_replace('{{partner_profilelink}}','<a href="'.$link.'">visit their admin page here</a>',$emailTempate);
                    $emailTempate = str_replace('{{partner_company_name}}',$company_name,$emailTempate);
                    $emailTempate = str_replace('{{partner_subscription_package}}',$partner_package_type,$emailTempate);
                    echo $emailTempate;
                    ?>
					</body>
					</html>
					<?php
					$message = ob_get_clean();
					$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
					$headers  = array( 'Content-Type: text/html; charset=UTF-8' );
					$title    = sprintf( __( '[%s] PerformanceIN Profile Hub Confirmation' ), $blogname );
					wp_mail( $user_email, $title, $message, $headers );
				}
			}
        }
		$pi_thank_you_message = get_field('pi_thank_you_message','option');
		$html ="";
		$html .= '<div class="pi-text-center">';
		$html .= $pi_thank_you_message;
		$html .= '</div>';
		$result['success'] = true;
		$result['html']    = $html;
		$result['msg']     = esc_html__( 'Thank you for submitting the request , We will get back to you via email.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();

	} else {
		$result['msg'] = $new_customer_id->get_error_message();
		echo wp_json_encode( $result );
		wp_die();

	}
}

add_action( 'wp_ajax_nopriv_partner_account_registration_form', 'performancein_pi_partner_account_registration_form' );
add_action( 'wp_ajax_partner_account_registration_form', 'performancein_pi_partner_account_registration_form' );


/**
 * Job article listing callback function.
 */
function performancein_article_listing_ajax_callback() {
	$result            = array();
	$result['success'] = 0;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$page_number          = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$extra_fields         = filter_input( INPUT_POST, 'extra_fields', FILTER_SANITIZE_STRING );
	$extra_fields         = html_entity_decode( $extra_fields );
	$extra_fields         = json_decode( $extra_fields, true );
	$post_type            = isset( $extra_fields['post_type'] ) ? $extra_fields['post_type'] : 'post';
	$post_taxs            = $extra_fields['post_taxs'];
	$post_category        = $extra_fields['post_category'];
	$category_description = isset( $extra_fields['category_description'] ) ? $extra_fields['category_description'] : false;
	$number_of_post       = ( ! empty( $extra_fields['number_of_post'] ) && 0 !== $extra_fields['number_of_post'] ) ? $extra_fields['number_of_post'] : - 1;
	$exclude_post         = $extra_fields['exclude_post'];

	$args = array(
		'post_status'    => array( 'publish' ),
		'posts_per_page' => $number_of_post,
		'paged'          => $page_number,
		'post_type'      => $post_type
	);

	if ( true !== $category_description ) {
		if ( $post_taxs !== '' && $post_category !== '' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'         => $post_taxs,
					'terms'            => $post_category,
					'field'            => 'slug',
					'include_children' => true,
					'operator'         => 'IN'
				)
			);
		}
		if ( ! empty( $exclude_post ) ) {
			$exclude_post         = explode( ",", $exclude_post );
			$args['post__not_in'] = $exclude_post; // phpcs:ignore
		}
	} else {
		$category           = get_term_by( 'name', $post_category, 'category' );
		$args['meta_key']   = 'pi_primary_category';
		$args['meta_value'] = $category->term_id;
	}
	ob_start();
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		if ( 'post' === $post_type || '' === $post_type ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$pi_primary_category_id = get_field( 'pi_primary_category', get_the_ID() );
				if ( empty( $pi_primary_category_id ) ) {
					$post_terms   = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
					$pi_term_name = array();
					$pi_term_link = array();
					foreach ( $post_terms as $post_term ) {
						$pi_term_name[] = $post_term->name;
						$term_id        = $post_term->term_id;
						$pi_term_link[] = get_term_link( $term_id );
					}
					$pi_term_name = $pi_term_name[0];
					$pi_term_link = $pi_term_link[0];
				} else {
					$pi_term_link = get_term_link( $pi_primary_category_id );
					$pi_term      = get_term( $pi_primary_category_id );
					$pi_term_name = $pi_term->name;
				}
				$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', get_the_ID() );
				$post_image_id                     = get_field( 'pi_article_image', get_the_ID() );
				$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
				$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
				$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
				$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
				$post_permalink                    = get_the_permalink();
				$post_title                        = get_the_title();
				$articleFlagCategory               = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
				$articleFlagCategoryArray          = array();
				$flagTermName                      = array();
				$flagTermLink                      = array();
				foreach ( $articleFlagCategory as $articleFlagCategoryList ) {
					$parent_category = $articleFlagCategoryList->parent;
					if ( 0 !== $parent_category ) {
						$parent = get_term_by( 'id', $parent_category, 'category' );
						if ( ! empty( $parent ) ) {
							$articleFlagCategoryArray[] = $parent->name;
							$termArray                  = $articleFlagCategoryList;
							$flagTermName[]             = $termArray->name;
							$flagTermID                 = $termArray->term_id;
							$flagTermLink[]             = get_term_link( $flagTermID );
						}
					}
				}
				if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
					$flagTermLink = $flagTermLink[0];
					$flagTermName = $flagTermName[0];
				}
				?>
                <article class="pi-articleListItem">
                    <div class="pi-article-item-inner">
						<?php if ( 'image' === $pi_article_banner_section_choices ) { ?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"/>
                                </a>
                            </div>
							<?php
						} elseif ( 'video' === $pi_article_banner_section_choices ) {
							$post_image = wp_get_attachment_image_src( get_field( 'pi_article_video_thumbnail', get_the_ID() ), 'full' );
							$post_image = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
							?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"/>
                                    <div class="pi-videoIcon"></div>
                                </a>

                            </div>
						<?php } else {
							$post_image = wp_get_attachment_image_src( get_field( 'pi_article_image_gallery_thumbnail', get_the_ID() ), 'full' );
							$post_image = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
							?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"/>
                                </a>
                            </div>
						<?php }
						?>
                        <div class="pi-news-details">
							<?php
							if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
								if ( true === in_array( 'Regional', $articleFlagCategoryArray, true ) ) { ?>
                                    <a href="<?php echo esc_url( $flagTermLink ); ?>" class="articleRegionalFlag"><?php echo esc_html( $flagTermName ); ?></a>
								<?php }
							}
							if ( ! empty( $post_title ) ):?>
                                <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                                    <h2 class="title"><?php echo esc_html( $post_title ); ?></h2></a>
							<?php endif; ?>
                            <ul class="pi-category-list">
                                <li class="pi-listCategories-item">
                                    <a href="<?php echo esc_url( $pi_term_link ); ?>"><?php echo esc_html( $pi_term_name ); ?></a>
                                </li>
								<?php if ( ! empty( get_field( 'pi_sponsored', get_the_ID() ) ) || ! empty( get_field( 'pi_partners', get_the_ID() ) ) ) { ?>
									<?php if ( empty( get_field( 'pi_sponsored', get_the_ID() ) ) ) {
										$partners = get_field( 'pi_partners', get_the_ID() );
										?>
                                        <li class="pi-listCategories-item pi-listCategories-item-partner">
                                            <a href="<?php echo esc_url( get_the_permalink( $partners ) ) ?>"><?php esc_html_e( 'Partner Networks' ); ?></a>
                                        </li>
									<?php } elseif ( ! empty( get_field( 'pi_sponsored', get_the_ID() ) ) ) { ?>

                                        <li class="pi-listCategories-item pi-listCategories-item-sponsored">
											<?php esc_html_e( 'Sponsored', 'performacein' ); ?>
                                        </li>
									<?php }
								} ?>
                            </ul>
                            <time class="pi-articleListItem-date" datetime="<?php echo get_the_date( 'F j, Y' ); ?>">
								<?php echo get_the_date( 'd M y' ); ?>
                            </time>
                        </div>
                    </div>
                </article>
				<?php
			}
		} elseif ( 'pi_events' === $post_type ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_permalink = get_the_permalink();
				$post_title     = get_the_title();
				$eventStartDate = get_field( 'pi_event_start_date', get_the_ID() );
				?>
                <article class="pi-articleListItem">
                    <div class="pi-article-item-inner">

                        <div class="pi-post-thumbnail">
                            <a href="<?php echo esc_url( $post_permalink ); ?>">
                                <img width="520" height="245" src="<?php echo esc_url( wp_get_attachment_url( get_field( 'pi_event_image', get_the_ID() ) ) ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"/>
                            </a>
                        </div>


                        <div class="pi-news-details">
                            <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                                <h2 class="title"><?php echo esc_html( $post_title ); ?></h2>
                            </a>
                            <time class="pi-eventListItem-date" datetime="<?php echo esc_attr( $eventStartDate ); ?>">
								<?php echo esc_html( $eventStartDate ); ?>
                            </time>
                        </div>
                    </div>
                </article>

				<?php
			}
		}
	} else {
		echo '';
	}
	wp_reset_postdata();
	$result['html'] = ob_get_clean();

	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class, wp_json_encode( $extra_fields ) );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_pi_article_listing', 'performancein_article_listing_ajax_callback' );
add_action( 'wp_ajax_pi_article_listing', 'performancein_article_listing_ajax_callback' );

function performancein_author_listing_ajax_callback() {

	$result            = array();
	$result['success'] = 0;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$page_number    = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$extra_fields   = filter_input( INPUT_POST, 'extra_fields', FILTER_SANITIZE_STRING );
	$extra_fields   = html_entity_decode( $extra_fields );
	$extra_fields   = json_decode( $extra_fields, true );
	$number_of_post = ( ! empty( $extra_fields['number_of_post'] ) && 0 !== $extra_fields['number_of_post'] ) ? $extra_fields['number_of_post'] : - 1;
	$author_name    = ( ! empty( $extra_fields['author_name'] ) ) ? $extra_fields['author_name'] : '';

	if ( ! empty( $author_name ) ) {
		$args      = array( 'posts_per_page' => $number_of_post, 'post_type' => 'post', 'author_name' => $author_name, 'paged' => $page_number );
		$the_query = new WP_Query( $args );
		ob_start();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$pi_primary_category_id = get_field( 'pi_primary_category', get_the_ID() );
				if ( empty( $pi_primary_category_id ) ) {
					$post_terms   = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
					$pi_term_name = array();
					$pi_term_link = array();
					foreach ( $post_terms as $post_term ) {
						$pi_term_name[] = $post_term->name;
						$term_id        = $post_term->term_id;
						$pi_term_link[] = get_term_link( $term_id );
					}
					$pi_term_name = $pi_term_name[0];
					$pi_term_link = $pi_term_link[0];
				} else {
					$pi_term_link = get_term_link( $pi_primary_category_id );
					$pi_term      = get_term( $pi_primary_category_id );
					$pi_term_name = $pi_term->name;
				}
				$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', get_the_ID() );
				$post_image_id                     = get_field( 'pi_article_image', get_the_ID() );
				$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
				$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
				$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
				$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
				$post_permalink                    = get_the_permalink();
				$post_title                        = get_the_title();
				$articleFlagCategory               = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
				$articleFlagCategoryArray          = array();
				$flagTermName                      = array();
				$flagTermLink                      = array();
				foreach ( $articleFlagCategory as $articleFlagCategoryList ) {
					$parent_category = $articleFlagCategoryList->parent;
					if ( 0 !== $parent_category ) {
						$parent = get_term_by( 'id', $parent_category, 'category' );
						if ( ! empty( $parent ) ) {
							$articleFlagCategoryArray[] = $parent->name;
							$termArray                  = $articleFlagCategoryList;
							$flagTermName[]             = $termArray->name;
							$flagTermID                 = $termArray->term_id;
							$flagTermLink[]             = get_term_link( $flagTermID );
						}
					}
				}
				if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
					$flagTermLink = $flagTermLink[0];
					$flagTermName = $flagTermName[0];
				}
				$auth_categories          = wp_get_post_categories( get_the_ID() );
				$auth_pi_primary_category = array(
					get_field( 'pi_primary_category', get_the_ID() ),
				);
				if ( ! empty( $auth_pi_primary_category ) ) {
					$auth_categories = array_filter( array_merge( $auth_pi_primary_category, $auth_categories ) );
					$auth_categories = array_unique( $auth_categories );
				}
				$CategorriesFindRegionalName = array();
				$CategorriesFindRegionalLink = array();
				foreach ( $auth_categories as $category ) {
					$piCategoryObj             = get_term_by( 'id', $category, 'category' );
					$piParentCategoryParent    = $piCategoryObj->parent;
					$parentIDObj               = get_term_by( 'id', $piParentCategoryParent, 'category' );
					$CategorriesParentRegional = $parentIDObj->slug;
					if ( 'regional' === $CategorriesParentRegional ) {
						$piCategoryObj                 = get_term_by( 'id', $category, 'category' );
						$CategorriesFindRegionalName[] = $piCategoryObj->name;
						$CategorriesFindRegionalLink[] = get_term_link( $piCategoryObj->term_id );
					}


				}
				if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
					$CategorriesFindRegionalLink = $CategorriesFindRegionalLink[0];
					$CategorriesFindRegionalName = $CategorriesFindRegionalName[0];
				}
				?>


                <article class="pi-articleListItem">
                    <div class="pi-article-item-inner">
						<?php if ( 'image' === $pi_article_banner_section_choices ) { ?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>"/>
                                </a>
                            </div>
							<?php
						} elseif ( 'video' === $pi_article_banner_section_choices ) {
							$post_image_id = get_field( 'pi_article_video_thumbnail', get_the_ID() );
							$post_image    = wp_get_attachment_image_src( $post_image_id, 'full' );
							$post_image    = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
							?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>"/>
                                    <div class="pi-videoIcon"></div>
                                </a>

                            </div>
						<?php } else {
							$post_image_id = get_field( 'pi_article_image_gallery_thumbnail', get_the_ID() );
							$post_image    = wp_get_attachment_image_src( $post_image_id, 'full' );
							$post_image    = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
							?>
                            <div class="pi-post-thumbnail">
                                <a href="<?php echo esc_url( $post_permalink ); ?>">
                                    <img width="520" height="245" src="<?php echo esc_url( $post_image ); ?>"/>
                                </a>
                            </div>
						<?php }
						?>
                        <div class="pi-news-details">
							<?php
							if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
								if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { ?>
                                    <a href="<?php echo $CategorriesFindRegionalLink; ?>" class="articleRegionalFlag"><?php echo esc_html( $CategorriesFindRegionalName ); ?></a>
								<?php }

							}
							if ( ! empty( $post_title ) ):?>
                                <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                                    <h2 class="title"><?php echo esc_html( $post_title ); ?></h2></a>
							<?php endif; ?>
                            <ul class="pi-category-list">
                                <li class="pi-listCategories-item">
                                    <a href="<?php echo $pi_term_link; ?>"><?php echo esc_html( $pi_term_name ); ?></a>
                                </li>
								<?php if ( ! empty( get_field( 'pi_partners', get_the_ID() ) ) ) {
									$partners = get_field( 'pi_partners', get_the_ID() );
									?>
                                    <li class="pi-listCategories-item pi-listCategories-item-partner">
                                        <a href="<?php echo esc_url( get_the_permalink( $partners ) ) ?>"><?php esc_html_e( 'Partner Networks' ); ?></a>
                                    </li>
								<?php } elseif ( ! empty( get_field( 'pi_sponsored', get_the_ID() ) ) ) { ?>

                                    <li class="pi-listCategories-item pi-listCategories-item-sponsored">
										<?php esc_html_e( 'Sponsored', 'performacein' ); ?>
                                    </li>
								<?php } ?>
                            </ul>
                            <time class="pi-articleListItem-date"
                                  datetime="<?php echo get_the_date( 'F j, Y' ); ?>"><?php echo get_the_date( 'd M y' ); ?></time>
                        </div>
                    </div>
                </article>

			<?php }
		} else {
			echo '';
		}
		wp_reset_postdata();
		$result['html'] = ob_get_clean();
		ob_start();
		if ( $the_query->max_num_pages !== 0 ) {
			pi_pagination_html( $the_query, $current_action, $current_class, wp_json_encode( $extra_fields ) );
		}
		$result['pagination_html'] = ob_get_clean();
		$result['success']         = true;
		echo wp_json_encode( $result );
		wp_die();
	} else {
		ob_start();
		$result['html']    = ob_get_clean();
		$result['success'] = true;
		echo wp_json_encode( $result );
		wp_die();
	}

}

add_action( 'wp_ajax_nopriv_pi_post_author_listing', 'performancein_author_listing_ajax_callback' );
add_action( 'wp_ajax_pi_post_author_listing', 'performancein_author_listing_ajax_callback' );


/*
 * Ajax callback function for the search
 *
 * */
function performancein_search_posts_listing_ajax_callback() {

	$result            = array();
	$result['success'] = 0;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	$settingArray         = pi_theme_setting();
	$search_post_per_page = $settingArray['search_post_per_page'];

	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

	$page_number = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$args        = array( 'posts_per_page' => $search_post_per_page, 'post_type' => array( 'post', 'pi_events' ), 'paged' => $page_number );
	$the_query   = new WP_Query( $args );
	ob_start();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			get_template_part( 'template-parts/content', 'search' );
		}
	}
	wp_reset_postdata();
	$result['html'] = ob_get_clean();
	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_pi_search_posts_listing', 'performancein_search_posts_listing_ajax_callback' );
add_action( 'wp_ajax_pi_search_posts_listing', 'performancein_search_posts_listing_ajax_callback' );


/**
 * Admin partner function
 */
function pi_partner_confirm_ajax() {
	$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
	if ( isset( $action ) && 'pi_partner_confirm_ajax' === $action ) {
		$postID          = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT );
		$checkbox_status = filter_input( INPUT_POST, 'status', FILTER_VALIDATE_BOOLEAN );
		$checkbox_value  = filter_input( INPUT_POST, 'value', FILTER_SANITIZE_STRING );
		$meta_confirm    = array();
		if ( true === (bool) $checkbox_status ) {
			$meta_confirm = array(
				$checkbox_value
			);
		}
		update_post_meta( $postID, 'pi_partner_is_conform', $meta_confirm );
		$result['msg'] = esc_html__( 'Partner status updated.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}

}

add_action( 'wp_ajax_pi_partner_confirm_ajax', 'pi_partner_confirm_ajax' );


function performancein_partner_network_listing_ajax_callback() {
	$result            = array();
	$result['success'] = true;
	$result['html']    = '';

	$nonce          = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class  = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$page_number         = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$extra_fields        = filter_input( INPUT_POST, 'extra_fields', FILTER_SANITIZE_STRING );
	$extra_fields        = html_entity_decode( $extra_fields );
	$extra_fields        = json_decode( $extra_fields, true );
	$postPerPage         = ( ! empty( $extra_fields['number_of_post'] ) ) ? $extra_fields['number_of_post'] : - 1;
	$pi_product_category = ( ! empty( $extra_fields['category'] ) ) ? $extra_fields['category'] : 'basic-membership';

	$args               = array(
		'post_type'           => 'pi_partner_networks',
		'post_status'         => 'publish',
		'meta_key'            => 'pi_partner_is_conform',
		'meta_value'          => ' ',
		'meta_compare'        => '!=',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $postPerPage,
		'orderby'             => 'title',
		'order'               => 'ASC',
		'paged'               => $page_number,
	);
	$product_obj        = get_page_by_path( $pi_product_category, OBJECT, 'product' );
	$args['meta_query'] = array(
		array(
			'key'   => 'pi_package_selection',
			'value' => $product_obj->ID,
		)
	);
	ob_start();
	$performancein_query = new WP_Query( $args );
	if ( $performancein_query->have_posts() ) {
		while ( $performancein_query->have_posts() ) {
			$performancein_query->the_post();
			get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
		}
	}
	wp_reset_postdata();
	$result['html'] = ob_get_clean();
	ob_start();
	if ( $performancein_query->max_num_pages !== 0 ) {
		pi_pagination_html( $performancein_query, $current_action, $current_class, wp_json_encode( $extra_fields ) );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();

}

add_action( 'wp_ajax_nopriv_pi_partner_network_listing', 'performancein_partner_network_listing_ajax_callback' );
add_action( 'wp_ajax_pi_partner_network_listing', 'performancein_partner_network_listing_ajax_callback' );
