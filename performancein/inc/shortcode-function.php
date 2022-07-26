<?php
/**
 * File include all the Shortcode functions.
 *
 * @package performancein
 */

/**
 * Filter options for browse pages
 *
 * @param $atts
 *
 * @return string
 */
function performancein_browse_filter_callback( $atts ) {

	$atts = shortcode_atts( array(
		'type'     => '',
		'taxonomy' => 'category',
	), $atts );

	$filter_class = 'browse-' . $atts['type'] . '-filter';
	ob_start();
	?>
    <div class="browse-filter main-filter row <?php echo esc_attr( $filter_class ); ?>">
        <div class="left-side col-xl-6">
            <div class="search-box">
                <div class="input-wrap">
                    <input id="browse-search" class="search" name="browse-search" type="text"
                           placeholder="Type your keyword...">
                </div>
            </div>
        </div>
        <div class="select-items col-xl-3">
            <div class="select-wrap">
                <select id="select-date" class="select-opt">
                    <option><?php esc_html_e( 'Filter by Date', 'performancein' ); ?></option>
					<?php
					$args = array(
						'type'      => 'monthly',
						'post_type' => $atts['type'],
						'format'    => 'option',
					);
					wp_get_archives( $args );
					?>
                </select>
            </div>
        </div>
		<?php
		storyful_get_term_list_options( $atts['taxonomy'] );
		?>
    </div>
	<?php

	$html = ob_get_clean();

	return $html;
}


/**
 * Job package shortcode.
 *
 * @param $atts
 *
 * @return false|string
 */
function performancein_job_package_html( $atts ) {
	wp_enqueue_script( 'performancein-custom' );
	$atts       = shortcode_atts( array(
		'category' => 'job-package',
	), $atts, 'performancein_job_package' );
	$categories = explode( ',', $atts['category'] );
	ob_start();
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 10,
		'tax_query'      => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $categories
			),
		),
		'orderby'        => 'date',
		'order'          => 'ASC',
	);

	$product_loop = get_transient( 'performancein_job_package_transient' );
	if ( false === $product_loop ) {
		$product_loop = new WP_Query( $args );
		set_transient( 'performancein_job_package_transient', $product_loop, 12 * HOUR_IN_SECONDS );
	}
	?>
    <header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header> <!-- .entry-header -->
    <form method="post" class="job-package-form" action="javascript:void (0);">
        <input type="hidden" name="action" value="job_package_form">
		<?php
		wp_nonce_field( 'job_package_nonce', 'security' );
		$product_count = 1;
		if ( $product_loop->have_posts() ) {
			while ( $product_loop->have_posts() ) : $product_loop->the_post();
				$product = wc_get_product( get_the_ID() );
				?>
                <div class="product product-<?php echo esc_attr( $product_count ); ?>">
                    <label for="product_quantity_<?php the_ID(); ?>">
						<?php echo esc_html( $product->get_name() ); ?>
                    </label>
                    <div class="product_description">
						<?php
						$args       = apply_filters(
							'wc_price_args',
							wp_parse_args(
								$args,
								array(
									'ex_tax_label'       => false,
									'currency'           => '',
									'decimal_separator'  => wc_get_price_decimal_separator(),
									'thousand_separator' => wc_get_price_thousand_separator(),
									'decimals'           => wc_get_price_decimals(),
									'price_format'       => get_woocommerce_price_format(),
								)
							)
						);
						$price      = apply_filters( 'formatted_woocommerce_price', number_format( $product->get_regular_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $product->get_regular_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
						$allow_html = array(
							'ul' => array(),
							'li' => array(),
						);
						echo wp_kses( $product->get_short_description(), $allow_html );
						printf( '<p class="price">%s%s <span>%s</span></p>',
							esc_html( get_woocommerce_currency_symbol() ),
							esc_html( $price ),
							esc_html__( '+VAT', 'performancein' )
						);
						$credits_limit = get_post_meta( $product->get_id(), '_credits_limit', true );
						?>
                        <div class="quantity_wrap">
							<?php if ( isset( $credits_limit ) && ! empty( $credits_limit ) ) { ?>
                                <select id="product_quantity_<?php the_ID(); ?>"
                                        name="product_quantity[<?php echo esc_attr( $product->get_id() ); ?>]">
									<?php
									printf( "<option value=''>%s</option>", esc_html__( 'Choose Credits', 'performancein' ) );
									for ( $credit = 1; $credit <= $credits_limit; $credit ++ ) {
										printf( "<option value='%s'>%s</option>", esc_attr( $credit ), esc_html( $credit ) );
									}
									?>
                                </select>
							<?php } ?>
                        </div>
                    </div>
                </div>
				<?php
				$product_count ++;
			endwhile;
		} else {
			echo esc_html__( 'No Job Package found' );
		}
		wp_reset_postdata();
		?>
        <input type="submit" value="<?php esc_attr_e( 'Order', 'performancein' ); ?>" id="job_package_button">
    </form>
	<?php
	return ob_get_clean();
}

/**
 * Account shortcode html.
 * @return false|string
 */
function performancein_account_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();

		?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <nav class="account_nav">
			<?php
			$details_active_class = '';
			$jobs_active_class    = '';
			$edit_active_class    = '';
			if ( is_page( 'account/details/' ) ) {
				$details_active_class = 'active';
			} elseif ( is_page( 'account/details/jobs/' ) ) {
				$jobs_active_class = 'active';
			} elseif ( is_page( 'profile-hub/edit/' ) ) {
				$edit_active_class = 'active';
			}
			?>
            <a href="<?php echo esc_url( site_url( '/account/details/' ) ); ?>" class="<?php echo esc_attr( $details_active_class ); ?>"><?php esc_html_e( 'Summary', 'performancein' ); ?></a>
            <a href="<?php echo esc_url( site_url( '/account/details/jobs/' ) ); ?>" class="<?php echo esc_attr( $jobs_active_class ); ?>"><?php esc_html_e( 'Jobs', 'performancein' ); ?></a>
			<?php if ( true === pi_is_partner_account( $user ) ) { ?>
                <a href="<?php echo esc_url( site_url( '/profile-hub/edit/' ) ); ?>" class="<?php echo esc_attr( $edit_active_class ); ?>"><?php esc_html_e( 'Company Profile', 'performancein' ); ?></a>
			<?php } ?>
            <a href="<?php echo esc_url( site_url( '/account/logout/' ) ); ?>?logout=true"><?php esc_html_e( 'Sign Out', 'performancein' ); ?></a>
        </nav>

		<?php if ( is_page( 'account/details/' ) ) {
			pi_account_detail_html( $user );
		} elseif ( is_page( 'account/details/jobs/' ) ) {
			pi_account_detail_job_html( $user );
		} elseif ( is_page( 'profile-hub/edit/' ) ) {
			if ( true === pi_is_partner_account( $user ) ) {
				pi_profile_hub_edit_html( $user );
			} else {
				pi_account_not_found_page_html();
			}
		}
	} else {
		pi_account_not_found_page_html();
	}

	return ob_get_clean();
}


/**
 * Account login html.
 * @return false|string
 */
