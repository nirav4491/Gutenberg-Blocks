<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package performancein
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
$sticky = get_option( 'sticky_posts' );
if ( ! empty( $sticky ) ) {
	rsort( $sticky );
}
$args                   = array(
	'post_status'    => 'publish',
	'post__in'       => $sticky,
	'posts_per_page' => 2,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'suppress_filters' => true,
	'fields'         => 'ids',
	'include_sticky_posts' => true,

);
$pi_sidebar_posts_query = get_posts( $args );
$Resourcesargs              = array(
	'post_status'    => 'publish',
	'post_type'      => 'pi_resources',
	'posts_per_page' => 2,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'meta_key'       => 'pi_resources_highlight',
	'meta_value'     => ' ',
	'meta_compare'   => '!=',
	'fields'         => 'ids'
);
$pi_sidebar_resources_query = get_posts( $Resourcesargs );
$adSetting                  = get_field( 'pi_google_ad', 'option' );
$pi_sponsored               = get_field( 'pi_sponsored', get_the_ID() );
$pi_partners                = get_field( 'pi_partners', get_the_ID() );
if ( empty( $pi_sponsored ) && empty( $pi_partners ) ) {
	$advertiseClass             = ! empty( $adSetting ) ? 'stickygoogleadd' : '';
} else{
	$advertiseClass = '';
}

