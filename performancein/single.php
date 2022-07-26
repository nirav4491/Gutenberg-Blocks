<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package performancein
 */
global $post;
get_header();
$author_id            = get_post_field( 'post_author', get_the_ID() );
$userID               = get_the_author_meta( 'ID', $author_id );
$user_meta            = get_userdata( $userID );
$userAvtarImg         = get_avatar_url( $userID );
$userCustomAvtar      = get_user_meta( $userID, 'author_avtar_image', true );
$userCustomAvtar      = wp_get_attachment_image_src( $userCustomAvtar, array( 245, 245 ) );
$userAvtarImg         = ! empty( $userCustomAvtar ) ? $userCustomAvtar[0] : $userAvtarImg;
$Username             = get_user_meta( $userID, 'pi_user_name', true );
$Username             = ! empty( $Username ) ? $Username : $user_meta->display_name;
$userDescription      = $user_meta->description;
$userPermalink        = get_author_posts_url( $userID );
$userLinkedinURL      = get_user_meta( $userID, 'pi_linkedin_url', true );
$userTwitterURL       = get_user_meta( $userID, 'pi_twitter_url', true );
$settingArray         = pi_theme_setting();
$youMaySectionPerPage = $settingArray['you_may_like_per_page'];
$pi_partners          = get_field( 'pi_partners' );
$pi_sponsored         = get_field( 'pi_sponsored' );

