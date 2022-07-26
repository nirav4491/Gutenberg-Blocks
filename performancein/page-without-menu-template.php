<?php
/**
 * Template Name: Without menu page.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header('withtout-menu');
/*HTML Goes here*/

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content', 'without-menu-page' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

endwhile; // End of the loop.

get_footer('without-menu');
