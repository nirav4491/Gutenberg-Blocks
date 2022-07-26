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
$settingArray = pi_theme_setting();
$footerLogoID = get_theme_mod( 'custom_logo' );
$footerLogo = wp_get_attachment_image_src( $footerLogoID, 'full' );
$logo_image_alt        = get_post_meta( $footerLogoID, '_wp_attachment_image_alt', true );
$facebookLink = $settingArray['facebook_link'];
$twitterLink = $settingArray['twitter_link'];
$linkedinLink = $settingArray['linkedin_link'];
$youtubeLink = $settingArray['youtube_link'];
$copyrightContent = get_theme_mod( 'pi_footer_display', '© 2020 PerformanceIN – All rights reserved unless stated.' );
$pi_year = date('Y');
$copyrightContent = str_replace('[year]',$pi_year,$copyrightContent);
?>



	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<a href="<?php echo esc_url(site_url()); ?>" class="foot_perfin"><img src="<?php echo esc_url($footerLogo[0]); ?>" alt="<?php esc_attr_e($logo_image_alt,'performancein');?>" /></a>
			<ul class="footer-socialList">
				<li>
					<a href="<?php echo esc_url($facebookLink); ?>" class="socialList-facebook">&nbsp;</a>
				</li>
				<li>
					<a href="<?php echo esc_url($twitterLink); ?>" class="socialList-twitter"></a>
				</li>
				<li>
					<a href="<?php echo esc_url($linkedinLink); ?>" class="socialList-linkedin"></a>
				</li>
				<li>
					<a href="<?php echo esc_url($youtubeLink); ?>" class="socialList-youtube"></a>
				</li>
			</ul>
			<div class="footerLinks">
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu',
						)
					);
				?>
			</div>
			<span class="all-rights"><?php esc_html_e($copyrightContent,'performancein');?></span>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

<script>
	let aboveFoldlazyImages = [].slice.call(document.querySelectorAll('img[lazyload=\'true\']'));
	for (let i = 0; i < aboveFoldlazyImages.length; i++) {
		if (piIsInViewport(aboveFoldlazyImages[i])) {
			aboveFoldlazyImages[i].src = aboveFoldlazyImages[i].dataset.pisrcset;
			aboveFoldlazyImages[i].srcset = aboveFoldlazyImages[i].dataset.pisrcset;
		}
	}

	function piIsInViewport(el) {
		let pirect = el.getBoundingClientRect();
		return (pirect.bottom >= 0 && pirect.right >= 0 && pirect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
			pirect.left <= (window.innerWidth || document.documentElement.clientWidth));
	}

	document.addEventListener('DOMContentLoaded', function() {
		let lazyImages = [].slice.call(document.querySelectorAll('img[lazyload=\'true\']'));
		let active = false;
		const lazyLoad = function() {
			if (active === false) {
				active = true;
				setTimeout(function() {
					lazyImages.forEach(function(lazyImage) {
						if ((lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0) &&
							getComputedStyle(lazyImage).display !== 'none') {
							lazyImage.src = lazyImage.dataset.pisrcset;
							//lazyImage.srcset = lazyImage.dataset.pisrcset;
							lazyImages = lazyImages.filter(function(image) { return image !== lazyImage; });
							if (lazyImages.length === 0) {
								document.removeEventListener('scroll', lazyLoad);
								window.removeEventListener('resize', lazyLoad);
								window.removeEventListener('orientationchange', lazyLoad);
							}
						}
					});
					active = false;
				}, 1000);
			}
		};
		document.addEventListener('scroll', lazyLoad);
		window.addEventListener('resize', lazyLoad);
		window.addEventListener('orientationchange', lazyLoad);
	});
	!function(t) {
		'use strict';
		t.loadCSS || (t.loadCSS = function() {});
		var e = loadCSS.relpreload = {};
		if (e.support = function() {
			var e;
			try {e = t.document.createElement('link').relList.supports('preload');}
			catch (t) {e = !1;}
			return function() {return e;};
		}(), e.bindMediaToggle = function(t) {
			function e() {t.media = a;}

			var a = t.media || 'all';
			t.addEventListener ? t.addEventListener('load', e) : t.attachEvent && t.attachEvent('onload', e), setTimeout(
				function() {t.rel = 'stylesheet', t.media = 'only x';}), setTimeout(e, 3e3);
		}, e.poly = function() {
			if ( !e.support()) {
				for (var a = t.document.getElementsByTagName('link'), n = 0; n < a.length; n++) {
					var o = a[n];
					'preload' !== o.rel || 'style' !== o.getAttribute('as') || o.getAttribute('data-loadcss') ||
					(o.setAttribute('data-loadcss', !0), e.bindMediaToggle(o));
				}
			}
		}, !e.support()) {
			e.poly();
			var a = t.setInterval(e.poly, 500);
			t.addEventListener ? t.addEventListener('load', function() {e.poly(), t.clearInterval(a);}) : t.attachEvent &&
				t.attachEvent('onload', function() {e.poly(), t.clearInterval(a);});
		}
		'undefined' != typeof exports ? exports.loadCSS = loadCSS : t.loadCSS = loadCSS;
	}('undefined' != typeof global ? global : this);
</script>
</body>
</html>
