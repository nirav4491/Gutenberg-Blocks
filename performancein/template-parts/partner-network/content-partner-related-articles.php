<?php
$args = array(
	'posts_per_page' => -1,
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'fields' => 'ids',
	'meta_query'     => array(  // phpcs:ignore
		array(
			'key'     => 'pi_partners',
			'value'   => get_the_ID(),
			'compare' => '='
		),
	)
);
ob_start(); ?>

<?php

$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) {
	echo '<div class="profileHubContent" id="articles">
			<h2>Related Articles</h2>
			<div class="profileHubContentList">';
				while ( $the_query->have_posts() ) {
					$term_link = array();
					$cats = array();
					$the_query->the_post();
					$categories = wp_get_post_categories( get_the_ID() );
					$pi_primary_category = get_field( 'pi_primary_category', get_the_ID() );
					if( isset( $pi_primary_category ) && !empty( $pi_primary_category ) ) {
						$term = get_term( $pi_primary_category );
						if( is_object( $term ) ) {
							$cats[]     = $term->name;
							$term_id     = $term->term_id;
							$term_link[] = get_term_link( $term_id );
						}
                    } else {
						if ( ! empty( $categories ) && is_array( $categories ) ) {
							foreach ( $categories as $category ) {
								if ( 1 !== $category ) {
									$term        = get_term( $category );
									$cats[]     =  $term->name;
									$term_id     = $term->term_id;
									$term_link[] = get_term_link( $term_id );
								}
							}
						}
                    }
					$categories     = $cats[0];
					$term_link      = $term_link[0];
					$post_image_id                     = get_field( 'pi_article_image', get_the_ID() );
					$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
					$placeHolderImageID = get_field('pi_article_placeholder_image','option');
					$placeHolderImageSrc = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
					$post_image                        = ! empty( $post_image ) ? $post_image[0] : $placeHolderImageSrc[0];
					$post_permalink = get_the_permalink();
					$post_title     = get_the_title();
					$articleFlagCategory               = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
					$related_categories                        = wp_get_post_categories( get_the_ID() );
					$related_pi_primary_category               = array(
						get_field( 'pi_primary_category', get_the_ID() ),
					);
					if ( ! empty( $pi_primary_category ) ) {
						$related_categories = array_filter( array_merge( $related_pi_primary_category, $related_categories ) );
						$related_categories = array_unique( $related_categories );
					}
					$CategorriesFindRegionalName = array();
					$CategorriesFindRegionalLink = array();
					foreach ($related_categories as $category) {
						$piCategoryObj = get_term_by( 'id', $category, 'category' );
						$piParentCategoryParent = $piCategoryObj->parent;
						$parentIDObj = get_term_by( 'id', $piParentCategoryParent, 'category' );
						if(!empty($parentIDObj)) {
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
					} ?>
					<article class="profileHubContentListItem">
						<a href="<?php echo esc_url( $post_permalink ); ?>"
						   class="profileHubContentListItem-link responsively-lazy">
							<?php if ( ! empty( $post_image ) ): ?>
								<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"
								     class="profileHubContentListItem-image responsively-lazy-loaded"><?php
							endif; ?>
						</a>
						<div class="profileHubContentListItem-textContainer">
							<?php
							if ( ! empty( $CategorriesFindRegionalName ) && ! empty( $CategorriesFindRegionalLink ) ) {
								if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { ?>
									<a href="<?php echo $CategorriesFindRegionalLink; ?>" class="articleRegionalFlag"><?php echo esc_html( $CategorriesFindRegionalName ); ?></a>
								<?php }

							}
							?>
							<?php
							if ( ! empty( $post_title ) ):?>
								<a href="<?php echo esc_url( $post_permalink ); ?>" class="profileHubContentListItem-link"><h2
										class="profileHubContentListItem-title"><?php echo esc_html( $post_title ); ?></h2></a>
							<?php endif; ?>
							<?php if ( ! empty( $categories ) ) { ?>
								<ul class="listCategories">
									<li class="pi-listCategories-item"><a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $categories ); ?></a></li>
                                    <?php if ( ! empty( get_field( 'pi_partners', get_the_ID() ) ) ) {
                                        $partners = get_field( 'pi_partners', get_the_ID() );
                                        ?>
                                        <li class="pi-sidebarCategories-item pi-sidebarCategories-item-partner">
                                            <a href="<?php echo esc_url( get_the_permalink( $partners ) ) ?>"><?php esc_html_e( 'Partner Networks' ); ?></a>
                                        </li>
                                    <?php } elseif ( ! empty( get_field( 'pi_sponsored', get_the_ID() ) ) ) { ?>

                                        <li class="pi-sidebarCategories-item pi-sidebarCategories-item-sponsored">
                                            <?php esc_html_e( 'Sponsored', 'performacein' ); ?>
                                        </li>
                                    <?php } ?>
								</ul>
							<?php } ?>
							<div class="profileHubContentListItem-date">
								<?php echo get_the_date( 'd M y' ); ?>
							</div>
						</div>
					</article>
					<?php
				}
			?>
		</div>
    </div>
	<?php
}
$return_related_post_data = ob_get_clean();

echo wp_kses_post( $return_related_post_data );
