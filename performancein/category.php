<?php
get_header();
$postPerPage       = get_field( 'pi_category_post_per_page', 'option' );
$piCategory        = get_queried_object();
$piTermID          = $piCategory->term_id;
$piTermName        = $piCategory->name;
$piTermslug        = $piCategory->slug;
$piParentCategory =  $piCategory->parent;
$piTermDescription = get_field( 'pi_category_information', $piCategory );
$piChildCategories = get_terms( array(
	'taxonomy'   => 'category', // you could also use $taxonomy as defined in the first lines
	'child_of'   => $piTermID,
	'parent'     => $piTermID, // disable this line to see more child elements (child-child-child-terms)
	'hide_empty' => false,
) );
$pibradCums =  get_field( 'pi_breadcrumbs_category', $piCategory );;
$pagenumber        = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
$pagenumber        = ( $pagenumber ) ? $pagenumber : 1;
$postType = 'post';
if('webinars' === $piTermslug) {
    $postType = array('post','pi_resources');
}
$args = array(
	'post_type'      => $postType,
	'fields'         => 'ids',
	'posts_per_page' => $postPerPage,
	'post_status' =>'publish',
	'paged'          => $pagenumber,
	'tax_query'      => array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $piTermID
		)
	),
    'orderby' => 'taxonomy,id',
    'order' => 'DESC'
);
?>

<div class="grid">
	<section class="content">
		<div id="page_att_search" data-search="<?php echo esc_attr( $piTermID ); ?>"></div>
		<header class="contentNav-first">
			<?php
			if( 0 === $piParentCategory) { ?>
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
			<?php } else {
				$piParentCategoryObj = get_term_by( 'id', $piParentCategory, 'category' );
				$piParentID          = $piParentCategoryObj->term_id;
				$piParentCategoryName = $piParentCategoryObj->name; ?>
					<h1 class="contentNav-title">
						<a href="<?php echo wp_kses_post( get_term_link( $piParentID ) ); ?>" title="<?php esc_attr_e( $piParentCategoryName, 'performancein' ); ?>"><?php esc_html_e( $piParentCategoryName, 'performancein' ); ?></a>
						<span class="contentNav-titleDivider"></span><a href="<?php echo wp_kses_post( get_term_link( $piTermID ) ); ?>" title="<?php esc_attr_e( $piTermName, 'performancein' ); ?>"><?php esc_html_e( $piTermName, 'performancein' ); ?></a>
					</h1>


			<?php }
			?>

		</header>
		<?php
		if(! empty($piTermDescription)){ ?>
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
					$postID                 = get_the_ID();
					$pi_primary_category_id = get_field( 'pi_primary_category', $postID );
					if ( empty( $pi_primary_category_id ) ) {
						$post_terms   = wp_get_post_terms( $postID, 'category',array( 'orderby' => 'term_order' ) );
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
					$post_image                        = wp_get_attachment_image_src( $post_image_id, 'medium' );
					$placeHolderImageID = get_field('pi_article_placeholder_image','option');
					$placeHolderImageSrc = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
					//$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
					$post_permalink                    = get_the_permalink();
					$post_title                        = get_the_title();
					$articleFlagCategory               = wp_get_post_terms( $postID, 'category' );
					$categories                        = wp_get_post_categories( $postID );
					$pi_img_attri_data                 = ! empty( $post_image ) ? pi_get_img_attributes (  $post_image[0], $post_image_id ) :  pi_get_img_attributes (  $placeHolderImageSrc[0], $placeHolderImageID );
					$pi_primary_category               = array(
						get_field( 'pi_primary_category', $postID ),
					);
					if ( ! empty( $pi_primary_category ) ) {
						$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
						$categories = array_unique( $categories );
					}
					$CategorriesFindRegionalName = array();
					$CategorriesFindRegionalLink = array();
					foreach ($categories as $category) {
						$piCategoryObj = get_term_by( 'id', $category, 'category' );
						$piParentCategoryParent = $piCategoryObj->parent;
						$parentIDObj = get_term_by( 'id', $piParentCategoryParent, 'category' );
						if(! empty($parentIDObj)) {
							$CategorriesParentRegional = $parentIDObj->slug;
							if('regional' === $CategorriesParentRegional) {
								$piCategoryObj = get_term_by( 'id', $category, 'category' );
								$CategorriesFindRegionalName[] = $piCategoryObj->name;
								$CategorriesFindRegionalLink[] = get_term_link( $piCategoryObj->term_id );
							}
                        }




					}
					if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
						$CategorriesFindRegionalLink = $CategorriesFindRegionalLink[0];
						$CategorriesFindRegionalName = $CategorriesFindRegionalName[0];
					}
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
										<img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
									</a>
								</div>
								<?php
							} elseif ( 'video' === $pi_article_banner_section_choices ) {
								$post_image_id = get_field( 'pi_article_video_thumbnail', $postID );
								$post_image    = wp_get_attachment_image_src( $post_image_id, 'medium' );
								$pi_img_attri_data                 = ! empty( $post_image ) ? pi_get_img_attributes (  $post_image[0], $post_image_id ) :  pi_get_img_attributes (  $placeHolderImageSrc[0], $placeHolderImageID );
								?>
								<div class="pi-post-thumbnail">
									<a href="<?php echo esc_url( $post_permalink ); ?>">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
										<div class="pi-videoIcon"></div>
									</a>
								</div>
							<?php } else {
								$post_image_id = get_field( 'pi_article_image_gallery_thumbnail', $postID );
								$post_image    = wp_get_attachment_image_src( $post_image_id, 'medium' );
								$pi_img_attri_data                 = ! empty( $post_image ) ? pi_get_img_attributes (  $post_image[0], $post_image_id ) :  pi_get_img_attributes (  $placeHolderImageSrc[0], $placeHolderImageID );
								?>
								<div class="pi-post-thumbnail">
									<a href="<?php echo esc_url( $post_permalink ); ?>">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
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
				<?php
				endwhile;
				wp_reset_query();
				wp_reset_postdata();
			} else {
				get_template_part( 'template-parts/content', 'no-post' );
			} ?>
		</div>
	</section>
	<?php pi_load_more_with_pagination( $piQuery, $pagenumber, 'pi_post_category_listing' ); ?>
</div>
<?php
get_footer();
?>
