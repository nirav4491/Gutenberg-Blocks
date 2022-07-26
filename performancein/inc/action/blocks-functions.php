<?php
/**
 * This file contains all gutenberg blocks action functions.
 *
 * @package performancein
 */

/**
 * Register dynamic blocks
 * @since 1.0
 */
function performancein_register_dynamic_blocks() {

	register_block_type( 'performancein/article-listing',
		array(
			'render_callback' => 'performancein_article_listing_callback',
			'attributes'      => [
				'post_type'            => [
					'default' => '',
					'type'    => 'string'
				],
				'post_taxs'            => [
					'default' => '',
					'type'    => 'string'
				],
				'post_category'        => [
					'default' => '',
					'type'    => 'string'
				],
				'number_of_post'       => [
					'default' => '0',
					'type'    => 'number'
				],
				'category_description' => [
					'default' => false,
					'type'    => 'bool'
				],
				'exclude_post'         => [
					'default' => '',
					'type'    => 'string'
				],

			]
		)
	);
	register_block_type( 'performancein/user-grid',
		array(
			'attributes'      => array(
				'UsersFinalData' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'object' )
				),
				'userPerPage'    => array(
					'type'    => 'number',
					'default' => '0'
				)
			),
			'render_callback' => 'performancein_user_grid_render_callback',
		)
	);
	register_block_type( 'performancein/download-form',
		array(
			'attributes'      => array(
				'header'            => array(
					'type'    => 'string',
					'default' => 'Download Now'
				),
				'footertext'        => array(
					'type'    => 'string',
					'default' => 'By downloading the media pack you are consenting to be contacted by PerformanceIN regarding our products and services.'
				),
				'contactforms'      => [
					'type'  => 'array',
					'items' => array( 'type' => 'string' )
				],
				'contactform_value' => [
					'type'    => 'string',
					'default' => ''
				]
			),
			'render_callback' => 'performancein_download_form_render_callback',
		)
	);
	/*register_block_type( 'performancein/partner-listing',
		array(
			'render_callback' => 'performancein_partner_hub_listing_callback',
			'attributes'      => [
				'categories'           => [
					'type'  => 'array',
					'items' => array( 'type' => 'string' )
				],
				'categories_value'     => [
					'type' => 'string'
				],
				'basicTypePostListing' => [
					'type'    => 'number',
					'default' => 2
				]
			]
		)
	);*/
	register_block_type( 'performancein/partner-listing', array(
			'render_callback' => 'performancein_partner_listing_callback',
			'attributes'      => [
				'categories'       => [
					'type'  => 'array',
					'items' => array( 'type' => 'string' )
				],
				'categories_value' => [
					'type' => 'string'
				],
				'postsToShow'      => [
					'type'    => 'number',
					'default' => 1,
				],
			]
		)
	);
	register_block_type( 'performancein/job-listing',
		array(
			'render_callback' => 'performancein_job_listing_callback',
		)
	);

}


/**
 * Job listing block render function.
 *
 * @param $attributes
 * @param $content
 *
 * @return false|string
 * @throws Exception
 */
function performancein_job_listing_callback( $attributes, $content ) {
	ob_start();
	$page_number = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	$page_number = ( $page_number ) ? $page_number : 1;

	$job_ids        = pi_get_job_orderby();
	$posts_per_page = 10;
	$args           = array(
		'post_type'      => 'pi_jobs',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'post__in'       => $job_ids,
		'orderby'        => 'post__in',
		'order'          => 'DESC',
		'paged'          => $page_number,
	);
	$pi_jobs_query  = new WP_Query( $args );
	?>
    <div class="jobList pi_listing" id="js-jobList lazyload">
		<?php
		if ( $pi_jobs_query->have_posts() ) {
			while ( $pi_jobs_query->have_posts() ) : $pi_jobs_query->the_post();
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
			endwhile;
		} else { ?>
            <div class="page_not_found"><?php esc_html_e( 'No Job Found...!', 'performancein' ); ?></div>
		<?php } ?>
    </div>
	<?php
	global $wp;
	if ( empty( $wp->query_vars['rest_route'] ) ) {
		$pagination_extra_fields = array( 'posts_per_page' => $posts_per_page );
		pi_load_more_with_pagination( $pi_jobs_query, $page_number, 'pi_job_listing', 'listing-item', 'pagination_nonce', wp_json_encode( $pagination_extra_fields ) );
	}
	?>

	<?php

	return ob_get_clean();
}

