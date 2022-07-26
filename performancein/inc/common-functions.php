<?php
/**
 * This file contains all common callback functions.
 *
 * @package performancein
 */

/**
 * Account detail.
 *
 * @param $user
 */
function pi_account_detail_html( $user ) {


	$company_name_value  = get_field( 'pi_company_name', "user_{$user->ID}" );
	$is_link_with_google = get_the_author_meta( 'is_link_with_google', $user->ID );
	?>
    <div class="account_details">
        <h2><?php esc_html_e( 'Account Summary', 'performancein' ); ?></h2>
        <dl>
            <dt><?php esc_html_e( 'Email:', 'performancein' ); ?></dt>
            <dd><?php echo esc_html( $user->user_email ); ?></dd>
            <dt><?php esc_html_e( 'Name:', 'performancein' ); ?></dt>
            <dd><?php echo esc_html( $user->display_name ); ?></dd>
			<?php if ( true === pi_is_partner_account( $user ) ) { ?>
                <dt><?php esc_html_e( 'Company:', 'performancein' ); ?></dt>
                <dd><?php echo esc_html( $company_name_value ); ?></dd>
			<?php } ?>
        </dl>
        <h3><?php esc_html_e( 'Social Accounts', 'performancein' ); ?></h3>
        <p><?php esc_html_e( 'Connect your social accounts to PerformanceIN and log in using your social account instead.', 'performancein' ); ?></p>
        <ul class="social-account-list">

            <li class="google">
                <span class="site"><span class="social-icon"></span><?php esc_html_e( 'Google', 'performancein' ); ?></span>
				<?php if ( isset( $is_link_with_google ) && ! empty( $is_link_with_google ) && true === (bool) $is_link_with_google ) { ?>
                    <a class="button remove" href="<?php echo esc_url( site_url( 'account/google-remove/' ) ); ?>">
						<?php esc_html_e( 'Remove', 'performancein' ); ?>
                    </a>
                    <span class="details"><?php echo esc_html( $user->user_nicename ) ?></span>
				<?php } else { ?>
                    <a class="button add" href="<?php echo esc_url( pi_get_google_sign_in_url( GOOGLE_CLIENT_ID, GOOGLE_CLIENT_REDIRECT_URL ) ); ?>">
						<?php esc_html_e( 'Add', 'performancein' ); ?>
                    </a>
				<?php } ?>

            </li>
        </ul>
    </div>
	<?php
}


/**
 * Account detail job html.
 *
 * @param $user
 */
function pi_account_detail_job_html( $user ) {

	$recruiter_company_name = get_field( 'pi_recruiter_company_name', "user_{$user->ID}" );
	$recruiter_logo_url     = pi_get_recruiter_logo( $user->ID, true );
	$pi_credit_package      = pi_get_credit_package( $user->ID );
	?>
    <div class="account_details" id="job_credits">
        <div class="jobs">
            <h2><?php esc_html_e( 'Job Credits', 'performancein' ); ?></h2>
            <hr>
            <table>
                <thead>
                <tr>
                    <th class="first_col"><?php esc_html_e( 'Job Type', 'performancein' ); ?></th>
                    <th class="second_col"><?php esc_html_e( 'Available', 'performancein' ); ?></th>
                    <th class="action_col"><?php esc_html_e( 'Action', 'performancein' ); ?></th>
                </tr>
                </thead>
                <tbody>
				<?php
				$args              = array(
					'post_type'      => 'product',
					'posts_per_page' => 10,
					'tax_query'      => array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'slug',
							'terms'    => array( 'job-package' )
						),
					),
					'orderby'        => 'date',
					'order'          => 'ASC',
				);
				$job_package_query = new WP_Query( $args );
				foreach ( $job_package_query->posts as $pi_product ) {
					$credit = pi_get_credit( $pi_credit_package, $pi_product->ID );
					?>
                    <tr>
                        <td class="first_col"><?php echo esc_html( $pi_product->post_title ); ?></td>
                        <td class="second_col"><?php echo esc_attr( $credit ); ?></td>
                        <td class="action_col">
							<?php if ( $credit > 0 ) { ?>
                                <a href="<?php echo esc_url( add_query_arg( array( 'type' => base64_encode( $pi_product->ID ) ), site_url( 'order/jobs/new/' ) ) ); ?>"><?php esc_html_e( 'Add', 'performancein' ); ?></a>
							<?php } else { ?>
                                <a href="<?php echo esc_url( site_url( 'order/jobs/' ) ); ?>"><?php esc_html_e( 'Buy Credits', 'performancein' ); ?></a>
							<?php } ?>
                        </td>
                    </tr>

				<?php } ?>
                </tbody>
            </table>
        </div>
        <a href="<?php echo esc_url( site_url( 'order/jobs/' ) ); ?>" class="button"><?php esc_html_e( 'Purchase Job Credits', 'performancein' ); ?></a>
        <p>&nbsp;</p>
        <h2><?php esc_html_e( 'Listed Jobs', 'performancein' ); ?></h2>
        <hr>
		<?php
		$args          = array(
			'post_type'   => 'pi_jobs',
			'post_status' => 'publish',
			'author__in'  => array( get_current_user_id() ),
		);
		$pi_jobs_query = new WP_Query( $args );
		?>

        <table>
            <thead>
            <tr>
                <th class="first_col"><?php esc_html_e( 'Job Title', 'performancein' ); ?></th>
                <th class="second_col"><?php esc_html_e( 'Status', 'performancein' ); ?></th>
                <th class="action_col"><?php esc_html_e( 'Action', 'performancein' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			if ( $pi_jobs_query->have_posts() ) {
				$today = date( 'd/m/Y' );
				while ( $pi_jobs_query->have_posts() ) : $pi_jobs_query->the_post();
					$pi_closing_date = get_field( 'pi_closing_date', get_the_ID() );
					$pi_product      = get_field( 'pi_jobs_packages', get_the_ID() );
					$is_status       = esc_html__( 'Live', 'performancein' );
					$is_expired      = false;
					if ( true == pi_is_expired_job( $pi_closing_date ) ) {
						$is_status  = esc_html__( 'Expired', 'performancein' );
						$is_expired = true;
					}
					?>
                    <tr>
                        <td class="first_col">
                            <a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>">
								<?php the_title() ?>
                            </a>
                        </td>
                        <td class="second_col <?php echo esc_attr( strtolower( $is_status ) ); ?> job-<?php echo esc_attr( strtolower( $is_status ) ); ?>">
							<?php echo esc_html( $is_status ); ?>
                        </td>
                        <td class="action_col">
							<?php if ( true === $is_expired ) {
								esc_html_e( 'N/A', 'performancein' );
							} else {
								$edit_job_url = wp_nonce_url(
									add_query_arg(
										array(
											'id'   => get_the_ID(),
											'type' => base64_encode( $pi_product )
										),
										site_url( '/job-edit/' )
									),
									'edit_job_page_nonce',
									'security'
								);
								?>
                                <a class="edit_job" href="<?php echo esc_url( $edit_job_url ); ?>"><?php esc_html_e( 'Edit Job', 'performancein' ); ?></a>
							<?php } ?>
                        </td>
                    </tr>
				<?php
				endwhile;
			} else {
				?>
                <tr>
                    <td colspan="3"><?php esc_html_e( 'You have not posted any jobs yet!', 'performancein' ); ?></td>
                </tr>
				<?php
			}
			?>

            </tbody>
        </table>
        <h2><?php esc_html_e( 'Recruiter Details', 'performancein' ); ?></h2>
        <hr>
        <p><b><?php esc_html_e( 'Recruiter Logo:', 'performancein' ); ?></b></p>
        <p>
			<?php printf( "<em>%s <strong> %s </strong>%s </em>",
				esc_html__( 'Recruiter logos will appear on', 'performancein' ),
				esc_html__( 'featured job listings', 'performancein' ),
				esc_html__( ',and will be resized to fit within 218x97 pixels.', 'performancein' )
			); ?>
        </p>
        <img src="<?php echo esc_url( $recruiter_logo_url ); ?>" alt="<?php echo esc_attr( $recruiter_company_name ); ?>">
        <form method="POST" action="javascript:void(0);" class="recruiter-form" enctype="multipart/form-data">
			<?php wp_nonce_field( 'recruiter_form_nonce', 'recruiter_form_name' ); ?>
            <p>
                <label for="id_image"><?php esc_html_e( 'Recruiter Logo', 'performancein' ); ?></label>
                <input id="id_image" name="image" type="file" accept=".png,.gif,.jpeg,.jpg">
            </p>
            <p>
                <label for="id_recruiter_name"><?php esc_html_e( 'Recruiter/Company Name', 'performancein' ); ?></label>
                <input id="id_recruiter_name" maxlength="255" name="recruiter_name" type="text" value="<?php echo esc_attr( $recruiter_company_name ); ?>">
            </p>
            <p>
                <input class="button" type="submit" id="recruiter_button" value="<?php esc_html_e( 'Update Details', 'performancein' ); ?>">
            </p>
        </form>
    </div>
	<?php
}