function performancein_account_login_html() {

	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	$_referer = filter_input( INPUT_GET, 'referer', FILTER_SANITIZE_STRING );
	?>
	<?php the_title( '<h1 class="entry-title small_heading1">', '</h1>' ); ?>
    <div class="form login">
        <form method="post" class="login-form" action="javascript:void (0);">
			<?php wp_nonce_field( 'login_form_nonce', 'login_form_name' ); ?>
            <p>
                <label for="id_email"><?php esc_html_e( 'Email:', 'performancein' ); ?></label>
                <input id="id_email" maxlength="255" name="email" type="email">

            </p>
            <p>
                <label for="id_password"><?php esc_html_e( 'Password:', 'performancein' ); ?></label>
                <input id="id_password" name="password" type="password">
                <input id="id_referer" name="referer" type="hidden" value="<?php echo esc_attr( $_referer ); ?>">

            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Login', 'performancein' ); ?>"
                       data-value="<?php esc_attr_e( 'Register', 'performancein' ); ?>" id="login_button">
            </p>
            <p>
                <a href="<?php echo esc_url( site_url( 'account/iforgot' ) ) ?>"
                   class="subtleLink"><?php esc_html_e( 'Forgot your password?', 'performancein' ); ?></a>
            </p>
        </form>
        <!--        //Account login failed!-->
        <p class="alternate-login"><?php esc_html_e( '– OR –', 'performancein' ); ?></p>
        <p>
            <a href="<?php echo esc_url( pi_get_google_sign_in_url( GOOGLE_CLIENT_ID, GOOGLE_CLIENT_REDIRECT_URL ) ); ?>"
               class="login-google social-login-button">
                <span class="social-icon"></span>
				<?php esc_html_e( 'Login with Google', 'performancein' ); ?>
            </a>
        </p>
    </div>
	<?php
	return ob_get_clean();
}

/**
 * Forgot password html.
 * @return false|string
 */
function performancein_account_iforgot_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	?>
    <div class="form iforgot">
        <form method="post" class="iforgot-form" action="javascript:void(0);">
			<?php wp_nonce_field( 'iforgot_form_nonce', 'iforgot_form_name' ); ?>
            <p>
                <b><?php esc_html_e( 'Forgotten your password?', 'performancein' ); ?></b>
                <span><?php esc_html_e( 'Enter your email address below and a password reset link will be sent to your inbox.', 'performancein' ); ?></span>
            </p>
            <p>
                <label for="id_email"><?php esc_html_e( 'Email:', 'performancein' ); ?></label>
                <input id="id_email" maxlength="255" name="email" type="email">
            </p>
            <p>
                <input type="submit" id="iforgot_button" value="<?php esc_attr_e( 'Submit', 'performancein' ); ?>">
            </p>
        </form>
    </div>
	<?php
	return ob_get_clean();
}

/**
 * Register html.
 * @return false|string
 */
function performancein_registration_form_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	$_referer = filter_input( INPUT_GET, 'referer', FILTER_SANITIZE_STRING );
	$_email   = filter_input( INPUT_GET, 'email', FILTER_SANITIZE_EMAIL );
	?>

	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    <div class="form">
        <form method="post" class="registration-form" action="javascript:void (0);">
			<?php wp_nonce_field( 'registration_form_nonce', 'registration_form_name' ); ?>
            <p>
                <label for="id_email"><?php esc_html_e( 'Email:', 'performancein' ); ?></label>
                <input id="id_email" maxlength="255" name="email" type="email" value="<?php echo esc_attr( $_email ); ?>">

            </p>
            <p>
                <label for="id_password"><?php esc_html_e( 'Password:', 'performancein' ); ?></label>
                <input id="id_password" name="password" type="password">

            </p>
            <p>
                <label for="id_confirm_password"><?php esc_html_e( 'Confirm Password:', 'performancein' ); ?></label>
                <input id="id_confirm_password" name="confirm_password" type="password">

            </p>
            <p>
                <label for="id_first_name"><?php esc_html_e( 'First Name:', 'performancein' ); ?></label>
                <input id="id_first_name" maxlength="255" name="first_name" type="text">

            </p>
            <p>
                <label for="id_last_name"><?php esc_html_e( 'Last Name:', 'performancein' ); ?></label>
                <input id="id_last_name" maxlength="255" name="last_name" type="text">
                <input id="id_referer" name="referer" type="hidden" value="<?php echo esc_attr( $_referer ); ?>">

            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Register', 'performancein' ); ?>" id="registration_button">
            </p>
        </form>
        <p class="alternate-login"><?php esc_html_e( '– OR –', 'performancein' ); ?></p>
        <p>
            <a href="<?php echo esc_url( pi_get_google_sign_in_url( GOOGLE_CLIENT_ID, GOOGLE_CLIENT_REDIRECT_URL ) ); ?>"
               class="login-google social-login-button">
                <span class="social-icon"></span>
				<?php esc_html_e( 'Sign with Google', 'performancein' ); ?>
            </a>
        </p>
    </div>
	<?php
	return ob_get_clean();
}

/**
 * Complete profile HTML shortcode.
 *
 * @return false|string
 */