/**
 * Fetch Donwload Form
 *
 * @param $attributes
 */
function performancein_download_form_render_callback( $attributes ) {
	$header               = isset( $attributes['header'] ) && ! empty( $attributes['header'] ) ? $attributes['header'] : 'Download Now';
	$contactformsID       = $attributes['contactform_value'];
	$contactformName      = get_the_title( $contactformsID );
	$contactFormShortcode = '[contact-form-7 id="' . $contactformsID . '" title="' . $contactformName . '"]';
	$footertext           = isset( $attributes['footertext'] ) && ! empty( $attributes['footertext'] ) ? $attributes['footertext'] : 'By downloading the media pack you are consenting to be contacted by PerformanceIN regarding our products and services.';
	$class_name           = isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? $attributes['className'] : '';
	ob_start(); ?>
    <section id="block-form"
             class="contact-form <?php echo ( $class_name ) ? esc_attr( $class_name ) : ''; ?>">
        <div class="container">
            <div class="section-headline">
                <h2 class="bg-primary">
					<?php if ( $header ) {
						echo esc_html( $header );
					} else {
						esc_html_e( 'Start Making Sense', 'performancein' );
					} ?></h2>
            </div>
            <div class="row justify-content-center">
				<?php echo do_shortcode( $contactFormShortcode ); ?>
                <p><?php esc_html_e( $footertext, 'performancein' ); ?></p>
            </div>
        </div>
    </section>
	<?php

	$html = ob_get_clean();

	return $html;
}

/**
 * Fetch dynamic Users Lists
 *
 * @param $attributes
 *
 * @return false|string
 */
