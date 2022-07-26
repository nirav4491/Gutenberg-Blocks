<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package performancein
 */

?>

<article class="" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- <header class="entry-header">
		<?php //the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header> --><!-- .entry-header -->

	<?php performancein_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		$pi_sidebar_enable  = get_field('pi_sidebar_enable','option');
		if(is_page($pi_sidebar_enable)) { ?>
			<div class="grid">
				<section class="content contentWithSidebar">
					<?php the_content(); ?>
				</section>
				<?php get_sidebar(); ?>
			</div>
		<?php } else {
			the_content();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'performancein' ),
				'after'  => '</div>',
			) );
		}
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'performancein' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