function performancein_complete_profile_form_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );

	$demographic         = array(
		'advertiser'    => __( 'Advertiser', 'performancein' ),
		'agency'        => __( 'Agency', 'performancein' ),
		'network'       => __( 'Network', 'performancein' ),
		'other'         => __( 'Other', 'performancein' ),
		'press'         => __( 'Press', 'performancein' ),
		'publisher'     => __( 'Publisher', 'performancein' ),
		'tech-provider' => __( 'Tech Provider', 'performancein' ),
	);
	$regions_of_interest = array(
		'europe' => __( 'Europe', 'performancein' ),
		'global' => __( 'Global', 'performancein' ),
		'usa'    => __( 'USA', 'performancein' ),
	);
	$verticals           = array(
		'automotive' => __( 'Automotive', 'performancein' ),
		'electrical' => __( 'Electrical', 'performancein' ),
		'fashion'    => __( 'Fashion', 'performancein' ),
		'finance'    => __( 'Finance', 'performancein' ),
		'retail'     => __( 'Retail', 'performancein' ),
		'telecoms'   => __( 'Telecoms', 'performancein' ),
		'travel'     => __( 'Travel', 'performancein' )
	);
	$topics              = array(
		'affiliate'       => __( 'Affiliate', 'performancein' ),
		'display'         => __( 'Display', 'performancein' ),
		'email'           => __( 'Email', 'performancein' ),
		'lead generation' => __( 'Lead Generation', 'performancein' ),
		'mobile'          => __( 'Mobile', 'performancein' ),
		'search'          => __( 'Search', 'performancein' ),
		'social'          => __( 'Social', 'performancein' ),
	);

	$username             = '';
	$email                = '';
	$performancein_cookie = filter_input( INPUT_COOKIE, 'performancein_cookie', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	if ( isset( $performancein_cookie ) && ! empty( $performancein_cookie ) ) {
		$performancein_cookie = explode( '+', $performancein_cookie );

		$username = isset( $performancein_cookie[1] ) ? $performancein_cookie[1] : '';
		$email    = isset( $performancein_cookie[0] ) ? $performancein_cookie[0] : '';
	}

	?>
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    <div class="form login">
        <form method="post" class="complete-profile-form" action="javascript:void (0);">
			<?php wp_nonce_field( 'complete_profile_form_nonce', 'complete_profile_form_name' ); ?>
            <input type="hidden" id="user_email" name="user_email" value="<?php echo esc_attr( $email ); ?>">
            <div>
				<span
                        class="pi_user_name"><?php esc_html_e( 'Hello ', 'performancein' ); ?><?php echo esc_html( $username ); ?></span><br/><br/>
                <span><?php esc_html_e( 'Thanks for connecting with PerformanceIN.', 'performancein' ); ?></span><br/><br/>
                <span><?php esc_html_e( 'Help us prioritise content for you selecting your interests below.', 'performancein' ); ?></span>
            </div>
            <div>
                <label for="id_company_name"><?php esc_html_e( 'Company Name:', 'performancein' ); ?></label>
                <input id="id_company_name" maxlength="255" name="company_name" type="text">

            </div>
            <div>
                <label for="id_job_title"><?php esc_html_e( 'Job Title:', 'performancein' ); ?></label>
                <input id="id_job_title" maxlength="255" name="job_title" type="text">

            </div>
            <div>
                <label for="id_demographic"><?php esc_html_e( 'Demographic:', 'performancein' ); ?></label>
                <select id="id_demographic" name="demographic">
                    <option value="">---------</option>
					<?php foreach ( $demographic as $key => $value ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php } ?>
                </select>

            </div>
            <div>
                <label for="id_regions_0"><?php esc_html_e( 'Regions of Interest:', 'performancein' ); ?></label>
                <ul id="id_regions">
					<?php
					$regions_of_interest_number = 1;
					foreach ( $regions_of_interest as $key => $value ) {
						?>
                        <li>
                            <label for="id_regions_<?php echo esc_attr( $regions_of_interest_number ); ?>">
                                <input id="id_regions_<?php echo esc_attr( $regions_of_interest_number ); ?>"
                                       name="regions[]" type="checkbox" value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
                            </label>
                        </li>
						<?php
						$regions_of_interest_number ++;
					}
					?>
                </ul>
            </div>
            <div>
                <label for="id_verticals_0"><?php esc_html_e( 'Verticals:', 'performancein' ); ?></label>
                <ul id="id_verticals">
					<?php
					$verticals_number = 1;
					foreach ( $verticals as $key => $value ) {
						?>
                        <li>
                            <label for="id_verticals_<?php echo esc_attr( $verticals_number ); ?>">
                                <input id="id_verticals_<?php echo esc_attr( $verticals_number ); ?>" name="verticals[]"
                                       type="checkbox" value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
                            </label>
                        </li>
						<?php
						$verticals_number ++;
					}
					?>
                </ul>
            </div>
            <div>
                <label for="id_topics_0"><?php esc_html_e( 'Topics:', 'performancein' ); ?></label>
                <ul id="id_topics">
					<?php
					$topic_number = 1;
					foreach ( $topics as $key => $value ) {
						?>
                        <li>
                            <label for="id_topics_<?php echo esc_attr( $topic_number ); ?>">
                                <input id="id_topics_<?php echo esc_attr( $topic_number ); ?>" name="topics[]"
                                       type="checkbox" value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
                            </label>
                        </li>
						<?php
						$topic_number ++;
					}
					?>
                </ul>
            </div>
            <div>
                <input type="submit" value="<?php esc_attr_e( 'Complete Your Profile', 'performancein' ); ?>"
                       id="complete_profile_button">
            </div>
        </form>
    </div>
	<?php
	return ob_get_clean();
}


/**
 * Complete profile HTML shortcode.
 *
 * @return false|string
 */
function performancein_check_inbox_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	?>
    <div>
        <p><?php esc_html_e( 'Great! Your PerformanceIN account has been created.', 'performancein' ); ?></p>
        <p>
            <strong class="pi_user_email">
				<?php esc_html_e( 'Please click the confirmation link in your email:', 'performancein' ); ?>
            </strong>
			<?php esc_html_e( '— you will only have to do this once.', 'performancein' ); ?>
        </p>
        <p><?php esc_html_e( 'Once you\'ve confirmed your account, you will be redirected to the next stage.', 'performancein' ); ?></p>
        <p>
			<?php
			$request_url    = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );
			$send_email_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'send_user_activation',
					),
					site_url( $request_url )
				),
				'send_user_activation_nonce'
			);

			printf( "%s <a class='send_activation_link' href='%s'>%s</a>",
				esc_html__( 'If you haven\'t got email ', 'performancein' ),
				esc_url( $send_email_url ),
				esc_html__( 'please click here.', 'performancein' )
			);
			?>
        </p>
    </div>
	<?php
	return ob_get_clean();
}

/**
 * Job save/edit form html.
 * @return false|string
 */
