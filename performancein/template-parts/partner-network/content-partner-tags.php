<?php

if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( is_object( $current_user ) ) {
		$user_email                    = $current_user->user_email;
		$currentUserID                 = $current_user->ID;
		$partnerProfilePermalink       = get_the_permalink();
		$pi_partner_key_services       = get_field( 'pi_partner_key_services' );
		$pi_partner_key_services_title = $pi_partner_key_services['pi_partner_key_services_title'];
		$pi_partner_tags               = $pi_partner_key_services['pi_partner_tags'];
		if(true === (bool)$pi_partner_tags) {
			$pi_partner_tags               = array_filter( $pi_partner_tags );
		}
		if ( ! empty( $pi_partner_tags ) && isset( $pi_partner_tags ) ) { ?>
			<h2><?php echo esc_html( $pi_partner_key_services_title ); ?></h2>
			<ul class="profile-hub-tags">
				<?php
				foreach ( $pi_partner_tags as $partner_tag ) {
					$termObject = get_term_by( 'id', $partner_tag, 'partner_network_tag' );
					$term_link  = get_term_link( $partner_tag, 'partner_network_tag' );
					$term_link  = ( $term_link ) ? $term_link : 'JavaScript:Void(0);';
					?>
					<li class="profile-hub-tags-item"><a
							href="<?php echo esc_url( $term_link ); ?>"
							class="profile-hub-tags-item-style"><span><?php echo esc_html( $termObject->name ) ?></span></a>
					</li>
				<?php } ?>
			</ul>
			<?php
		}
	}
} else {
	$partnerProfilePermalink       = get_the_permalink();
	$pi_partner_key_services       = get_field( 'pi_partner_key_services' );
	$pi_partner_key_services_title = $pi_partner_key_services['pi_partner_key_services_title'];
	$pi_partner_tags               = $pi_partner_key_services['pi_partner_tags'];
	if(true === (bool)$pi_partner_tags) {
		$pi_partner_tags               = array_filter( $pi_partner_tags );
	}
	if ( ! empty( $pi_partner_tags ) && isset( $pi_partner_tags ) ) { ?>
		<h2><?php echo esc_html( $pi_partner_key_services_title ); ?></h2>
		<ul class="profile-hub-tags">
			<?php
			foreach ( $pi_partner_tags as $partner_tag ) {
				$termObject = get_term_by( 'id', $partner_tag, 'partner_network_tag' );
				$term_link  = get_term_link( $partner_tag, 'partner_network_tag' );
				$term_link  = ( $term_link ) ? $term_link : 'JavaScript:Void(0);';
				?>
				<li class="profile-hub-tags-item"><a
						href="<?php echo esc_url( $term_link ); ?>"
						class="profile-hub-tags-item-style"><span><?php echo esc_html( $termObject->name ) ?></span></a>
				</li>
			<?php } ?>
		</ul>
	<?php }
}
?>
