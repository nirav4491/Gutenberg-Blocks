<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package performancein
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">


			<div class="grid mainContent">
				<div class="content contentWithSidebar">
					<h1>Page not found</h1>
					<h2>The abyss only has four corners</h2>
					<p>This was not one of them or the page you were looking for.</p>
					<p><a href="/">Return to the homepage</a></p>
				</div>
				<!-- sidebar Start -->
					<?php get_sidebar(); ?>
				<!-- sidebar End -->
			</div>
 
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
