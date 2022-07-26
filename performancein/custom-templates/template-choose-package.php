<?php
/**
 * Template Name: Choose Package.
 */

get_header();
?>
    <main id="main" class="site-main">
        <div class="grid mainContent clearfix nosidebar" role="main">
            <section class="content contentWithSidebar">

				<?php

				echo do_shortcode( '[performancein_partner_banner search=false tag=false H1-tag-design=false]' );

				$args = array(
					'post_type'      => 'product',
					'posts_per_page' => 10,
					'tax_query'      => array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'slug',
							'terms'    => 'partner-packages'
						),
					),
					'orderby'        => 'date',
					'order'          => 'ASC',
				);

				$partner_package_loop = get_transient( 'performancein_partner_package_transient' );
				if ( false === $partner_package_loop ) {
					$partner_package_loop = new WP_Query( $args );
					set_transient( 'performancein_partner_package_transient', $partner_package_loop, 12 * HOUR_IN_SECONDS );
				}

				?>
                <section class="site-width-content">
                    <h2 class="site-width-content-header"><span>Select Your Package</span></h2>
                    <div class="packages-intro">
                        <p>Partner Network is the go-to source for performance marketing services, products, technology
                            and cutting-edge insight. NFREEew and improved for 2018, Partner Network members gain regular
                            access to PerformanceIN's targeted audience among a number of other perks.</p>
                    </div>
                    <hr class="packages-intro-bottom-hr">
                    <div class="profile-packages">
						<?php
						if ( $partner_package_loop->have_posts() ) {
							while ( $partner_package_loop->have_posts() ) : $partner_package_loop->the_post();
								$product_package = wc_get_product( get_the_ID() );
								?>

                                <div class="profile-packages-item">
                                    <h3><?php echo esc_html( get_the_title() ); ?></h3>
                                    <ul class="profile-packages-item-contents">
										<?php
										$allow_html = array(
											'ul' => array(),
											'li' => array(),
										);
										echo wp_kses( $product_package->get_description(), $allow_html ); ?>
                                    </ul>
                                    <div class="profile-packages-item-choose">
										<?php
										if ( $product_package->get_regular_price() !== '0' ) {
											printf( '<h4>%s%s/%s</h4>',
												esc_html( get_woocommerce_currency_symbol() ),
												( $product_package->get_regular_price() !== '0' ) ? esc_html( $product_package->get_regular_price() ) : '',
												esc_html__( 'yr', 'performancein' )
											);
										} else { ?>
                                            <h4><?php esc_html_e( 'FREE', 'performancein' ); ?></h4>
										<?php }
										?>
                                        <form action="/profile-hub/new?package=<?php echo esc_html( $product_package->get_slug() ); ?>" method="POST">
                                            <input class="profile-packages-item-button-free" type="submit" value="<?php esc_attr_e( 'Request Profile Now', 'performancein' ); ?>">
                                        </form>

                                    </div>
                                </div>
							<?php

							endwhile;
						} else {
							echo esc_html__( 'No Job Package found' );
						} ?>
                    </div>
                    <div class="site-width-content membership-terms">
                        <hr class="packages-intro-bottom-hr">
                        <p>* Sponsored editorial as part of Premium and Associate membership packages will be limited to
                            2/1 article per quarter, dependent on date of sign up.<br>
                            † Type of coverage dependent on availability of PerformanceIN content team and event
                            location.<br>
                            ‡ Dependent on bookings and relevance.</p><a href="/membership-terms/"
                                                                         style="color: #1080e0;">Membership Terms &amp;
                            Conditions</a></div>
                </section>
            </section>
        </div>
    </main><!-- #main -->
<?php
get_footer();
