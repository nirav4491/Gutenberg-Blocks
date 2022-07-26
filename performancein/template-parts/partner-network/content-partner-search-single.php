<?php
$partner_id    = get_the_ID();
$title_partner         = get_the_title(); /* phpcs:ignore */
$parmalink     = get_the_permalink();
$partner_image = get_the_post_thumbnail_url();
$pi_partner_sidebar = get_field( 'pi_partner_sidebar' );
$pi_further_info    = $pi_partner_sidebar['pi_further_info'];
$pi_founded_year    = $pi_further_info['pi_founded_year'];
$pi_number_of_staff = $pi_further_info['pi_number_of_staff'];
$pi_head_office_info = $pi_partner_sidebar['pi_head_office_info'];
$pi_city             = $pi_head_office_info['pi_city'];
$short_content       = pi_partner_get_excerpt(140);
$pakage = get_post_meta( $partner_id, 'pi_package_selection', true );
$pakage = get_post_field( 'post_name', $pakage );

if ( 'basic-membership' === $pakage ) {
	$class_partner = 'mod-4-max basic';
} elseif ( 'associate-membership' === $pakage ) {
	$class_partner = 'mod-3-max profile-package-associate';
} elseif ( 'premium-membership' === $pakage ) {
	$class_partner = 'mod-3-max profile-package-partner';
} else {
	$class_partner = 'mod-3-max profile-package-partner';
}
?>
<article class="profile-hub-list-company <?php echo esc_attr( $class_partner ); ?>">
    <a href="<?php echo esc_url( $parmalink ); ?>">
		<?php if ( $partner_image ): ?>
			<span class="profile-hub-list-company-image-link">
				<img src="<?php echo esc_url( $partner_image ); ?>" alt="<?php echo esc_html( $title_partner ); ?>"
				     class="responsively-lazy responsively-lazy-loaded"
				     srcset="<?php echo esc_url( $partner_image ); ?>"/>
			</span>
		<?php endif; ?> 
		<div class="profile-hub-list-company-details">
			<?php if ( $title_partner ): ?>
				<h3 class="profile-hub-list-company-details-name mod-premier-name"><?php echo esc_html( $title_partner ); ?></h3>
			<?php endif;
			if ( $pi_city ): ?>
				<p class="profile-hub-list-company-details-location"><span
						data-icon=""></span> <?php echo esc_html( $pi_city ); ?></p>
			<?php endif;
			if ( $pi_founded_year || $pi_number_of_staff ) : ?>
				<p>
					<?php if ( $pi_founded_year ) : ?>
						<span
							class="profile-hub-list-company-details-founded">Founded <?php echo esc_html( $pi_founded_year ); ?></span>
					<?php endif;
					if ( $pi_number_of_staff ) : ?>
						<span
							class="profile-hub-list-company-details-employees"><?php echo esc_html( $pi_number_of_staff ); ?> Employees</span>
					<?php endif; ?>
				</p>
			<?php
			endif;
			if ( $short_content ) :?>
				<p class="profile-hub-list-company-description"><?php echo wp_kses_post( $short_content ); ?></p>
			<?php endif; ?>
		</div>
	</a>
</article>