/**
 * Profile hub edit page in account html.
 * * @param $user
 */
function pi_profile_hub_edit_html( $user ) {
	$args          = array(
		'posts_per_page'         => 1,
		'post_type'              => 'pi_partner_networks',
		'post_status'            => array( 'draft', 'publish' ),
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'meta_query'             => array( /* phpcs:ignore */
			array(
				'key'   => 'pi_user_selection',
				'value' => $user->ID,
			)
		),
		'update_post_term_cache' => false,
		'no_found_rows'          => true,
	);
	$the_query     = new WP_Query( $args );
	$pi_company_id = isset( $the_query->posts[0] ) ? $the_query->posts[0] : 0;

	?>
    <div class="account_details">
        <h2><?php esc_html_e( 'Edit Your Profile', 'performancein' ); ?></h2>
        <p>
            <a href="<?php echo esc_url( get_the_permalink( $pi_company_id ) ); ?>" class="button"><?php esc_html_e( 'View your profile', 'performancein' ); ?></a>
            <a href="javascript:void(0)" id="js_toggle_subscription_info" class="button"><?php esc_html_e( 'Subscription Info', 'performancein' ); ?></a>
        </p>
		<?php echo do_shortcode( '[performancein_company_profile_form company_id=' . $pi_company_id . ']' ); ?>


        <p><a href="/membership-terms/"><?php esc_html_e( 'Membership Terms &amp; Conditions', 'performancein' ); ?></a></p>
    </div>
	<?php
}

function pi_is_partner_account( $user ) {
	$company_allow_user_roles = array(
		'account',
		'editor',
		'administrator',
	);
	if ( is_user_logged_in() && in_array( $user->roles[0], $company_allow_user_roles, true ) ) {
		if ( 'account' === $user->roles[0] ) {
			$args  = array(
				'post_type'   => 'pi_partner_networks',
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'     => 'pi_user_selection',
						'value'   => $user->ID,
						'compare' => 'IN'
					),
				),
			);
			$query = new WP_Query( $args );
			if ( isset( $query->posts[0]->ID ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	return false;
}

function pi_account_not_found_page_html() {
	?>
    <h1><?php esc_html_e( 'Page not found', 'performancein' ); ?></h1>
    <h2><?php esc_html_e( 'The abyss only has four corners', 'performancein' ); ?></h2>
    <p><?php esc_html_e( 'This was not one of them or the page you were looking for.', 'performancein' ); ?></p>
    <a href="/"><?php esc_html_e( 'Return to the homepage', 'performancein' ); ?></a>
	<?php
}

/**
 * function to add the winner images
 */
function pi_process_image( $file, $post_id = 0 ) {

	if ( ! empty( $file ) ) {

		if ( empty( $_FILES[ $file ]['error'] ) && $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) {
			__return_false();
		}
		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		$attachment_id = media_handle_upload( $file, $post_id );
		if ( ! is_wp_error( $attachment_id ) && ! empty( $attachment_id ) ) {
			return $attachment_id;
		}
	}

	return 0;
}

/**
 * New user confirmation email.
 *
 * @param $user_id
 *
 * @return bool
 */
function pi_wp_new_user_notification( $user_id ) {
	if ( 0 === $user_id ) {
		return false;
	}

	// get user data
	$user_info = get_userdata( $user_id );
	// create md5 code to verify later
	$code = md5( time() );
	// make it into a code to send it to user via email
	$string = array( 'id' => $user_id, 'code' => $code );
	// create the activation code and activation status
	update_user_meta( $user_id, 'activation_code', $code );
	update_user_meta( $user_id, 'is_confirm', false );

	ob_start(); ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo __( 'Registration Confirmation' ); ?></title>
    </head>

    <body>
    <p>
		<?php echo __( 'Thank you for registering your account with PerformanceIN.' ); ?>
    </p>
    <p>
		<?php echo __( 'Please confirm your registration by visiting this link:' ); ?>
        <a href="<?php echo network_site_url( "account/confirm/?account=" . $user_info->ID . "&code=" . base64_encode( wp_json_encode( $string ) ) ); ?>"><?php echo network_site_url( "account/confirm/?account=" . $user_info->ID . "&code=" . base64_encode( wp_json_encode( $string ) ) ); ?></a>
    </p>
    </body>
    </html>
	<?php
	$message = ob_get_clean();

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	/* translators: %s: user login */
	/*$message = __( 'Thank you for registering your account with PerformanceIN.' ) . "\r\n\r\n";
	$message .= __( 'Please confirm your registration by visiting this link:' );
	$message .= network_site_url( "account/confirm/?account=" . $user_info->ID . "&code=" . base64_encode( wp_json_encode( $string ) ) );*/

	// send an email out to user
	return wp_mail( $user_info->user_email, __( 'PerformanceIN Account Confirmation', 'performancein' ), $message, $headers );
}

/**
 * Get the saved credit packages by user id.
 *
 * @param $user_id
 *
 * @return string
 */
function pi_get_credit_package( $user_id ) {
	return get_the_author_meta( 'pi_credit_package', $user_id );
}

/**
 * Get the matching credit packages by product id.
 *
 * @param $credit_package
 * @param int $product_id
 *
 * @return int|mixed
 */
function pi_get_credit( $credit_package, $product_id = 0 ) {
	if ( 0 === $product_id ) {
		return 0;
	}
	$credit_package = isset( $credit_package ) ? json_decode( $credit_package, true ) : array();

	return isset( $credit_package[ $product_id ] ) && ! empty( $credit_package[ $product_id ] ) ? $credit_package[ $product_id ] : 0;
}

/**
 * Get the matching credit packages by product id.
 *
 * @param $credit_package
 * @param int $product_id
 * @param int $credit
 *
 * @return int|mixed
 */
function pi_add_credit( $credit_package, $items ) {
	if ( empty( $items ) ) {
		return $credit_package;
	}
	$credit_package = isset( $credit_package ) ? json_decode( $credit_package, true ) : array();
	foreach ( $items as $item ) {
		if ( isset( $credit_package[ $item['product_id'] ] ) && ! empty( $credit_package[ $item['product_id'] ] ) ) {
			$credit_package[ $item['product_id'] ] = $credit_package[ $item['product_id'] ] + $item['quantity'];
		} else {
			$credit_package[ $item['product_id'] ] = $item['quantity'];
		}
	}

	return $credit_package;
}

/**
 * Updated credit data.
 *
 * @param $credit_package
 * @param $update_credit
 * @param int $product_id
 * @param bool $json
 *
 * @return array|false|int|mixed|string
 */
function pi_update_credit( $credit_package, $update_credit, $product_id = 0, $json = true ) {
	if ( 0 === $product_id ) {
		return 0;
	}
	$credit_package = isset( $credit_package ) ? json_decode( $credit_package, true ) : array();
	if ( isset( $credit_package[ $product_id ] ) ) {
		$credit_package[ $product_id ] = $update_credit;
	}
	if ( true === $json ) {
		return wp_json_encode( $credit_package );
	}

	return $credit_package;
}

/**
 * Function to return breadcums html
 *
 * @param $post_id
 */
function pi_breadcums_structure( $post_id ) { ?>
    <ul class="pi-breadcums-lists">
		<?php if ( is_singular( 'post' ) ) {
			$categories          = wp_get_post_categories( $post_id );
			$pi_primary_category = array(
				get_field( 'pi_primary_category', get_the_ID() ),
			);
			if ( ! empty( $pi_primary_category ) ) {
				$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
				$categories = array_unique( $categories );
			}
			$html = '';
			$html .= '<li><a href="' . esc_url( site_url() ) . '">' . esc_html__( 'Home', 'performancein' ) . '</a></li>';
			if ( ! empty( $categories ) && is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$term = get_term( $category );
					if ( 1 !== $category ) {
						$cat_name = $term->name;
						$cat_link = get_term_link( $term );
						$html     .= '<li><a href="' . $cat_link . '">' . $cat_name . '</a></li>';
					}

				}
			}

		} elseif ( is_singular( 'pi_events' ) ) { ?>
			<?php
			$eventLabel = pi_custom_post_type_label( $post_id );
			$html       = '';
			$html       .= '<li><a href="' . esc_url( site_url() ) . '">' . esc_html__( 'Home', 'performancein' ) . '</a></li>';
			$html       .= '<li><a href="' . site_url() . '/events">' . esc_html__( $eventLabel, 'performancein' ) . '</a></li>';

		}

		return $html;
		?>
    </ul>
<?php }

/**
 * Function to return breadcums html
 *
 * @param $post_id
 */
function pi_breadcums_structure_amp( $post_id ) { ?>
    <ul class="pi-breadcums-lists">
		<?php if ( is_singular( 'post' ) ) {
			$categories          = wp_get_post_categories( $post_id );
			$pi_primary_category = array(
				get_field( 'pi_primary_category', get_the_ID() ),
			);
			if ( ! empty( $pi_primary_category ) ) {
				$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
				$categories = array_unique( $categories );
			}
			$html = '';
			if ( ! empty( $categories ) && is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$term = get_term( $category );
					if ( 1 !== $category ) {
						$cat_name = $term->name;
						$cat_link = get_term_link( $term );
						$html     .= '<li><a href="' . $cat_link . '">' . $cat_name . '</a></li>';
					}

				}
			}

		} elseif ( is_singular( 'pi_events' ) ) { ?>
			<?php
			$eventLabel = pi_custom_post_type_label( $post_id );
			$html       = '';
			$html       .= '<li><a href="' . site_url() . '/events">' . esc_html__( $eventLabel, 'performancein' ) . '</a></li>';

		}

		return $html;
		?>
    </ul>
<?php }


