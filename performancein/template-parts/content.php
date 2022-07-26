<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package performancein
 */
global $post; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		/*  if (is_singular()) :
			  the_title('<h1 class="entry-title">', '</h1>');
		  else :
			  the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
		  endif;*/

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				/*performancein_posted_on();*/

				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php /* performancein_post_thumbnail(); */ ?>
	<?php if ( is_singular( 'post' ) ) {
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
		$categories                        = wp_get_post_categories( $post->ID );
		$PopupEnable                       = get_field( 'pi_popup_enable', 'option' );
		$pi_primary_category               = array(
			get_field( 'pi_primary_category', get_the_ID() ),
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
		$articleFlagCategory      = wp_get_post_terms( get_the_ID(), 'category', array( 'orderby' => 'term_order' ) );
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

		<article class="articlefull" itemscope itemtype="http://schema.org/NewsArticle">
            <meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="https://google.com/article"/>
			<?php
			if ( 'gallery' === $pi_article_banner_section_choices ) { ?>
				<div id="gallery-carousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<?php foreach ( $pi_article_image_galleries_ids as $pi_image_gallery ) {
							$pi_image_gallery_src    = wp_get_attachment_image_src( $pi_image_gallery, 'full' );
							$pi_article_image_srcset = wp_get_attachment_image_srcset( $pi_image_gallery );
							$pi_img_attri_data     = ! empty( $pi_image_gallery_src ) ? pi_get_img_attributes( $pi_image_gallery_src[0], $pi_image_gallery ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID ); ?>
							<div class="item" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
								<img class="pi-featured-gallery" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>">
                                <meta itemprop="url" content="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>">
                                <meta itemprop="width" content="800">
                                <meta itemprop="height" content="800">
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
				$pi_img_attri_data = ! empty( $pi_article_image ) ? pi_get_img_attributes( $pi_article_image[0], $pi_article_image_id) :  pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
				?>
				<figure class="pi-articlefeat responsively-lazy" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
					<img itemprop="image" class="pi-featured-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>" sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>" data-pisrcset="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" lazyload="true" data-lazy-sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"/>
                    <meta itemprop="url" content="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>">
                    <meta itemprop="width" content="800">
                    <meta itemprop="height" content="800">
				</figure>
			<?php }
			?>
			<div class="article-header">
				<?php
				if ( ! empty( $flagTermName ) && ! empty( $flagTermLink ) ) {
					if ( true === in_array( 'Regional', $articleFlagCategoryArray ) ) { ?>
						<a href="<?php echo $CategorriesFindRegionalLink; ?>" class="articleRegionalFlag"><?php echo esc_html( $CategorriesFindRegionalName ); ?></a>
					<?php }

				}
				?>
				<div class="pi-breadcums">
					<?php echo wp_kses_post( pi_breadcums_structure( $post->ID ) ); ?>
				</div>
				<h1 class="fullPageTitle" itemprop="headline"><?php the_title(); ?></h1>
				<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" class="microdata-hidden">
					<span itemprop="name"><?php echo get_bloginfo( 'name' ); ?></span>
                    <meta itemprop="name" content="Google">
					<span itemprop="url"><?php echo esc_url( site_url() ); ?></span>
					<span itemprop="logo" itemscope="" itemtype="https://schema.org/ImageObject">
						<?php
						$custom_logo_id = get_theme_mod( 'custom_logo' );
						$logo_image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
						$logo_image_alt = ! empty( $logo_image_alt ) ? $logo_image_alt : 'performancein';
						$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
						?>

						<img itemprop="url" src="<?php echo esc_url( $image[0] ); ?>" alt="<?php esc_attr_e( $logo_image_alt, 'performancein' ); ?>">
                          <meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
                          <meta itemprop="width" content="600">
                          <meta itemprop="height" content="60">
					</span>
				</span>
				<p class="articleIntro">
                    <?php
                    if ( has_excerpt() ) { echo wp_kses_post( get_the_excerpt() ); } else { echo ''; }
                     ?>
                </p>
				<div class="meta">
							<span class="author" itemprop="author" itemscope itemtype="https://schema.org/Person">
								<?php
								$author_id = $post->post_author;
								wp_kses_post( performancein_posted_by_single_article( $pi_partners ) );
								?>
							</span>
					<span class="time">
								<span itemprop="datePublished"><?php
									$publish_date = get_the_date( '', $post->ID );
									$publish_date = date( "d M Y", strtotime( $publish_date ) );
									esc_html_e( $publish_date, 'performancein' );
									?></span>
							</span>
					<?php if ( ! empty( $categories ) && is_array( $categories ) ) { ?>
						<div class="fullPageCategories">
							<?php
							if ( ! empty( $categories ) && is_array( $categories ) ) {
								foreach ( $categories as $category ) {
									if ( 1 !== $category ) {
										$term      = get_term( $category );
										$term_name = $term->name; ?>
										<li class="fullPageCategories-item">
											<a itemprop="articleSection" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php esc_html_e( $term_name, 'performancein' ); ?></a>
										</li>
									<?php }
								}
							}

							?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="articlecont">


				<div class="article_text">
					<?php
					$categoryName = pi_categories_name( $post->ID );
					if (  in_array( 'webinars', $categoryName, true ) ) { ?>
						<div class="socialhead"><?php echo do_shortcode( '[addtoany]' ); ?></div>
					<?php }
					?>
					<div class="article-body">
						<?php
						$adSetting = get_field( 'pi_google_ad', 'option' );
						if ( ! empty( $adSetting ) ) {
							if ( ( ! current_user_can( 'administrator' ) && current_user_can( 'account' ) ) || ! is_user_logged_in() ) { ?>
								<?php if ( empty( $pi_sponsored ) && empty( $pi_partners ) ) { ?>
									<div class="inline-advertise">
										<div id='div-gpt-ad-1563886937722-0' class="ad300x250 inlineAd300x250">
											<script>
												googletag.cmd.push(function() { googletag.display('div-gpt-ad-1563886937722-0'); });
											</script>
										</div>
									</div>
								<?php } ?>
							<?php }
						}
						?>
						<div itemprop="description">
							<?php
							the_content( sprintf(
								wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'performancein' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								get_the_title()
							) );


							/*wp_link_pages( array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'performancein' ),
								'after'  => '</div>',
							) );*/
							?>
						</div>
						<?php if ( ! empty( $pi_partners ) ) {
							$partnerNetworkImageID  = get_field( 'pi_partner_network_main_logo', 'option' );
							$partnerNetworkImageURL = wp_get_attachment_image_src( $partnerNetworkImageID, 'full' );
							$partnerNetworkImageALT = get_post_meta( $partnerNetworkImageID, '_wp_attachment_image_alt', true );
							$partnerNetworkImageALT = ! empty( $partnerNetworkImageALT ) ? $partnerNetworkImageALT : 'Partner Network';
							$partnerNetworkDes      = get_field( 'pi_partner_network_text', 'option' );
							$partnerNetworkGroup    = get_field( 'partner_network_button', 'option' );
							$partnerNetworBtnkName  = $partnerNetworkGroup['pi_partner_network_button_name'];
							$partnerNetworBtnkLink  = $partnerNetworkGroup['pi_partner_network_link']; ?>
							<span id="js-endofcontent"></span>
							<img src="<?php echo esc_url( $partnerNetworkImageURL[0] ); ?>" alt="<?php esc_attr_e( $partnerNetworkImageALT ); ?>" class="articlePartnerNetworkPlug">
							<h3><?php esc_html_e( $partnerNetworkDes, 'performancein' ); ?>
								<span class="nobreak">– <a href="<?php echo esc_url( $partnerNetworBtnkLink ); ?>"><?php esc_html_e( $partnerNetworBtnkName, 'performancein' ); ?></a></span>
							</h3>
						<?php } ?>

					</div><!-- article_body -->
					<?php if ( empty( $pi_sponsored ) && ! empty( $pi_partners ) ) {
						$partnersDescription     = get_field( 'pi_partner_description', $pi_partners );
						/*$partnersDescription     = $partnersDescription['pi_partner_description'];*/
                        $partnersDescription = get_field( 'pi_partner_description_pi_partner_description',$pi_partners );
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
									<img src="<?php echo esc_url( $partnersLogo ); ?>">
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
//						$partnersDescription = $partnersDescription['pi_partner_description'];
						$partnersDescription = get_field( 'pi_partner_description_pi_partner_description',$pi_partners );
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
									<img src="<?php echo esc_url( $partnersLogo ); ?>">
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
						$author_id       = get_post_field( 'post_author', get_the_ID() );
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
								<img src="<?php echo esc_url( $userAvtarImg ); ?>" alt="<?php esc_attr_e( $pi_image_alt, 'performancein' ); ?>">
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
				</div><!-- article_text -->
				<!-- socialfoot -->
				<?php if ( empty( $pi_partners ) ) {
					$beforeCommentContent = get_field( 'pi_comment_content_pi_before_content', 'option' );
					$beforeCommentContent = ! empty( $beforeCommentContent ) ? $beforeCommentContent : '';
					$AfterCommentContent  = get_field( 'pi_comment_content_pi_after_content', 'option' );
					$AfterCommentContent  = ! empty( $AfterCommentContent ) ? $AfterCommentContent : '';
					?>
					<div class="comments-area">
						<?php echo wp_kses_post( $beforeCommentContent ); ?>
						<?php
						// For discuss plugin comments form integrate
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						?>
						<?php
						// Facebook comments form
						/*
						$settingArray  = pi_theme_setting();
						$facebookAppID = $settingArray['facebook_app_id'];
						if ( ! empty( $facebookAppID ) ) { ?>
							<div class="fb-comments" data-href="https://developers.facebook.com/docs/plugins/comments#configurator" data-width="100%" data-numposts="5"></div>
						<?php } */
						?>
						<?php echo wp_kses_post( $AfterCommentContent ); ?>
					</div>
				<?php } ?>
				<?php
				$categoryName = pi_categories_name( $post->ID );
				if ( in_array( 'webinars', $categoryName, true ) ) { ?>
					<div class="socialfoot"><?php echo do_shortcode( '[addtoany]' ); ?></div>
				<?php }
				?>

			</div><!-- .articlecont -->
			<!-- Side popup -->
			<?php
			$relatedResourcesId = get_field( 'pi_related_resources', get_the_ID() );
			if ( ! empty( $relatedResourcesId ) ) {
				if ( ! empty( $PopupEnable ) ) {
					$PopupTitle       = get_field( 'pi_popup_title', 'option' );
					$PopupDescription = get_the_title($relatedResourcesId);
					$PopupButton      = get_field( 'pi_popup_button', 'option' );
					$popupBtnName     = get_field( 'pi_button_name', 'option' );
					$pi_landing_page_url                     = get_field( 'pi_landing_page_url',$relatedResourcesId );
					$DocumentsSrc = get_field('pi_resource_document', $relatedResourcesId);
					$DocumentsSrc =  ! empty($DocumentsSrc) ? $DocumentsSrc : '#';
					$pi_landing_page_url                     = ! empty( $pi_landing_page_url ) ? $pi_landing_page_url : $DocumentsSrc;
					$popupBtnLink = $pi_landing_page_url;
					?>
					<div id="js-relatedResource" class="pi-relatedContentPanel  show animated fadeInRight">
						<a href="#" id="js-close-relatedResource" class="pi-relatedContentPanel-close">×</a>
						<h3 class="pi-relatedContentPanel-free"><?php esc_html_e( $PopupTitle, 'performancein' ); ?>
							<br>
							<a href="<?php echo esc_url( $popupBtnLink ); ?>" class="pi-relatedContentPanel-title"><?php echo wp_kses_post( $PopupDescription ); ?></a>
						</h3>
						<a href="<?php echo esc_url( $popupBtnLink ); ?>" class="button"><?php esc_html_e( $popupBtnName, 'performancein' ); ?>
							<div class="animated infinite animateLeftRight" style="display:inline-block;">❯</div>
						</a>
					</div>
				<?php }

			} ?>
			<!-- Side popup -->
			<?php
			$relatedEventId = get_field( 'pi_article_related_event', get_the_ID() );
			if ( ! empty( $relatedEventId ) ) {
				$eventPermalink        = get_the_permalink( $relatedEventId );
				$eventTitle            = get_the_title( $relatedEventId );
				$event_image_id        = get_field( 'pi_event_image', $relatedEventId );
				$event_image           = wp_get_attachment_image_src( $event_image_id, 'full' );
				$placeHolderImageID    = get_field( 'pi_article_placeholder_image', 'option' );
				$placeHolderImageSrc   = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
				$event_image           = ! empty( $event_image ) ? $event_image[0] : $placeHolderImageSrc[0];
				$image_alt             = get_post_meta( $event_image_id, '_wp_attachment_image_alt', true );
				$image_title           = get_the_title( $event_image_id );
				$eventStartDate        = get_field( 'pi_event_start_date', $relatedEventId );
				$eventStartDatestrtime = strtotime( $eventStartDate );
				$eventEndDate          = get_field( 'pi_event_end_date', $relatedEventId );
				$eventEndDatestrtime   = strtotime( $eventEndDate );
				$currentDate           = date( "j M Y" );
				$currentDate           = strtotime( $currentDate );
				if ( $currentDate <= $eventStartDatestrtime && $currentDate <= $eventEndDatestrtime ) { ?>
					<h3 class=""><?php esc_html_e( 'You may be interested in…', 'performancein' ); ?></h3>
					<article class="pi-articleListItem">
						<div class="pi-post-thumbnail">
							<a href="<?php echo esc_url( $eventPermalink ); ?>" class="articleListItem-link responsively-lazy">
								<img src="<?php echo esc_url( $event_image ); ?>" alt="<?php esc_attr_e( $image_alt, 'performancein' ); ?>" class="articleListItem-image responsively-lazy-loaded">
							</a>
						</div>
						<div class="pi-news-details">
							<a href="<?php echo esc_url( $eventPermalink ); ?>" class="pi-articleListItem-link">
								<h2 class="title"><?php esc_html_e( $eventTitle ); ?></h2>
							</a>
							<div class="eventListItem-date">
								<time class="pi-eventListItem-date" datetime="<?php echo get_the_date( 'F j, Y', $relatedEventId ); ?>"><?php echo get_the_date( 'd M y', $relatedEventId ); ?></time>
							</div>
						</div>
					</article>
				<?php }
			}
			?>
			<?php
			$publish_date = get_the_date( '', $post->ID );
			$publish_date = date( "d M Y", strtotime( $publish_date ) );
			$modifiedDate = get_the_modified_date('', $post->ID);
			$modifiedDate = date( "d M Y", strtotime( $modifiedDate ) );
			?>
            <meta itemprop="datePublished" content="<?php esc_html_e( $publish_date, 'performancein' ); ?>"/>
            <meta itemprop="dateModified" content="<?php esc_html_e( $modifiedDate, 'performancein' ); ?>"/>
		</article>

	<?php } ?>


	<!-- 	<footer class="entry-footer">
		<?php // performancein_entry_footer(); ?>
	</footer>!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

