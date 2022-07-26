<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package performancein
 */
$copyrightContent = get_theme_mod( 'pi_footer_display', '© 2020 PerformanceIN – All rights reserved unless stated.' );
$pi_year = date('Y');
$copyrightContent = str_replace('[year]',$pi_year,$copyrightContent);
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer monetise-footer">
		<div class="site-info">
			 <span><?php esc_html_e($copyrightContent,'performancein');?></span>
			 <a href="<?php echo esc_url('/terms/'); ?>"><?php echo esc_html_e('Terms &amp; Conditions','performancein')?></a><a href="<?php echo esc_url('/membership-terms/'); ?>"><?php echo esc_html_e('Membership Terms &amp; Conditions','performancein')?></a><a href="<?php echo esc_url('/privacy/'); ?>"><?php echo esc_html_e('Privacy Policy','performancein')?></a>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