/**
 * Function to return category slug
 *
 * @param $post_id
 */
function pi_categories_name( $post_id ) {
	$categoryName = array();
	if ( is_singular( 'post' ) ) {
		$categories = wp_get_post_categories( $post_id );
		foreach ( $categories as $category ) {
			$term = get_term( $category );
			if ( 1 !== $category ) {
				$categoryName[] = $term->slug;
			}
		}

	}

	return $categoryName;

}

/**
 * Funtion to return post type labels
 *
 * @param $post_id
 *
 * @return mixed
 */
function pi_custom_post_type_label( $post_id ) {
	$postType = get_post_type( $post_id );
	$pt       = get_post_type_object( $postType );
	$label    = $pt->labels->name;

	return $label;
}

/**
 * Function to return array of global setting of theme.
 * @return array
 */
function pi_theme_setting() {
	$headerLogo               = get_field( 'pi_performancein_header_logo', 'option' );
	$footerLogo               = get_field( 'pi_performancein_footer_logo', 'option' );
	$performanceINTagline     = get_field( 'pi_performancein_tag_line', 'option' );
	$joinPatnerNetwork        = get_field( 'pi_join_patner_network', 'option' );
	$joinPatnerNetworkEnable  = $joinPatnerNetwork['pi_join_patner_network_enable__disable'];
	$joinPatnerNetworkTagline = $joinPatnerNetwork['pi_join_section_tagline'];
	$joinPatnerNetworkImage   = $joinPatnerNetwork['join_section_image'];
	$joinPatnerNetworkLink    = $joinPatnerNetwork['pi_join_section_link'];
	$searchVisible            = get_field( 'pi_header_search_visible', 'option' );
	$copyRightContent         = get_field( 'pi_copyright_content', 'option' );
	$facebookLink             = get_field( 'pi_general_facebook_link', 'option' );
	$twitterLink              = get_field( 'pi_general_twitter_link', 'option' );
	$linkdeinLink             = get_field( 'pi_general_linkedin_link', 'option' );
	$youtubeLink              = get_field( 'pi_general_youtube_link', 'option' );
	$loginPageLink            = get_field( 'pi_login_page_link', 'option' );
	$logoutPageLink           = get_field( 'pi_logout_page_link', 'option' );
	$myaccountPageLink        = get_field( 'pi_my_account_page_link', 'option' );
	$youMayLikePerPage        = get_field( 'pi_you_may_like_per_page', 'option' );
	$facebookAppID            = get_field( 'pi_facebook_app_id', 'option' );
	$PostPerPageCategory      = get_field( 'pi_category_post_per_page', 'option' );
	$authorPostsPerPage       = get_field( 'pi_author_post_list_per_page', 'option' );
	$searchPerPage            = get_field( 'pi_search_post_list_per_page', 'option' );
	$settingArray             = array(
		'join_patner_network_enable'  => $joinPatnerNetworkEnable,
		'join_patner_network_tagline' => $joinPatnerNetworkTagline,
		'join_patner_network_image'   => $joinPatnerNetworkImage,
		'join_patner_network_link'    => $joinPatnerNetworkLink,
		'header_search_visible'       => $searchVisible,
		'facebook_link'               => $facebookLink,
		'twitter_link'                => $twitterLink,
		'linkedin_link'               => $linkdeinLink,
		'youtube_link'                => $youtubeLink,
		'login_page_link'             => $loginPageLink,
		'logout_page_link'            => $logoutPageLink,
		'my_account_page_link'        => $myaccountPageLink,
		'you_may_like_per_page'       => $youMayLikePerPage,
		'facebook_app_id'             => $facebookAppID,
		'category_post_per_page'      => $PostPerPageCategory,
		'author_post_per_page'        => $authorPostsPerPage,
		'search_post_per_page'        => $searchPerPage,
	);

	return $settingArray;
}


/**
 * Retrieve password email.
 *
 * @param $user_login
 *
 * @return bool
 */
function pi_retrieve_password( $user_login ) {
	global $wpdb, $wp_hasher;

	$user_login = sanitize_text_field( $user_login );

	if ( empty( $user_login ) ) {
		return false;
	} else if ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( $user_login ) );
		if ( empty( $user_data ) ) {
			return false;
		}
	} else {
		$login     = trim( $user_login );
		$user_data = get_user_by( 'login', $login );
	}

	do_action( 'lostpassword_post' );

	if ( ! $user_data ) {
		return false;
	}

	// redefining user_login ensures we return the right case in the email
	$display_name = $user_data->display_name;
	$user_login   = $user_data->user_login;
	$user_email   = $user_data->user_email;

	do_action( 'retreive_password', $user_login );  // Misspelled and deprecated
	do_action( 'retrieve_password', $user_login );

	$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

	if ( ! $allow ) {
		return false;
	} else if ( is_wp_error( $allow ) ) {
		return false;
	}

	$key = wp_generate_password( 20, false );
	do_action( 'retrieve_password_key', $user_login, $key );

	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

	ob_start(); ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo __( 'Password Reset' ); ?></title>
    </head>

    <body>
    <p>
		<?php echo sprintf( __( 'Dear : %s' ), $display_name ); ?>
    </p>

    <p>
		<?php echo __( 'A password reset was requested for your account.' ); ?>
    </p>
    <p>
		<?php echo __( 'You can reset your password by visiting this link:' ); ?>
        <a href="<?php echo site_url( "/account/password-reset/?email={$user_email}&code={$hashed}" ); ?>"><?php echo site_url( "/account/password-reset/?email={$user_email}&code={$hashed}" ); ?></a>
    </p>
    </body>
    </html>
	<?php
	$message = ob_get_clean();
	/*$message = sprintf( __( 'Dear : %s' ), $display_name ) . "\r\n\r\n";
	$message .= __( 'A password reset was requested for your account.' ) . "\r\n\r\n";
	$message .= __( 'You can reset your password by visiting this link:' ) . "\r\n\r\n";
	$message .= site_url( "/account/password-reset/?email={$user_email}&code={$hashed}" );*/
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$headers  = array( 'Content-Type: text/html; charset=UTF-8' );
	$title    = sprintf( __( '[%s] Account Password Reset' ), $blogname );

	return wp_mail( $user_email, $title, $message, $headers );
}