?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">
			<?php
			if ( 'post' === get_post_type() || 'pi_events' === get_post_type() ) {
			?>
            <div class="grid mainContent pi-featured-image-style">
                <section class="content contentWithSidebar">
					<?php }
					?>
					<?php
					while ( have_posts() ) :
						the_post();
						/*the_post_navigation();*/

						// If comments are open or we have at least one comment, load up the comment template.
						/*if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif; */
						if ( 'post' === get_post_type() ) {
							get_template_part( 'template-parts/content', get_post_type() );
							$categories          = wp_get_post_categories( $post->ID);
							$pi_primary_category = array(
								get_field( 'pi_primary_category', $post->ID ),
							);

							$pi_primary_category = array_filter( $pi_primary_category );
							if ( ! empty( $pi_primary_category ) ) {
								$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
								$categories = array_unique( $categories );
								/*$removeCategories = array_search($pi_primary_category[0], $categories);
								unset($categories[$removeCategories]);*/
							}
							/*if(count($categories) >= 2) {
								$removeCategoriesAffiliate = array_search(104, $categories);
								unset($categories[$removeCategoriesAffiliate]);
                            }*/

							$currentID      = get_the_ID();

							$args = array(
							                'category__in' => $categories,
							                'posts_per_page' => $youMaySectionPerPage,
                                            'post__not_in' => array($post->ID),
							                'post_status'      => 'publish',
							                'fields'         => 'ids',
							                'orderby'        => 'date',
							                'order'          => 'DESC',

                                    );


							/*$args           = array(
								'post_type'      => 'post',
								'post_status'      => 'publish',
                                'posts_per_page' => $youMaySectionPerPage,
                                'fields'         => 'ids',
								'post__not_in'   => array( $currentID ),
								'orderby'        => 'date',
								'order'          => 'DESC',
								'tax_query'      => array(
									array(
										'taxonomy'         => 'category',
										'terms'            => $categories,
										'field'            => 'term_id',
                                    )
								),
							);*/
							$query          = new WP_Query( $args );
							$relatedPostIds = $query->posts;
							if ( empty( $pi_sponsored ) && empty( $pi_partners ) ) { ?>
                                <div class="pi-authorProfile" id="pi-authorProfile">
                                    <img src="<?php echo esc_url( $userAvtarImg ); ?>" class="pi-greyscale greyscale">
                                    <div class="pi-authorProfile-content">
                                        <h3><?php esc_html_e( $Username, 'performancein' ); ?></h3>
                                        <ul class="pi-authorfollow">
											<?php
											if ( '' !== $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                                                <li>
                                                    <a href="<?php echo "http://twitter.com/" . $userTwitterURL; ?>" data-icon="" rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                                                </li>

                                                <li>
                                                    <a href="<?php echo $userLinkedinURL; ?>" data-icon="" rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                                                </li>
											<?php } elseif ( '' !== $userTwitterURL && '' === $userLinkedinURL ) { ?>
                                                <li>
                                                    <a href="<?php echo "http://twitter.com/" . $userTwitterURL; ?>" data-icon="" rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                                                </li>
											<?php } elseif ( '' === $userTwitterURL && '' !== $userLinkedinURL ) { ?>
                                                <li>
                                                    <a href="<?php echo $userLinkedinURL; ?>" data-icon="" rel="nofollow"><span class="pi-visuallyhidden"></span></a>
                                                </li>
											<?php } ?>
                                        </ul>


                                        <div>
                                            <p><?php echo wp_kses_post($userDescription ); ?></p>
                                        </div>
										<?php
										$UsernameWP   = substr( $Username, 0, strpos( $Username, ' ' ) );
										$userLinkName = sprintf( 'Read more from %1$s', $UsernameWP )
										?>
                                        <a href="<?php echo $userPermalink; ?>"><?php esc_html_e( $userLinkName, 'performancein' ); ?></a>
                                    </div>
                                </div>
							<?php }
							?>

							<?php if ( ! empty( $relatedPostIds ) ) { ?>
                                <h3 class="pi-relatedContentTitle"><?php /*esc_html_e( 'You may also like…', 'performancein' ); */?></h3>
                                <nav class="pi-relatedContentList">
									<?php
									if ( is_array( $relatedPostIds ) ) {

										/*foreach ( $relatedPostIds as $relatedPostId ) {
										    $postPermalink                     = get_the_permalink( $relatedPostId );
											$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', $relatedPostId );
											$post_image_id                     = get_field( 'pi_article_image', $relatedPostId );
											$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
											$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
											$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
											//$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
											$pi_img_attri_data                 = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id) :  pi_get_img_attributes( $placeHolderImageSrc[0],$placeHolderImageID )  ;
											if ( 'video' === $pi_article_banner_section_choices ) {
												$post_image_id     = get_field( 'pi_article_video_thumbnail', $relatedPostId );
												$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
												$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id) :  pi_get_img_attributes( $placeHolderImageSrc[0],$placeHolderImageID );
											} elseif ( 'gallery' === $pi_article_banner_section_choices ) {
												$post_image_id     = get_field( 'pi_article_image_gallery_thumbnail', $relatedPostId );
												$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
												$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id) :  pi_get_img_attributes( $placeHolderImageSrc[0],$placeHolderImageID );
											}
											$pi_primary_category_id = get_field( 'pi_primary_category', $relatedPostId );
											if ( empty( $pi_primary_category_id ) ) {
												$post_terms   = wp_get_post_terms( $relatedPostId, 'category', array( 'orderby' => 'term_order' ) );
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
											$image_alt           = get_post_meta( $post_image_id, '_wp_attachment_image_alt', true );
											$image_title         = get_the_title( $post_image_id );
											$post_title          = get_the_title( $relatedPostId );
											$articleFlagCategory = wp_get_post_terms( $relatedPostId, 'category', array( 'orderby' => 'term_order' ) );

											$related_categories          = wp_get_post_categories( $relatedPostId );
											$related_pi_primary_category = array(
												get_field( 'pi_primary_category', $relatedPostId ),
											);
											if ( ! empty( $related_pi_primary_category ) ) {
												$related_categories = array_filter( array_merge( $related_pi_primary_category, $related_categories ) );
												$related_categories = array_unique( $related_categories );
											}
											$CategorriesFindRegionalName = array();
											$CategorriesFindRegionalLink = array();
											foreach ( $related_categories as $category ) {
												$piCategoryObj             = get_term_by( 'id', $category, 'category' );
												$piParentCategoryParent    = $piCategoryObj->parent;
												$parentIDObj               = get_term_by( 'id', $piParentCategoryParent, 'category' );
												if(! empty($parentIDObj)) {
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
											*/?><!--
                                            <article class="pi-relatedContentItem ">
                                                <a href="<?php /*echo esc_url( $postPermalink ); */?>" class="sidebarContentItem-link responsively-lazy">
                                                    <img src="<?php /*echo esc_attr( $pi_img_attri_data['image_src'] ); */?>" pi-srcset="<?php /*echo esc_attr( $pi_img_attri_data['image_srcset'] ); */?>"  sizes="<?php /*echo esc_attr( $pi_img_attri_data['image_size'] ); */?>" alt="<?php /*esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); */?>" class="sidebarContentItem-image responsively-lazy-loaded"/>
													<?php
/*													if ( 'video' === $pi_article_banner_section_choices ) { */?>
                                                        <div class="pi-videoIcon"></div>
													<?php /*}
													*/?>
                                                </a>
                                                <div class="pi-relatedContentItem-textContainer">
													<?php
/*													if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
														if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { */?>
                                                            <a href="<?php /*echo $CategorriesFindRegionalLink; */?>" class="articleRegionalFlag"><?php /*echo esc_html( $CategorriesFindRegionalName ); */?></a>
														<?php /*}

													}
													*/?>
                                                    <a href="<?php /*echo esc_url( $postPermalink ); */?>" class="sidebarContentItem-link">
                                                        <h3 class="pi-relatedContentItem-title"><?php /*esc_html_e( $post_title ); */?></h3>
                                                    </a>
                                                    <ul class="pi-sidebarCategories">
                                                        <li class="pi-sidebarCategories-item">
                                                            <a href="<?php /*echo $pi_term_link; */?>"><?php /*echo esc_html( $pi_term_name ); */?></a></li>
	                                                    <?php /*if ( ! empty( get_field( 'pi_partners', $relatedPostId ) ) ) {
		                                                    $partners = get_field( 'pi_partners', $relatedPostId );
		                                                    */?>
                                                            <li class="pi-listCategories-item pi-listCategories-item-partner">
                                                                <a href="<?php /*echo esc_url( get_the_permalink( $partners ) ) */?>"><?php /*esc_html_e( 'Partner Networks' ); */?></a>
                                                            </li>
	                                                    <?php /*} elseif ( ! empty( get_field( 'pi_sponsored', $relatedPostId ) ) ) { */?>

                                                            <li class="pi-listCategories-item pi-listCategories-item-sponsored">
			                                                    <?php /*esc_html_e( 'Sponsored', 'performacein' ); */?>
                                                            </li>
	                                                    <?php /*} */?>
                                                    </ul>
                                                </div>
                                            </article>
										--><?php /*}*/
										?>
									<?php } ?>
                                    <!-- Start of Bibblio RCM includes -->
                                    <link rel="stylesheet" type="text/css" href="https://cdn.bibblio.org/rcm/4.9/bib-related-content.min.css">

                                    <?php
								    if ( ! empty( $pi_sponsored ) ||  ! empty( $pi_partners ) ) { ?>
                                        <script id="bib--rcm-src" charset="UTF-8" src="https://cdn.bibblio.org/rcm/4.9/bib-related-content.min.js" data-auto-ingestion="true" data-auto-ingestion-custom-catalogue-id="partner_articles" data-recommendation-key="341de4ef-12e3-406c-9f01-086430e7cd79"></script>
                                        <h2>More from our partners</h2>
                                        <div class="bib--rcm-init" data-auto-ingestion-custom-catalogue-id="partner_articles" data-recommendation-key="341de4ef-12e3-406c-9f01-086430e7cd79" data-style-classes="bib--recency-show bib--title-only bib--font-arial bib--wide bib--default bib--box-6">
                                        </div>
                                    <?php } else { ?>
                                        <script id="bib--rcm-src" charset="UTF-8" src="https://cdn.bibblio.org/rcm/4.9/bib-related-content.min.js" data-auto-ingestion="true" data-auto-ingestion-custom-catalogue-id="default_articles" data-recommendation-key="341de4ef-12e3-406c-9f01-086430e7cd79"></script>
                                        <h2>You may also like...</h2>
                                        <div class="bib--rcm-init" data-custom-catalogue-ids="default_articles" data-query-string-params="recirc=Bibblio" data-recommendation-key="341de4ef-12e3-406c-9f01-086430e7cd79" data-style-classes="bib--recency-show bib--title-only bib--font-arial bib--wide bib--default bib--box-6">
                                        </div>
                                    <?php }
                                    ?>
                                </nav>
							<?php } ?>

						<?php } elseif ( 'pi_events' === get_post_type() ) {
							get_template_part( 'template-parts/events', get_post_type() );
						}
						?>
					<?php endwhile; // End of the loop.
					?>
					<?php
					if ( 'post' === get_post_type() || 'pi_events' === get_post_type() ) {
					?>
                </section>
                <!-- sidebar Start -->
				<?php
				if ( 'post' === get_post_type() || 'pi_events' === get_post_type() ) {
					get_sidebar();
				}
				?>
                <!-- sidebar End -->
            </div>
		<?php }
		?>


        </main><!-- #main -->
    </div><!-- #primary -->
<?php
if ( 'post' === get_post_type() ) {
	get_sidebar();
}

get_footer();