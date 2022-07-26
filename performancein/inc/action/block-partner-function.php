<?php
/**
 * Function to return Partner Listing.
 *
 * @param $attributes
 *
 * @return false|string
 *
 */

function performancein_partner_hub_listing_block_callback() {


	return performancein_partner_hub_listing_callback();
}


function performancein_partner_hub_listing_callback() {
	global $wpdb;
	$items_per_page  = 4;
	$request_page_id = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	$page_number     = ( $request_page_id ) ? $request_page_id : 1;

	$args        = array(
		'post_type'   => 'product',
		'post_status' => 'publish',
		'fields'      => 'ids',
		'tax_query'   => array(
			array(
				'taxonomy' => 'product_cat',
				'terms'    => 'partner-packages',
				'field'    => 'slug',
				'operator' => 'IN'
			),
		),
	);
	$pi_products = new WP_Query( $args );

	$package_values              = is_array( $pi_products->posts ) ? $pi_products->posts : array();
	$partner_query_results       = pi_partner_custom_query( $package_values, 0, $items_per_page );
	$total_posts                 = count( $partner_query_results );
	$offset                      = ( $page_number - 1 ) * $items_per_page;
	$partner_query_results_limit = pi_partner_custom_query( $package_values, $offset, $items_per_page, true );
	echo '<pre>';
	print_r( $partner_query_results_limit );
	echo '</pre>';
	ob_start();
	wp_enqueue_script( 'performancein-custom' );
	?>
    <div class="grid mainContent nosidebar">
        <div class="content contentWithSidebar">
            <div class="site-width-content">
                <div class="profile-hub-list">
                    <div class="pi_listing profile-hub">
						<?php
						$first_section_added = false;
						$package_order       = 1;
						foreach ( $partner_query_results_limit as $result_post ) {

							if ( 2 === (int) $result_post->pi_member_order && false === $first_section_added ) {
								$first_section_added = true;
								$terms = get_terms( array( 'taxonomy' => 'partner_network_tag', 'hide_empty' => false ) );
								if ( ! empty( $terms ) ) {
									?>
                                    <div class="partnerNetwork-tagWrap">
                                        <h2><?php esc_html_e( 'Search companies by specialism', 'performancein' ); ?></h2>
                                        <ul class="profile-hub-tags">
											<?php foreach ( $terms as $term ) {
												?>
                                                <li class="profile-hub-tags-item">
                                                    <a href="/profile-hub/tag/<?php echo $term->slug; ?>" class="profile-hub-tags-item-style">
                                                        <span><?php echo esc_html( $term->name ); ?></span>
                                                    </a>
                                                </li>
											<?php } ?>
                                        </ul>
                                    </div>
									<?php
								}
							}

							$title_partner      = get_the_title( $result_post->ID );
							$parmalink          = get_the_permalink( $result_post->ID );
							$partner_image      = get_the_post_thumbnail_url( $result_post->ID );
							$pi_partner_sidebar = get_field( 'pi_partner_sidebar' );

							$pi_further_info    = $pi_partner_sidebar['pi_further_info'];
							$pi_founded_year    = $pi_further_info['pi_founded_year'];
							$pi_number_of_staff = $pi_further_info['pi_number_of_staff'];

							$pi_head_office_info = $pi_partner_sidebar['pi_head_office_info'];
							$pi_city             = $pi_head_office_info['pi_city'];

							$short_content = get_the_excerpt( $result_post->ID );

							$package_slug  = get_post_field( 'post_name', $result_post->meta_value );
							$class_partner = 'mod-3-max profile-package-partner';
							if ( 'basic-membership' === $package_slug ) {
								$class_partner = 'mod-4-max basic';
							} elseif ( 'associate-membership' === $package_slug ) {
								$class_partner = 'mod-3-max profile-package-associate';
							} elseif ( 'premium-membership' === $package_slug ) {
								$class_partner = 'mod-3-max profile-package-partner';
							}
							$package_title = get_the_title( $result_post->meta_value );
							if ( $package_order === (int) $result_post->pi_member_order ) {
								$package_order ++;
								?>
                                <h2 class="site-width-content-header"><span><?php echo esc_html( $package_title ); ?></span></h2>
								<?php
							}
							?>
                            <article class="profile-hub-list-company <?php echo esc_attr( $class_partner ); ?>">
                                <a href="<?php echo esc_url( $parmalink ); ?>">
									<?php if ( isset( $partner_image ) && ! empty( $partner_image ) ) { ?>
                                        <span class="profile-hub-list-company-image-link">
											<img src="<?php echo esc_url( $partner_image ); ?>"
                                                 alt="<?php echo esc_html( $title_partner ); ?>"
                                                 class="responsively-lazy responsively-lazy-loaded"
                                                 srcset="<?php echo esc_url( $partner_image ); ?>"/>
										</span>
									<?php } ?>
                                    <div class="profile-hub-list-company-details">
										<?php if ( isset( $title_partner ) && ! empty( $title_partner ) ) { ?>
                                            <h3 class="profile-hub-list-company-details-name mod-premier-name"><?php echo esc_html( $title_partner ); ?></h3>
										<?php }
										if ( isset( $pi_city ) && ! empty( $pi_city ) ) { ?>
                                            <p class="profile-hub-list-company-details-location">
                                                <span data-icon=""></span>
												<?php echo esc_html( $pi_city ); ?>
                                            </p>
										<?php }
										if ( $pi_founded_year || $pi_number_of_staff ) { ?>
                                            <p>
												<?php if ( $pi_founded_year ) { ?>
                                                    <span class="profile-hub-list-company-details-founded">
                                                        <?php esc_html_e( 'Founded ', 'performancein' ); ?><?php echo esc_html( $pi_founded_year ); ?>
                                                    </span>
												<?php }
												if ( $pi_number_of_staff ) { ?>
                                                    <span class="profile-hub-list-company-details-employees">
                                                        <?php echo esc_html( $pi_number_of_staff ); ?><?php esc_html_e( ' Employees', 'performancein' ); ?>
                                                    </span>
												<?php } ?>
                                            </p>
											<?php
										}
										if ( isset( $short_content ) && ! empty( $short_content ) ) { ?>
                                            <p class="profile-hub-list-company-description"><?php echo wp_kses_post( $short_content ); ?></p>
										<?php } ?>
                                    </div>
                                </a>
                            </article>
							<?php

						}
						?>
                    </div>
					<?php
					$total_pages = ceil( $total_posts / $items_per_page );
					wp_enqueue_script( 'performancein-custom' );
					if ( $page_number < $total_pages ) {
						$pagination_extra_fields = array(
							'items_per_page' => $items_per_page,
							'total_pages'    => $total_pages,
						);
						wp_json_encode( $pagination_extra_fields )
						?>
                        <div class="pi_endless_container listing-item-show">
                            <div class="pi_endless_more" data-loading="on"
                                 data-action="pi_partner_hub_listing"
                                 data-security="<?php echo esc_attr( wp_create_nonce( 'pagination_nonce' ) ); ?>"
                                 data-class="listing-item"
                                 data-page="<?php echo esc_attr( $page_number + 1 ); ?>"
                                 data-extra-fields="<?php echo esc_attr( wp_json_encode( $pagination_extra_fields ) ); ?>"
                                 data-total_pages="<?php echo esc_attr( $total_pages ); ?>" rel="page">
								<?php esc_html_e( 'More', 'performancein' ); ?>
                            </div>
                            <div class="pi_endless_loading" style="display: none;"><?php esc_html_e( 'Loading', 'performancein' ); ?></div>
                        </div>
						<?php
					}
					pi_user_pagination_html( $total_pages, $page_number );
					?>
                </div>
            </div>
        </div>
    </div>
	<?php
	return ob_get_clean();
}