/**
 * Get salary.
 *
 * @param int $min_salary min salary amount.
 * @param int $max_salary max salary amount.
 *
 * @return string salary string with currency..
 */
function pi_get_salary( $min_salary, $max_salary ) {
	$min_salary = number_format( $min_salary );
	$max_salary = number_format( $max_salary );
	if ( empty( intval( $min_salary ) ) || empty( intval( $max_salary ) ) ) {
		return sprintf( __( 'Salary Undisclosed ', 'performancein' ) );
	}

	if ( 0 === intval( $min_salary ) ) {
		return sprintf( __( 'up to ', 'performancein' ) . '%s%s', esc_html( get_woocommerce_currency_symbol() ), esc_html( $max_salary ) );
	} elseif ( intval( $min_salary ) === intval( $max_salary ) ) {
		return get_woocommerce_currency_symbol() . intval( $min_salary );
	}

	return sprintf( "%s-%s", get_woocommerce_currency_symbol() . $min_salary, get_woocommerce_currency_symbol() . $max_salary );

}


/**
 * Job fields
 *
 * @param int $post_id job post.
 *
 * @return array
 */
function pi_get_job_field_values( $post_id = 0 ) {
	$job_field_values = array();

	$job_field_values['job_title']           = '';
	$job_field_values['job_type']            = '';
	$job_field_values['contract_length']     = '';
	$job_field_values['geographic_location'] = '';
	$job_field_values['description']         = '';
	$job_field_values['minimum_salary']      = '';
	$job_field_values['maximum_salary']      = '';
	$job_field_values['closing_date']        = '';
	$job_field_values['contact_phone']       = '';
	$job_field_values['contact_email']       = '';
	$job_field_values['street_address']      = '';
	$job_field_values['post_code']           = '';
	$job_field_values['address_region']      = '';
	$job_field_values['address_country']     = '';
	$job_field_values['pi_jobs_employer']    = '';
	$job_field_values['term_list_ids']       = array();
	if ( isset( $post_id ) && ! empty( $post_id ) ) {
		$job_field_values['job_title']           = get_the_title( $post_id );
		$job_field_values['job_type']            = get_field( 'pi_job_type', $post_id );
		$job_field_values['contract_length']     = get_field( 'pi_contract_length', $post_id );
		$job_field_values['geographic_location'] = get_field( 'pi_geographic_location', $post_id );
		$job_field_values['description']         = get_field( 'pi_description', $post_id );
		$job_field_values['minimum_salary']      = get_field( 'pi_minimum_salary', $post_id );
		$job_field_values['maximum_salary']      = get_field( 'pi_maximum_salary', $post_id );
		$job_field_values['closing_date']        = get_field( 'pi_closing_date', $post_id );
		$job_field_values['contact_phone']       = get_field( 'pi_contact_phone', $post_id );
		$job_field_values['contact_email']       = get_field( 'pi_contact_email', $post_id );
		$job_field_values['street_address']      = get_field( 'pi_jobs_schema_streetaddress', $post_id );
		$job_field_values['post_code']           = get_field( 'pi_jobs_schema_postalcode', $post_id );
		$job_field_values['address_region']      = get_field( 'pi_jobs_schema_addressregion', $post_id );
		$job_field_values['address_country']     = get_field( 'pi_jobs_schema_addresscountry', $post_id );
		$job_field_values['pi_jobs_employer']    = get_field( 'pi_jobs_employer', $post_id );
		$job_field_values['term_list_ids']       = wp_get_object_terms( $post_id, 'pi_cat_jobs', array( 'fields' => 'ids' ) );
	}

	return $job_field_values;

}


function pi_get_available_packages() {
	$args          = array(
		'post_type'   => 'product',
		'post_status' => 'publish',
		'tax_query'   => array(
			array(
				'taxonomy' => 'product_cat',
				'terms'    => 'partner-packages',
				'field'    => 'slug',
				'operator' => 'IN'
			),
		),
		'orderby'     => 'taxonomy, id', // Just enter 2 parameters here, seprated by comma
		'order'       => 'DESC'
	);
	$products      = new WP_Query( $args );
	$package_value = [];
	if ( $products->have_posts() ) {
		while ( $products->have_posts() ) {
			$products->the_post();
			$package_value[] = get_the_ID();
		}
	}

	return $package_value;
}


/**
 * Job applied send email function.
 *
 * @param $body_content
 *
 * @return bool
 */
function pi_job_applied_send_email( $body_content ) {
	$body_content['job_id'];

	//extra jobs amendments
	$jobsAmendments = get_field('pi_job_application_amendments','option');

	$name            = $body_content['email'];
	$subject         = sprintf( __( '[%s] applied for "%s' ), $name, get_the_title( $body_content['job_id'] ) );
	$message         = $body_content['cover_description'] . "\n". $jobsAmendments;
	$to_email        = $body_content['to_email'];
	$attachment_url  = get_attached_file( $body_content['attachment_id'] );
	$mail_attachment = array( $attachment_url );
	$headers         = array( 'Content-Type: text/html; charset=UTF-8' );

	return wp_mail( $to_email, $subject, $message, $headers, $mail_attachment );
}

/**
 * Check is featured package or not.
 *
 * @param $product_id
 *
 * @return bool
 */
function pi_is_featured_package( $product_id ) {
	$product = get_post( $product_id );
	if ( 'product' === $product->post_type ) {
		$_is_featured = get_post_meta( $product_id, '_is_featured', true );
		if ( true === (bool) $_is_featured ) {
			return true;
		}

		return false;
	}

	return false;

}

/**
 * get remaining days of the job.
 *
 * @param $date
 * @param string $format
 *
 * @return false|float
 */
function pi_get_remaining_days( $date, $format = 'd-m-Y' ) {
	$date = str_replace( '/', '-', $date );
	$diff = strtotime( $date ) - strtotime( date( $format ) );

	return floor( $diff / ( 60 * 60 * 24 ) );
}


/**
 * get remaining days of the job.
 *
 * @param $date
 * @param string $format
 *
 * @return false|float
 */