function performancein_user_grid_render_callback( $attributes ) {

	$page_number = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	$page_number = ( $page_number ) ? $page_number : 1;
	//$userPerPage     = ( $attributes['userPerPage'] ) ? $attributes['userPerPage'] : 1;
	$users_per_page   = $attributes['userPerPage'];
	$users_per_page   = ! empty( $users_per_page ) ? $users_per_page : 10;
	$count_args       = array(
		'role'   => 'author',
		'fields' => 'all_with_meta',
		'number' => - 1
	);
	$user_count_query = new WP_User_Query( $count_args );
	$user_count       = $user_count_query->get_results();

	// count the number of users found in the query
	$total_users = $user_count ? count( $user_count ) : 1;
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
	?>
    <div class="pi-users-list pi-author-users-list pi_listing">
		<?php
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
		?>
    </div>
	<?php
	wp_enqueue_script( 'performancein-custom' );
	if ( $page_number < $total_pages ) {
		$pagination_extra_fields = array(
			'users_per_page' => $users_per_page,
			'total_users'    => $total_users,
		);
		wp_json_encode( $pagination_extra_fields )
		?>
        <div class="pi_endless_container listing-item-show">
            <div class="pi_endless_more" data-loading="on"
                 data-action="pi_author_listing"
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

	return ob_get_clean();
}

/**
 * Function to return Article Listing.
 *
 * @param $attributes
 *
 * @return false|string
 *
 */
function performancein_article_listing_callback( $attributes ) {
	global $wp;
	ob_start();
	$post_type     = $attributes['post_type'];
	$post_taxs     = $attributes['post_taxs'];
	$post_category = $attributes['post_category'];
	if ( $post_type !== 'pi_events' ) {
		$category_description = $attributes['category_description'];
	} else {
		$category_description = false;
	}
	$number_of_post = $attributes['number_of_post'];
	$number_of_post = ( ! empty( $number_of_post ) || 0 === $number_of_post ) ? $number_of_post : 40;
	$exclude_post   = $attributes['exclude_post'];
	$page_number    = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	$page_number    = ( $page_number ) ? $page_number : 1;
	if ( $post_type !== 'pi_events' ) {
		$args1              = array(
			'post_status'    => array( 'publish' ),
			'posts_per_page' => 3,
			'paged'          => $page_number,
			'post_type'      => 'post',
			'post__in'       => get_option( 'sticky_posts' ),
			'fields'         => 'ids'


		);
		$getPostsSticky    = get_posts( $args1 );
		$args2             = array(
			'post_status'    => array( 'publish' ),
			'posts_per_page' => 3,
			'paged'          => $page_number,
			'post_type'      => 'pi_resources',
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'pi_sticky_resources',
					'value'   => '',
					'compare' => '!='
				)
			)


		);
		$getPostsResources = get_posts( $args2 );
		$args3 = array(
			'post_status'         => array( 'publish' ),
			'posts_per_page'      => 58,
			'post_type' => array('post','pi_resources'),
			'paged'               => $page_number,
			'post__not_in' => $getPostsSticky,
			'fields' => 'ids',



		);
		$getPostsNormal = get_posts($args3);
		$semifinalPostIDS = array_merge($getPostsSticky,$getPostsResources);
		$args6 = array(
			'posts_per_page'      => 3,
			'post_type' => array('post','pi_resources'),
			'post__in' => $semifinalPostIDS,
			'fields' => 'ids',
		);
		$getFinalStick = get_posts($args6);
		$finalPostIDS = array_merge($getFinalStick,$getPostsNormal);
		$args = array(
			'posts_per_page' => $number_of_post,
			'paged'          => $page_number,
			'orderby' => 'post__in',
			'post__in' => $finalPostIDS,
			'post_type' => array('post','pi_resources'),
			'fields' => 'ids'
			/*'ignore_sticky_posts' => 1,*/


		);
		/*$getAllPostsQ = get_posts($args);*/
	} else {
		$args = array(
			'post_status'         => array( 'publish' ),
			'posts_per_page'      => $number_of_post,
			'paged'               => $page_number,
			'ignore_sticky_posts' => 1,
			'meta_query'          => array(
				'relation' => 'AND',
				array(
					'key'     => 'pi_event_start_date',
					'value'   => date( 'Ymd' ),
					'compare' => '>='
				),
				array(
					'key'     => 'pi_event_end_date',
					'value'   => date( 'Ymd' ),
					'compare' => '>='
				),
			),
			'orderby'             => 'meta_value',
			'order'               => 'ASC',
		);
	}

	if ( $post_type !== '' && $post_type !== 'post' ) {
		$args['post_type'] = $post_type;
	} elseif ( $post_type === 'post' ) {

	} else {
		return 'Select post type';
	}
	if ( true !== (bool) $category_description ) {
		if ( $post_taxs !== '' && $post_category !== '' ) {
			$args['tax_query'] = array(  // phpcs:ignore
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

	$performancein_query = new WP_Query( $args );
	if ( true === (bool) $category_description ) {
		$category          = get_term_by( 'name', $post_category, 'category' );
		$piTermDescription = get_field( 'pi_category_information', $category );
		?>
        <div class="pi-category-list-content">
			<?php echo wp_kses_post( $piTermDescription ); ?>
        </div>
		<?php
	}

	if ( $performancein_query->have_posts() ) {
		if ( 'post' === $post_type || '' === $post_type ) { ?>
            <div class="pi-article-list pi-home-article-list pi-<?php echo esc_attr( get_the_ID() ); ?>  pi_listing">
				<?php
				while ( $performancein_query->have_posts() ) {
					$performancein_query->the_post();
					if ( 'post' === get_post_type() ) {

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
						$post_image                        = wp_get_attachment_image_src( $post_image_id, array( 423, 238 ) );
						$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
						$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, array( 423, 238 ) );
						//$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
						$post_permalink           = empty( $wp->query_vars['rest_route'] ) ? get_the_permalink() : '#';
						$post_title               = get_the_title();
						$articleFlagCategory      = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
						$articleFlagCategoryArray = array();
						$flagTermName             = array();
						$flagTermLink             = array();
						//$pi_img_attri_data                 = pi_get_img_attributes( $post_image, $post_image_id );
						$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
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
						$related_categories          = wp_get_post_categories( get_the_ID() );
						$related_pi_primary_category = array(
							get_field( 'pi_primary_category', get_the_ID() ),
						);
						if ( ! empty( $related_pi_primary_category ) ) {
							$related_categories = array_filter( array_merge( $related_pi_primary_category, $related_categories ) );
							$related_categories = array_unique( $related_categories );
						}
						$CategorriesFindRegionalName = array();
						$CategorriesFindRegionalLink = array();
						foreach ( $related_categories as $category ) {
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
									$video_post_id     = get_field( 'pi_article_video_thumbnail', get_the_ID() );
									$post_image        = wp_get_attachment_image_src( $video_post_id, 'medium' );
									$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $video_post_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
									?>
                                    <div class="pi-post-thumbnail">
                                        <a href="<?php echo esc_url( $post_permalink ); ?>">
                                            <img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"/>
                                            <div class="pi-videoIcon"></div>
                                        </a>

                                    </div>
								<?php } else {
									$video_post_id     = get_field( 'pi_article_image_gallery_thumbnail', get_the_ID() );
									$post_image        = wp_get_attachment_image_src( $video_post_id, 'medium' );
									$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $video_post_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
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
									if ( ! empty( $post_title ) ):?>
                                        <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                                            <h2 class="title"><?php echo esc_html( $post_title ); ?></h2></a>
									<?php endif; ?>
                                    <ul class="pi-category-list">
                                        <li class="pi-listCategories-item">
                                            <a href="<?php echo esc_url( $pi_term_link ); ?>"><?php echo esc_html( $pi_term_name ); ?></a>
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
                                    <time class="pi-articleListItem-date" datetime="<?php echo get_the_date( 'F j, Y' ); ?>">
										<?php echo get_the_date( 'j M y' ); ?>
                                    </time>
                                </div>
                            </div>
                        </article>
					<?php } elseif ( 'pi_resources' === get_post_type() ) {
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
						if ( 1 < count( $post_terms ) ) {
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
					<?php }

				}

				wp_reset_query();
				?>
            </div>
		<?php } elseif ( 'pi_events' === $post_type ) { ?>
            <div class="pi-article-list events-list pi_listing">
				<?php
				while ( $performancein_query->have_posts() ) {
					$performancein_query->the_post();
					$post_permalink        = empty( $wp->query_vars['rest_route'] ) ? get_the_permalink() : '#';
					$post_title            = get_the_title();
					$eventStartDate        = get_field( 'pi_event_start_date', get_the_ID() );
					$eventStartDatestrtime = strtotime( $eventStartDate );
					$eventEndDate          = get_field( 'pi_event_end_date', get_the_ID() );
					$eventEndDatestrtime   = strtotime( $eventEndDate );
					$currentDate           = date( "j M Y" );
					$currentDate           = strtotime( $currentDate );
					$eventimageID          = get_field( 'pi_event_image', get_the_ID() );
					$eventImage            = wp_get_attachment_image_src( $eventimageID, 'medium' );
					$placeHolderImageID    = get_field( 'pi_article_placeholder_image', 'option' );
					$placeHolderImageSrc   = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
					//$eventImage            = ! empty( $eventImage ) ? $eventImage[0] : $placeHolderImageSrc[0];

					$pi_img_attri_data = ! empty( $eventImage ) ? pi_get_img_attributes( $eventImage[0], $eventimageID ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
					if ( $currentDate <= $eventStartDatestrtime && $currentDate <= $eventEndDatestrtime ) { ?>
                        <article class="pi-articleListItem">
                            <div class="pi-article-item-inner">

                                <div class="pi-post-thumbnail">
                                    <a href="<?php echo esc_url( $post_permalink ); ?>">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
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
					<?php }
				}
				?>
            </div>
		<?php }
	} else {
		get_template_part( 'template-parts/content', 'no-post' );
		?>
		<?php
	}
	wp_reset_postdata();

	if ( ! is_front_page() ) {
		if ( empty( $wp->query_vars['rest_route'] ) ) {
			if ( 0 !== $attributes['number_of_post'] ) {
				$pagination_extra_fields = array(
					'post_type'            => $post_type,
					'post_taxs'            => $post_taxs,
					'post_category'        => $post_category,
					'category_description' => $category_description,
					'number_of_post'       => $number_of_post,
					'exclude_post'         => $exclude_post,
				);

				pi_load_more_with_pagination( $performancein_query, $page_number, 'pi_article_listing', 'listing-item', 'pagination_nonce', wp_json_encode( $pagination_extra_fields ) );
			}
		}
	}

	$return_post_data = ob_get_clean();

	return $return_post_data;
}

/**
 * Function to return Partner Listing.
 *
 * @param $attributes
 *
 * @return false|string
 *
 */
function performancein_partner_listing_callback_backup() {

	global $wpdb;

	$items_per_page = 2;

	$request_page_id = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	//$paged = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_STRING);

	//echo $request_page_id; exit;

	$args = array(
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


	$package_value = is_array( $products->posts ) ? $products->posts : array();

	ob_start();
	//$paged = filter_input(INPUT_GET, 'pid', FILTER_SANITIZE_STRING);
	$paged = ( $request_page_id ) ? $request_page_id : 1;
//	$args = array(
//		'post_type' => 'pi_partner_networks',
//		'post_status' => 'publish',
//		'orderby' => 'meta_value_list',
//		'meta_query' => array(
//			array(
//				'key' => 'pi_package_selection',
//				'value' => array('628','627','565'),
//				'compare' => 'IN'
//			)
//		),
//		'posts_per_page' => 2,
//		'paged' => $paged,
//		//'update_post_term_cache' => false,
//	);
//	$partner_data = new WP_Query($args);


	$sql_query = "SELECT SQL_CALC_FOUND_ROWS  wp_posts.ID FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  AND (
  ( wp_postmeta.meta_key = 'pi_package_selection' AND wp_postmeta.meta_value IN ('628','627','565') )
) AND wp_posts.post_type = 'pi_partner_networks' AND ((wp_posts.post_status = 'publish')) GROUP BY wp_posts.ID ORDER BY CASE
WHEN wp_postmeta.meta_value = '628' THEN 1
WHEN wp_postmeta.meta_value = '627' THEN 2
WHEN wp_postmeta.meta_value = '565' THEN 3
END, wp_posts.post_date DESC LIMIT 0, 20";

	$result_sql_query = $wpdb->get_results( $sql_query );

	$total_posts = count( $result_sql_query );

	$offset = ( $paged - 1 ) * $items_per_page;


	$sql_query = "SELECT SQL_CALC_FOUND_ROWS  wp_posts.ID FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  AND (
  ( wp_postmeta.meta_key = 'pi_package_selection' AND wp_postmeta.meta_value IN ('628','627','565') )
) AND wp_posts.post_type = 'pi_partner_networks' AND ((wp_posts.post_status = 'publish')) GROUP BY wp_posts.ID ORDER BY CASE
WHEN wp_postmeta.meta_value = '628' THEN 1
WHEN wp_postmeta.meta_value = '627' THEN 2
WHEN wp_postmeta.meta_value = '565' THEN 3
END, wp_posts.post_date DESC LIMIT " . $offset . ", " . $items_per_page;

	$result_sql_query = $wpdb->get_results( $sql_query );

//	echo "<pre>";print_r($result_sql_query); exit;

	$keys     = array();
	$tag_data = '';
	if ( ! empty( $result_sql_query ) ) { ?>
        <div class="grid mainContent nosidebar">
        <div class="content contentWithSidebar">
        <div class="site-width-content">
        <div class="profile-hub-list">
        <div class="pi_listing profile-hub">
			<?php
			$pri = true;
			$ass = true;
			$bas = true;

			$premium_count   = 0;
			$basic_count     = 0;
			$associate_count = 0;
			foreach ( $result_sql_query as $result_post ) {
				$package_value_ids = get_post_meta( $result_post->ID, 'pi_package_selection', true );
				if ( $pri === true && $package_value_ids === '628' ) {
					$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
					$pri                                    = false;
				}
				if ( $ass === true && $package_value_ids === '627' ) {
					$associate_count ++;
					if ( $associate_count === 1 ) {

					}
					$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
					$ass                                    = false;
				}
				if ( $bas === true && $package_value_ids === '565' ) {
					$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
					$bas                                    = false;
				}
				ob_start();
				$title_partner       = get_the_title( $result_post->ID ); /* phpcs:ignore */
				$parmalink           = get_the_permalink( $result_post->ID );
				$placeHolderImageID  = get_field( 'pi_article_placeholder_image', 'option' );
				$placeHolderImageSrc = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
				$partner_image       = get_the_post_thumbnail_url( $result_post->ID );
				$partner_image       = ! empty( $partner_image ) ? $partner_image : $placeHolderImageSrc[0];

				$pi_partner_sidebar = get_field( 'pi_partner_sidebar' );

				$pi_further_info    = $pi_partner_sidebar['pi_further_info'];
				$pi_founded_year    = $pi_further_info['pi_founded_year'];
				$pi_number_of_staff = $pi_further_info['pi_number_of_staff'];

				$pi_head_office_info = $pi_partner_sidebar['pi_head_office_info'];
				$pi_city             = $pi_head_office_info['pi_city'];

				$short_content = get_the_excerpt( $result_post->ID );

				$pakage = get_post_meta( $result_post->ID, 'pi_package_selection', true );
				$pakage = get_post_field( 'post_name', $pakage );

				if ( 'basic-membership' === $pakage ) {
					$class_partner = 'mod-4-max basic';
				} elseif ( 'associate-membership' === $pakage ) {
					$class_partner = 'mod-3-max profile-package-associate';
				} elseif ( 'premium-membership' === $pakage ) {
					$class_partner = 'mod-3-max profile-package-partner';
				} else {
					$class_partner = 'mod-3-max profile-package-partner';
				}
				?>

                <article class="profile-hub-list-company <?php echo esc_attr( $class_partner ); ?>">
                    <a href="<?php echo esc_url( $parmalink ); ?>">
						<?php if ( $partner_image ): ?>
                            <span class="profile-hub-list-company-image-link">
						<img src="<?php echo esc_url( $partner_image ); ?>" alt="<?php echo esc_html( $title_partner ); ?>"
                             class="responsively-lazy responsively-lazy-loaded"
                             pi-srcset="<?php echo esc_url( $partner_image ); ?>"/>
					</span>
						<?php endif; ?>
                        <div class="profile-hub-list-company-details">
							<?php if ( $title_partner ): ?>
                                <h3 class="profile-hub-list-company-details-name mod-premier-name"><?php echo esc_html( $title_partner ); ?></h3>
							<?php endif;
							if ( $pi_city ): ?>
                                <p class="profile-hub-list-company-details-location"><span
                                            data-icon=""></span> <?php echo esc_html( $pi_city ); ?></p>
							<?php endif;
							if ( $pi_founded_year || $pi_number_of_staff ) : ?>
                                <p>
									<?php if ( $pi_founded_year ) : ?>
                                        <span
                                                class="profile-hub-list-company-details-founded">Founded <?php echo esc_html( $pi_founded_year ); ?></span>
									<?php endif;
									if ( $pi_number_of_staff ) : ?>
                                        <span
                                                class="profile-hub-list-company-details-employees"><?php echo esc_html( $pi_number_of_staff ); ?> Employees</span>
									<?php endif; ?>
                                </p>
							<?php
							endif;
							if ( $short_content ) :?>
                                <p class="profile-hub-list-company-description"><?php echo wp_kses_post( $short_content ); ?></p>
							<?php endif; ?>
                        </div>
                    </a>
                </article>

				<?php
				//get_template_part('template-parts/partner-network/content', 'partner-search-single');
				$temp_var                               = ob_get_clean();
				$keys[ $package_value_ids . '_html' ][] = $temp_var;
			}

			$first_key_array = array_keys( $keys );

			if ( ! empty( $first_key_array[0] ) ) {

				if ( 3 >= count( $keys[ $first_key_array[0] ] ) ) {
					$terms = get_terms( array( 'taxonomy' => 'partner_network_tag', 'hide_empty' => false ) );
					if ( ! empty( $terms ) ) {
						$tag_data .= '<div class="partnerNetwork-tagWrap"><h2>' . esc_html__( 'Search companies by specialism', 'performancein' ) . '</h2><ul class="profile-hub-tags">';
						foreach ( $terms as $term ) {
							$tag_data .= '<li class="profile-hub-tags-item"><a href="/profile-hub/tag/' . $term->slug . '/" class="profile-hub-tags-item-style"><span>' . $term->name . '</span></a></li></li>';
						}
						$tag_data .= '</ul></div>';
					}
					$keys[ $first_key_array[0] ][] = $tag_data;
				}
			}

			if ( ! empty( $keys ) ) {
				foreach ( $keys as $key => $partner_section_array ) {
					echo implode( '', $partner_section_array );
				}
			}

			?>
        </div>
	<?php } else { ?>
        <div class="page_not_found">
			<?php get_template_part( 'template-parts/content', 'no-post' ); ?>
        </div>
	<?php }

	global $wp;

	$count_pages = ceil( $total_posts / $items_per_page );

	for ( $i = 1; $i <= $count_pages; $i ++ ) {
		echo $i;
		echo "<br />";
	}

	//pi_load_more_with_pagination_partner($total_posts, $paged);

//	if ($total_posts > 5) {
//		pi_load_more_with_pagination_partner($partner_data, $paged, 'profile_hub_ajax_callback_function', 'listing-item', 'pagination_nonce');
//	}

	?></div></div>
    </div></div>        <?php
	return ob_get_clean();
}

function performancein_partner_listing_callback( $attributes ) {

	global $wp;
	if ( 'premium-membership' === $attributes['categories_value'] || 'associate-membership' === $attributes['categories_value'] ) {
		$post_per_pages = - 1;
	} else {
		$post_per_pages = $attributes['postsToShow'];
	}
	$pagenumber = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
	$pagenumber = ( $pagenumber ) ? $pagenumber : 1;
	$args       = array(
		'post_type'           => 'pi_partner_networks',
		'post_status'         => 'publish',
		'meta_key'            => 'pi_partner_is_conform',
		'meta_value'          => ' ',
		'meta_compare'        => '!=',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $post_per_pages,
		'orderby'             => 'title',
		'order'               => 'ASC',
		'paged'               => $pagenumber,
	);
	if ( isset( $attributes['categories_value'] ) && 'all' != $attributes['categories_value'] ) {
		$args['category'] = $attributes['categories_value'];
		if ( 'partnerNetworkTag' !== $args['category'] ) {
			if ( 'all' !== $args['category'] ) {
				$product_obj = get_page_by_path( $args['category'], OBJECT, 'product' );
				if ( isset( $product_obj->post_title ) && ! empty( $product_obj->post_title ) ) {
					$pi_product_title = $product_obj->post_title;
					if ( 'Basic Membership' != $pi_product_title ) {
						$pi_product_title = str_replace( "Membership", "Partners", $pi_product_title );
					} else {
						$pi_product_title = str_replace( "Membership", "Members", $pi_product_title );
					}
				}
				$args['meta_query'] = array(
					array(
						'key'   => 'pi_package_selection',
						'value' => $product_obj->ID,
					)
				);
			}
			ob_start();
			$performancein_query = new WP_Query( $args );
			if ( $performancein_query->have_posts() ) { ?>
                <div class="grid mainContent nosidebar">
                    <div class="content contentWithSidebar">
                        <div class="site-width-content">
                            <div class="profile-hub-list">
                                <div class="pi_listing profile-hub">
                                    <h2 class="site-width-content-header"><span><?php echo $pi_product_title; ?></span></h2>
									<?php while ( $performancein_query->have_posts() ) {
										$performancein_query->the_post();
										get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
									} ?>
                                </div>
								<?php if ( 'basic-membership' === $args['category'] ) {
									global $wp;
									if ( empty( $wp->query_vars['rest_route'] ) ) {
										$pagination_extra_fields = array(
											'number_of_post' => $post_per_pages,
											'category'       => 'basic-membership'
										);
										pi_load_more_with_pagination( $performancein_query, $pagenumber, 'pi_partner_network_listing', 'listing-item', 'pagination_nonce', wp_json_encode( $pagination_extra_fields ) );
									}
								}
								?>
                            </div>
                        </div>
                    </div>
                </div>
			<?php } else { ?>
                <div class="pi-article-list pi-home-article-list">
					<?php esc_html_e( 'No post Found..!', 'performancein' ); ?>
                </div>
			<?php }
			$return_post_data = ob_get_clean();
		} else {
			ob_start();
			$terms = get_terms( array( 'taxonomy' => 'partner_network_tag', 'hide_empty' => false ) );
			if ( ! empty( $terms ) ) {
				?>
                <div class="grid mainContent nosidebar remove-space">
                    <div class="content contentWithSidebar">
                        <div class="site-width-content">
                            <div class="profile-hub-list">
                                <div class="pi_listing profile-hub">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			wp_reset_postdata();

			$return_post_data = ob_get_clean();
		}

	} else {
		ob_start(); ?>
        <div class="pi-article-list pi-home-article-list">
			<?php esc_html_e( 'No post Found..!', 'performancein' ); ?>
        </div>
		<?php $return_post_data = ob_get_clean();
	}

	return $return_post_data;
}

function profile_hub_ajax_callback_function() {
	$result            = array();
	$result['success'] = true;
	$result['html']    = '';
	$nonce             = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action    = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class     = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$page_number = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$page_number = ( $page_number ) ? $page_number : 1;

	$args          = array(
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
	$products      = new WP_Query( $args );
	$package_value = is_array( $products->posts ) ? $products->posts : array();
	$args          = array(
		'post_type'       => 'pi_partner_networks',
		'post_status'     => 'publish',
		'orderby'         => 'meta_value_list',
		'meta_value_list' => $package_value,
		'meta_query'      => array(
			array(
				'key'     => 'pi_package_selection',
				'value'   => $package_value,
				'compare' => 'IN'
			)
		),
		'posts_per_page'  => 2,
		'paged'           => $page_number,
		//'update_post_term_cache' => false,
	);
	$the_query     = new WP_Query( $args );
	$tag_data      = '';
	if ( $the_query->have_posts() ) {
		$pri = true;
		$ass = true;
		$bas = true;
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$package_value_ids = get_post_meta( get_the_ID(), 'pi_package_selection', true );
			if ( $pri === true && $package_value_ids === '628' ) {
				$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
				$pri                                    = false;
			}
			if ( $ass === true && $package_value_ids === '627' ) {
				$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
				$ass                                    = false;
			}
			if ( $bas === true && $package_value_ids === '565' ) {
				$keys[ $package_value_ids . '_html' ][] = '<h2 class="site-width-content-header"><span>' . get_the_title( $package_value_ids ) . '</span></h2>';
				$bas                                    = false;
			}
			ob_start();
			get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
			$temp_var                               = ob_get_clean();
			$keys[ $package_value_ids . '_html' ][] = $temp_var;
		}

		$first_key_array = array_keys( $keys );

		if ( ! empty( $first_key_array[0] ) ) {
			if ( $page_number + 2 < count( $keys[ $first_key_array[0] ] ) ) {
				$terms = get_terms( array( 'taxonomy' => 'partner_network_tag', 'hide_empty' => false ) );
				if ( ! empty( $terms ) ) {
					$tag_data .= '<div class="partnerNetwork-tagWrap"><h2>' . esc_html__( 'Search companies by specialism', 'performancein' ) . '</h2><ul class="profile-hub-tags">';
					foreach ( $terms as $term ) {
						$tag_data .= '<li class="profile-hub-tags-item"><a href="/profile-hub/tag/' . $term->slug . '/" class="profile-hub-tags-item-style"><span>' . $term->name . '</span></a></li></li>';
					}
					$tag_data .= '</ul></div>';
				}
				$keys[ $first_key_array[0] ][] = $tag_data;
			}
		}

		if ( ! empty( $keys ) ) {
			foreach ( $keys as $key => $partner_section_array ) {
				$data = implode( '', $partner_section_array );
			}
		}
	}
	$result['html'] = $data;
	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

add_action( 'wp_ajax_nopriv_profile_hub_ajax_callback_function', 'profile_hub_ajax_callback_function' );
add_action( 'wp_ajax_profile_hub_ajax_callback_function', 'profile_hub_ajax_callback_function' );
