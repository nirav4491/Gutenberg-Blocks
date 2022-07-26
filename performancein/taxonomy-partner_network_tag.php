<?php
/**
 * Taxonomy Template for Partner Network Tag
 */
get_header();
wp_enqueue_script( 'performancein-custom' );
global $wp_query;
$tax_obj     = $wp_query->get_queried_object();
$search_text = $tax_obj->name;
$description = $tax_obj->description;
$pagenumber       = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
$pagenumber       = ( $pagenumber ) ? $pagenumber : 1;

get_template_part( 'template-parts/partner-network/content', 'partner-search' );
get_template_part( 'template-parts/partner-network/content', 'partner-signup' );
?>
	<div class="grid mainContent nosidebar">
	<section class="content contentWithSidebar">
	<section class="site-width-content">
		<div class="profile-hub-list">
			<h1 class="site-width-content-header-parent"><span><?php echo esc_html( $search_text ); ?></span></h1>
			<?php if($description): ?>
				<div class="tagContentWrap"><p><?php echo esc_html( $description ); ?></p></div>
			<?php endif;?>
			<div id="page_att_search" data-search="<?php echo esc_attr( $search_text ); ?>"></div>
			<div id="lazyload" class="grid_12 pi_listing tag-detail">
				<?php
				$args = array(
					'posts_per_page' => 48,
					'post_type'      => 'pi_partner_networks',
					'post_status'    => 'publish',
					'meta_query'     => array( /* phpcs:ignore */
						array(
							'key'     => 'pi_package_selection',
							'value'   => pi_get_available_packages(),
							'compare' => 'IN'
						)
					),
					'orderby' => array('meta_value_num'=>'desc','title'=>'asc'), // Just enter 2 parameters here, seprated by comma
					'order'=>'DESC',
					'paged'          => $pagenumber,
				);
				if ( term_exists( $search_text, 'partner_network_tag' ) ) {
					$args['tax_query'] = array(  /* phpcs:ignore */
						array(
							'taxonomy' => 'partner_network_tag',
							'terms'    => $search_text,
							'field'    => 'name',
						),
					);
				}
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) { ?>
						<?php while ( $the_query->have_posts() ) {
							$the_query->the_post();
							get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
						} ?>
				<?php } else { ?>
					<div class="page_not_found">
						<?php get_template_part( 'template-parts/content', 'no-post' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php pi_load_more_with_pagination( $the_query, $pagenumber, 'pi_partner_search_ajax' );?>
			</div>
</section>
</section>
</div>
<?php
get_footer();