function pi_is_expired_job( $closing_date, $format = 'd-m-Y' ) {
	$closing_date = str_replace( '/', '-', $closing_date );

	$expire = strtotime( $closing_date . ' + 1 days' );
	$today  = strtotime( "today midnight" );

	if ( $today >= $expire ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Get added dates for job.
 *
 * @param $post_date
 * @param string $format
 *
 * @return mixed
 * @throws Exception
 */
function pi_get_since_added_days( $post_date, $format = 'd-m-Y' ) {

	$today_obj     = new DateTime( date( $format, strtotime( 'today' ) ) ); // Get today's Date Object
	$post_date_obj = new DateTime( date( $format, strtotime( $post_date ) ) ); // Get the registration Date Object
	$interval_obj  = $today_obj->diff( $post_date_obj );

	return $interval_obj->days;
}

/**
 * Get the recruiter logo.
 *
 * @param $user_id
 * @param bool $default
 *
 * @return string
 */
function pi_get_recruiter_logo( $user_id, $default = false ) {
	$recruiter_logo = get_field( 'pi_recruiter_logo', "user_{$user_id}" );
	if ( isset( $recruiter_logo ) && ! empty( $recruiter_logo ) ) {
		return wp_get_attachment_url( $recruiter_logo );
	}
	if ( true === $default ) {
		return get_template_directory_uri() . '/assets/images/no-recruiter-image.png';
	}

	return '';
}

/**
 * Sign in url.
 *
 * @param $client_id
 * @param $redirect_url
 *
 * @return string
 */
function pi_get_google_sign_in_url( $client_id, $redirect_url ) {
	return add_query_arg(
		array(
			'scope'           => urlencode( 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email' ),
			'redirect_uri'    => urlencode( $redirect_url ),
			'response_type'   => 'code',
			'client_id'       => $client_id,
			'access_type'     => 'online',
			'approval_prompt' => 'force'
		),
		'https://accounts.google.com/o/oauth2/auth'
	);
}

/**
 *
 * @param $user_info
 *
 * @return string|void
 */
function pi_social_user_registration( $user_info ) {
	$email          = $user_info['email'];
	$first_name     = $user_info['given_name'];
	$last_name      = $user_info['family_name'];
	$verified_email = $user_info['verified_email'];
	$nickname       = $user_info['name'];
	$username       = wc_create_new_customer_username( $email );
	$username       = sanitize_user( $username );
	$password       = wp_generate_password();

	$user_data       = array(
		'user_login' => $username,
		'user_email' => $email,
		'user_pass'  => $password,
		'first_name' => $first_name,
		'last_name'  => $last_name,
		'nickname'   => $nickname,
		'role'       => 'customer',
	);
	$new_customer_id = wp_insert_user( $user_data );
	if ( ! is_wp_error( $new_customer_id ) ) {
		setcookie( 'performancein_cookie', $email . '+' . $username, time() + 62208000, '/', $_SERVER['HTTP_HOST'] );
		//$user_email_status = pi_wp_new_user_notification( $new_customer_id );
		update_user_meta( $new_customer_id, 'is_confirm', (bool) $verified_email );
		update_user_meta( $new_customer_id, 'is_link_with_google', true );

		return site_url( 'account/complete-profile/' );
	}

	return home_url();
}


/**
 * Order by job [featured, standard and expired]
 *
 * @return array
 */
function pi_get_job_orderby() {
	$args               = array(
		'post_type'   => 'product',
		'post_status' => 'publish',
		'fields'      => 'ids',
		'orderby'     => 'meta_value',
		'meta_query'  => array(
			'relation' => 'OR',
			array(
				'meta_key'   => '_is_featured',
				'meta_value' => '1',
			)
		),
		'tax_query'   => array(
			array(
				'taxonomy' => 'product_cat',
				'terms'    => 'job-package',
				'field'    => 'slug',
				'operator' => 'IN'
			),
		),
	);
	$products_query     = new WP_Query( $args );
	$featured_array     = array();
	$non_featured_array = array();
	foreach ( $products_query->posts as $product_id ) {
		$_is_featured = get_post_meta( $product_id, '_is_featured', true );
		if ( true === (bool) $_is_featured ) {
			$featured_array[] = $product_id;
		} else {
			$non_featured_array [] = $product_id;
		}
	}
	$job_package_value = array_merge( $featured_array, $non_featured_array );

	global $wpdb;
	$date               = date( "Ymd" );
	$prepare_args       = array();
	$product_meta_query = "SELECT {$wpdb->posts}.ID, mt1.meta_value, mt2.meta_value ";

	if ( isset( $job_package_value ) && ! empty( $job_package_value ) ) {
		$product_meta_query .= ", case ";
		$position           = 1;
		foreach ( $job_package_value as $key => $value ) {
			$position           = $key + 1;
			$product_meta_query .= " when (mt1.meta_value = %d AND mt2.meta_key = '%s' AND CAST(mt2.meta_value AS DATE) >= '%s') then %d ";
			$prepare_args[]     = $value;
			$prepare_args[]     = 'pi_closing_date';
			$prepare_args[]     = $date;
			$prepare_args[]     = $position;
		}
		$product_meta_query .= "else %d end ";
		$prepare_args[]     = $position + 1;
	}
	$product_meta_query .= "AS pi_order_type
    FROM {$wpdb->posts}
    LEFT JOIN {$wpdb->postmeta} AS mt1
    ON ( {$wpdb->posts}.ID = mt1.post_id )
    LEFT JOIN {$wpdb->postmeta} AS mt2
    ON ( {$wpdb->posts}.ID = mt2.post_id )
    WHERE ( mt2.meta_key = '%s')
    AND ( mt1.meta_key = '%s' ";
	$prepare_args[]     = 'pi_closing_date';
	$prepare_args[]     = 'pi_jobs_packages';
	if ( isset( $job_package_value ) && ! empty( $job_package_value ) ) {
		$package_prepare_string = implode( ',', array_fill( 0, count( $job_package_value ), '%d' ) );
		$product_meta_query     .= " AND mt1.meta_value IN ({$package_prepare_string}) ) ";
		$prepare_args           = array_merge( $prepare_args, array_map( 'intval', $job_package_value ) );
	}
	$product_meta_query .= " AND wp_posts.post_type = '%s'
    AND wp_posts.post_status = '%s'
    ORDER BY pi_order_type , {$wpdb->posts}.post_date DESC";
	$prepare_args[]     = 'pi_jobs';
	$prepare_args[]     = 'publish';
	$jobs_results       = $wpdb->get_results( $wpdb->prepare( $product_meta_query, $prepare_args ), ARRAY_A );

	$job_ids = array();
	if ( isset( $jobs_results ) && ! empty( $jobs_results ) ) {
		$job_ids = array_map( "pi_get_jobs_ids_functions", $jobs_results );
	}

	return $job_ids;
}

/**
 * Function to return limit content of paragraph
 *
 * @param $partnersDescription
 * @param $length
 *
 * @return false|string
 */
function pi_limit_content( $partnersDescription, $length ) {
	$partnersDescription = strip_tags( $partnersDescription );
	if ( strlen( $partnersDescription ) > $length ) {

		// truncate string
		$pistringCut = substr( $partnersDescription, 0, $length );
		$piendPoint  = strrpos( $pistringCut, ' ' );

		//if the string doesn't contain any space then it will cut without word basis.
		$partnersDescription = $piendPoint ? substr( $pistringCut, 0, $piendPoint ) : substr( $piendPoint, 0 );
		$partnersDescription .= '... ';
	}

	return $partnersDescription;
}


/**
 * Get jobs ids.
 *
 * @param $job
 *
 * @return int|mixed
 */
function pi_get_jobs_ids_functions( $job ) {
	return isset( $job['ID'] ) ? $job['ID'] : 0;
}


/**
 * Load more pagination
 *
 * @param $query_obj
 * @param $page_name
 * @param string $ajax_action
 * @param string $class
 * @param string $nonce
 * @param string $extra_fields
 */
function pi_load_more_with_pagination( $query_obj, $page_number, $ajax_action = 'pi_job_listing', $class = 'listing-item', $nonce = 'pagination_nonce', $extra_fields = '', $number_pagination = true ) {
	wp_enqueue_script( 'performancein-custom' );
	if ( $page_number < $query_obj->max_num_pages ) {
		?>
        <div class="pi_endless_container <?php echo esc_attr( $class ); ?>-show">
            <div class="pi_endless_more" data-loading="on"
                 data-action="<?php echo esc_attr( $ajax_action ); ?>"
                 data-security="<?php echo esc_attr( wp_create_nonce( $nonce ) ); ?>"
                 data-class="<?php echo esc_attr( $class ); ?>"
                 data-extra-fields="<?php echo esc_attr( $extra_fields ); ?>"
                 data-page="<?php echo esc_attr( $page_number + 1 ); ?>"
                 data-total_pages="<?php echo esc_attr( $query_obj->max_num_pages ); ?>" rel="page">
				<?php esc_html_e( 'More', 'performancein' ); ?>
            </div>
            <div class="pi_endless_loading" style="display: none;"><?php esc_html_e( 'Loading', 'performancein' ); ?></div>
        </div>
		<?php
	}
	if ( true === (bool) $number_pagination ) {
		?>
        <div class="pi-main-pagination">
			<?php
			if ( $query_obj->max_num_pages !== 0 ) {
				pi_pagination_html( $query_obj, $ajax_action, $class, $nonce, $extra_fields );
			}
			?>
        </div>
		<?php
	}

}

/**
 * Pagination html
 *
 * @param $query_obj
 * @param string $ajax_action
 * @param string $class
 * @param string $nonce
 * @param string $extra_fields
 */
function pi_pagination_html( $query_obj, $ajax_action = 'pi_job_listing', $class = 'listing-item', $nonce = 'pagination_nonce', $extra_fields = '' ) {

	$request_url = filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL );

	// Current page.
	$current_page = (int) $query_obj->query_vars['paged'];
	// The overall amount of pages.
	$max_page = $query_obj->max_num_pages;

	// We don't have to display pagination or load more button in this case.
	if ( $max_page <= 1 ) {
		echo '';
	}

	// Set the current page to 1 if not exists.
	if ( empty( $current_page ) || $current_page === 0 ) {
		$current_page = 1;
	}

	// You can play with this parameter - how much links to display in pagination.
	$links_in_the_middle         = 8;
	$links_in_the_middle_minus_1 = $links_in_the_middle - 1;

	// The code below is required to display the pagination properly for large amount of pages.
	$first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1 / 2 );
	$last_link_in_the_middle  = $current_page + ceil( $links_in_the_middle_minus_1 / 2 );

	// Some calculations with $first_link_in_the_middle and $last_link_in_the_middle.
	if ( $first_link_in_the_middle <= 0 ) {
		$first_link_in_the_middle = 1;
	}
	if ( ( $last_link_in_the_middle - $first_link_in_the_middle ) !== $links_in_the_middle_minus_1 ) {
		$last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1;
	}
	if ( $last_link_in_the_middle > $max_page ) {
		$first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1;
		$last_link_in_the_middle  = (int) $max_page;
	}
	if ( $first_link_in_the_middle <= 0 ) {
		$first_link_in_the_middle = 1;
	}
	ob_start();
	if ( empty( $request_url ) ) {
		$request_host   = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL );
		$request_scheme = filter_input( INPUT_SERVER, 'REQUEST_SCHEME', FILTER_SANITIZE_STRING );
		$request_part   = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );

		$request_url = $request_host . $request_part;
	}
	$request_url = remove_query_arg( array( 'pid' ), $request_url );
	?>
    <nav class="pagination-box">
        <ul class="pagination_index <?php echo esc_attr( $class ); ?>-show">
			<?php
			if ( $first_link_in_the_middle >= 3 && $links_in_the_middle < $max_page ) {

				?>
                <li>
                    <a href="<?php echo esc_url( $request_url ); ?>"
                       data-security="<?php echo esc_attr( wp_create_nonce( $nonce ) ); ?>"
                       data-action="<?php echo esc_attr( $ajax_action ); ?>"
                       data-class="<?php echo esc_attr( $class ); ?>"
                       data-extra-fields="<?php echo esc_attr( $extra_fields ); ?>"
                       rel="page"
                       class="pi_endless_page_link page_number_1">
                        1
                    </a>
                </li>
				<?php
				if ( $first_link_in_the_middle !== 2 ) {
					?>
                    <li>...</li>
					<?php
				}
			}
			for ( $lopp_number = $first_link_in_the_middle; $lopp_number <= $last_link_in_the_middle; $lopp_number ++ ) {
				if ( (int) $lopp_number === (int) $current_page ) {
					?>
                    <li><strong><?php echo esc_html( $lopp_number ) ?></strong></li>
					<?php
				} else {
					?>
                    <li class="11">
                        <a
							<?php if ( 1 === (int) $lopp_number ) { ?>
                                href="<?php echo esc_url( $request_url ); ?>"
							<?php } else { ?>
                                href="<?php echo esc_url( add_query_arg( array( 'pid' => $lopp_number ), $request_url ) ); ?>"
							<?php } ?>
                                data-security="<?php echo esc_attr( wp_create_nonce( $nonce ) ); ?>"
                                data-action="<?php echo esc_attr( $ajax_action ); ?>"
                                data-class="<?php echo esc_attr( $class ); ?>"
                                data-extra-fields="<?php echo esc_attr( $extra_fields ); ?>"
                                rel="page"
                                class="pi_endless_page_link page_number_<?php echo esc_attr( $lopp_number ); ?>">
							<?php echo esc_html( $lopp_number ); ?>
                        </a>
                    </li>
					<?php
				}
			}

			if ( $last_link_in_the_middle < $max_page ) {

				if ( $last_link_in_the_middle !== ( $max_page - 1 ) ) {
					?>
                    <li>...</li>
					<?php
				}
				?>
                <li>
                    <a
						<?php if ( 1 === (int) $max_page ) { ?>
                            href="<?php echo esc_url( $request_url ); ?>"
						<?php } else { ?>
                            href="<?php echo esc_url( add_query_arg( array( 'pid' => $max_page ), $request_url ) ); ?>"
						<?php } ?>
                            data-security="<?php echo esc_attr( wp_create_nonce( $nonce ) ); ?>"
                            data-action="<?php echo esc_attr( $ajax_action ); ?>"
                            data-class="<?php echo esc_attr( $class ); ?>"
                            data-extra-fields="<?php echo esc_attr( $extra_fields ); ?>"
                            rel="page"
                            class="pi_endless_page_link page_number_<?php echo esc_attr( $max_page ); ?>">
						<?php echo esc_html( $max_page ); ?>
                    </a>
                </li>
				<?php
			}

			?>
        </ul>
    </nav>
	<?php
	$pagination_html = ob_get_clean();

	$allow_html = array(
		'nav'    => array(
			'class' => array(),
		),
		'ul'     => array(
			'class'      => array(),
			'data-nonce' => array(),
		),
		'li'     => array(
			'class' => array(),
		),
		'strong' => array(
			'class' => array(),
		),
		'a'      => array(
			'class'             => array(),
			'href'              => array(),
			'data-action'       => array(),
			'data-security'     => array(),
			'data-class'        => array(),
			'data-extra-fields' => array(),
			'data-page-number'  => array(),
			'rel'               => array(),
		),
		'span'   => array(
			'class' => array(),
		),
	);
	echo wp_kses( $pagination_html, $allow_html );

}

