<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package performancein
 */
/*$time_start = microtime(true);
sleep(1);
$time_end = microtime(true);
$time = $time_end - $time_start;*/


$author_id              = get_post_field( 'post_author', get_the_ID() );
$user_id                = get_the_author_meta( 'ID', $author_id );
$user_meta              = get_userdata( $user_id );
$pi_primary_category_id = get_field( 'pi_primary_category', get_the_ID() );
if ( empty( $pi_primary_category_id ) ) {
	$post_terms   = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
	$pi_term_name = array();
	$pi_term_link = array();
	if ( is_array( $post_terms ) && ! empty( $post_terms ) ) {
		foreach ( $post_terms as $post_term ) {
			$pi_term_name[] = $post_term->name;
			$term_id        = $post_term->term_id;
			$pi_term_link[] = get_term_link( $term_id );
		}
	}
	$pi_term_name = ( isset( $pi_term_name ) && ! empty( $pi_term_name ) ) && array_filter( $pi_term_name ) ? $pi_term_name[0] : '';
	$pi_term_link = ( isset( $pi_term_link ) && ! empty( $pi_term_link ) ) && array_filter( $pi_term_link ) ? $pi_term_link[0] : '#';

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
$related_categories                = wp_get_post_categories( get_the_ID() );
$related_pi_primary_category       = array(
	get_field( 'pi_primary_category', get_the_ID() ),
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
	if(! empty($parentIDObj)){
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

			} ?>
			<?php
			if ( ! empty( $post_title ) ):?>
                <a href="<?php echo esc_url( $post_permalink ); ?>" class="pi-articleListItem-link">
                    <h2 class="title"><?php echo esc_html( $post_title ); ?></h2></a>
			<?php endif; ?>
            <ul class="pi-category-list">
				<?php if ( isset( $pi_term_name ) && ! empty( $pi_term_name ) ) { ?>
                    <li class="pi-listCategories-item">
                        <a href="<?php echo esc_url( $pi_term_link ); ?>"><?php echo esc_html( $pi_term_name ); ?></a>
                    </li>
				<?php } ?>
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
            <time class="pi-articleListItem-date" datetime="<?php echo get_the_date( 'F j, Y' ); ?>"><?php echo get_the_date( 'd M y' ); ?></time>
        </div>
    </div>
</article>
