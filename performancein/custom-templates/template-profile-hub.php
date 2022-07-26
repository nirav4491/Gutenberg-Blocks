<?php
/**
 * Template Name: Profile Hub
 *
 * @package WordPress
 * @subpackage Performancein
 * @since Performancein 1.0
 */
get_header();
wp_enqueue_script( 'performancein-custom' );
/*HTML Goes here*/
while ( have_posts() ) :
	the_post();
	the_content();
endwhile; // End of the loop.
get_footer();