/**
 * User pagination
 *
 * @param $total_pages
 * @param $page_number
 */
function pi_user_pagination_html( $total_pages, $page_number ) {

	$request_url = filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL );

	// Current page.
	$current_page = (int) $page_number;
	// The overall amount of pages.
	$max_page = $total_pages;

	// We don't have to display pagination or load more button in this case.
	if ( $max_page <= 1 ) {
		echo '';
	}

	// Set the current page to 1 if not exists.
	if ( empty( $current_page ) || $current_page === 0 ) {
		$current_page = 1;
	}

	// You can play with this parameter - how much links to display in pagination.
	$links_in_the_middle         = 3;
	$links_in_the_middle_minus_1 = $links_in_the_middle - 1;

	// The code below is required to display the pagination properly for large amount of pages.
	$first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1 / 2 );
	$last_link_in_the_middle  = $current_page + ceil( $links_in_the_middle_minus_1 / 2 );

	// Some calculations with $first_link_in_the_middle and $last_link_in_the_middle.
	if ( $first_link_in_the_middle <= 0 ) {
		$first_link_in_the_middle = 1;
	}
	if ( ( $last_link_in_the_middle - $first_link_in_the_middle ) !== $links_in_the_middle_minus_1 ) {
		$last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1;
	}
	if ( $last_link_in_the_middle > $max_page ) {
		$first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1;
		$last_link_in_the_middle  = (int) $max_page;
	}
	if ( $first_link_in_the_middle <= 0 ) {
		$first_link_in_the_middle = 1;
	}
	ob_start();
	if ( empty( $request_url ) ) {
		$request_host = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL );
		$request_part = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );

		$request_url = $request_host . $request_part;
	}
	$request_url = remove_query_arg( array( 'pid' ), $request_url );
	?>
    <nav class="pagination-box">
        <ul class="pagination_index listing-item-show">
			<?php
			if ( $first_link_in_the_middle >= 3 && $links_in_the_middle < $max_page ) {

				?>
                <li>
                    <a href="<?php echo esc_url( $request_url ); ?>" rel="page" class="pi_endless_page_link page_number_1">
                        1
                    </a>
                </li>
				<?php
				if ( $first_link_in_the_middle !== 2 ) {
					?>
                    <li>...</li>
					<?php
				}
			}
			for ( $lopp_number = $first_link_in_the_middle; $lopp_number <= $last_link_in_the_middle; $lopp_number ++ ) {
				if ( (int) $lopp_number === (int) $current_page ) {
					?>
                    <li><strong><?php echo esc_html( $lopp_number ) ?></strong></li>
					<?php
				} else {
					?>
                    <li class="11">
                        <a
							<?php if ( 1 === (int) $lopp_number ) { ?>
                                href="<?php echo esc_url( $request_url ); ?>"
							<?php } else { ?>
                                href="<?php echo esc_url( add_query_arg( array( 'pid' => $lopp_number ), $request_url ) ); ?>"
							<?php } ?>
                                rel="page"
                                class="pi_endless_page_link page_number_<?php echo esc_attr( $lopp_number ); ?>">
							<?php echo esc_html( $lopp_number ); ?>
                        </a>
                    </li>
					<?php
				}
			}

			if ( $last_link_in_the_middle < $max_page ) {

				if ( $last_link_in_the_middle !== ( $max_page - 1 ) ) {
					?>
                    <li>...</li>
					<?php
				}
				?>
                <li>
                    <a
						<?php if ( 1 === (int) $max_page ) { ?>
                            href="<?php echo esc_url( $request_url ); ?>"
						<?php } else { ?>
                            href="<?php echo esc_url( add_query_arg( array( 'pid' => $max_page ), $request_url ) ); ?>"
						<?php } ?>
                            rel="page"
                            class="pi_endless_page_link page_number_<?php echo esc_attr( $max_page ); ?>">
						<?php echo esc_html( $max_page ); ?>
                    </a>
                </li>
				<?php
			}

			?>
        </ul>
    </nav>
	<?php
	$pagination_html = ob_get_clean();

	$allow_html = array(
		'nav'    => array(
			'class' => array(),
		),
		'ul'     => array(
			'class'      => array(),
			'data-nonce' => array(),
		),
		'li'     => array(
			'class' => array(),
		),
		'strong' => array(
			'class' => array(),
		),
		'a'      => array(
			'class'            => array(),
			'href'             => array(),
			'data-class'       => array(),
			'data-page-number' => array(),
			'rel'              => array(),
		),
		'span'   => array(
			'class' => array(),
		),
	);
	echo wp_kses( $pagination_html, $allow_html );

}


