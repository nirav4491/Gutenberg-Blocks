<?php
$pi_partner_page_sub_menu     = get_field( 'pi_partner_page_sub_menu' );
$pi_facebook_link             = get_field( 'pi_facebook_link' );
$pi_twitter_link              = get_field( 'pi_twitter_link' );
$pi_linkedin_link             = get_field( 'pi_linkedin_link' );
$pi_client_testimonials_title = get_field( 'pi_client_testimonials_pi_client_testimonials_title' );
$pi_client_testimonial1       = get_field( 'pi_client_testimonials_pi_client_testimonial1' );
$pi_client_testimonial2       = get_field( 'pi_client_testimonials_pi_client_testimonial2' );
$pi_client_testimonial3       = get_field( 'pi_client_testimonials_pi_client_testimonial3' );
$piPartnerPackeageID          = get_field( 'pi_package_selection' );
$piPartnerPackeageSlug        = get_post_field( 'post_name', $piPartnerPackeageID );
$pi_partner_network_banner_image = get_field('pi_partner_network_banner_image');
$customclassmenu = empty($pi_partner_network_banner_image) ? 'pi_black_strip' : '';
?>
<?php
if ( 'basic-membership' !== $piPartnerPackeageSlug ) {
	if ( ! empty( $pi_client_testimonials_title ) || ! empty( $pi_client_testimonial1 ) || ! empty( $pi_client_testimonial2 ) || ! empty( $pi_client_testimonial3 ) || $pi_facebook_link || $pi_twitter_link || $pi_linkedin_link ) { ?>
		<div class="profileHub-profileNav <?php esc_attr_e($customclassmenu);?>">
			<div class="profileHub-profileNavWrap">
            <?php
            if ( ! empty( $pi_client_testimonial1 ) || ! empty( $pi_client_testimonial2 ) || ! empty( $pi_client_testimonial3 ) ) { ?>
					<ul class="profileHub-profileNavList">
						<li class="profileHub-profileNavItem"><a href="JavaScript:Void(0);" class="current"
						                                         data-tab="js-tab-profile">Profile</a></li>
						    <li class="profileHub-profileNavItem" data-tab="js-tab-testimonials">
                                <a href="JavaScript:Void(0);" data-tab="js-tab-testimonials">Client Testimonials</a>
                            </li>
					</ul>
             <?php } ?>

				<?php if ( $pi_facebook_link || $pi_twitter_link || $pi_linkedin_link ):
					?>

					<ul class="profile-hub-profile-feature-area-social-list">
						<?php
						if ( $pi_facebook_link ): ?>
							<li><a href="<?php echo esc_url( $pi_facebook_link ); ?>"
							       class="profile-hub-profile-feature-area-social-list-facebook">&nbsp;</a></li>
						<?php endif;

						if ( $pi_twitter_link ):
							?>
							<li><a href="<?php echo esc_url( $pi_twitter_link ); ?>"
							       class="profile-hub-profile-feature-area-social-list-twitter"></a></li>
						<?php endif;

						if ( $pi_linkedin_link ): ?>
							<li><a href="<?php echo esc_url( $pi_linkedin_link ); ?>"
							       class="profile-hub-profile-feature-area-social-list-linkedin"></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	<?php }
	?>

<?php } ?>


