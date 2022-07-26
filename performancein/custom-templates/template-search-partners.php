<?php
/**
 * Template Name: Search Partner
 *
 * @package WordPress
 * @subpackage Performancein
 * @since Performancein 1.0
 */
get_header();
wp_enqueue_script( 'performancein-custom' );
wp_reset_postdata();
/*HTML Goes here*/
get_template_part( 'template-parts/partner-network/content', 'partner-search' );
get_template_part( 'template-parts/partner-network/content', 'partner-signup' );
$search_text = filter_input( INPUT_GET, 'q', FILTER_SANITIZE_STRING );
$pagenumber = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
$pagenumber = ( $pagenumber ) ? $pagenumber : 1;
$args_search = array(
	'posts_per_page' => 48,
	'post_type'      => 'pi_partner_networks',
	'post_status'    => 'publish',
	'meta_query'    => array( /* phpcs:ignore */
		array(
			'key'       => 'pi_package_selection',
			'value'     =>  pi_get_available_packages(),
			'compare'   => 'IN'
		)
	),
	'orderby'       => array(
		'meta_value' => 'DESC',
		'title' => 'ASC',
	),
	'order'          =>'DESC',
	'paged'          => $pagenumber,
);
if ( term_exists( $search_text, 'partner_network_tag' ) ) {
	$args_search['tax_query'] = array( /* phpcs:ignore */
		array(
			'taxonomy' => 'partner_network_tag',
			'terms'    => $search_text,
			'field'    => 'name',
		),
	);
} else {
	$args_search['s'] = $search_text;
}
$search_query = new WP_Query($args_search);
?>
	<div class="grid mainContent nosidebar">
		<section class="content contentWithSidebar">
			<section class="site-width-content">
				<div class="profile-hub-list">
					<h2 class="site-width-content-header"><span>Results for <?php echo esc_html( $search_text ); ?></span></h2>
					<div id="page_att_search" data-search="<?php echo esc_attr( $search_text ); ?>"></div>
					<div id="lazyload" class="grid_12 pi_listing tag-detail">
						<?php if ( $search_query->have_posts() ) { ?>
							<?php while ( $search_query->have_posts() ) {
								$search_query->the_post();
								get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
							} ?>
						<?php } else { ?>
							<div class="page_not_found">
								<?php get_template_part( 'template-parts/content', 'no-post' ); ?>
							</div>
						<?php } ?>
					</div>
					<?php pi_load_more_with_pagination( $search_query, $pagenumber, 'pi_partner_search_ajax' );?>
				</div>
			</section>
		</section>
	</div>
<?php
get_footer();