function pi_partner_hub_load_more_with_pagination( $premium_count, $associate_count, $basic_count, $paged, $total_posts, $items_per_page ) {
	wp_enqueue_script( 'performancein-custom' );
	?>
    <div class="pi_endless_container listing-item-show">
        <div class="pi_endless_more" rel="page"
             data-premium-count="<?php echo $premium_count; ?>"
             data-associate-count="<?php echo $associate_count; ?>"
             data-basic-count="<?php echo $basic_count; ?>"
             data-action="performancein_partner_hub_listing_load_more_ajax_callback_function"
             data-security="<?php echo esc_attr( wp_create_nonce( 'pagination_nonce' ) ); ?>"
             data-class="<?php echo esc_attr( 'listing-item' ); ?>"
             data-page="<?php echo esc_attr( $paged + 1 ); ?>"
             data-total_pages="2" rel="dsddsdsdds"
        >more
        </div>
        <div class="endless_loading" style="display: none;">loading</div>
    </div>
    <ul class="pagination_index articleListItem-show">


		<?php
		$count_pages = ceil( $total_posts / $items_per_page );

		for ( $i = 1; $i <= $count_pages; $i ++ ) { ?>
            <li>
                <a href="/profile-hub/?pid=<?php echo $i; ?>" rel="page"
                   onclick="window.location.href='/profile-hub/'"
                   class="endless_page_link"><?php echo $i; ?></a>
            </li>
		<?php }
		?>
    </ul>
	<?php
}


function pi_partner_hub_listing_callback() {
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
	$users_per_page = isset( $extra_fields['users_per_page'] ) ? $extra_fields['users_per_page'] : 30;
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
			$userID          = $user_data->ID;
			$Username        = $user_data->display_name;
			$userDescription = $user_data->description;
			$userAvtardata   = get_avatar_data( $userID );
			$userAvtarImg    = $userAvtardata['url'];
			$userPermalink   = get_author_posts_url( $userID );
			$userLinkedinURL = get_user_meta( $userID, 'pi_linkedin_url', true );
			$userTwitterURL  = get_user_meta( $userID, 'pi_twitter_url', true );
			?>
            <article class="pi-authorProfile">
                <img src="<?php echo esc_url( $userAvtarImg ); ?>" class=" pi-greyscale">
                <div class="pi-authorProfile-content">
                    <h3><?php esc_html_e( $Username, 'performanceIN' ); ?></h3>
                    <ul class="pi-authorfollow">
						<?php
						if ( '' !== $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $userTwitterURL ); ?>" data-icon="" rel="nofollow">
                                    <span class="pi-visuallyhidden"></span></a>
                            </li>

                            <li>
                                <a href="<?php echo esc_url( $userLinkedinURL ); ?>" data-icon=""
                                   rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } elseif ( '' !== $userTwitterURL && '' === $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $userTwitterURL ); ?>" data-icon="" rel="nofollow"><span
                                            class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } elseif ( '' === $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $userLinkedinURL ); ?>" data-icon=""
                                   rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                            </li>
						<?php } ?>
                    </ul>


                    <div>
                        <p><?php esc_html_e( $userDescription, 'performanceIN' ); ?></p>
                    </div>
                    <a href="<?php echo $userPermalink; ?>"><?php esc_html_e( sprintf( 'Read more from %1$s', $Username ) ); ?></a>
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


add_action( 'wp_ajax_nopriv_pi_partner_hub_listing', 'pi_partner_hub_listing_callback' );
add_action( 'wp_ajax_pi_partner_hub_listing', 'pi_partner_hub_listing_callback' );