function performancein_job_form_html() {
	ob_start();
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'performancein-select2-style' );
	wp_enqueue_style( 'performancein-jquery-ui' );
	wp_enqueue_script( 'performancein-custom' );


	$id_encoded = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
	$job_id     = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
	$nonce      = filter_input( INPUT_GET, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'edit_job_page_nonce' ) ) {
		$job_id = 0;
	}
	$job_id              = isset( $job_id ) && ! empty( $job_id ) ? $job_id : 0;
	$job_field_values    = pi_get_job_field_values( $job_id );
	$job_title           = $job_field_values['job_title'];
	$job_type            = $job_field_values['job_type'];
	$contract_length     = $job_field_values['contract_length'];
	$geographic_location = $job_field_values['geographic_location'];
	$description         = $job_field_values['description'];
	$minimum_salary      = $job_field_values['minimum_salary'];
	$maximum_salary      = $job_field_values['maximum_salary'];
	$closing_date        = $job_field_values['closing_date'];
	$contact_phone       = $job_field_values['contact_phone'];
	$contact_email       = $job_field_values['contact_email'];
	$street_address      = $job_field_values['street_address'];
	$post_code           = $job_field_values['post_code'];
	$address_region      = $job_field_values['address_region'];
	$address_country     = $job_field_values['address_country'];
	$term_list_ids       = $job_field_values['term_list_ids'];

	if ( is_numeric( base64_decode( $id_encoded ) ) ) {
		the_title( '<h1 class="entry-title">', '</h1>' );
		$product_id = base64_decode( $id_encoded );
		?>
        <div class="form">
            <form id="edit_job_form" class="save-job-form <?php if ( 0 !== $job_id ) {
				echo esc_attr( 'has-edit-job' );
			} ?>" action="javascript:void(0)" method="post">
                <h2><?php echo esc_html( get_the_title( $product_id ) ); ?></h2>
                <input type="hidden" id="id_product" value="<?php echo esc_attr( $product_id ); ?>">
				<?php if ( 0 !== $job_id ) { ?>
                    <input type="hidden" id="id_job" value="<?php echo esc_attr( $job_id ); ?>">
					<?php wp_nonce_field( 'save_edited_job_form_nonce', 'save_edited_job_form_name' ); ?>
				<?php } else { ?>
					<?php wp_nonce_field( 'save_job_form_nonce', 'save_job_form_name' ); ?>
				<?php } ?>

                <input type="hidden" id="id_user" value="<?php echo esc_attr( get_current_user_id() ); ?>">

				<?php wp_nonce_field( 'preview_job_form_nonce', 'preview_job_form_name' ); ?>
                <div id="div_id_job_title" class="control-group">
                    <label for="id_job_title" class="control-label">
						<?php esc_html_e( 'Job title', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <input class="textinput textInput" id="id_job_title" maxlength="100" name="job_title"
                               value="<?php echo esc_attr( $job_title ); ?>" type="text">
                    </div>
                </div>
                <div id="div_id_job_type" class="control-group">
                    <label for="id_job_type" class="control-label">
						<?php esc_html_e( 'Job type', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <select class="select" id="id_job_type" name="job_type">
                            <option value="0">---------</option>
                            <option
                                    value="<?php esc_attr_e( 'Full-time', 'performancein' ) ?>" <?php selected( $job_type, 'Full-time', true ) ?>><?php esc_html_e( 'Full-time', 'performancein' ) ?></option>
                            <option
                                    value="<?php esc_attr_e( 'Part-time', 'performancein' ) ?>" <?php selected( $job_type, 'Part-time', true ) ?>><?php esc_html_e( 'Part-time', 'performancein' ) ?></option>
                        </select>
                    </div>
                </div>
                <div id="div_id_job_length" class="control-group">
                    <label for="id_job_length" class="control-label">
						<?php esc_html_e( 'Contract length', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <select class="select" id="id_job_length" name="job_length">
                            <option value="0">---------</option>
                            <option
                                    value="<?php esc_attr_e( 'Permanent', 'performancein' ); ?>" <?php selected( $contract_length, 'Permanent', true ) ?>><?php esc_html_e( 'Permanent', 'performancein' ); ?></option>
                            <option
                                    value="<?php esc_attr_e( 'Temporary', 'performancein' ); ?>" <?php selected( $contract_length, 'Temporary', true ) ?>><?php esc_html_e( 'Temporary', 'performancein' ); ?></option>
                            <option
                                    value="<?php esc_attr_e( 'Contract', 'performancein' ); ?>" <?php selected( $contract_length, 'Contract', true ) ?>><?php esc_html_e( 'Contract', 'performancein' ); ?></option>
                        </select>
                    </div>
                </div>
                <div id="div_id_job_area" class="control-group">
                    <label for="id_job_area" class="control-label">
						<?php esc_html_e( 'Geographic location', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <input class="textinput textInput" id="id_job_area" maxlength="255" name="job_area"
                               value="<?php echo esc_attr( $geographic_location ); ?>" type="text">
                        <p id="hint_id_job_area"
                           class="help-block"><?php esc_html_e( 'The place where the job will be located.', 'performancein' ); ?></p>
                    </div>
                </div>
                <div id="div_id_description" class="control-group">
                    <label for="id_description" class="control-label ">
						<?php esc_html_e( 'Description', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
						<?php
						wp_editor( $description,
							'id_job_description',
							array(
								'media_buttons' => false,
								'quicktags'     => false,
								'textarea_rows' => 12,
								'editor_class'  => 'pi_wp_editor_class'
							)
						);
						?>
                    </div>
                </div>
                <div id="div_id_minimum_salary" class="control-group">
                    <label for="id_minimum_salary" class="control-label ">
						<?php esc_html_e( 'Minimum salary', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                        <input class="numberinput" id="id_minimum_salary" min="0" name="minimum_salary" type="number"
                               value="<?php echo esc_attr( $minimum_salary ); ?>">
                    </div>
                </div>
                <div id="div_id_maximum_salary" class="control-group">
                    <label for="id_maximum_salary" class="control-label ">
						<?php esc_html_e( 'Maximum salary', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                        <input class="numberinput" id="id_maximum_salary" min="0" name="maximum_salary" type="number"
                               value="<?php echo esc_attr( $maximum_salary ); ?>">
                    </div>
                </div>
                <div id="div_id_categories" class="control-group">
                    <label for="id_categories" class="control-label ">
						<?php esc_html_e( 'Categories', 'performancein' ); ?>
                    </label>
                    <div class="controls">
						<?php
						$taxonomies = get_terms( array(
							'taxonomy'   => 'pi_cat_jobs',
							'hide_empty' => false
						) );
						if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
							$output = '<select class="selectmultiple" multiple="multiple" id="id_categories" name="categories">';
							foreach ( $taxonomies as $category ) {
								$output .= '<option value="' . esc_attr( $category->term_id ) . '" ' . selected( in_array( $category->term_id, $term_list_ids, true ), true, false ) . '> ' . esc_html( $category->name ) . '</option>';
							}
							$output .= '</select>';

							echo wp_kses( $output, array(
								'select' => array(
									'class'    => array(),
									'multiple' => array(),
									'id'       => array(),
									'name'     => array()
								),
								'option' => array(
									'value'    => array(),
									'selected' => array()
								)
							) );
						}
						?>
                        <p id="hint_id_categories" class="help-block">
							<?php esc_html_e( 'Select 1 or 2 categories this job is relevant for. Hold down "Control", or "Command" on a Mac, to select more than one.', 'performancein' ); ?>
                        </p>
                    </div>
                </div>
                <div id="div_id_closing_date" class="control-group">
                    <label for="id_closing_date" class="control-label ">
						<?php esc_html_e( 'Closing date', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <input class="datepicker" id="id_closing_date" name="closing_date" autocomplete="off"
                               value="<?php echo esc_attr( $closing_date ); ?>" type="text">
                    </div>
                </div>
                <div id="div_id_contact_phone" class="control-group">
                    <label for="id_contact_phone" class="control-label ">
						<?php esc_html_e( 'Contact phone', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <input class="textinput textInput" id="id_contact_phone" maxlength="20" name="contact_phone"
                               value="<?php echo esc_attr( $contact_phone ); ?>" type="text">
                    </div>
                </div>
                <div id="div_id_contact_email" class="control-group">
                    <label for="id_contact_email" class="control-label ">
						<?php esc_html_e( 'Contact email', 'performancein' ); ?>
                        <span class="asteriskField">*</span>
                    </label>
                    <div class="controls">
                        <input class="emailinput" id="id_contact_email" maxlength="255" name="contact_email"
                               value="<?php echo esc_attr( $contact_email ); ?>" type="email">
                    </div>
                </div>
                <div id="div_id_street_address" class="control-group">
                    <label for="id_street_address" class="control-label ">
						<?php esc_html_e( 'Street Address', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                         <textarea class="textinput textInput"  cols="5" id="id_street_address" maxlength="200" name="street_address"
                                   rows="5"><?php echo esc_html( $street_address ); ?></textarea>
                    </div>
                </div>
                <div id="div_id_post_code" class="control-group">
                    <label for="id_post_code" class="control-label ">
						<?php esc_html_e( 'Post Code', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                        <input class="textinput textInput" id="id_post_code" maxlength="20" name="post_code"
                               value="<?php echo esc_attr( $post_code ); ?>" type="text">
                    </div>
                </div>
                <div id="div_id_address_region" class="control-group">
                    <label for="id_address_region" class="control-label ">
						<?php esc_html_e( 'Address Region', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                        <input class="emailinput" id="id_address_region" maxlength="255" name="address_region"
                               value="<?php echo esc_attr( $address_region ); ?>" type="text">
                    </div>
                </div>
                <div id="div_id_address_country" class="control-group">
                    <label for="id_address_country" class="control-label ">
						<?php esc_html_e( 'Address Country', 'performancein' ); ?>
                    </label>
                    <div class="controls">
                        <input class="textinput textInput" id="id_address_country" maxlength="255" name="address_country"
                               value="<?php echo esc_attr( $address_country ); ?>" type="text">
                    </div>
                </div>
                <p>
                    <strong><?php esc_html_e( 'Preview opens in a new window', 'performancein' ); ?></strong>
                    <br> <?php esc_html_e( 'If nothing appears please check your browser isn\'t blocking pop-ups.', 'performancein' ); ?>
                </p>
                <button class="preview_button" id="preview_job"
                        type="submit"><?php esc_html_e( 'Preview', 'performancein' ); ?></button>
                <button class="submit_button" id="save_new_job"
                        type="submit"><?php esc_html_e( 'Submit', 'performancein' ); ?></button>
            </form>
        </div>
		<?php
	} else {
		?>
        <div class="pi-not-found">
			<?php
			pi_account_not_found_page_html();
			?>
        </div>
		<?php
	}

	return ob_get_clean();
}

/**
 * Password reset html.
 * @return false|string
 */
function performancein_account_password_reset_html() {
	ob_start();
	$email = filter_input( INPUT_GET, 'email', FILTER_SANITIZE_EMAIL );
	$code  = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	wp_enqueue_script( 'performancein-custom' );
	?>
	<?php the_title( '<h1 class="entry-title small_heading1">', '</h1>' ); ?>
    <div class="form">
        <form method="post" class="passowrd-reset-form" action="javascript:void(0);">
			<?php wp_nonce_field( 'password_reset_form_nonce', 'password_reset_form_name' ); ?>
            <input type="hidden" id="id_email" name="email" value="<?php echo esc_attr( $email ); ?>">
            <input type="hidden" id="id_code" name="code" value="<?php echo esc_attr( $code ); ?>">
            <p>
                <label for="id_password"><?php esc_html_e( 'Password:', 'performancein' ); ?></label>
                <input id="id_password" name="password" type="password">
            </p>
            <p>
                <label for="id_confirm_password"><?php esc_html_e( 'Confirm Password:', 'performancein' ); ?></label>
                <input id="id_confirm_password" name="confirm_password" type="password">
            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Submit', 'performancein' ); ?>" id="password_reset_button">
            </p>
        </form>
    </div>
	<?php
	return ob_get_clean();
}

function performancein_partner_banner_html( $atts, $content = null ) {
	$attributes = shortcode_atts( array(
		'search'        => false,
		'tag'           => false,
		'H1-tag-design' => false,
	), $atts );

	ob_start();
	?>
    <section class="profile-hub-search <?php echo ( $attributes['H1-tag-design'] === false ) ? 'not-required-h1-design' : ''; ?>">
        <h1>
            <a href="/profile-hub/">
                <img
                        src="<?php echo get_template_directory_uri() ?>/assets/images/partner-network-logo.svg"
                        alt="Partner Network"
                        class="pn-logo">
            </a>
        </h1>
		<?php if ( $attributes['search'] === 'true' ): ?>
            <form class="profile-hub-search-form" action="/profile-hub/search/" method="GET">
                <div class="profile-hub-search-form-input-group">
                    <input class="profile-hub-search-form-input-group-form-control"
                           placeholder="Keyword e.g programmatic" type="search" name="q">
                    <span class="profile-hub-search-form-input-group-btn">
				<button class="button mod-profile-hub-search"><span class="icon"></span> Search</button>
			</span>
                </div>
            </form>

		<?php
		endif;
		if ( $attributes['tag'] === 'true' ):
			$terms = get_field( 'pi_partner_tag_selection', 'option' );
			if ( ! empty( $terms ) && isset( $terms ) ) { ?>
                <ul class="profile-hub-tags mod-profile-hub-search">
					<?php foreach ( $terms as $term ) { ?>
                        <li class="profile-hub-tags-item">
                            <a href="/profile-hub/tag/<?php echo $term->slug ?>/" class="profile-hub-tags-item-style">
                                <span><?php echo $term->name ?></span>
                            </a>
                        </li>
					<?php } ?>
                </ul>
			<?php }


		endif;

		?>
    </section>

	<?php
	return ob_get_clean();
}

function performancein_partner_login_html() {

	$current_user = wp_get_current_user();
	if ( is_user_logged_in() ) {
		if ( 'account' !== $current_user->roles[0] ) {
			if ( 'customer' === $current_user->roles[0] ) {
				ob_start(); ?>
                <section class="profile-hub-plug">
                    Not listed here? Request a FREE company profile today &nbsp;
                    <a href="/profile-hub/choose-package/" class="button mod-profile-hub-plug-button">Signup Now</a>
                </section>
				<?php return ob_get_clean();
			} else {
				return "";
			}
		}
		$user_email = $current_user->user_email;
	} else {
		ob_start(); ?>
        <section class="profile-hub-plug">
            Not listed here? Request a FREE company profile today &nbsp;
            <a href="/profile-hub/choose-package/" class="button mod-profile-hub-plug-button">Signup Now</a>
        </section>
		<?php return ob_get_clean();
	}

	$args  = array(
		'post_type'   => 'pi_partner_networks',
		'post_status' => 'publish',
		'meta_query'  => array(
			array(
				'key'     => 'pi_user_selection',
				'value'   => $current_user->ID,
				'compare' => 'IN'
			),
		),
	);
	$query = new WP_Query( $args );
	ob_start(); ?>
    <section class="profile-hub-plug">
		<?php
		if ( isset( $query->posts[0]->ID ) ) {
			$post_permalink = get_post_permalink( $query->posts[0]->ID );
			printf( __( 'Welcome back: %s   -  ', 'textdomain' ), esc_html( $current_user->user_email ) ); ?>
            <a href=<?php echo $post_permalink; ?> class="button mod-profile-hub-plug-button">View Your Profile Now</a>

		<?php } else { ?>
            Not listed here? Request a FREE company profile today
            <a href="/profile-hub/choose-package/" class="button mod-profile-hub-plug-button">Signup Now</a>
		<?php } ?>
    </section>
	<?php return ob_get_clean();
}


/**
 * Register html.
 * @return false|string
 */
function performancein_company_profile_form_html( $atts ) {

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'performancein-select2-style' );
	wp_enqueue_style( 'performancein-jquery-ui' );
	wp_enqueue_script( 'performancein-custom' );
	$atts = shortcode_atts( array(
		'company_id' => 0,
	), $atts );
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	$countries       = array(
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
	$pi_partner_tags = get_terms( array(
		'taxonomy'   => 'partner_network_tag',
		'hide_empty' => false,
	) );
	$company_id      = $atts['company_id'];

	$company_keys_services = isset( $company_id ) ? get_field( 'pi_partner_key_services_pi_partner_tags', $company_id ) : array();
	//echo '<pre>'; print_r($company_keys_services);
	$company_keys_service_ids = array();
	if ( isset( $company_keys_services ) && ! empty( $company_keys_services ) ) {
		foreach ( $company_keys_services as $company_keys_service ) {
			if ( $company_keys_service ) {
				$company_keys_service_ids[] = $company_keys_service;
			}

		}
	}


	$company_name          = ! empty( $company_id ) ? get_the_title( $company_id ) : '';
	$pi_package            = ! empty( $company_id ) ? get_field( 'pi_package_selection', $company_id ) : '';
	$pi_user_id            = ! empty( $company_id ) ? get_field( 'pi_user_selection', $company_id ) : '';
	$company_email         = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_contact_info_pi_email_id', $company_id ) : '';
	$logo_url              = ! empty( $company_id ) ? wp_get_attachment_url( get_post_thumbnail_id( $company_id ) ) : '';
	$company_description   = ! empty( $company_id ) ? get_field( 'pi_partner_description_pi_partner_description', $company_id ) : '';
	$custom_header         = ! empty( $company_id ) ? get_field( 'pi_partner_network_banner_image', $company_id ) : '';
	$website_url           = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_contact_info_pi_website_url', $company_id ) : '';
	$address1              = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_head_office_info_pi_address1', $company_id ) : '';
	$address2              = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_head_office_info_pi_address2', $company_id ) : '';
	$city                  = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_head_office_info_pi_city', $company_id ) : '';
	$postcode              = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_head_office_info_pi_postcode', $company_id ) : '';
	$country               = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_head_office_info_pi_country', $company_id ) : '';
	$telephone_number      = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_contact_info_pi_telephone_number', $company_id ) : '';
	$facebook_profile      = ! empty( $company_id ) ? get_field( 'pi_facebook_link', $company_id ) : '';
	$twitter_profile       = ! empty( $company_id ) ? get_field( 'pi_twitter_link', $company_id ) : '';
	$linkedin_profile      = ! empty( $company_id ) ? get_field( 'pi_linkedin_link', $company_id ) : '';
	$founded_year          = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_further_info_pi_founded_year', $company_id ) : '';
	$number_of_staff       = ! empty( $company_id ) ? get_field( 'pi_partner_sidebar_pi_further_info_pi_number_of_staff', $company_id ) : '';
	$client_testimonial_1  = ! empty( $company_id ) ? get_field( 'pi_client_testimonials_pi_client_testimonial1', $company_id ) : '';
	$client_testimonial_2  = ! empty( $company_id ) ? get_field( 'pi_client_testimonials_pi_client_testimonial2', $company_id ) : '';
	$client_testimonial_3  = ! empty( $company_id ) ? get_field( 'pi_client_testimonials_pi_client_testimonial3', $company_id ) : '';
	$piPartnerPackeageSlug = ! empty( $pi_package ) ? get_post_field( 'post_name', $pi_package ) : '';
	$is_uploaded           = "";
	$checked               = ( 'publish' === get_post_status( $company_id ) ) ? 'checked="checked"' : '';
	$warning_text          = 'This will not display on your profile with the Basic package. To enable it, you will need to upgrade to: Associate';
	?>
    <div class="form">
        <div id="subscription_info" class="subscription_info" style="display: none; overflow: hidden;"><p><label
                        for="id_is_active">Profile Active Status: </label><input <?php echo esc_attr( $checked ); ?> form="supplier-edit-form"
                                                                                                                     id="id_is_active" name="is_active"
                                                                                                                     type="checkbox"></p>
            <p>To change or cancel your membership package please contact our sales team on +44 117 990 2960.</p>
            <p></p></div>
        <form method="post" class="company_profile_form" action="company-profile-form" action="javascript:void (0);"
              enctype="multipart/form-data">
			<?php wp_nonce_field( 'company_profile_form_nonce', 'company_profile_form_name' ); ?>
            <input id="id_account" name="account" type="hidden" value="<?php echo esc_attr( $pi_user_id ); ?>">
            <input id="id_product" name="product" type="hidden" value="<?php echo esc_attr( $pi_package ); ?>">
            <input id="id_company" name="company_id" type="hidden" value="<?php echo esc_attr( $company_id ); ?>">
            <p>
				<?php
				if ( isset( $pi_package ) && ! empty( $pi_package ) ) { ?>
                    <label for="id_company_name">
                        <b><?php esc_html_e( 'Package : ', 'performancein' ); ?></b>
                    </label>
					<?php
					$product_name = get_the_title( $pi_package );
					$packageName  = explode( " ", $product_name );
					echo $packageName[0];
				}
				?>
            </p>
            <hr/>
            <div id="div_id_company_name" class="control-group ">
                <label for="id_company_name"><?php esc_html_e( 'Company name:', 'performancein' ); ?></label>
                <input id="id_company_name" maxlength="100" name="company_name"
                       value="<?php echo esc_attr( $company_name ); ?>" type="text">
            </div>
            <div id="div_id_company_email" class="control-group ">
                <label for="id_company_email"><?php esc_html_e( 'Company email:', 'performancein' ); ?></label>
                <input id="id_company_email" maxlength="75" name="company_email"
                       value="<?php echo esc_attr( $company_email ); ?>" type="email">
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_logo" class="control-group ">
                <label for="id_logo"><?php esc_html_e( 'Logo:', 'performancein' ); ?></label>
				<?php if ( isset( $logo_url ) && ! empty( $logo_url ) ) {
					$is_uploaded = 'true';
					?>
                    <img src="<?php echo esc_url( $logo_url ) ?>"
                         alt="<?php esc_attr_e( 'Company logo', 'performancein' ); ?>">
				<?php } ?>
                <input id="id_logo" name="logo" type="file">
                <input id="id_logo_hidden" name="logo_hidden" type="hidden" value="<?php echo esc_attr( $is_uploaded ); ?>">
                <p class="help-block"><?php esc_html_e( '.jpg or .png files only. Logos will be resized to fit within 380px x 185px', 'performancein' ); ?></p>
            </div>
            <div id="div_id_company_description" class="control-group ">
                <label
                        for="id_company_description"><?php esc_html_e( 'Company description:', 'performancein' ); ?></label>
                <p>
					<?php
					wp_editor( $company_description,
						'id_company_description',
						array(
							'media_buttons' => false,
							'quicktags'     => false,
							'textarea_rows' => 20,
							'editor_class'  => 'pi_wp_editor_class'
						)
					);
					?>
                </p>
                <p class="help-block"><?php esc_html_e( 'Maximum length: 2000', 'performancein' ); ?></p>
            </div>
            <div id="div_id_company_tags" class="control-group">
                <label for="id_company_tags"><?php esc_html_e( 'Key Services:', 'performancein' ); ?></label>
                <div id="tags" class="tagSelector">
					<?php
					if ( ! empty( $pi_partner_tags ) && ! is_wp_error( $pi_partner_tags ) ) {
						echo "<select name='company_tags' id='id_company_tags' multiple='multiple'>";
						foreach ( $pi_partner_tags as $pi_partner_tag ) {
							echo "<option value='{$pi_partner_tag->term_id}' " . selected( in_array( $pi_partner_tag->term_id, $company_keys_service_ids ), true, false ) . ">{$pi_partner_tag->name}</option>";
						}
						echo "</select>";
					}
					?>
                </div>
                <p class="help-block"><?php esc_html_e( 'Maximum of 10. ', 'performancein' ); ?></p>

            </div>
            <div id="div_id_custom_header" class="control-group ">
                <label for="id_custom_header"><?php esc_html_e( 'Custom header:', 'performancein' ); ?></label>
				<?php if ( isset( $custom_header ) && ! empty( $custom_header ) ) { ?>
                    <img src="<?php echo esc_url( $custom_header ) ?>"
                         alt="<?php esc_attr_e( 'Company header', 'performancein' ); ?>">
				<?php } ?>
                <input id="id_custom_header" name="custom_header" type="file">
                <p class="help-block"><?php esc_html_e( '.jpg or files only. Images will be resized, but for best results size your header to 1500px x 500px. HINT:
                    that\'s the same dimensions as your Twitter Header. Minimum image size 750px x 250px.', 'performancein' ); ?></p>
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_website_url" class="control-group ">
                <label for="id_website_url"><?php esc_html_e( 'Website url:', 'performancein' ); ?></label>
                <input id="id_website_url" maxlength="200" name="website_url"
                       value="<?php echo esc_url( $website_url ); ?>" type="text">
            </div>
            <div id="div_id_address1" class="control-group ">
                <label for="id_address1"><?php esc_html_e( 'Address1:', 'performancein' ); ?></label>
                <input id="id_address1" maxlength="255" name="address1" value="<?php echo esc_attr( $address1 ); ?>"
                       type="text">
            </div>
            <div id="div_id_address2" class="control-group ">
                <label for="id_address2"><?php esc_html_e( 'Address2:', 'performancein' ); ?></label>
                <input id="id_address2" maxlength="255" name="address2" value="<?php echo esc_attr( $address2 ); ?>"
                       type="text">
            </div>
            <div id="div_id_city" class="control-group ">
                <label for="id_city"><?php esc_html_e( 'City:', 'performancein' ); ?></label>
                <input id="id_city" maxlength="255" name="city" value="<?php echo esc_attr( $city ); ?>" type="text">
            </div>
            <div id="div_id_postcode" class="control-group ">
                <label for="id_postcode"><?php esc_html_e( 'Postcode:', 'performancein' ); ?></label>
                <input id="id_postcode" maxlength="255" name="postcode" value="<?php echo esc_attr( $postcode ); ?>"
                       type="text">
            </div>
            <div id="div_id_country" class="control-group ">
                <label for="id_country"><?php esc_html_e( 'Country:', 'performancein' ); ?></label>
				<?php
				woocommerce_form_field( 'country', array(
					'type'    => 'select',
					'id'      => 'id_country',
					'options' => $countries,
				),
					$country
				);
				?>
            </div>
            <div id="div_id_telephone_number" class="control-group ">
                <label for="id_telephone_number"><?php esc_html_e( 'Telephone number:', 'performancein' ); ?></label>
                <input id="id_telephone_number" maxlength="100" name="telephone_number"
                       value="<?php echo esc_attr( $telephone_number ); ?>" type="text">
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_facebook_profile" class="control-group ">
                <label for="id_facebook_profile"><?php esc_html_e( 'Facebook profile:', 'performancein' ); ?></label>
                <input id="id_facebook_profile" maxlength="200" name="facebook_profile"
                       value="<?php echo esc_url( $facebook_profile ); ?>" type="url">
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_twitter_profile" class="control-group ">
                <label for="id_twitter_profile"><?php esc_html_e( 'Twitter profile:', 'performancein' ); ?></label>
                <input id="id_twitter_profile" maxlength="200" name="twitter_profile"
                       value="<?php echo esc_url( $twitter_profile ) ?>" type="url">
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_linkedin_profile" class="control-group ">
                <label for="id_linkedin_profile"><?php esc_html_e( 'Linkedin profile:', 'performancein' ); ?></label>
                <input id="id_linkedin_profile" maxlength="200" name="linkedin_profile"
                       value="<?php echo esc_url( $linkedin_profile ); ?>" type="url">
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_founded_year" class="control-group ">
                <label for="id_founded_year"><?php esc_html_e( 'Founded year:', 'performancein' ); ?></label>
                <select id="id_founded_year" name="founded_year">
                    <option value="" selected="selected">---------</option>
					<?php
					$latest_year = date( 'Y' );
					for ( $i = $latest_year; $i >= 1950; $i -- ) {
						printf( '<option value="%d" %s>%s</option>', esc_attr( $i ), selected( $founded_year, $i, false ), esc_html( $i ) );
					}
					?>
                </select>
            </div>
            <div id="div_id_number_of_staff" class="control-group ">
                <label for="id_number_of_staff"><?php esc_html_e( 'Number of staff:', 'performancein' ); ?></label>
                <select id="id_number_of_staff" name="number_of_staff">
                    <option value="">---------</option>
                    <option
                            value="<?php esc_attr_e( '1-10' ); ?>" <?php selected( $number_of_staff, '1-10' ); ?>><?php esc_html_e( '1-10' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '10-50' ); ?>" <?php selected( $number_of_staff, '10-50' ); ?>><?php esc_html_e( '10-50' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '50-200' ); ?>" <?php selected( $number_of_staff, '50-200' ); ?>><?php esc_html_e( '50-200' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '200-500' ); ?>" <?php selected( $number_of_staff, '200-500' ); ?>><?php esc_html_e( '200-500' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '500-1000' ); ?>" <?php selected( $number_of_staff, '500-1000' ); ?>><?php esc_html_e( '500-1000' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '1000-2000' ); ?>" <?php selected( $number_of_staff, '1000-2000' ); ?>><?php esc_html_e( '1000-2000' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '2000-5000' ); ?>" <?php selected( $number_of_staff, '2000-5000' ); ?>><?php esc_html_e( '2000-5000' ); ?></option>
                    <option
                            value="<?php esc_attr_e( '5000+' ); ?>" <?php selected( $number_of_staff, '5000+' ); ?>><?php esc_html_e( '5000+' ); ?></option>
                </select>
            </div>
            <div id="div_id_client_testimonial_1" class="control-group ">
                <label
                        for="id_client_testimonial_1"><?php esc_html_e( 'Client testimonial 1:', 'performancein' ); ?></label>
                <textarea cols="40" id="id_client_testimonial_1" maxlength="800" name="client_testimonial_1"
                          rows="10"><?php echo esc_html( $client_testimonial_1 ); ?></textarea>
                <p class="help-block"><?php esc_html_e( 'Max length: 800', 'performancein' ); ?></p>
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_client_testimonial_2" class="control-group ">
                <label
                        for="id_client_testimonial_2"><?php esc_html_e( 'Client testimonial 2:', 'performancein' ); ?></label>
                <textarea cols="40" id="id_client_testimonial_2" maxlength="800" name="client_testimonial_2"
                          rows="10"><?php echo esc_html( $client_testimonial_2 ); ?></textarea>
                <p class="help-block"><?php esc_html_e( 'Max length: 800', 'performancein' ); ?></p>
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <div id="div_id_client_testimonial_3" class="control-group ">
                <label
                        for="id_client_testimonial_3"><?php esc_html_e( 'Client testimonial 3:', 'performancein' ); ?></label>
                <textarea cols="40" id="id_client_testimonial_3" maxlength="800" name="client_testimonial_3"
                          rows="10"><?php echo esc_html( $client_testimonial_3 ); ?></textarea>
                <p class="help-block"><?php esc_html_e( 'Max length: 800', 'performancein' ); ?></p>
				<?php if ( 'basic-membership' === $piPartnerPackeageSlug ) { ?>
                    <p class="help-block"><?php esc_html_e( $warning_text, 'performancein' ); ?></p>
				<?php } ?>
            </div>

            <p>
                <input type="submit" value="<?php esc_attr_e( 'Continue', 'performancein' ); ?>"
                       id="company_profile_button">
            </p>
        </form>
    </div>
	<?php
	return ob_get_clean();
}


/**
 * Shortcode for Remove social.
 */
function performancein_remove_social_html() {
	ob_start();
	$remove_google_url = wp_nonce_url(
		add_query_arg(
			array(
				'remove' => true,
				'id'     => 'google'
			)
		),
		'remove_google_link_nonce',
		'security'
	);
	$current_user      = wp_get_current_user();
	?>
	<?php the_title( '<h1 class="entry-title small_heading1">', '</h1>' ); ?>
    <div class="form">
        <form method="GET" action="">
            <input type="hidden" name="remove" value="true">
            <input type="hidden" name="id" value="google">
			<?php wp_nonce_field( 'remove_google_link_nonce', 'security' ); ?>
            <p>
				<?php printf( __( 'Do you wish to remove your Google account (%s) from your PerformanceIN account?' ), esc_html( $current_user->nickname ) ); ?>
            </p>
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Cancel', 'performancein' ); ?>" onclick="location.href = '/account/details/';"/><br/>
                <input type="submit" class="danger" value="<?php esc_attr_e( 'Remove', 'performancein' ); ?>" name="social-remove"/>
            </p>
        </form>
    </div>
	<?php
	return ob_get_clean();
}

function performancein_partner_account_request_form_html() {
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	$pakage_slug = filter_input( INPUT_GET, 'package', FILTER_SANITIZE_STRING );
	the_title( '<h1 class="entry-title small_heading1">', '</h1>' ); ?>
    <div class="form">
        <form method="post" class="partner_account_register_form" action="javascript:void (0);">
			<?php wp_nonce_field( 'partner_account_register_form_nonce', 'partner_account_register_form_name' ); ?>
            <p>
                <input id="id_partner_package_type" name="partner_package_type" type="hidden" value="<?php echo esc_html( $pakage_slug ); ?>">
            </p>
            <p>
                <label for="id_full_name">Full Name:</label>
                <input id="id_full_name" name="full_name" type="text"></p>
            <p>
                <label for="id_email">Email:</label>
                <input id="id_email" maxlength="255" name="email" type="email"></p>
            <p>
            <p>
                <label for="id_company_name">Company Name:</label>
                <input id="id_company_name" name="company_name" type="text"></p>
            <p>
                <label for="id_website_url">Website Url:</label>
                <input id="id_website_url" name="website_url" type="text"></p>
            <p>
            <p>
                <label for="id_company_biography">Company Biography:</label>
				<?php

				wp_editor( '',
					'company_biography',
					array(
						'media_buttons' => false,
						'quicktags'     => false,
						'textarea_rows' => 12,
						'editor_class'  => 'pi_wp_editor_class'
					)
				);
				?>
            <p>
            <p><input type="submit" value="Account Request" data-value="Register" id="partner_account_register_button"></p>
        </form>
    </div>
	<?php
	return ob_get_clean();
}

function performancein_advertising_form_content_html() {
	ob_start(); ?>
    <div class="advertising-interest-form">
        <h2>Download Now</h2>

        <div id="mc_embed_signup">
            <form action="https://performancein.us20.list-manage.com/subscribe/post?u=3a1e8490d918e5239f966054e&amp;id=f51b399f07" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">

                    <div class="mc-field-group">
                        <label for="mce-EMAIL">Email Address <span class="asterisk">*</span>
                        </label>
                        <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                    </div>
                    <div class="mc-field-group">
                        <label for="mce-FNAME">First Name <span class="asterisk">*</span></label>
                        <input type="text" value="" name="FNAME" class="required" id="mce-FNAME">
                    </div>
                    <div class="mc-field-group">
                        <label for="mce-LNAME">Last Name <span class="asterisk">*</span></label>
                        <input type="text" value="" name="LNAME" class="required" id="mce-LNAME">
                    </div>

                    <div class="mc-field-group size1of2">
                        <label for="mce-PHONE">Phone Number <span class="asterisk">*</span>
                        </label>
                        <input type="text" name="PHONE" class="required" value="" id="mce-PHONE">
                    </div>

                    <div class="mc-field-group">
                        <label for="mce-COMPANY">Company <span class="asterisk">*</span>
                        </label>
                        <input type="text" value="" name="COMPANY" class="required" id="mce-COMPANY">
                    </div>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display:none"></div>
                        <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true">
                        <input type="text" name="b_3a1e8490d918e5239f966054e_f51b399f07" tabindex="-1" value=""></div>
                    <div class="clear"><input type="submit" value="Send Me the Media Pack" name="subscribe" id="mc-embedded-subscribe" class="button">
                    </div>
                </div>
                <p>By downloading the media pack you are consenting to be contacted by PerformanceIN regarding our products and services. </p>
            </form>
        </div>
        <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
        <script type='text/javascript'>(function($) {
				window.fnames = new Array();
				window.ftypes = new Array();
				fnames[0] = 'EMAIL';
				ftypes[0] = 'email';
				fnames[1] = 'FULLNAME';
				ftypes[1] = 'text';
				fnames[2] = 'LNAME';
				ftypes[2] = 'text';
				fnames[3] = 'ADDRESS';
				ftypes[3] = 'address';
				fnames[4] = 'PHONE';
				ftypes[4] = 'phone';
				fnames[5] = 'COMPANY';
				ftypes[5] = 'text';
			}(jQuery));
			var $mcj = jQuery.noConflict(true);</script>
        <!--End mc_embed_signup-->
    </div>
	<?php return ob_get_clean();
}

function performancein_select_package_content_html() {

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 10,
		'tax_query'      => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'partner-packages'
			),
		),
		'orderby'        => 'taxonomy, id', // Just enter 2 parameters here, seprated by comma
		'order'          => 'ASC'
	);

	$partner_package_loop = get_transient( 'performancein_partner_package_transient' );
	if ( false === $partner_package_loop ) {
		$partner_package_loop = new WP_Query( $args );
		set_transient( 'performancein_partner_package_transient', $partner_package_loop, 12 * HOUR_IN_SECONDS );
	}

	ob_start(); ?>
    <div class="profile-packages">
		<?php
		if ( $partner_package_loop->have_posts() ) {
			while ( $partner_package_loop->have_posts() ) : $partner_package_loop->the_post();
				$product_package = wc_get_product( get_the_ID() );
				?>

                <div class="profile-packages-item">
                    <h3><?php echo esc_html( get_the_title() ); ?></h3>
                    <ul class="profile-packages-item-contents">
						<?php
						$allow_html = array(
							'ul' => array(),
							'li' => array(),
						);
						echo wp_kses( $product_package->get_description(), $allow_html ); ?>
                    </ul>
                    <div class="profile-packages-item-choose">
						<?php
						$args  = apply_filters(
							'wc_price_args',
							wp_parse_args(
								$args,
								array(
									'ex_tax_label'       => false,
									'currency'           => '',
									'decimal_separator'  => wc_get_price_decimal_separator(),
									'thousand_separator' => wc_get_price_thousand_separator(),
									'decimals'           => 0,
									'price_format'       => get_woocommerce_price_format(),
								)
							)
						);
						$price = apply_filters( 'formatted_woocommerce_price', number_format( $product_package->get_regular_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $product_package->get_regular_price(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
						if ( $product_package->get_regular_price() !== '0' ) {
							printf( '<h4>%s%s/%s</h4>',
								esc_html( get_woocommerce_currency_symbol() ),
								( $product_package->get_regular_price() !== '0' ) ? esc_html( $price ) : '',
								esc_html__( 'yr', 'performancein' )
							); ?>
                            <a href="mailto:sales@performancein.com" class="profile-packages-item-button"><?php esc_attr_e( 'Email the Sales Team', 'performancein' ); ?></a>
                            <p><?php esc_attr_e( 'or call +44 777 588 2944', 'performancein' ); ?></p>
							<?php
						} else { ?>
                            <h4><?php esc_html_e( 'FREE', 'performancein' ); ?></h4>
                            <form action="/profile-hub/new?package=<?php echo esc_html( $product_package->get_slug() ); ?>"
                                  method="POST">
                                <input class="profile-packages-item-button-free" type="submit"
                                       value="<?php esc_attr_e( 'Request Profile Now', 'performancein' ); ?>">
                            </form>
						<?php }
						?>
                    </div>
                </div>
			<?php

			endwhile;
		} else {
			echo esc_html__( 'No Job Package found' );
		} ?>
    </div>
	<?php return ob_get_clean();
}
