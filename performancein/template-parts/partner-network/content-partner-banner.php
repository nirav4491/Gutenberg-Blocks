<?php
$piPartnerPackeageID   = get_field( 'pi_package_selection' );
$piPartnerPackeageSlug = get_post_field( 'post_name', $piPartnerPackeageID );
$pi_partner_network_banner_image= '';
$partner_network_bg_image = '';
if ( 'basic-membership' !== $piPartnerPackeageSlug ) {
	$pi_partner_network_banner_image = get_field( 'pi_partner_network_banner_image' );
	if( empty( $pi_partner_network_banner_image ) ) {
		$upload_dir = wp_upload_dir();
		$pi_partner_network_banner_image = $upload_dir['baseurl'].'/2016/08/feat-header-default.gif';
	}
	$partner_network_bg_image        = ( $pi_partner_network_banner_image ) ? 'style="background-image: url(' . esc_url( $pi_partner_network_banner_image ) . ')"' : '';
}
$partner_network_banner_logo     = get_the_post_thumbnail_url(get_the_ID(),'medium');
$image_class                     = ( $partner_network_bg_image ) ? 'mod-image' : '';
if ( $pi_partner_network_banner_image || $partner_network_banner_logo ):
	?>
    <div id="js-ProfileHeader" class="profile-hub-profile-feature-area <?php echo esc_attr( $image_class ); ?>" <?php echo wp_kses_post( $partner_network_bg_image ); ?>>
        <div class="site-width-content mod-flex">
			<?php if ( $partner_network_banner_logo ) : ?>
                <div class="profile-hub-profile-feature-area-logo-wrap">
                    <div class="profile-hub-profile-feature-area-logo">
                        <img src="<?php echo esc_url( $partner_network_banner_logo ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
<?php endif;


