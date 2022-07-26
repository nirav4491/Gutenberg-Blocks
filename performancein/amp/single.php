
<!doctype html>
<html amp <?php echo esc_attr( AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ) ); ?>>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
	<?php do_action( 'amp_post_template_head', $this ); ?>
	<style amp-custom>
		<?php $this->load_parts( array( 'style' ) ); ?>
		<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>
<body class="<?php echo esc_attr( $this->get( 'body_class' ) ); ?>">
<?php $this->load_parts( array( 'header-bar' ) ); ?>
<?php
$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices' );
$pi_article_image_id               = get_field( 'pi_article_image' );
$pi_article_image                  = wp_get_attachment_image_src( $pi_article_image_id, 'full' );
$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
$pi_article_image_srcset           = wp_get_attachment_image_srcset( $pi_article_image_id );
$pi_image_alt                      = get_post_meta( $pi_article_image_id, '_wp_attachment_image_alt', true );
$pi_image_alt                      = ! empty( $pi_image_alt ) ? $pi_image_alt : 'performancein';
$pi_article_video                  = get_field( 'pi_article_video' );
$pi_article_image_galleries_ids    = get_field( 'pi_article_image_galleries' );
$pi_partners                       = get_field( 'pi_partners' );
$pi_sponsored                      = get_field( 'pi_sponsored' );
$piImageCreditName                 = get_post_meta( $pi_article_image_id, 'pi_image_credit_name', true );
$piImageCreditURL                  = get_post_meta( $pi_article_image_id, 'pi_image_credit_url', true );
$categories                        = wp_get_post_categories( $this->ID );
$PopupEnable                       = get_field( 'pi_popup_enable', 'option' );
$pi_primary_category               = array(
	get_field( 'pi_primary_category', $this->ID ),
);
if ( ! empty( $pi_primary_category ) ) {
	$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
	$categories = array_unique( $categories );
}
$articleFlagCategory      = wp_get_post_terms( $this->ID, 'category' );
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
<article class="amp-wp-article">
	<?php
	if ( 'gallery' === $pi_article_banner_section_choices ) { ?>
		<div id="gallery-carousel" class="carousel slide" data-ride="carousel">
			<div class="carousel-inner">
				<?php foreach ( $pi_article_image_galleries_ids as $pi_image_gallery ) {
					$pi_image_gallery_src    = wp_get_attachment_image_src( $pi_image_gallery, 'full' );
					$pi_article_image_srcset = wp_get_attachment_image_srcset( $pi_image_gallery );
					?>
					<div class="item">
						<amp-img src="<?php echo esc_url( $pi_image_gallery_src[0] ); ?>" alt="<?php esc_attr_e( $pi_image_alt, 'performancein' ); ?>" width="400" height="300" layout="fixed"></amp-img>
					</div>

				<?php } ?>

				<a class="left carousel-control" href="#gallery-carousel" role="button" data-slide="prev" tabindex="-1"><span class="icon icon-arrow-left"></span><span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#gallery-carousel" role="button" data-slide="next" tabindex="-1">
					<span class="icon icon-arrow-right"></span><span class="sr-only">Next</span>
				</a>
			</div>
		</div>
	<?php } elseif ( 'video' === $pi_article_banner_section_choices ) { ?>
		<div class="pi-articleVideo">
			<?php echo $pi_article_video; ?>
		</div>

	<?php } else {
		$pi_article_image  = ! empty( $pi_article_image ) ? $pi_article_image[0] : $placeHolderImageSrc[0];
		$pi_img_attri_data  = ! empty( $pi_article_image ) ? pi_get_img_attributes( $pi_article_image[0], $pi_article_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
		?>
		<figure class="pi-articlefeat responsively-lazy">

			<amp-img itemprop="image" class="pi-featured-image" src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" width="800" height="400" layout="fixed"></amp-img>
		</figure>
	<?php }
	?>
	<header class="amp-wp-article-header">
		<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>
		<div class="amp-wp-meta amp-wp-posted-on">



			<?php
			$authorObj    = $this->get( 'post_author' );
			$authorID     = $authorObj->ID;
			$userAvtarImg = get_avatar_url($authorID);
			$userCustomAvtar = get_user_meta($authorID,'author_avtar_image', true);
			$userCustomAvtar  = wp_get_attachment_image_src( $userCustomAvtar, array( 245, 245 )  );
			$userAvtarImg = ! empty($userCustomAvtar) ? $userCustomAvtar[0] : $userAvtarImg;
			$userInfo = get_user_meta($authorID,'pi_user_name', true);
			$userInfo = ! empty($userInfo) ? $userInfo : get_the_author();
			$byline = sprintf(
			/* translators: %s: post author. */
				esc_html_x( '%s', 'post author', 'performancein' ),
				'<img src="' . esc_url($userAvtarImg) . '" class="pi-author-image greyscale"> <a id="author_info" class="author_info url fn n" href="#pi-authorProfile"><span class="author vcard" itemprop="name">' . esc_html( $userInfo ) . '</span></a>'
			);
			echo '<span class="byline"> ' . $byline . '</span>';
			?>
			<time datetime="<?php echo esc_attr( date( 'c', $this->get( 'post_publish_timestamp' ) ) ); ?>">
				<?php
				echo esc_html(
					sprintf(
					/* translators: %s: the human-readable time difference. */
						__( '%s ago', 'amp' ),
						human_time_diff( $this->get( 'post_publish_timestamp' ), current_time( 'timestamp' ) )
					)
				);
				?>
			</time>
		</div>
	</header>
    <div class="amp-wp-article-content">
		<?php echo $this->get( 'post_amp_content' ); // amphtml content; no kses ?>
		<?php if ( empty( $pi_sponsored ) && ! empty( $pi_partners ) ) {
			$partnersDescription     = get_field( 'pi_partner_description', $pi_partners );
			$partnersDescription     = $partnersDescription['pi_partner_description'];
			$partnersDescription     = pi_limit_content( $partnersDescription, 483 );
			$partnersName            = get_the_title( $pi_partners );
			$partnersLogo            = get_the_post_thumbnail_url( $pi_partners, 'medium' );
			$partnersPermalink       = get_the_permalink( $pi_partners );
			$partneresImaheID        = get_post_thumbnail_id( $pi_partners );
			$pi_article_image_srcset = wp_get_attachment_image_srcset( $pi_article_image_id );
			?>
			<div class="sponsorProfile">
				<?php
				if ( $partnersLogo ) { ?>
					<div class="sponsorImages">
						<amp-img src="<?php echo esc_url( $partnersLogo ); ?>" width="150" height="150" layout="fixed"></amp-img>
					</div>
				<?php }
				?>
				<div class="sponsorProfile-content">
					<div class="pi-pn-infotext">
						<h2><?php esc_html_e( 'Content Partner', 'performancein' ); ?></h2>
						<h3><?php esc_html_e( $partnersName, 'performanein' ); ?></h3>
						<p><?php echo wp_kses_post( $partnersDescription ); ?></p>
						<a href="<?php echo esc_url( $partnersPermalink ); ?>"><?php esc_html_e( sprintf( 'Read more about %1$s', $partnersName ), 'performancein' ); ?></a>
					</div>
				</div>
			</div> <!-- sponsorProfile -->
		<?php } elseif ( ! empty( $pi_sponsored ) && ! empty( $pi_partners ) ) {
			$partnersDescription = get_field( 'pi_partner_description', $pi_partners );
			$partnersDescription = $partnersDescription['pi_partner_description'];
			$partnersDescription = pi_limit_content( $partnersDescription, 483 );
			$partnersName        = get_the_title( $pi_partners );
			$pi_article_image    = wp_get_attachment_image_src( $pi_partners, 'full' );
			$partnersLogo        = get_the_post_thumbnail_url( $pi_partners, 'medium' );
			$partnersPermalink   = get_the_permalink( $pi_partners );
			?>
			<div class="sponsorProfile">
				<?php
				if ( $partnersLogo ) { ?>
					<div class="sponsorImages">
						<amp-img src="<?php echo esc_url( $partnersLogo ); ?>" width="150" height="150" layout="fixed"></amp-img>
					</div>
				<?php }
				?>
				<div class="sponsorProfile-content">
					<div class="pi-pn-infotext">
						<h2><?php esc_html_e( 'Content Partner', 'performancein' ); ?></h2>
						<h3><?php esc_html_e( $partnersName, 'performancein' ); ?></h3>
						<p><?php echo wp_kses_post( $partnersDescription ); ?></p>
						<a href="<?php echo esc_url( $partnersPermalink ); ?>"><?php esc_html_e( sprintf( 'Read more about %1$s', $partnersName ), 'performancein' ); ?></a>
					</div>
				</div>
			</div> <!-- sponsorProfile -->
		<?php } elseif ( ! empty( $pi_sponsored ) && empty( $pi_partners ) ) {
			$author_id       = get_post_field( 'post_author', $this->ID );
			$userID          = get_the_author_meta( 'ID', $author_id );
			$user_meta       = get_userdata( $userID );
			$userAvtarImg    = get_avatar_url( $userID );
			$userCustomAvtar      = get_user_meta( $userID, 'author_avtar_image', true );
			$userCustomAvtar      = wp_get_attachment_image_src( $userCustomAvtar, 'full' );
			$userAvtarImg         = ! empty( $userCustomAvtar ) ? $userCustomAvtar[0] : $userAvtarImg;
			$Username        = $user_meta->display_name;
			$userDescription = $user_meta->description;
			$userDescription = pi_limit_content( $userDescription, 483 );
			$userPermalink   = get_author_posts_url( $userID ); ?>
			<div class="sponsorProfile">
				<div class="sponsorImages">
					<amp-img src="<?php echo esc_url( $userAvtarImg ); ?>" alt="<?php esc_attr_e( $pi_image_alt, 'performancein' ); ?>" width="150" height="150" layout="fixed"></amp-img>
				</div>
				<div class="sponsorProfile-content">
					<div class="pi-pn-infotext">
						<h2><?php esc_html_e( 'Content Sponsor', 'performancein' ); ?></h2>
						<h3><?php esc_html_e( $Username, 'performancein' ); ?></h3>
						<p><?php esc_html_e( $userDescription, 'performancein' ); ?></p>
						<a href="<?php echo esc_url( $userPermalink ); ?>"><?php esc_html_e( sprintf( 'Read more about %1$s', $Username ), 'performancein' ); ?></a>
					</div>
				</div>
			</div>
			<!-- sponsorProfile -->
		<?php } ?>
	</div>
	<!--<footer class="amp-wp-article-footer">
		<?php /*$this->load_parts( apply_filters( 'amp_post_article_footer_meta', array( 'meta-taxonomy', 'meta-comments-link' ) ) ); */ ?>
	</footer>-->
</article>
<?php
$settingArray         = pi_theme_setting();
$youMaySectionPerPage = $settingArray['you_may_like_per_page'];
$currentID            = get_the_ID();
$categories           = wp_get_post_categories( $this->ID );
$args                 = array(
	'post_type'      => 'post',
	'posts_per_page' => $youMaySectionPerPage,
	'post__not_in'   => array( $currentID ),
	'fields'         => 'ids',
	'tax_query'      => array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $categories[0]
		)
	)
);
$query                = new WP_Query( $args );
$relatedPostIds       = $query->posts;
if ( ! empty( $relatedPostIds ) ) { ?>
	<h2><?php esc_html_e( 'Related Articles', 'performancein' ); ?></h2>
	<?php
	foreach ( $relatedPostIds as $relatedPostId ) {
		$postPermalink                     = get_the_permalink( $relatedPostId );
		$pi_article_banner_section_choices = get_field( 'pi_article_banner_section_choices', $relatedPostId );
		$post_image_id                     = get_field( 'pi_article_image', $relatedPostId );
		$post_image                        = wp_get_attachment_image_src( $post_image_id, 'full' );
		$placeHolderImageID                = get_field( 'pi_article_placeholder_image', 'option' );
		$placeHolderImageSrc               = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
		$pi_img_attri_data                 = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
		$postPublishDate                   = get_the_date( $relatedPostId );
		if ( 'video' === $pi_article_banner_section_choices ) {
			$post_image_id     = get_field( 'pi_article_video_thumbnail', $relatedPostId );
			$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
			$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
		} elseif ( 'gallery' === $pi_article_banner_section_choices ) {
			$post_image_id     = get_field( 'pi_article_image_gallery_thumbnail', $relatedPostId );
			$post_image        = wp_get_attachment_image_src( $post_image_id, 'full' );
			$pi_img_attri_data = ! empty( $post_image ) ? pi_get_img_attributes( $post_image[0], $post_image_id ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
		}
		$pi_primary_category_id = get_field( 'pi_primary_category', $relatedPostId );
		if ( empty( $pi_primary_category_id ) ) {
			$post_terms   = wp_get_post_terms( $relatedPostId, 'category' );
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
		$image_alt                = get_post_meta( $post_image_id, '_wp_attachment_image_alt', true );
		$image_title              = get_the_title( $post_image_id );
		$post_title               = get_the_title( $relatedPostId );
		$articleFlagCategory      = wp_get_post_terms( $relatedPostId, 'category' );
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
		/*$author_id    = get_post_field( 'post_author', $relatedPostId );
		$userMeta     = get_user_meta( $author_id, 'pi_company_name', true );
		$userID       = get_the_author_meta( 'ID', $author_id );
		$user_meta    = get_userdata( $userID );
		$userAvtarImg = get_avatar_url( $userID );
		$userInfo     = $user_meta->display_name;
		if ( ! empty( $userMeta ) ) {
			$userInfo       = $userMeta;
			$partnerImageID = get_post_thumbnail_id( $pi_partners );
			$partnerImage   = wp_get_attachment_image_src( $partnerImageID, 'full' );
			$partnerImage   = $partnerImage[0];
			$userAvtarImgID = get_user_meta( $author_id, 'pi_company_small_logo', true );
			$userAvtarImg   = wp_get_attachment_image_src( $userAvtarImgID, 'full' );
			$userAvtarImg   = $userAvtarImg[0];
			$userAvtarImg   = ! empty( $userAvtarImg ) ? $userAvtarImg : $partnerImage;

		}*/

		$author_id    = get_post_field( 'post_author', $relatedPostId );
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


		?>
		<article class="">
			<a href="<?php echo esc_url( $postPermalink ); ?>" class="sidebarContentItem-link responsively-lazy">
				<amp-img src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" class="sidebarContentItem-image responsively-lazy-loaded" width="400" height="250" layout="fixed"></amp-img>
				<?php
				if ( 'video' === $pi_article_banner_section_choices ) { ?>
					<div class="pi-videoIcon"></div>
				<?php }
				?>
			</a>
			<div class="pi-relatedContentItem-textContainer">
				<a href="<?php echo esc_url( $postPermalink ); ?>" class="sidebarContentItem-link">
					<h3 class="pi-relatedContentItem-title"><?php esc_html_e( $post_title ); ?></h3>
				</a>
			</div>
			<?php esc_html_e( "By " . $Username . " Posted ", 'performancein' ); ?>
			<div class="amp-wp-meta amp-wp-posted-on">
				<time datetime="<?php echo esc_attr( date( 'c', $postPublishDate ) ); ?>">
					<?php
					echo esc_html(
						sprintf(
						/* translators: %s: the human-readable time difference. */
							__( '%s ago', 'amp' ),
							human_time_diff( get_the_time( 'U', $relatedPostId ), current_time( 'timestamp' ) )
						)
					);
					?>
				</time>
			</div>
			<?php echo pi_breadcums_structure_amp( $relatedPostId ); ?>


		</article>
	<?php }
}
?>

<?php $this->load_parts( array( 'footer' ) ); ?>
<?php do_action( 'amp_post_template_footer', $this ); ?>
</body>
</html>
