<?php
get_header( 'without-content' );
$ID                  = get_the_ID();
$LandingPageImageID  = get_field( 'pi_the_image_for_the_landing_page', $ID );
$LandingPageImageURL = wp_get_attachment_image_src( $LandingPageImageID, 'full' );
$LandingPageImageAlt = get_post_meta( $LandingPageImageID, '_wp_attachment_image_alt', true );
$Title               = get_the_title( $ID );
$DownloadDocument    = get_field( 'pi_resource_document', $ID );
$sponserecBY         = get_field( 'pi_resource_sponsors', $ID );
$Date                = get_the_date( '- F y -' );

?>

	<div class="resourceLandingHeadWrap">
		<div class="resourceLandingHead ">
			<?php
			if ( ! empty( $LandingPageImageID ) ) { ?>
				<figure class="resourceLandingImage clearfix ">
					<img itemprop="image" src="<?php echo esc_url( $LandingPageImageURL[0] ); ?>" alt="<?php esc_attr_e( $LandingPageImageAlt ); ?>">
				</figure>
			<?php } ?>
			<header class="resourceHeader">
				<h1 class="resourceLandingPageTitle" itemprop="name"><?php esc_html_e( $Title ); ?></h1>
				<p class="resourcePublishDate"><?php esc_html_e( $Date ); ?></p>
				<?php
				if ( ! empty( $DownloadDocument ) ) { ?>
					<a href="<?php echo esc_url( $DownloadDocument ); ?>" class="CTAButton"><?php esc_html_e( 'Download Now', 'performancein' ); ?></a>
				<?php }
				?>

			</header>
		</div>
	</div>
	<div class="resourceLandingContent">
		<div class="resourceLandingText">
			<div class="resourceLandingTextBody">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();

					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'performancein' ),
						'after'  => '</div>',
					) );
					?>
					<?php if ( get_edit_post_link() ) : ?>
					<footer class="entry-footer">
						<?php
						edit_post_link(
							sprintf(
								wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Edit <span class="screen-reader-text">%s</span>', 'performancein' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								get_the_title()
							),
							'<span class="edit-link">',
							'</span>'
						);
						?>
					</footer><!-- .entry-footer -->
				<?php endif;
				endwhile;
				?>
			</div>
			<?php
			if ( ! empty( $sponserecBY ) ) { ?>
				<hr>
				<p><?php esc_html_e('SPONSORED BY','performancein'); ?></p>
				<?php
				if ( is_array( $sponserecBY ) ) {
					foreach ( $sponserecBY as $sponsered ) {
						$SponceredLink = $sponsered['pi_resource_sponsored_name'];
						$SponceredName = $sponsered['pi_resource_sponsored_url'];
						$SponceredImage = $sponsered['pi_resource_sponsored_image']; ?>
						<a href="<?php echo esc_url($SponceredLink); ?>" target="_blank"><img src="<?php echo esc_url($SponceredImage); ?>" alt="<?php esc_html_e($SponceredName,'performancein');?>"></a>
					<?php }
				}
			}
			?>

		</div>
	</div>
<?php
