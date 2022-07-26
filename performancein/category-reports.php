<?php
get_header();
$postPerPage       = get_field( 'pi_category_post_per_page', 'option' );
$piCategory        = get_queried_object();
$piTermID          = $piCategory->term_id;
$piTermName        = $piCategory->name;
$piTermDescription = get_field( 'pi_category_information', $piCategory );
$piChildCategories = get_terms( array(
	'taxonomy'   => 'category', // you could also use $taxonomy as defined in the first lines
	'child_of'   => $piTermID,
	'parent'     => $piTermID, // disable this line to see more child elements (child-child-child-terms)
	'hide_empty' => false,
) );
$pibradCums        = get_field( 'pi_breadcrumbs_category', $piCategory );;
$pagenumber = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
$pagenumber = ( $pagenumber ) ? $pagenumber : 1;
/**
 * Just previous reference.
 */
/*$args = array(
	'post_type'           => array( 'post', 'pi_resources' ),
	'fields'              => 'ids',
	'ignore_sticky_posts' => 1,
	'posts_per_page'      => $postPerPage,
	'paged'               => $pagenumber,
	'tax_query'           => array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $piTermID
		)
	)
);*/

$args = array(
	'post_type'           => 'pi_resources',
	'fields'              => 'ids',
	'ignore_sticky_posts' => 1,
	'post_status' => 'publish',
	'posts_per_page'      => $postPerPage,
	'paged'               => $pagenumber,
);
?>

<div class="grid">
	<section class="content">
		<div id="page_att_search" data-search="<?php echo esc_attr( $piTermID ); ?>"></div>
		<header class="contentNav-first">
			<h1 class="contentNav-title">
				<a href="<?php echo wp_kses_post( get_term_link( $piTermID ) ); ?>" title="<?php esc_attr_e( $piTermName, 'performancein' ); ?>"><?php esc_html_e( $piTermName, 'performancein' ); ?></a>
			</h1>
			<ul class="contentNav-subcategories">
				<?php
				if ( ! empty( $pibradCums ) ) {
					if ( is_array( $pibradCums ) ) {
						foreach ( $pibradCums as $piChildCategory ) {
							$piChildCategoryID   = $piChildCategory->term_id;
							$piChildCategoryName = $piChildCategory->name;
							$piChildCategoryLink = get_term_link( $piChildCategoryID );

							?>
							<li>
								<a href="<?php echo esc_url( $piChildCategoryLink ); ?>" title="<?php esc_attr_e( $piChildCategoryName, 'performancein' ); ?>"><?php esc_attr_e( $piChildCategoryName, 'performancein' ); ?></a>
							</li>
						<?php } ?>
					<?php }
				} ?>


			</ul>
		</header>
		<?php
		if ( ! empty( $piTermDescription ) ) { ?>
			<div class="pi-category-list-content">
				<?php echo wp_kses_post( $piTermDescription ); ?>
			</div>
		<?php }
		?>
		<div class="pi-article-list pi_listing">
			<?php
			$piQuery = new WP_Query( $args );
			if ( $piQuery->have_posts() ) {
				while ( $piQuery->have_posts() ) : $piQuery->the_post();
					$pi_the_image_shown_on_article_lists_ids = get_field( 'pi_the_image_shown_on_article_lists', get_the_ID() );
					$post_image                              = wp_get_attachment_image_src( $pi_the_image_shown_on_article_lists_ids, 'full' );
					$placeHolderImageID = get_field('pi_article_placeholder_image','option');
					$placeHolderImageSrc = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
					//$post_image                              = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
					$image_alt                               = get_post_meta( $pi_the_image_shown_on_article_lists_ids, '_wp_attachment_image_alt', true );
					$pi_landing_page_url                     = get_field( 'pi_landing_page_url', get_the_ID() );
					$post_title                              = get_the_title();
					$post_terms                              = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
					$DocumentsSrc = get_field('pi_resource_document', get_the_ID());
					$DocumentsSrc =  ! empty($DocumentsSrc) ? $DocumentsSrc : '#';
					$pi_landing_page_url                     = ! empty( $pi_landing_page_url ) ? $pi_landing_page_url : $DocumentsSrc;
					$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes ( $post_image[0], $pi_the_image_shown_on_article_lists_ids ) : pi_get_img_attributes ( $placeHolderImageSrc[0], $placeHolderImageID );
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
                                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
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
				endwhile;
			} else {
				get_template_part( 'template-parts/content', 'no-post' );
			} ?>
		</div>
	</section>
	<?php pi_load_more_with_pagination( $piQuery, $pagenumber, 'pi_resources_category_listing' ); ?>
</div>
<?php
get_footer();
?>
