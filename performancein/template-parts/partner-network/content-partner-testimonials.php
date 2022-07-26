<?php

$pi_client_testimonials       = get_field( 'pi_client_testimonials' );
$pi_client_testimonials_title = $pi_client_testimonials['pi_client_testimonials_title'];
$pi_client_testimonial1       = $pi_client_testimonials['pi_client_testimonial1'];
$pi_client_testimonial2       = $pi_client_testimonials['pi_client_testimonial2'];
$pi_client_testimonial3       = $pi_client_testimonials['pi_client_testimonial3'];
$pi_client_testimonials_title =  ! empty($pi_client_testimonials_title) ? $pi_client_testimonials_title : esc_html('Client Testimonials');
if ( $pi_client_testimonials_title ): ?>
	<h2><?php echo esc_html( $pi_client_testimonials_title ); ?></h2>
<?php
endif;
if ( $pi_client_testimonial1 ):
	echo wp_kses_post( $pi_client_testimonial1 );
endif;
if ( $pi_client_testimonial2 ):
	echo wp_kses_post( $pi_client_testimonial2 );
endif;
if ( $pi_client_testimonial3 ):
	echo wp_kses_post( $pi_client_testimonial3 );
endif;
?>
