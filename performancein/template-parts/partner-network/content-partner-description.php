<?php
$pi_partner_description       = get_field( 'pi_partner_description' );

//$pi_partner_description_title = $pi_partner_description['pi_partner_description_title'];
//$pi_partner_description       = $pi_partner_description['pi_partner_description'];

$pi_partner_description_title = get_field( 'pi_partner_description_pi_partner_description_title', get_the_ID() );
$pi_description = get_field( 'pi_partner_description_pi_partner_description', get_the_ID() );

if ( $pi_description ):
	if ( $pi_partner_description_title ): ?>
        <h2><?php echo esc_html( $pi_partner_description_title ); ?></h2>
	<?php
	endif;
	echo wp_kses_post( $pi_description );
endif; ?>