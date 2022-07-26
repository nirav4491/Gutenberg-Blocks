<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package performancein
 */
// $settingArray          = pi_theme_setting();
// $header_search_visible = $settingArray['header_search_visible'];
// $joinNetworksEnable    = $settingArray['join_patner_network_enable'];
// $facebookAppID         = $settingArray['facebook_app_id'];
// $facebookLink          = $settingArray['facebook_link'];
// $twitterLink           = $settingArray['twitter_link'];
// $linkedinLink          = $settingArray['linkedin_link'];
// $youtubeLink           = $settingArray['youtube_link'];
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/wp-content/themes/performancein/assets/images/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/wp-content/themes/performancein/assets/images/favicon-16x16.png" sizes="16x16">
    <link rel="mask-icon" href="/wp-content/themes/performancein/assets/images/safari-pinned-tab.svg" color="#1080e0">
    <link rel="shortcut icon" href="/wp-content/themes/performancein/assets/images/favicon.ico">
    <link rel="canonical" href="<?php echo get_the_permalink(); ?>"/>
    <!-- <meta name="msapplication-config" content="/wp-content/themes/performancein/assets/images/browserconfig.xml"> -->
    <meta name="theme-color" content="#ffffff">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans:300,400,400i,500,700" rel="stylesheet">
    <!-- Google Tag Manager -->
    <script>(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start':
					new Date().getTime(), event: 'gtm.js',
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-K5LRS4');</script>
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
		window.googletag = window.googletag || {cmd: []};
    </script>
    <script>

		// GPT slots
		var gptAdSlots = [];
		googletag.cmd.push(function() {

			// Define a size mapping object. The first parameter to addSize is
			// a viewport size, while the second is a list of allowed ad sizes.
			var mapping = googletag.sizeMapping().
				addSize([0, 0], []).
				addSize([520, 200], [468, 60]).
				addSize([780, 200], [728, 90]).
				addSize([1020, 200], [970, 90]).build();

			// Define the GPT slot
			gptAdSlots[0] = googletag.defineSlot('/3948140/PI-responsive-1', [468, 60], 'div-gpt-ad-1564676702306-0').
				defineSizeMapping(mapping).
				setCollapseEmptyDiv(true).
				addService(googletag.pubads());

			googletag.defineSlot('/3948140/PI-300x250_1', [300, 250], 'div-gpt-ad-1562237667996-0').
				setCollapseEmptyDiv(true).
				addService(googletag.pubads());
			googletag.defineSlot('/3948140/PI-300x250_2', [300, 250], 'div-gpt-ad-1563886937722-0').
				setCollapseEmptyDiv(true).
				addService(googletag.pubads());

			// Start ad fetching
			googletag.enableServices();
		});


    </script>
	<?php wp_head(); ?>
</head>

<body <?php esc_html( body_class() ); ?>>
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5LRS4"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="page" class="site">
    <!--<a class="skip-link screen-reader-text" href="#content"><?php /*esc_html_e( 'Skip to content', 'performancein' ); */ ?></a>-->
	<?php
	
	?>
    <header id="masthead" class="site-header">
        <div class="container masthead">
			<?php the_custom_logo(); ?>
            <button class="hamburger hamburger--spin navMegaMenu-openButton" type="button" id="js-navMegaMenu-openButton" aria-label="Menu" aria-controls="site-navigation">
                <span class="navMegaMenu-openButton-label"><?php esc_html_e( 'All Sections', 'performancin' ); ?></span>
                <span class="hamburger-box">
						<span class="hamburger-inner"></span>
					</span>
            </button>
			<?php
			if ( ! empty( $header_search_visible ) ) {
				$searchKeyword = filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING );
				?>
                <div class="largesearch">
                    <form action="<?php echo esc_url( site_url() ); ?>/?s=<?php esc_html_e( $searchKeyword, 'performancein' ); ?>" method="get" id="searchfm" class="searchForm">
                        <input type="text" name="s" results="5" placeholder="Search" value="" class="search searchstart" id="searchinput">
                        <button data-icon="" type="button" id="searchbtn">
                            <span class="visuallyhidden">Search</span>
                        </button>
                    </form>
                </div>
			<?php }
			?>
			<?php
			if ( ! empty( $joinNetworksEnable ) ) {
				$joinNetworkTagline    = $settingArray['join_patner_network_tagline'];
				$joinNetworkImageID    = $settingArray['join_patner_network_image'];
				$joinNetworkImageSRC   = wp_get_attachment_image_src( $joinNetworkImageID, 'full' );
				$joinNetworkimageAlt   = get_post_meta( $joinNetworkImageID, '_wp_attachment_image_alt', true );
				$joinNetworkimageTitle = get_the_title( $joinNetworkImageID );
				$joinNetworkLink       = $settingArray['join_patner_network_link']; ?>
                <a href="<?php echo esc_url( $joinNetworkLink ); ?>" id="js-tipoffCTA" class="partnerNetworkLink">
					<?php esc_html_e( $joinNetworkTagline, 'performancein' ); ?>
                    <img src="<?php echo esc_url( $joinNetworkImageSRC[0] ); ?>" alt="<?php esc_attr_e( $joinNetworkimageAlt, 'performancein' ); ?>"></a>
			<?php }
			?>

        </div><!-- Container-->

    </header><!-- #masthead -->

    <!-- Blue Navigation -->
    <nav class="navCategories">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'sticky-menu',
			)
		);
		?>
    </nav>

    <!-- Mega Navigation -->
    <nav id="site-navigation" class="navMegaMenu mod-BannerExists">
		<?php
		$searchKeyword = filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING );
		?>
        <form action="<?php echo esc_url( site_url() ); ?>/?s=<?php esc_html_e( $searchKeyword, 'performancein' ); ?>" method="get" id="megaSearchForm" class="megaSearch">
            <input type="text" name="s" results="5" placeholder="Search" value="" class="search" id="megasearchinput">
            <button data-icon="" type="button" id="megasearchbtn"><span class="visuallyhidden">Search</span></button>
        </form>

        <div class="navMegaMenu-width">
            <div class="navMegaMenu-wrap">
                <div class="navMegaMenu-list-primaryContent">
					<?php
					wp_nav_menu(
						array(
							'container'      => '',
							'theme_location' => 'mega-menu',
						)
					);
					?>
                </div>
                <div class="navMegaMenu-wrap-secondaryContent">

                </div>
				<?php if ( is_user_logged_in() ) {
					$logout_page_link  = str_replace( site_url(), "", $settingArray['logout_page_link'] );
					$account_page_link = str_replace( site_url(), "", $settingArray['my_account_page_link'] );
					?>
                    <div class="navMegaMenu-accountLogin">
                        <a href="<?php echo esc_url( site_url( $account_page_link ) ) ?>"><?php esc_html_e( 'My Account', 'performancein' ); ?></a>
                        <a href="<?php echo esc_url( site_url( $logout_page_link ) ); ?>"><?php esc_html_e( 'Sign Out', 'performancein' ); ?></a>
                    </div>
				<?php } else {
					$login_page_link = str_replace( site_url(), "", $settingArray['login_page_link'] );
					?>
                    <div class="navMegaMenu-accountLogin">
                        <a href="<?php echo esc_url( site_url( $login_page_link ) ) ?>">
							<?php esc_html_e( 'Login', 'performancein' ); ?>
                        </a> <?php esc_html_e( 'to edit your Profile Hub account or job listings', 'performancein' ); ?>
                    </div>
				<?php } ?>

                <ul class="navMegaMenu-socialList">
                    <li><a href="<?php echo esc_url( $facebookLink ); ?>" class="socialList-facebook">&nbsp;</a></li>
                    <li><a href="<?php echo esc_url( $twitterLink ); ?>" class="socialList-twitter">&nbsp;</a></li>
                    <li><a href="<?php echo esc_url( $linkedinLink ); ?>" class="socialList-linkedin">&nbsp;</a></li>
                    <li><a href="<?php echo esc_url( $youtubeLink ); ?>" class="socialList-youtube">&nbsp;</a></li>
                </ul>
            </div>
        </div>

    </nav><!-- #Full-Screen-navigation -->

    <div id="content" class="site-content">