function pi_partner_custom_query( $package_values, $offset = 0, $items_per_page = 10, $is_limit = false ) {
	global $wpdb;
	$prepare_args       = array();
	$product_meta_query = "SELECT  {$wpdb->posts}.ID , {$wpdb->postmeta}.meta_value";

	if ( isset( $package_values ) && ! empty( $package_values ) ) {
		$product_meta_query .= ", CASE ";
		$position           = 1;
		foreach ( $package_values as $key => $value ) {
			$position           = $key + 1;
			$product_meta_query .= " WHEN {$wpdb->postmeta}.meta_value = %d THEN %d ";
			$prepare_args[]     = $value;
			$prepare_args[]     = $position;
		}
		$product_meta_query .= "else %d end ";
		$prepare_args[]     = $position + 1;
	}
	$product_meta_query .= "AS pi_member_order
	FROM
	{$wpdb->posts}
    INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
    WHERE 1=1 ";
	if ( isset( $package_values ) && ! empty( $package_values ) ) {
		$package_prepare_string = implode( ',', array_fill( 0, count( $package_values ), '%d' ) );
		$product_meta_query     .= "AND ( ( {$wpdb->postmeta}.meta_key = '%s'
           AND {$wpdb->postmeta}.meta_value IN ({$package_prepare_string}) ))";
		$prepare_args[]         = 'pi_package_selection';
		$prepare_args           = array_merge( $prepare_args, array_map( 'intval', $package_values ) );
	}

	$product_meta_query .= " AND {$wpdb->posts}.post_type = '%s'
    AND (({$wpdb->posts}.post_status = '%s'))
    ORDER BY pi_member_order, {$wpdb->posts}.post_date DESC ";
	$prepare_args[]     = 'pi_partner_networks';
	$prepare_args[]     = 'publish';
	if ( true === $is_limit ) {
		$product_meta_query .= " LIMIT %d, %d";
		$prepare_args[]     = $offset;
		$prepare_args[]     = $items_per_page;
	}

	return $wpdb->get_results( $wpdb->prepare( $product_meta_query, $prepare_args ) );
}

function pi_add_class_no_post() {

}

/**
 * Get+ the image attributes.
 *
 * @param $post_image
 *
 * @return array
 */
function pi_get_img_attributes( $post_image, $post_image_id = 0 ) {
	$image_id           = $post_image_id;
	$image_default_attr = array(
		'image_id'     => '',
		'image_src'    => '',
		'image_srcset' => '',
		'image_alt'    => '',
		'image_size'   => '',
	);
	$image_attr         = array();
	//$image_id           = pi_get_attachment_id_by_url( $post_image );
//	$img_src            = wp_get_attachment_image_url( $image_id, 'small' );
	$img_src      = wp_get_attachment_image_src( $image_id, 'large' );
	$img_srcset   = wp_get_attachment_image_srcset( $image_id, 'large' );
	$img_srcsizes = wp_get_attachment_image_sizes( $image_id, 'large' );
	 if ( is_front_page() ) {
		$img_src      = wp_get_attachment_image_src( $image_id, 'article-image' );
		$img_srcset   = wp_get_attachment_image_srcset( $image_id, 'large' );
		$img_srcsizes = wp_get_attachment_image_sizes( $image_id, 'large' );
	} elseif (is_page('events')){
		$img_src      = wp_get_attachment_image_src( $image_id, 'article-image' );
		$img_srcset   = wp_get_attachment_image_srcset( $image_id, 'large' );
		$img_srcsizes = wp_get_attachment_image_sizes( $image_id, 'large' );
    } elseif (is_category()){
		$img_src      = wp_get_attachment_image_src( $image_id, 'article-image' );
		$img_srcset   = wp_get_attachment_image_srcset( $image_id, 'large' );
		$img_srcsizes = wp_get_attachment_image_sizes( $image_id, 'large' );
    } elseif (! is_singular()) {
		 $img_src      = wp_get_attachment_image_src( $image_id, 'article-image' );
		 $img_srcset   = wp_get_attachment_image_srcset( $image_id, 'large' );
		 $img_srcsizes = wp_get_attachment_image_sizes( $image_id, 'large' );
     }
	$img_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

	if ( isset( $image_id ) && ! empty( $image_id ) ) {
		$image_attr['image_id'] = $image_id;
	}
	if ( isset( $img_src ) && ! empty( $img_src ) ) {
		$image_attr['image_src'] = $img_src[0];
	}

	if ( isset( $img_srcset ) && ! empty( $img_srcset ) ) {
		$image_attr['image_srcset'] = $img_srcset;
	}
	if ( isset( $img_alt ) && ! empty( $img_alt ) ) {
		$image_attr['image_alt'] = $img_alt;
	}
	if ( isset( $img_srcsizes ) && ! empty( $img_srcsizes ) ) {
		$image_attr['image_size'] = $img_srcsizes;
	}

	return wp_parse_args( $image_attr, $image_default_attr );
}

/*function remove_max_srcset_image_width( $max_width ) {
	$max_width = 400;
	return $max_width;
}
add_filter( 'max_srcset_image_width', 'remove_max_srcset_image_width' );*/


function pi_get_attachment_id_by_url( $url ) {
	global $wpdb;
	//$attachment = $wpdb->prepare("SELECT ID FROM $wpdb->posts LIKE guid=%s", $url));
	//return $attachment[0];
	$attachment_id = 0;
	$newUploadPath = explode( "/", $url, - 1 );
	$month         = end( $newUploadPath );
	$year          = prev( $newUploadPath );
	$dir           = wp_upload_dir( $year . '/' . $month );
	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
		$file = basename( $url );

		$result = $wpdb->get_var( 'SELECT post_id  FROM `wp_postmeta` WHERE `meta_key` LIKE \'%_wp_attachment_metadata%\' AND `meta_value` LIKE \'%' . $file . '%\'' );

		return $result;

		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {

			foreach ( $query->posts as $post_id ) {

				$attachment_id = $post_id;
				break;

//				$meta = wp_get_attachment_metadata( $post_id );
//
//				$original_file       = basename( $meta['file'] );
//				$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
//
//				if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
//					$attachment_id = $post_id;
//					break;
//				}
			}
		}
	}

	return $attachment_id;
}