?>
<aside class="sidebar">
	<?php if ( ! empty( $pi_sidebar_resources_query ) ) { ?>
        <section class="module">
            <h2 class="moduleTitle"><?php esc_html_e( 'Latest Resources', 'performancein' ); ?></h2>
			<?php
			$breakResources = 0;
			foreach ( $pi_sidebar_resources_query as $resourcesID ) {
				if ( $breakResources === 2 ) {
					break;
				}
				$LandingPageURL    = get_field( 'pi_landing_page_url', $resourcesID );
				$LandingPageURL    = ! empty( $LandingPageURL ) ? $LandingPageURL : get_the_permalink( $resourcesID );
				$ImageID           = get_field( 'pi_the_image_shown_on_article_lists', $resourcesID );
				$imageURL          = wp_get_attachment_image_src( $ImageID, 'full' );
				$image_alt         = get_post_meta( $ImageID, '_wp_attachment_image_alt', true );
				$resourcesTitle    = get_the_title( $resourcesID );
				$post_terms        = wp_get_post_terms( $resourcesID, 'category', array( 'orderby' => 'term_order' ) );
				$pi_img_attri_data = ! empty( $imageURL ) ? pi_get_img_attributes( $imageURL[0], $ImageID ) : pi_get_img_attributes( $imageURL[0], $ImageID );
				$pi_term_name      = array();
				$pi_term_link      = array();

				foreach ( $post_terms as $post_term ) {
					$pi_term_name[] = $post_term->name;
					$term_id        = $post_term->term_id;
					$pi_term_link[] = get_term_link( $term_id );
				}

				if ( isset( $pi_term_name ) && ! empty( $pi_term_name ) && array_filter( $pi_term_name ) ) {
					$pi_term_name = $pi_term_name[0];
					$pi_term_link = $pi_term_link[0];
				}
				?>
                <article class="sidebarContentItem">
                    <a href="<?php echo esc_url( $LandingPageURL ); ?>" class="sidebarContentItem-link responsively-lazy">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"  data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
                    </a>
                    <div class="sidebarContentItem-textContainer">
                        <a href="<?php echo esc_url( $LandingPageURL ); ?>" class="sidebarContentItem-link">
                            <h3 class="sidebarContentItem-title"><?php echo esc_html( $resourcesTitle ); ?></h3></a>
                        <ul class="sidebarCategories">
                            <li class="sidebarCategories-item">
								<?php if ( ! empty( $pi_term_name ) && ! empty( $pi_term_link ) ) { ?>
                                    <a href="<?php echo esc_url( $pi_term_link ); ?>">
										<?php echo esc_html( $pi_term_name ); ?>
                                    </a>
								<?php } ?>
                            </li>
                        </ul>
                    </div>
                </article>
				<?php
				$breakResources ++;
			}
			?>
        </section>
	<?php } ?>

    <div class="stickySidebar <?php esc_attr_e( $advertiseClass ); ?>">
        <div class="module stickyBanner">
            <section class="module">
				<?php
				if ( ! empty( $sticky ) && ! empty( $pi_sidebar_posts_query ) ) { ?>
                    <h2 class="moduleTitle"><?php esc_html_e( 'Latest Highlights', 'performancein' ); ?></h2>
					<?php
					$breakResources = 0;
					foreach ( $pi_sidebar_posts_query as $pi_sidebar_post_id ) {
						if ( $breakResources === 2 ) {
							break;
						}
						$postPermalink                     = get_the_permalink( $pi_sidebar_post_id );
						$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', $pi_sidebar_post_id );
						$post_image_id                     = get_field( 'pi_article_image', $pi_sidebar_post_id );
						$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
						$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
						$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
						//$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
						$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
						if ( 'video' === $pi_article_banner_section_choices ) {
							$post_image_id     = get_field( 'pi_article_video_thumbnail', $pi_sidebar_post_id );
							$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
							$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
						} elseif ( 'gallery' === $pi_article_banner_section_choices ) {
							$post_image_id     = get_field( 'pi_article_image_gallery_thumbnail', $pi_sidebar_post_id );
							$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
							$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
						}
						$pi_primary_category_id = get_field( 'pi_primary_category', $pi_sidebar_post_id );
						if ( empty( $pi_primary_category_id ) ) {
							$post_terms   = wp_get_post_terms( $pi_sidebar_post_id, 'category', array( 'orderby' => 'term_order' ) );
							$pi_term_name = array();
							$pi_term_link = array();
							foreach ( $post_terms as $post_term ) {
								$pi_term_name[] = $post_term->name;
								$term_id        = $post_term->term_id;
								$pi_term_link[] = get_term_link( $term_id );
							}
							$pi_term_name = isset( $pi_term_name[0] ) ? $pi_term_name[0] : '';
							$pi_term_link = isset( $pi_term_link[0] ) ? $pi_term_link[0] : '';
						} else {
							$pi_term_link = get_term_link( $pi_primary_category_id );
							$pi_term      = get_term( $pi_primary_category_id );
							$pi_term_name = $pi_term->name;
						}
						$image_alt   = get_post_meta( $post_image_id, '_wp_attachment_image_alt', true );
						$image_title = get_the_title( $post_image_id );
						$post_title  = get_the_title( $pi_sidebar_post_id );

						$articleFlagCategory      = wp_get_post_terms( $pi_sidebar_post_id, 'category', array( 'orderby' => 'term_order' ) );
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
						?>
                        <article class="sidebarContentItem">
                            <a href="<?php echo esc_url( $postPermalink ); ?>" class="sidebarContentItem-link responsively-lazy">
                                <img class="sidebarContentItem-image responsively-lazy-loaded" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                     pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"  data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
								<?php
								if ( 'video' === $pi_article_banner_section_choices ) { ?>
                                    <div class="pi-videoIcon"></div>
								<?php }
								?>
                            </a>
                            <div class="sidebarContentItem-textContainer">
								<?php
								if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
									if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { ?>
                                        <a href="<?php echo $flagTermLink; ?>" class="articleRegionalFlag"><?php echo esc_html( $flagTermName ); ?></a>
									<?php }
								}
								?>
                                <a href="<?php echo esc_url( $postPermalink ); ?>" class="sidebarContentItem-link">
                                    <h3 class="sidebarContentItem-title"><?php esc_html_e( $post_title ); ?></h3>
                                </a>
                                <ul class="sidebarCategories">
                                    <li class="sidebarCategories-item">
                                        <a href="<?php echo $pi_term_link; ?>"><?php echo esc_html( $pi_term_name ); ?></a>
                                    </li>
									<?php
									if ( ! empty( get_field( 'pi_sponsored', $pi_sidebar_post_id ) ) && ! empty( get_field( 'pi_partners', $pi_sidebar_post_id ) ) ) { ?>
                                        <li class="sidebarCategories-item sidebarCategories-item-sponsored">
											<?php esc_html_e( 'Sponsored', 'performancein' ); ?>
                                        </li>
									<?php } elseif ( empty( get_field( 'pi_sponsored', $pi_sidebar_post_id ) ) && ! empty( get_field( 'pi_partners', $pi_sidebar_post_id ) ) ) {
										$partners = get_field( 'pi_partners', $pi_sidebar_post_id );
										?>
                                        <li class="sidebarCategories-item sidebarCategories-item-partner">
                                            <a href="<?php echo esc_url( get_the_permalink( $partners ) ) ?>"><?php esc_html_e( 'Partner Networks', 'performancein' ); ?></a>
                                        </li>
									<?php } elseif ( ! empty( get_field( 'pi_sponsored', $pi_sidebar_post_id ) ) && empty( get_field( 'pi_partners', $pi_sidebar_post_id ) ) ) { ?>
                                        <li class="sidebarCategories-item sidebarCategories-item-sponsored">
											<?php esc_html_e( 'Sponsored', 'performancein' ); ?>
                                        </li>
									<?php }
									?>
                                </ul>
                            </div>
                        </article>

					<?php
						$breakResources ++;
					}
				}
				wp_reset_postdata();
				wp_reset_query();
				?>
            </section>
        </div>


		<?php
		$adSetting    = get_field( 'pi_google_ad', 'option' );
		$pi_sponsored = get_field( 'pi_sponsored', get_the_ID() );
		$pi_partners  = get_field( 'pi_partners', get_the_ID() );
		if ( ! empty( $adSetting ) ) {
			if ( ( ! current_user_can( 'administrator' ) && current_user_can( 'account' ) ) || ! is_user_logged_in() ) { ?>
				<?php if ( empty( $pi_sponsored ) && empty( $pi_partners ) ) { ?>

                    <div class="googleAd">
                        <div id='div-gpt-ad-1562237667996-0' class="ad300x250">
                            <script>
								googletag.cmd.push(function() { googletag.display('div-gpt-ad-1562237667996-0'); });
                            </script>
                        </div>
                    </div>

				<?php } ?>
			<?php }
		}
		?>

    </div>


</aside>
