<?php
/**
 * Template part for displaying events posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package performancein
 */
$eventExcerpt        = get_the_excerpt( get_the_ID() );
$eventTitle          = get_the_title( get_the_ID() );
$eventLocation       = get_field( 'pi_event_location', get_the_ID() );
$eventStartDate      = get_field( 'pi_event_start_date', get_the_ID() );
$eventEndDate        = get_field( 'pi_event_end_date', get_the_ID() );
$eventHashtag        = get_field( 'pi_event_hashtag', get_the_ID() );
$eventURL            = get_field( 'pi_event_url', get_the_ID() );
$eventPerformerName  = get_field( 'pi_event_performer_name', get_the_ID() );
$eventimageID        = get_field( 'pi_event_image' );
$eventImage          = wp_get_attachment_image_src( $eventimageID, 'event-header' );
$placeHolderImageID  = get_field( 'pi_article_placeholder_image', 'option' );
$placeHolderImageSrc = wp_get_attachment_image_src( $placeHolderImageID, 'full' );
$pi_img_attri_data          = ! empty( $eventImage ) ? pi_get_img_attributes( $eventImage[0], $eventimageID ) : pi_get_img_attributes( $placeHolderImageSrc[0], $placeHolderImageID );
$itmPropStartDate    = date( "j M y", strtotime( $eventStartDate ) );
$itmPropStartDate    = date( "yy-m-d", strtotime( $eventStartDate ) );
$itmPropEndtDate     = date( "j M y", strtotime( $eventEndDate ) );
$itmPropEndtDate     = date( "yy-m-d", strtotime( $eventEndDate ) );
$eventSchemaPrice = get_field('pi_event_price',get_the_ID());
$eventSchemaAvailiblity = get_field('pi_event_availability',get_the_ID());
$eventSchemaValidFrom = get_field('pi_event_valid_from',get_the_ID());
$eventSchemaStreetAddress = get_field('pi_event_street_address',get_the_ID());
$eventSchemaRegion = get_field('pi_event_region',get_the_ID());
$eventSchemaPostalcode = get_field('pi_event_postal_code',get_the_ID());
$eventSchemaCountry = get_field('pi_event_country',get_the_ID());
$itmPropStartValidFrom    = date( "j M y", strtotime( $eventSchemaValidFrom ) );
$itmPropStartValidFrom    = date( "yy-m-d", strtotime( $eventSchemaValidFrom ) );
if( empty( $eventPerformerName ) ) {
	$eventPerformerName = 'PerformanceIN';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		/*  if (is_singular()) :
			  the_title('<h1 class="entry-title">', '</h1>');
		  else :
			  the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
		  endif;*/

		if ( 'pi_events' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				/*performancein_posted_on();*/

				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->
	<article class="articlefull event-detail" itemscope itemtype="https://schema.org/Event">
		<figure class="pi-articlefeat responsively-lazy">
			<img class="pi-featured-image" itemprop="image" src="<?php echo esc_attr( $pi_img_attri_data['image_src'] ); ?>" pi-srcset="<?php echo esc_attr( $pi_img_attri_data['image_srcset'] ); ?>"  sizes="<?php echo esc_attr( $pi_img_attri_data['image_size'] ); ?>"  alt="<?php esc_attr_e( $pi_img_attri_data['image_alt'], 'performancein' ); ?>"/>
		</figure>
		<div class="article-header">
			<div class="pi-breadcums">
				<?php echo wp_kses_post( pi_breadcums_structure( get_the_ID() ) ); ?>
			</div>
			<h1 class="fullPageTitle" itemprop="name"><?php the_title(); ?></h1>
			<div class="meta">
                <?php if ($eventStartDate === $eventEndDate) { ?>
                    <span class="time" itemprop="startDate" content="<?php esc_attr_e( $itmPropStartDate, 'performancein' ); ?>"><?php esc_html_e( $eventStartDate, 'performancein' ); ?></span>
                    <span class="hiddenschemaurl" itemprop="endDate" content="<?php esc_attr_e( $itmPropEndtDate, 'performancein' ); ?>"> <?php esc_html_e( ' - ', 'performancein' ); ?><?php esc_html_e( $eventEndDate, 'performancein' ); ?></span>
                <?php } else { ?>
                    <span class="time" itemprop="startDate" content="<?php esc_attr_e( $itmPropStartDate, 'performancein' ); ?>"><?php esc_html_e( $eventStartDate, 'performancein' ); ?></span>
                    <span class="time" itemprop="endDate" content="<?php esc_attr_e( $itmPropEndtDate, 'performancein' ); ?>"> <?php esc_html_e( ' - ', 'performancein' ); ?><?php esc_html_e( $eventEndDate, 'performancein' ); ?></span>
                <?php } ?>

				<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" itemprop="url" class="hiddenschemaurl"><?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?></a>
				<?php if ( ! empty( $eventLocation ) ) { ?>
					<span class="event-venue" itemprop="location" itemscope itemtype="http://schema.org/Place">
						<span class="location" itemprop="name"><?php esc_html_e( sprintf( '%1$s ', $eventLocation ), 'performancein' ); ?></span>
						<span class="hiddenschemaurl" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                            <span itemprop="streetAddress"><?php esc_html_e( sprintf( '%1$s ', $eventSchemaStreetAddress ), 'performancein' ); ?></span>
							<span itemprop="addressLocality"><?php esc_html_e( sprintf( '%1$s ', $eventLocation ), 'performancein' ); ?></span>
							<span itemprop="addressRegion"><?php esc_html_e( sprintf( '%1$s ', $eventSchemaRegion ), 'performancein' ); ?></span>
							<span itemprop="postalCode"><?php esc_html_e( sprintf( '%1$s ', $eventSchemaPostalcode ), 'performancein' ); ?></span>
							<span itemprop="addressCountry"><?php esc_html_e( sprintf( '%1$s ', $eventSchemaCountry ), 'performancein' ); ?></span>
						</span>
					</span>
					
					<?php
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo_image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
					$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' ); ?>
					<span class="hiddenschemaurl" itemprop="performer" itemscope="" itemtype="http://schema.org/Organization">
						<link itemprop="sameAs" href="<?php echo esc_url(site_url()); ?>" />
						<span itemprop="name"><?php  echo esc_html( $eventPerformerName ); ?></span>
					</span>
                    <div class="hiddenschemaurl" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <span itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>"><?php echo get_woocommerce_currency_symbol();?></span><span
                                itemprop="price" content="<?php esc_html_e($eventSchemaPrice,'performancein');?>"><?php esc_html_e($eventSchemaPrice,'performancein');?></span>
                        <link itemprop="availability" href="http://schema.org/InStock" />In stock
                        <span itemprop="url"><?php esc_html_e($eventURL,'performancein');?></span>
                        <span itemprop="validFrom"><?php echo $itmPropStartValidFrom; ?></span>

                    </div>
				<?php }
				if ( ! empty( $eventHashtag ) ) { ?>
					<span class="hashtag"><a href="https://twitter.com/search?q=%23<?php echo esc_attr( $eventHashtag ) ?>&amp;src=hash" target="_blank"><?php esc_html_e( sprintf( '#%1$s', $eventHashtag ), 'performancein' ); ?></a></span>
				<?php }
				?>
			</div>
		</div>
		<div class="articlecont">
			<div class="article_text">
				<header class="socialhead">
					<?php echo do_shortcode( '[addtoany]' ); ?>
				</header>
				<div class="article-body">
					<!--<p class="pi-article-excerpt">
						<strong><?php /*echo wp_kses_post( $eventExcerpt ); */?></strong>
					</p>-->
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
							$eventTitle
						) );
						?>

					</div>
					<a href="<?php echo esc_url( $eventURL ); ?>" target="_blank" rel="external"><?php esc_html_e( 'Visit Event Website →', 'performancein' ); ?></a><br/><br/>
				</div>
			</div>
		</div>
	</article>

</article>
