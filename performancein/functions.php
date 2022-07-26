<?php
/**
 * performancein functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package performancein
 */

if ( ! function_exists( 'performancein_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function performancein_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on performancein, use a find and replace
		 * to change 'performancein' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'performancein', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'sticky-menu' => esc_html__( 'Topnav', 'performancein' ),
		) );

		register_nav_menus( array(
			'mega-menu' => esc_html__( 'Primary', 'performancein' ),
		) );

		register_nav_menus( array(
			'footer-menu' => esc_html__( 'Footermenu', 'performancein' ),
		) );

		register_nav_menus( array(
			'amp-menu' => esc_html__( 'AMP Menu', 'performancein' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'performancein_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'performancein_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function performancein_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'performancein_content_width', 640 );
}

add_action( 'after_setup_theme', 'performancein_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function performancein_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'performancein' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'performancein' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'widgets_init', 'performancein_widgets_init' );

/**
 * Enqueue scripts and styles.
 */


add_filter( 'wp_prepare_themes_for_js', function ( $themes ) {
	$themes['performancein']['screenshot'][0] = get_template_directory_uri() . '/screenshot.png';

	return $themes;
} );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';
/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


/**
 * All the actions.
 */
require get_template_directory() . '/inc/action/action.php';

/**
 * Included all the callback functions of action.
 */
require get_template_directory() . '/inc/action/action-function.php';

/**
 * Custom or dynamic blocks functions
 */
require get_template_directory() . '/inc/action/blocks-functions.php';

/**
 * Custom or dynamic blocks functions
 */
require get_template_directory() . '/inc/action/block-partner-function.php';

/**
 * All the filters.
 */
require get_template_directory() . '/inc/filter.php';

/**
 * Included all the callback functions of filter.
 */
require get_template_directory() . '/inc/filter-function.php';

/**
 * All the ajax functions.
 */
require get_template_directory() . '/inc/ajax.php';

/**
 * Include all shortcodes.
 */
require get_template_directory() . '/inc/shortcode.php';

/**
 * Shortcode and its callback functions.
 */
require get_template_directory() . '/inc/shortcode-function.php';

/**
 * All the shortcode functions.
 */
// require get_template_directory() . '/inc/common-functions.php';

/**
 * Minify HTML.
 */
// require get_template_directory() . '/html-minify.php';

/**
 * All the cli functions.
 */
require get_template_directory() . '/inc/cli/main-cli.php';

/*
 * Lazy load function
 */
require get_template_directory() . '/inc/pi_lazy_load.php';

/**
 * All custom post types and taxonomies functions
 */
require get_template_directory() . '/inc/action/custom-post-types.php';
if ( function_exists( 'gutenberg_ramp_load_gutenberg' ) ) {
	gutenberg_ramp_load_gutenberg();
}


function performancein_scripts() {
	wp_enqueue_style( 'performancein-style', get_stylesheet_uri() );

	//asad
	wp_enqueue_script( "jquery" );

	wp_enqueue_script( 'bxslider-Js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array(), '20151215', true );
	wp_enqueue_script( 'performancein-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_style( 'frontstyle', get_template_directory_uri() . '/assets/css/front-style.css', array(), '1.1', 'all' );
	wp_enqueue_script( 'performancein-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	// wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/assets/css/slick.css', array(), '1.1', 'all' );


	wp_enqueue_script( 'performancein-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	// Register jquery ui for date picker style.
	wp_register_style( 'performancein-select2-style', WP_PLUGIN_URL . '/woocommerce/assets/css/select2.css', array(), '1.1', 'all' );
	wp_register_style( 'performancein-jquery-ui', get_template_directory_uri() . '/assets/css/jquery-ui.css', array(), '1.1', 'all' );

	// Custom js register.
	wp_register_script( 'performancein-custom', get_template_directory_uri() . '/assets/js/perfomanceIn-custom.js', array(
		'jquery',
		'selectWoo'
	), '20151215', true );


	$performancein_custom = array(
		'admin_url'                        => admin_url( 'admin-ajax.php' ),
		'register_checkbox_text'           => __( 'I do not have an account', 'performancein' ),
		'this_field_required_text'         => __( 'This field is required.', 'performancein' ),
		'email_input_validation_text'      => __( 'Please enter valid email address.', 'performancein' ),
		'confirm_password_validation_text' => __( 'Passwords do not match.', 'performancein' ),
		'password_match_input_text'        => __( 'Passwords do not match.', 'performancein' ),
		'logo_validation_text'             => __( 'Please resize your logo within 218x97 pixels.', 'performancein' ),
		'logo_validation_type_text'        => __( 'This type is not valid:', 'performancein' ),
		'select2_categories_text'          => __( 'Select a categories', 'performancein' ),
		'select2_categories_limit_text'    => __( 'You must select 1 or 2 categories!', 'performancein' ),
		'minimum_salary_input_text'        => __( 'The salary must be larger than the 0', 'performancein' ),
		'maximum_salary_input_text'        => __( 'The salary must be larger than the 1', 'performancein' ),
		'camper_salary_input_text'         => __( 'The maximum salary must be larger than the minimum salary', 'performancein' ),
		'valid_phone_number_input_text'    => __( 'Please valid phone number', 'performancein' ),
		'job_min_limit_date'               => 0,
	);

	$id_encoded = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
	if ( isset( $id_encoded ) && ! empty( $id_encoded ) ) {
		$job_id         = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
		$product_id     = base64_decode( $id_encoded );
		$_duration_days = get_post_meta( $product_id, '_duration_days', true );
		if ( isset( $job_id ) && ! empty( $job_id ) ) {
			$created_date_human_day_diff                = pi_get_since_added_days( get_the_date( 'd-m-Y', $job_id ) );
			$performancein_custom['job_min_limit_date'] = $created_date_human_day_diff;
			$performancein_custom['job_max_limit_date'] = $_duration_days - $created_date_human_day_diff;
		} else {
			$performancein_custom['job_max_limit_date'] = $_duration_days;
		}

	}
	wp_localize_script( 'performancein-custom',
		'performanceinCustom',
		$performancein_custom
	);


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'performancein_scripts' );

function performancein_admin_assets() {
	wp_enqueue_style( 'PI-adminstyle', get_template_directory_uri() . '/assets/css/pi-admin-style.css', array(), '1.1', 'all' );
}

add_action( 'admin_enqueue_scripts', 'performancein_admin_assets' );

locate_template( '/inc/google-sign-in/google-with-sign-in.php', true );

$request_host = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL );
if ( 'performancein.md-staging.com' === $request_host ) {
	/* Google App Client Id */
	define( 'GOOGLE_CLIENT_ID', '157375612459-m262jc0d9614v9vprh62gd8s2fssfs68.apps.googleusercontent.com' );
	/* Google App Client Secret */
	define( 'GOOGLE_CLIENT_SECRET', '_75S1eOzBrgE2y22TQz7mggj' );
	/* Google App Redirect Url */
	define( 'GOOGLE_CLIENT_REDIRECT_URL', site_url( '/account/complete-profile/' ) );
} else {
	/* Google App Client Id */
	define( 'GOOGLE_CLIENT_ID', '665514690877-u6cnlofco7i19cfeg7e03vi09ibseeh2.apps.googleusercontent.com' );
	/* Google App Client Secret */
	define( 'GOOGLE_CLIENT_SECRET', '5tRo7psG4txGCFaFEm3nCxIG' );
	/* Google App Redirect Url */
	define( 'GOOGLE_CLIENT_REDIRECT_URL', site_url( '/account/complete-profile/' ) );
}

/* Profile-hub Sorting Code */
add_filter( 'posts_orderby', 'posts_orderby_meta_value_list', 10, 2 );
function posts_orderby_meta_value_list( $orderby, $query ) {
	$key = 'meta_value_list';
	if ( $key === $query->get( 'orderby' ) &&
	     ( $list = $query->get( $key ) ) ) {
		global $wpdb;
		$list = "'" . implode( wp_parse_list( $list ), "', '" ) . "'";

		return "FIELD( $wpdb->postmeta.meta_value, $list )";
	}

	return $orderby;
}

add_filter( 'get_custom_logo', 'pi_change_logo_html' );

/**
 * Function to return HTML of custom logo
 *
 * @param $html
 *
 * @return false|string
 */
function pi_change_logo_html( $html ) {
	ob_start();
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
	$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
	$tagLine        = get_bloginfo( 'description', 'display' );
	?>
	<a href="<?php echo esc_url( site_url() ) ?>" class="perfin fade">
		<img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php esc_attr_e( $logo_image_alt, 'performancein' ); ?>">
		<span class="tagline"><?php esc_html_e( $tagLine, 'performancein' ); ?></span>
	</a>
	<?php
	$html = ob_get_clean();

	return $html;
}

add_action( 'customize_register', 'cd_customizer_settings' );
/**
 * function to return add setting in customizer
 *
 * @param $wp_customize
 */
function cd_customizer_settings( $wp_customize ) {
	$year = date( 'Y' );
	$wp_customize->add_section( 'pi_footer', array(
		'title'    => 'Footer',
		'priority' => 20,
	) );

	$wp_customize->add_setting( 'pi_footer_display', array(
		'default'   => '© 2019 PerformanceIN – All rights reserved unless stated.',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 'pi_footer_display', array(
		'label'    => 'Copyright Content',
		'section'  => 'pi_footer',
		'settings' => 'pi_footer_display',
		'type'     => 'textarea',
	) );
}

add_action( 'save_post_pi_partner_networks', 'save_partner_post', 10, 3 );
function save_partner_post( $ID, $post, $update ) {
	global $wpdb;
	$pi_is_conform_status = filter_input( INPUT_POST, 'pi_partner_hidden_confirm_check', FILTER_VALIDATE_BOOLEAN );
	if ( true === (bool) $pi_is_conform_status ) { // If the user/admin checked anytime the "Partner is Confirm" checkbox checked.
		$partner_user_id = get_field( 'pi_user_selection', $ID );
		if ( isset( $partner_user_id ) && ! empty( $partner_user_id ) ) {
			$partner_userdata = get_userdata( $partner_user_id );
			if ( isset( $partner_userdata ) && ! empty( $partner_userdata ) && isset( $partner_userdata->user_email ) ) {
				$user_email = $partner_userdata->user_email;
				$is_confirm = get_user_meta( $partner_user_id, 'is_confirm' );
				if ( true !== (bool) $is_confirm ) {
					update_user_meta( $partner_user_id, 'is_confirm', 1 );
					$password = wp_generate_password();
					$hash     = wp_hash_password( $password );
					$wpdb->update(
						$wpdb->users,
						array(
							'user_pass'           => $hash,
							'user_activation_key' => '',
						),
						array( 'ID' => $partner_user_id )
					);
					clean_user_cache( $partner_user_id );
					$to      = $user_email;
					$subject = esc_html__( 'Your application to the PerformanceIN Partner Network has been approved', 'performancein' );
					ob_start();
					?>
                    <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                        <title><?php echo __( 'PerformanceIN Profile Hub Confirmation' ); ?></title>
                    </head>

                    <body>
                    <p><?php esc_html_e('Thanks for applying to the PerformanceIN Partner Network. We\'re pleased to let you know that your application has been approved.','performancein');?></p>
                    <p><?php esc_html_e('You can edit your company profile using following the login details:','');?></p>
                    <p><b>User: <?php esc_html_e($user_email,'performancein');?></b></p>
                    <p><b>Password: <?php esc_html_e($password,'performancein');?></b></p>
                    <p><b>URL: <?php esc_html_e(site_url('account/login'),'performancein');?></b></p>
                    </body>
                    </html>
					<?php
					$body = ob_get_clean();
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );
					wp_mail( $to, $subject, $body, $headers );
				}
			}
		}
	}
}

function mvp_add_custom_types( $query ) {

	remove_filter( 'pre_get_posts', 'mvp_add_custom_types' );
	$settingArray         = pi_theme_setting();
	$author_post_per_page = $settingArray['author_post_per_page'];
	$search_post_per_page = $settingArray['search_post_per_page'];
	add_filter( 'pre_get_posts', 'mvp_add_custom_types' );


	if ( ( is_author() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) ) {
		$pagenumber = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
		$pagenumber = ( $pagenumber ) ? $pagenumber : 1;
		$query->set( 'paged', $pagenumber );
		$query->set( 'posts_per_page', $author_post_per_page );

		return $query;
	}
	if ( is_search() ) {
		$pagenumber = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
		$pagenumber = ( $pagenumber ) ? $pagenumber : 1;
		$query->set( 'paged', $pagenumber );
		$query->set( 'posts_per_page', $search_post_per_page );

		return $query;
	}

	return $query;
}

// add_filter( 'pre_get_posts', 'mvp_add_custom_types' );


/*Code for category page slug*/
// Remove category base
add_filter( 'category_link', 'no_category_parents', 1000, 2 );
function no_category_parents( $catlink, $category_id ) {
	$category = get_category( $category_id );
	if ( is_wp_error( $category ) ) {
		return $category;
	}
	$category_nicename = $category->slug;

	$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $category_nicename, 'category' );

	return $catlink;
}

// Add our custom category rewrite rules
add_filter( 'category_rewrite_rules', 'no_category_parents_rewrite_rules' );
function no_category_parents_rewrite_rules( $category_rewrite ) {

	$category_rewrite = array();
	$categories       = get_categories( array( 'hide_empty' => false ) );
	foreach ( $categories as $category ) {
		$category_nicename                                                                        = $category->slug;
		$category_rewrite[ '(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		$category_rewrite[ '(' . $category_nicename . ')/page/?([0-9]{1,})/?$' ]                  = 'index.php?category_name=$matches[1]&paged=$matches[2]';
		$category_rewrite[ '(' . $category_nicename . ')/?$' ]                                    = 'index.php?category_name=$matches[1]';
	}
	// Redirect support from Old Category Base
	global $wp_rewrite;
	$old_base                            = $wp_rewrite->get_category_permastruct();
	$old_base                            = str_replace( '%category%', '(.+)', $old_base );
	$old_base                            = trim( $old_base, '/' );
	$category_rewrite[ $old_base . '$' ] = 'index.php?category_redirect=$matches[1]';

	//print_r($category_rewrite); // For Debugging
	return $category_rewrite;
}

// Add 'category_redirect' query variable
add_filter( 'query_vars', 'no_category_parents_query_vars' );
function no_category_parents_query_vars( $public_query_vars ) {
	$public_query_vars[] = 'category_redirect';

	return $public_query_vars;
}

// Redirect if 'category_redirect' is set
add_filter( 'request', 'no_category_parents_request' );
function no_category_parents_request( $query_vars ) {
	if ( isset( $query_vars['category_redirect'] ) ) {
		$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
		status_header( 301 );
		header( "Location: $catlink" );
		exit();
	}

	return $query_vars;
}

/*Remove thumbnail from default post type*/
add_action( 'admin_init', 'wps_cpt_support' );
function wps_cpt_support() {
	remove_post_type_support( 'post', 'thumbnail' );
	remove_post_type_support( 'pi_events', 'thumbnail' );
	remove_post_type_support( 'pi_resources', 'thumbnail' );
}


/**
 * When category add and update rewrite rule flush.
 *
 * @param int $category_id category id of default post.
 */
function pi_category_flush_rewrite_rules( $category_id ) {
	flush_rewrite_rules( true );
}

add_action( 'edited_category', 'pi_category_flush_rewrite_rules' );
add_action( 'create_category', 'pi_category_flush_rewrite_rules' );

/**
 * Filter to prevent users to login from admin area if they are not confirmed
 *
 * @param WP_User|WP_Error $user WP_User or WP_Error object if a previous
 *                                   callback failed authentication.
 * @param string $password Password to check against the user.
 *
 * @return WP_Error
 */
function pi_user_authenticate_validation( $user, $password ) {
	$allow_login_roles = array( 'administrator', 'editor' );
	if ( ! in_array( $user->roles[0], $allow_login_roles, true ) ) {
		if ( 'author' === $user->roles[0] ) {
			return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Author is not accessed this module', 'performancein' ) );
		} else {
			$is_confirm = get_the_author_meta( 'is_confirm', $user->ID );
			if ( true === (bool) $is_confirm ) {
				return $user;
			} else {
				return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Your account is not confirmed', 'performancein' ) );
			}
		}
	}

	return $user;
}

add_filter( 'wp_authenticate_user', 'pi_user_authenticate_validation', 10, 2 );

/**
 * Enqueue a script in the WordPress admin,
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function pi_enqueue_admin_script( $hook ) {
	wp_enqueue_script( 'pi_admin_script', get_template_directory_uri() . '/assets/js/admin/pi-admin-script.js', array(), false, true );
	wp_localize_script(
		'pi_admin_script', 'performanceinAdminScript', array( 'admin_url' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'admin_enqueue_scripts', 'pi_enqueue_admin_script' );
/**
 * Function to return js in footer
 */
function pi_custom_js() { ?>
	<script>
		jQuery('#js-close-relatedResource').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			jQuery('#js-relatedResource').fadeOut('slow', function() {
				jQuery('#js-relatedResource').css('display', 'none');
			});
		});
		jQuery(document).on('click', '#author_info', function() {
			jQuery('html, body').animate({scrollTop: jQuery('#pi-authorProfile').offset().top - 100}, 1000);
		});
	</script>
<?php
	if ( 'post' === get_post_type() ) { ?>
        <script>
			jQuery(function() {
				jQuery(window).scroll(function() {

					if (jQuery(window).scrollTop() >= jQuery('.pi-relatedContentTitle').offset().top + jQuery('.pi-relatedContentTitle').
						outerHeight() - window.innerHeight) {
						jQuery('#js-relatedResource').css('visibility','visible');
					}
				});
			});
        </script>
        <script>
			// Add a class for Mega Menu spacing if the responsive banner exists
			jQuery(function() {
				if( jQuery("#div-gpt-ad-1564676702306-0").css('display') != 'none') {
					jQuery('#js-navMegaMenu').addClass("mod-BannerExists");
				}
			});
        </script>
    <?php }
    ?>


<?php }

add_action( 'wp_footer', 'pi_custom_js' );


function template_chooser( $template ) {
	global $wp_query;
	$post_type = $wp_query->query_vars["pagename"];
	if ( isset( $_GET['q'] ) && $post_type === 'search' ) {
		return get_template_part( 'custom-templates/template', 'search-partners' );
	}

	return $template;
}

add_filter( 'template_include', 'template_chooser' );


add_action( 'pre_get_posts', function ( $q ) {
	if ( $q->is_search() && ! is_admin() ) {
		$q->set( 'post_type', [ 'post', 'pi_events' ] );
	}
} );


function pi_partner_get_excerpt( $limit, $source = null ) {
	global $post;
	$excerpt = $source == "content" ? get_the_content() : get_the_excerpt();
	if ( empty( $excerpt ) ) {
		return $excerpt;
	}
	$excerpt = preg_replace( " (\[.*?\])", '', $excerpt );
	$excerpt = strip_shortcodes( $excerpt );
	$excerpt = strip_tags( $excerpt );
	$excerpt = substr( $excerpt, 0, $limit );
	$excerpt = substr( $excerpt, 0, strripos( $excerpt, " " ) );
	$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
	$excerpt = $excerpt . '... <a href="' . get_permalink( $post->ID ) . '" class="pi-partner-list-btn">Read more</a>';

	return $excerpt;
}


add_action( "woocommerce_email_after_order_table", "custom_woocommerce_email_after_order_table", 10, 1 );

function custom_woocommerce_email_after_order_table( $order ) {
	$userID =  $order->get_user_id();
	$userMeta  = get_userdata( $userID );
	$userRoles = $userMeta->roles;
	if ( 'account' === $userRoles[0] ) {
		$args = array(
			'post_type'   => 'pi_partner_networks',
			'post_status' => 'publish',
			'meta_key'    => 'pi_user_selection',
			'meta_value'  => $userID,
			'fields'      => 'ids'
		);

		$getPartnerObj = get_posts( $args );
		foreach ( $getPartnerObj as $queryResultID ) {
			$partnerID             = $queryResultID;
			$partnerTitle          = get_the_title( $partnerID );
			$partnerTelephonNumber = get_field( 'pi_partner_sidebar_pi_contact_info_pi_telephone_number', $partnerID );
			$partnerEmaiID         = get_field( 'pi_partner_sidebar_pi_contact_info_pi_email_id', $partnerID );
			$partnerAdderess1      = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_address1', $partnerID );
			$partnerAdderess2      = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_address2', $partnerID );
			$partnerPostalCode     = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_postcode', $partnerID );
			$partnerCity           = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_city', $partnerID );
			$partnerCountry        = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_country', $partnerID );
			$countries = pi_all_countries_array();
			if(array_key_exists($partnerCountry,$countries)) {
				$partnerCountry = $countries[$partnerCountry];
				$partnerCountryCode        = get_field( 'pi_partner_sidebar_pi_head_office_info_pi_country', $partnerID );
				$partnerCountry = sprintf(esc_html__('%1$s (%2$s)','performancein'),$partnerCountry,$partnerCountryCode);
			}
			?>
			<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%;vertical-align: top;margin-bottom: 40px;padding: 0">
				<tbody>
				<tr>
					<td valign="top" width="50%" style="text-align: left;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;border: 0;padding: 0">
						<h2 style="color: #96588a;font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;font-size: 18px;font-weight: bold;line-height: 130%;margin: 0 0 18px;text-align: left"><?php esc_html_e( 'Registered Company Address', 'performancein' ); ?></h2>

						<address class="address" style="padding: 12px;color: #636363;border: 1px solid #e5e5e5">
							<?php if ( ! empty( $partnerTitle ) && isset( $partnerTitle ) ) {
								esc_html_e( $partnerTitle, 'performancein' ); ?>
								<br/>
							<?php }
							if ( ! empty( $partnerAdderess1 ) && isset( $partnerAdderess1 ) ) {
								esc_html_e( $partnerAdderess1, 'performancein' ); ?>
								<br/>
							<?php }
							if ( ! empty( $partnerAdderess2 ) && isset( $partnerAdderess2 ) ) {
								esc_html_e( $partnerAdderess2, 'performancein' ); ?>
								<br/>
							<?php }
							if ( ! empty( $partnerCity ) && isset( $partnerCity ) ) {
								esc_html_e( sprintf('%1$s,',$partnerCity), 'performancein' ); ?>
							<?php }
							if ( ! empty( $partnerPostalCode ) && isset( $partnerPostalCode ) ) {
								esc_html_e( $partnerPostalCode, 'performancein' ); ?>
								<br />
							<?php }
							if ( ! empty( $partnerCountry ) && isset( $partnerCountry ) ) {
								esc_html_e( $partnerCountry, 'performancein' ); ?>
								<br/>
							<?php }
							if ( ! empty( $partnerTelephonNumber ) && isset( $partnerTelephonNumber ) ) {
								esc_html_e( $partnerTelephonNumber, 'performancein' ); ?>
								<br/>
							<?php }
							if ( ! empty( $partnerEmaiID ) && isset( $partnerEmaiID ) ) {
								esc_html_e( $partnerEmaiID, 'performancein' ); ?>
								<br/>
							<?php } ?>

						</address>
					</td>
				</tr>
				</tbody>
			</table>
		<?php }
	}
}

add_action( 'save_post', 'rudr_save_metaboxdata', 10, 1 );
function rudr_save_metaboxdata( $post_id ) {
	//Check it's not an auto save routine
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	//Perform permission checks! For example:
	if ( !current_user_can('edit_post', $post_id) )
		return;

	//Check your nonce!

	//If calling wp_update_post, unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'rudr_save_metaboxdata');


	if(isset($_COOKIE["article_author"]) && !empty($_COOKIE["article_author"])){
		wp_update_post(array('ID' => $post_id, 'post_author' => intval($_COOKIE["article_author"])));
		setcookie("article_author", "", time() - 3600);
		$_COOKIE["article_author"] = '';

	}
	// call wp_update_post update, which calls save_post again. E.g:


	// re-hook this function
	add_action('save_post', 'rudr_save_metaboxdata');

}

function wpse_11826_search_by_title( $search, $wp_query ) {
	if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
		global $wpdb;

		$q = $wp_query->query_vars;
		$n = ! empty( $q['exact'] ) ? '' : '%';

		$search = array();

		foreach ( ( array ) $q['search_terms'] as $term )
			$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

		if ( ! is_user_logged_in() )
			$search[] = "$wpdb->posts.post_password = ''";

		$search = ' AND ' . implode( ' AND ', $search );
	}

	return $search;
}

add_filter( 'posts_search', 'wpse_11826_search_by_title', 10, 2 );


//add_action( 'save_post', 'post_update_category', 10, 1 );
function post_update_category( $post_id ) {
	//Check it's not an auto save routine
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	//Perform permission checks! For example:
	if ( !current_user_can('edit_post', $post_id) )
		return;

	//Check your nonce!

	//If calling wp_update_post, unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'rudr_save_metaboxdata');

    $primary_cat_id = get_field('pi_primary_category',$post_id, true);
	if( isset( $primary_cat_id ) && !empty( $primary_cat_id ) ) {
		$piCategory       = get_queried_object();
		$piParentCategory = $piCategory->parent;
		if ( null === $piParentCategory ) {
			wp_set_post_categories( $post_id, array( $primary_cat_id ), false );
		}
	}
	// re-hook this function
	add_action('save_post', 'post_update_category');
}

// Add the custom columns to the book post type:
add_filter( 'manage_pi_partner_networks_posts_columns', 'set_custom_edit_book_columns' );
function set_custom_edit_book_columns($columns) {
	unset( $columns['taxonomy-cat_partner_networks'] );
	unset( $columns['date'] );
	$columns['pi_users'] = __( 'User', 'performancein' );
	return $columns;
}
// Add the data to the custom columns for the book post type:
add_action( 'manage_pi_partner_networks_posts_custom_column' , 'custom_book_column', 10, 2 );
function custom_book_column( $column, $post_id ) {
	$pi_package_user = get_post_meta( $post_id , 'pi_user_selection' , true );

	switch ( $column ) {
		case 'pi_users' :
			$userdata = get_user_by('id',$pi_package_user);
			echo $userdata->user_email;
			break;
	}
}