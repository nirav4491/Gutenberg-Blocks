<?php /* phpcs:ignoreFile */
if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( is_object( $current_user ) ) {
		$user_email              = $current_user->user_email;
		$currentUserID           = $current_user->ID;
		$partnerUserAssignID     = get_field( 'pi_user_selection' );
		$partnerProfilePermalink = get_the_permalink();
		if ( $partnerUserAssignID === $currentUserID ) { ?>
			<section class="profile-hub-plug">
				<?php if ( isset( $user_email ) && ! empty( $user_email ) ) {

					$piPartnerPackeageID   = get_field( 'pi_package_selection' );
					$piPartnerPackeageSlug = get_post_field( 'post_name', $piPartnerPackeageID );
					$pi_partner_sidebar    = get_field( 'pi_partner_sidebar' );
					$pi_contact_info       = $pi_partner_sidebar['pi_contact_info'];
					$pi_contact_info_title = $pi_contact_info['pi_contact_info_title'];
					$pi_website_url        = $pi_contact_info['pi_website_url'];
					$pi_email_id           = '';
					$pi_telephone_number   = '';
					$pi_further_info       = $pi_partner_sidebar['pi_further_info'];
					$pi_further_info_title = $pi_further_info['pi_further_info_title'];
					$pi_founded_year       = $pi_further_info['pi_founded_year'];
					$pi_number_of_staff    = $pi_further_info['pi_number_of_staff'];

					$pi_head_office_info       = $pi_partner_sidebar['pi_head_office_info'];
					$pi_head_office_info_title = $pi_head_office_info['pi_head_office_info_title'];
					$pi_address1               = $pi_head_office_info['pi_address1'];
					$pi_address2               = $pi_head_office_info['pi_address2'];
					$pi_postcode               = $pi_head_office_info['pi_postcode'];
					$pi_city                   = $pi_head_office_info['pi_city'];
					$pi_country                = $pi_head_office_info['pi_country'];
					$pi_company_map_url        = $pi_head_office_info['pi_company_location'];
					if ( 'basic-membership' !== $piPartnerPackeageSlug ) {
						$pi_telephone_number = $pi_contact_info['pi_telephone_number'];
						$pi_email_id         = $pi_contact_info['pi_email_id'];
						if ( ! empty( $pi_website_url ) && ! empty( $pi_founded_year ) && ! empty( $pi_number_of_staff ) && ! empty( $pi_address1 ) && ! empty( $pi_address2 ) && ! empty( $pi_postcode )  && ! empty( $pi_city ) && ! empty( $pi_country ) && ! empty( $pi_company_map_url ) && !empty($pi_telephone_number) && ! empty($pi_email_id)) {
							printf( __( 'Welcome back: %s   -  ', 'performancein' ), esc_html( $current_user->user_email ) ); ?>
							<a href="<?php echo esc_html_e( $partnerProfilePermalink, 'performancein' ); ?>" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'View Your Profile Now', 'performancein' ); ?></a>
					<?php } else {
							printf( __( '%s : Improve your profile -  ', 'performancein' ), esc_html( $current_user->user_email ) ); ?>
							<a href="<?php echo esc_url( '/profile-hub/edit/', 'performancein' ); ?>" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'Update Your Profile', 'performancein' ); ?></a>
						<?php }

					} else {
						if ( ! empty( $pi_website_url ) && ! empty( $pi_founded_year ) && ! empty( $pi_number_of_staff ) && ! empty( $pi_address1 ) && ! empty( $pi_address2 ) && ! empty( $pi_postcode )  && ! empty( $pi_city ) && ! empty( $pi_country ) && ! empty( $pi_company_map_url )) {
							printf( __( 'Welcome back: %s   -  ', 'performancein' ), esc_html( $current_user->user_email ) ); ?>
							<a href="<?php echo esc_html_e( $partnerProfilePermalink, 'performancein' ); ?>" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'View Your Profile Now', 'performancein' ); ?></a>
						<?php } else {
							printf( __( '%s : Improve your profile -  ', 'performancein' ), esc_html( $current_user->user_email ) ); ?>
							<a href="<?php echo esc_url( '/profile-hub/edit/', 'performancein' ); ?>" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'Update Your Profile', 'performancein' ); ?></a>
						<?php }
					}
				} else { ?>
					<?php esc_html_e( 'Not listed here? Request a FREE company profile today', 'performancein' ); ?> &nbsp;
					<a href="/profile-hub/choose-package/" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'Signup Now', 'performancein' ); ?></a>
				<?php } ?>
			</section>
		<?php }
	}
} else { ?>
	<section class="profile-hub-plug">
		<?php esc_html_e( 'Not listed here? Request a FREE company profile today', 'performancein' ); ?> &nbsp;
		<a href="/profile-hub/choose-package/" class="button mod-profile-hub-plug-button"><?php esc_html_e( 'Signup Now', 'performancein' ); ?></a>
	</section>
<?php }
?>
