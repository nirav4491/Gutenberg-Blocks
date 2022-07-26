<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package performancein
 */

get_header();
global $wp_query;
$pagenumber = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">


			<div class="grid mainContent">
				<div class="content contentWithSidebar">
					<?php if ( have_posts() ) : ?>

						<header class="page-header">
							<?php
								if( isset( $wp_query->found_posts ) && !empty( $wp_query->found_posts ) ) {
									$result = $wp_query->found_posts;
								}
								if( isset( $result ) && !empty( $result ) ) { ?>
									<p><strong><?php echo $result.' search results'; ?></strong></p>
								<?php }
							?>
						<p><small>Search took <?php timer_stop(1); ?> seconds.</small></p>
						</header><!-- .page-header -->
						<div class="result pi_listing">
							<?php
							/* Start the Loop */
							while ( have_posts() ) :
								the_post();

								/**
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-search.php and that will be used instead.
								 */
								get_template_part( 'template-parts/content', 'search' );

							endwhile; ?>
						</div>

						<?php
						pi_load_more_with_pagination( $wp_query, $pagenumber, 'pi_search_posts_listing', 'listing-item', 'pagination_nonce', $extra_fields = '', false );


					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>
				</div>
				<!-- sidebar Start -->
				<?php get_sidebar(); ?>
				<!-- sidebar End -->
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