function pi_all_countries_array() {
	$countries = array(
		''   => "---------",
		'GB' => "United Kingdom",
		'US' => "United States",
		'AF' => "Afghanistan",
		'AL' => "Albania",
		'DZ' => "Algeria",
		'AS' => "American Samoa",
		'AD' => "Andorra",
		'AO' => "Angola",
		'AI' => "Anguilla",
		'AQ' => "Antarctica",
		'AG' => "Antigua And Barbuda",
		'AR' => "Argentina",
		'AM' => "Armenia",
		'AW' => "Aruba",
		'AU' => "Australia",
		'AT' => "Austria",
		'AZ' => "Azerbaijan",
		'BS' => "Bahamas",
		'BH' => "Bahrain",
		'BD' => "Bangladesh",
		'BB' => "Barbados",
		'BY' => "Belarus",
		'BE' => "Belgium",
		'BZ' => "Belize",
		'BJ' => "Benin",
		'BM' => "Bermuda",
		'BT' => "Bhutan",
		'BO' => "Bolivia, Plurinational State Of",
		'BA' => "Bosnia And Herzegovina",
		'BW' => "Botswana",
		'BV' => "Bouvet Island",
		'BR' => "Brazil",
		'IO' => "British Indian Ocean Territory",
		'BN' => "Brunei Darussalam",
		'BG' => "Bulgaria",
		'BF' => "Burkina Faso",
		'BI' => "Burundi",
		'KH' => "Cambodia",
		'CM' => "Cameroon",
		'CA' => "Canada",
		'CV' => "Cape Verde",
		'KY' => "Cayman Islands",
		'CF' => "Central African Republic",
		'TD' => "Chad",
		'CL' => "Chile",
		'CN' => "China",
		'CX' => "Christmas Island",
		'CC' => "Cocos (Keeling) Islands",
		'CO' => "Colombia",
		'KM' => "Comoros",
		'CG' => "Congo",
		'CD' => "Congo, The Democratic Republic Of The",
		'CK' => "Cook Islands",
		'CR' => "Costa Rica",
		'CI' => "Côte D'Ivoire",
		'HR' => "Croatia",
		'CU' => "Cuba",
		'CY' => "Cyprus",
		'CZ' => "Czech Republic",
		'DK' => "Denmark",
		'DJ' => "Djibouti",
		'DM' => "Dominica",
		'DO' => "Dominican Republic",
		'EC' => "Ecuador",
		'EG' => "Egypt",
		'SV' => "El Salvador",
		'GQ' => "Equatorial Guinea",
		'ER' => "Eritrea",
		'EE' => "Estonia",
		'ET' => "Ethiopia",
		'FK' => "Falkland Islands (Malvinas)",
		'FO' => "Faroe Islands",
		'FJ' => "Fiji",
		'FI' => "Finland",
		'FR' => "France",
		'GF' => "French Guiana",
		'PF' => "French Polynesia",
		'TF' => "French Southern Territories",
		'GA' => "Gabon",
		'GM' => "Gambia",
		'GE' => "Georgia",
		'DE' => "Germany",
		'GH' => "Ghana",
		'GI' => "Gibraltar",
		'GR' => "Greece",
		'GL' => "Greenland",
		'GD' => "Grenada",
		'GP' => "Guadeloupe",
		'GU' => "Guam",
		'GT' => "Guatemala",
		'GG' => "Guernsey",
		'GN' => "Guinea",
		'GW' => "Guinea-Bissau",
		'GY' => "Guyana",
		'HT' => "Haiti",
		'HM' => "Heard Island And Mcdonald Islands",
		'HN' => "Honduras",
		'HK' => "Hong Kong",
		'HU' => "Hungary",
		'IS' => "Iceland",
		'IN' => "India",
		'ID' => "Indonesia",
		'IR' => "Iran, Islamic Republic Of",
		'IQ' => "Iraq",
		'IE' => "Ireland",
		'IM' => "Isle Of Man",
		'IL' => "Israel",
		'IT' => "Italy",
		'JM' => "Jamaica",
		'JP' => "Japan",
		'JE' => "Jersey",
		'JO' => "Jordan",
		'KZ' => "Kazakhstan",
		'KE' => "Kenya",
		'KI' => "Kiribati",
		'KP' => "Korea, Democratic People's Republic Of",
		'KR' => "Korea, Republic Of",
		'KW' => "Kuwait",
		'KG' => "Kyrgyzstan",
		'LA' => "Lao People's Democratic Republic",
		'LV' => "Latvia",
		'LB' => "Lebanon",
		'LS' => "Lesotho",
		'LR' => "Liberia",
		'LY' => "Libyan Arab Jamahiriya",
		'LI' => "Liechtenstein",
		'LT' => "Lithuania",
		'LU' => "Luxembourg",
		'MO' => "Macao",
		'MK' => "Macedonia, The Former Yugoslav Republic Of",
		'MG' => "Madagascar",
		'MW' => "Malawi",
		'MY' => "Malaysia",
		'MV' => "Maldives",
		'ML' => "Mali",
		'MT' => "Malta",
		'MH' => "Marshall Islands",
		'MQ' => "Martinique",
		'MR' => "Mauritania",
		'MU' => "Mauritius",
		'YT' => "Mayotte",
		'MX' => "Mexico",
		'FM' => "Micronesia, Federated States Of",
		'MD' => "Moldova, Republic Of",
		'MC' => "Monaco",
		'MN' => "Mongolia",
		'ME' => "Montenegro",
		'MS' => "Montserrat",
		'MA' => "Morocco",
		'MZ' => "Mozambique",
		'MM' => "Myanmar",
		'NA' => "Namibia",
		'NR' => "Nauru",
		'NP' => "Nepal",
		'NL' => "Netherlands",
		'AN' => "Netherlands Antilles",
		'NC' => "New Caledonia",
		'NZ' => "New Zealand",
		'NI' => "Nicaragua",
		'NE' => "Niger",
		'NG' => "Nigeria",
		'NU' => "Niue",
		'NF' => "Norfolk Island",
		'MP' => "Northern Mariana Islands",
		'NO' => "Norway",
		'OM' => "Oman",
		'PK' => "Pakistan",
		'PW' => "Palau",
		'PS' => "Palestinian Territory, Occupied",
		'PA' => "Panama",
		'PG' => "Papua New Guinea",
		'PY' => "Paraguay",
		'PE' => "Peru",
		'PH' => "Philippines",
		'PN' => "Pitcairn",
		'PL' => "Poland",
		'PT' => "Portugal",
		'PR' => "Puerto Rico",
		'QA' => "Qatar",
		'RE' => "Réunion",
		'RO' => "Romania",
		'RU' => "Russian Federation",
		'RW' => "Rwanda",
		'BL' => "Saint Barthélemy",
		'SH' => "Saint Helena, Ascension And Tristan Da Cunha",
		'KN' => "Saint Kitts And Nevis",
		'LC' => "Saint Lucia",
		'MF' => "Saint Martin",
		'PM' => "Saint Pierre And Miquelon",
		'VC' => "Saint Vincent And The Grenadines",
		'WS' => "Samoa",
		'SM' => "San Marino",
		'ST' => "Sao Tome And Principe",
		'SA' => "Saudi Arabia",
		'SN' => "Senegal",
		'RS' => "Serbia",
		'SC' => "Seychelles",
		'SL' => "Sierra Leone",
		'SG' => "Singapore",
		'SK' => "Slovakia",
		'SI' => "Slovenia",
		'SB' => "Solomon Islands",
		'SO' => "Somalia",
		'ZA' => "South Africa",
		'GS' => "South Georgia And The South Sandwich Islands",
		'ES' => "Spain",
		'LK' => "Sri Lanka",
		'SD' => "Sudan",
		'SR' => "Suriname",
		'SJ' => "Svalbard And Jan Mayen",
		'SZ' => "Swaziland",
		'SE' => "Sweden",
		'CH' => "Switzerland",
		'SY' => "Syrian Arab Republic",
		'TW' => "Taiwan, Province Of China",
		'TJ' => "Tajikistan",
		'TZ' => "Tanzania, United Republic Of",
		'TH' => "Thailand",
		'TL' => "Timor-Leste",
		'TG' => "Togo",
		'TK' => "Tokelau",
		'TO' => "Tonga",
		'TT' => "Trinidad And Tobago",
		'TN' => "Tunisia",
		'TR' => "Turkey",
		'TM' => "Turkmenistan",
		'TC' => "Turks And Caicos Islands",
		'TV' => "Tuvalu",
		'UG' => "Uganda",
		'UA' => "Ukraine",
		'AE' => "United Arab Emirates",
		'UM' => "United States Minor Outlying Islands",
		'UY' => "Uruguay",
		'UZ' => "Uzbekistan",
		'VU' => "Vanuatu",
		'VA' => "Vatican City State",
		'VE' => "Venezuela, Bolivarian Republic Of",
		'VN' => "Viet Nam",
		'VG' => "Virgin Islands, British",
		'VI' => "Virgin Islands, U.S.",
		'WF' => "Wallis And Futuna",
		'EH' => "Western Sahara",
		'YE' => "Yemen",
		'ZM' => "Zambia",
		'ZW' => "Zimbabwe",
	);

	return $countries;
}
