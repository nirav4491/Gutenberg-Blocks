<?php
$piPartnerPackeageID   = get_field( 'pi_package_selection' );
$piPartnerPackeageSlug = get_post_field( 'post_name', $piPartnerPackeageID );
$pi_partner_sidebar = get_field( 'pi_partner_sidebar' );
$pi_contact_info       = $pi_partner_sidebar['pi_contact_info'];
$pi_contact_info_title = $pi_contact_info['pi_contact_info_title'];
$pi_website_url        = $pi_contact_info['pi_website_url'];
$pi_email_id           = '';
$pi_telephone_number = '';
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
	$pi_telephone_number   = $pi_contact_info['pi_telephone_number'];
	$pi_email_id           = $pi_contact_info['pi_email_id'];
}
if ( $pi_partner_sidebar ):
	$isdasdad = get_field( 'partner_description', 650 );
	?>
	<div class="profile-hub-profile-info-sidebar">
        <?php if ( ! empty( $pi_website_url ) || ! empty( $pi_email_id ) || ! empty( $pi_telephone_number ) ) { ?>
            <h3><?php echo esc_html( $pi_contact_info_title ); ?></h3>
        <?php } ?>
		<?php if ( ! empty( $pi_website_url ) && isset( $pi_website_url ) ) { ?>
			<?php esc_html_e( 'W : ', 'performancein' ); ?>
			<a href="<?php echo esc_url( $pi_website_url ); ?>"><?php esc_html_e( 'Visit Website', 'performancein' ); ?></a><br/>
		<?php } ?>
		<?php
		if(! empty($pi_email_id) && isset($pi_email_id) ){ ?>
			<?php esc_html_e( 'E : ', 'performancein' ); ?>
			<a href="mailto:<?php echo esc_html( $pi_email_id ); ?>"><?php echo esc_html( $pi_email_id ); ?></a><br/>
		<?php }
		?>
		<?php
		if ( ! empty( $pi_telephone_number ) && isset( $pi_telephone_number ) ) { ?>
			<?php esc_html_e( 'T : ', 'performancein' ); ?><?php echo esc_html( $pi_telephone_number ); ?><br/>
		<?php } ?>
		<?php
		if ( ( ! empty( $pi_founded_year ) && isset( $pi_founded_year ) ) || ( ! empty( $pi_number_of_staff ) && isset( $pi_number_of_staff ) ) ) {
			?>
			<h3><?php echo esc_html( $pi_further_info_title ); ?></h3>
			<?php if ( ! empty( $pi_founded_year ) && isset( $pi_founded_year ) ) { ?>
				<?php esc_html_e( 'Founded : ', 'performancein' ); ?><?php echo esc_html( $pi_founded_year ); ?><br/>
			<?php } ?>
			<?php if ( ! empty( $pi_number_of_staff ) && isset( $pi_number_of_staff ) ) { ?>
				<?php esc_html_e( 'Number of Staff : ', 'performancein' ); ?><?php echo esc_html( $pi_number_of_staff ); ?>
			<?php }
		} ?>

		<?php if ( ! empty( $pi_address1 ) || ! empty( $pi_address2 ) || ! empty( $pi_postcode ) || ! empty( $pi_city ) || ! empty( $pi_country ) || ! empty( $pi_company_map_url ) ) { ?>
			<h3><?php echo esc_html( $pi_head_office_info_title ); ?></h3>
			<?php
			if ( $pi_address1 ):
				echo esc_html( $pi_address1 ) . '<br/>';
			endif;
			if ( $pi_address2 ):
				echo esc_html( $pi_address2 ) . '<br/>';
			endif;
			if ( $pi_city ):
				echo esc_html( $pi_city ) . '<br/>';
			endif;
			if ( $pi_postcode ):
				echo esc_html( $pi_postcode ) . '<br/>';
			endif;
			if ( $pi_country ):
				echo esc_html( $pi_country ) . '<br/>';
			endif;
			if ( $pi_postcode ):?>
                <a href="//maps.google.com/?q=<?php echo $pi_postcode;?>" target="blank"><?php esc_html_e( 'View on Google Maps', 'performancein' ); ?></a><br/>
			<?php endif;
			?>
		<?php } ?>

	</div>
<?php
endif;
?>
