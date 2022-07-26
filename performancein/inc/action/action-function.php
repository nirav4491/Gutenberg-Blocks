<?php
/**
 * File include all the actions callback functions.
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

		add_image_size( 'performancein-recent-thumbnails', 104, 110, true ); // Sets Recent Posts Thumbnails

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'performancein' ),
				'footer'  => esc_html__( 'Footer', 'performancein' )
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'performancein_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

	}
endif;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function performancein_custom_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'performancein_custom_content_width', 640 );
}

/**
 * Remove Emoji from the page.
 * @since 1.0.0
 */
function performancein_remove_wp_emoji() {

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}

/**
 * Move render blocking JS to the footer.
 * @since 1.0.0
 */
function performancein_move_scripts_to_footer() {
	// Remove default jQuery registration through WordPress.
	wp_dequeue_script( 'jquery' );
	wp_dequeue_script( 'jquery-migrate' );
	wp_dequeue_script( 'wp-embed' );
	wp_deregister_script( 'jquery' );
	wp_deregister_script( 'jquery-migrate' );
	wp_deregister_script( 'wp-embed' );

	wp_enqueue_script( 'jquery', '/wp-includes/js/jquery/jquery.js', '', '', true );
}

/**
 * Enqueue gutenberg custom block script.
 *
 * @since 1.0.0
 */
function performancein_add_block_editor_assets() {
	wp_register_script(
		'performancein-gutenberg-block',
		get_template_directory_uri() . '/blocks/js/block.build.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components', 'jquery-ui-tabs', 'wp-api', ),
		'1.0',
		false
	);

	wp_register_style(
		'performancein-gutenberg-block',
		get_template_directory_uri() . '/blocks/css/editor.css',
		array(),
		'1.0'
	);

	register_block_type(
		'performancein/multipurpose-gutenberg-block',
		array(
			'editor_script' => 'performancein-gutenberg-block',
			'editor_style'  => 'performancein-gutenberg-block',
		)
	);

}

/**
 * Action Function to add SVG support in file uploads.
 *
 * @param $file_types
 *
 * @return array
 * @since 1.0.0
 *
 */
function performancein_add_file_types_to_uploads( $file_types ) {
	$new_filetypes        = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$file_types           = array_merge( $file_types, $new_filetypes );

	return $file_types;
}

/**
 * Register custom api endpoints to fetch all terms
 * @since 1.0.0
 */
function performancein_register_api_endpoints() {
	register_rest_route( 'performancein_api', '/request/all_terms', array(
		'methods'  => 'GET',
		'callback' => 'performancein_get_all_terms',
	) );
	register_rest_route( 'contact_form_api', '/request/v1/contactform/listing', array(
		'methods'  => 'GET',
		'callback' => 'performancein_get_all_contactform',
	) );
	register_rest_route( 'performancein_api', '/request/v1/partner/listing', array(
		'methods'  => 'GET',
		'callback' => 'performancein_get_all_partner_listing',
	) );
}


/**
 * Get all terms according to taxonomy
 * @return WP_REST_Response
 * @since 1.0.0
 */
function performancein_get_all_terms() {
	//get data from the cache
	$response = get_transient( 'performancein-get-all-terms-cache' );

	if ( false === $response ) {

		$return = array();
		//get all terms
		$terms = get_terms();

		//arrange term according to taxonomy
		foreach ( $terms as $term ) {
			$return[ $term->taxonomy ][] = array(
				"term_id" => $term->term_id,
				"name"    => $term->name,
				"slug"    => $term->slug
			);
		}

		//set response into the cache
		set_transient( 'performancein-get-all-terms-cache', $return, 60 * MINUTE_IN_SECONDS + wp_rand( 1, 60 ) );

		return new WP_REST_Response( $return, 200 );

	} else {
		//return cache data
		return new WP_REST_Response( $response, 200 );
	}

}

/**
 * function to return contact form api
 * @return array
 */
function performancein_get_all_contactform() {
	$args = array(
		'post_type'   => 'wpcf7_contact_form',
		'numberposts' => - 1
	);

	$product_data = new WP_Query( $args );
	$optionArray  = array();
	if ( $product_data->have_posts() ) {
		while ( $product_data->have_posts() ) {
			$product_data->the_post();
			foreach ( $product_data->posts as $posts ) {
				$optionArray[] = array( 'name' => $posts->post_title, 'id' => $posts->ID );
			}
			break;
		}
	}

	return $optionArray;
}

/**
 * Get all category for the partner
 * @return WP_REST_Response
 * @since 1.0.0
 */
function performancein_get_all_partner_listing() {
	$postSlug    = 'product';
	$args        = array(
		'object_type' => array( $postSlug )
	);
	$optionArray = array();
	$output      = 'names';
	$operator    = 'and';
	$taxonomies  = get_taxonomies( $args, $output, $operator );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			$terms        = get_term_by( 'slug', 'partner-packages', 'product_cat' );
			$args         = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'product_cat',
						'terms'    => 'partner-packages',
						'field'    => 'slug',
						'operator' => 'IN',
					),
				),
			);
			$product_data = new WP_Query( $args );
			// The Loop
			if ( $product_data->have_posts() ) {
				while ( $product_data->have_posts() ) {
					$product_data->the_post();
					foreach ( $product_data->posts as $posts ) {
						$optionArray[] = array( 'name' => $posts->post_title, 'slug' => $posts->post_name );
					}
					break;
				}
			}
			break;
		}
	}

	return $optionArray;
}

/**
 * Register Option page.
 */
function performancein_register_acf_options_pages() {

	// Check function exists.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// register options page.
	acf_add_options_page( array(
		'page_title' => __( 'PerformanceIN General Settings' ),
		'menu_title' => __( 'PerformanceIN Settings' ),
		'menu_slug'  => 'performancein-general-settings',
		'capability' => 'edit_posts',
		'redirect'   => false,
		'post_id'    => 'option'
	) );
}

/**
 * Class PerformanceIN_Recent_Post_Widget to extent WP_Widget
 */
class PerformanceIN_Recent_Post_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'PerformanceIN_Recent_Post_Widget',
			'description' => 'PerformanceIN Recent Post Widget',
		);
		parent::__construct( 'PerformanceIN_Recent_Post_Widget', 'PerformanceIN Recent Post', $widget_ops );

	}

	public function widget( $args, $instance ) {
		$recent_posts = new WP_Query();
		$recent_posts->query( 'showposts=5' );
		?>
		<div class="widget_recent_entries">
			<h2 class="widget-title"><?php esc_html_e( 'Recent Posts', 'performancein' ); ?></h2>
			<ul class="recent-posts-list">
				<?php
				while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
					<li>
						<a href="<?php esc_url( the_permalink() ); ?>">
							<div class="thumbnails-img">
								<?php the_post_thumbnail( 'performancein-recent-thumbnails' ); ?>
							</div>
							<div class="details">
								<p><?php esc_html( the_title() ); ?></p>
								<span class="post-date">
			                        <?php echo get_the_date(); ?>
		                        </span>
							</div>
						</a>

					</li>
				<?php endwhile;
				?>
			</ul>
		</div>
		<?php
		wp_reset_postdata();
	}
}

/**
 * Register the widget
 */
function performancein_register_recent_post_widget() {
	register_widget( 'PerformanceIN_Recent_Post_Widget' );
}


/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function performancein_enqueue_custom_admin_style( $hook ) {
	global $typenow;
	wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/assets/css/font-awesome.min.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
	wp_register_style( 'custom_wp_font_css', get_template_directory_uri() . '/assets/css/fonts.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_font_css' );
	/*if('post' === $typenow && 'post.php' === $hook) {
		wp_register_style( 'pi-select2css', get_template_directory_uri().'/assets/css/admin/select2.css', false, '1.0', 'all' );
		wp_register_script( 'pi-select2', get_template_directory_uri().'/assets/js/admin/select2.js', array( 'jquery' ), '1.0', true );
	}*/

}

/**
 * function to return postfilter rest api routs
 */
function performancein_theme_prefix_post_filter_register_rest_route() {
	$register_routes = array(
		'/posttypes/'  => array( 'GET', 'performancein_theme_prefix_post_filter_get_post_type' ),
		'/post_taxs/'  => array( 'GET', 'performancein_theme_prefix_post_filter_get_post_taxts' ),
		'/categories/' => array( 'GET', 'performancein_theme_prefix_post_filter_get_post_categories' ),
	);
	foreach ( $register_routes as $rout => $para ) {
		register_rest_route( 'postfilter_apis', $rout, array( 'methods' => $para[0], 'callback' => $para[1] ) );
	}
}

/**
 * function to return posttype rest api routes
 */
function performancein_theme_prefix_post_filter_get_post_type() {
	$args                = array(
		'public'   => true,
		'_builtin' => false
	);
	$output              = 'objects';
	$operator            = 'and';
	$post_types          = get_post_types( $args, $output, $operator );
	$posttype_with_lable = array(
		array(
			'label' => '--Select Posttype--',
			'value' => ''
		),
		array(
			'label' => 'News',
			'value' => 'post'
		)
	);
	ksort( $post_types, SORT_ASC );
	foreach ( $post_types as $post_type ) {
		$exclude = array( 'product', 'pi_jobs', 'pi_partner_networks', 'pi_resources' );
		if ( true === in_array( $post_type->name, $exclude ) ) {
			continue;
		}

		$posttype_with_lable[] = array(
			'label' => $post_type->label,
			'value' => $post_type->name
		);
	}

	return $posttype_with_lable;
}

/**
 * function to return taxonomies rest api routes
 *
 * @param $request
 *
 * @return array
 */
function performancein_theme_prefix_post_filter_get_post_taxts( $request ) {
	$taxonomy_objects = get_object_taxonomies( $request['post_type'], 'objects' );
	$post_taxs        = array(
		array(
			'label' => '--Select Taxonomy--',
			'value' => ''
		)
	);
	foreach ( $taxonomy_objects as $taxonomy_object ) {
		$post_taxs[] = array(
			'label' => $taxonomy_object->label,
			'value' => $taxonomy_object->name
		);
	}

	return $post_taxs;
}

/**
 * function to return category rest api routes
 *
 * @param $request
 *
 * @return array
 */
function performancein_theme_prefix_post_filter_get_post_categories( $request ) {
	$terms      = get_terms( array(
		'taxonomy'   => $request['tax'],
		'hide_empty' => false,
	) );
	$post_terms = array(
		array(
			'label' => '--Select Category--',
			'value' => ''
		)
	);
	foreach ( $terms as $term ) {
		$post_terms[] = array(
			'label' => $term->name,
			'value' => $term->slug
		);
	}

	return $post_terms;
}

/**
 * Add Job fields in product page admin side.
 */
function performancein_wc_product_job_options_html() {
	global $thepostid;
	$_duration_days = get_post_meta( $thepostid, '_duration_days', true );
	$_credits_limit = get_post_meta( $thepostid, '_credits_limit', true );
	$_is_featured   = get_post_meta( $thepostid, '_is_featured', true );
	woocommerce_wp_text_input(
		array(
			'id'          => '_duration_days',
			'value'       => ! empty( $_duration_days ) ? $_duration_days : '',
			'label'       => __( 'Duration Days', 'performancein' ),
			'data_type'   => 'text',
			'placeholder' => __( 'Enter Duration days' ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_credits_limit',
			'value'       => ! empty( $_credits_limit ) ? $_credits_limit : '',
			'data_type'   => 'price',
			'label'       => __( 'Credits Limit', 'performancein' ),
			'placeholder' => __( 'Enter Credits limit' ),
		)
	);

	woocommerce_wp_checkbox(
		array(
			'id'      => '_is_featured',
			'value'   => $_is_featured,
			'label'   => __( 'Is Featured', 'performancein' ),
			'cbvalue' => 1,
		)
	);
}

/**
 * Saving Job fields data of products metabox.
 *
 * @param int $post_id product id.
 */
function performancein_wc_save_product_job_options_fields( $post_id ) {

	$_duration_days = filter_input( INPUT_POST, '_duration_days', FILTER_SANITIZE_NUMBER_INT );
	$_credits_limit = filter_input( INPUT_POST, '_credits_limit', FILTER_SANITIZE_NUMBER_INT );
	$_is_featured   = filter_input( INPUT_POST, '_is_featured', FILTER_VALIDATE_BOOLEAN );

	// save the _duration_days field data
	if ( isset( $_duration_days ) ) {
		update_post_meta( $post_id, '_duration_days', $_duration_days );
	} else {
		delete_post_meta( $post_id, '_duration_days' );
	}

	// save the _credits_limit field data
	if ( isset( $_credits_limit ) ) {
		update_post_meta( $post_id, '_credits_limit', $_credits_limit );
	} else {
		delete_post_meta( $post_id, '_credits_limit' );
	}
	// save the _is_featured field data
	update_post_meta( $post_id, '_is_featured', $_is_featured );

}

/**
 * Redirect to specific page.
 */
function performancein_redirect_to_specific_page() {
	if ( is_page( 'password-reset' ) ) {
		$getEmail = filter_input( INPUT_GET, 'email', FILTER_SANITIZE_STRING );
		$code     = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING );
		if ( empty( $code ) || empty( $getEmail ) ) {
			$redirect_url = site_url( 'account/login/' );
			wp_redirect( $redirect_url );
			exit;
		}
	}
	if ( ( is_page( 'account/' ) || is_page( 'account/details' ) || is_page( 'account/details' ) ) ) {
		$referer = filter_input( INPUT_GET, 'referer', FILTER_SANITIZE_STRING );
		if ( ! is_user_logged_in() ) {
			$redirect_url = site_url( 'account/login/' );
			if ( isset( $referer ) && ! empty( $referer ) ) {
				$redirect_url = add_query_arg( 'referer', rawurlencode( $referer ), $redirect_url );
			}
			wp_redirect( $redirect_url );
			exit;
		} else {
			if ( ! is_page( 'account/details' ) ) {
				$redirect_url = site_url( 'account/details/' );
				if ( isset( $referer ) && ! empty( $referer ) ) {
					$redirect_url = add_query_arg( 'referer', rawurlencode( $referer ), $redirect_url );
				}
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}
	if ( is_page( 'account/register/' ) && is_user_logged_in() ) {
		wp_redirect( site_url( 'account/details/' ) );
		exit;
	}

	if ( is_page( 'job-edit/' ) && ! is_user_logged_in() ) {
		$redirect_url = site_url( 'account/login/' );
		wp_redirect( $redirect_url );
		exit;
	}
	if ( is_page( 'account/google-remove/' ) ) {

		if ( is_user_logged_in() ) {
			// Sanitize user input.
			$nonce = filter_input( INPUT_GET, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			// Verify nonce.
			if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'remove_google_link_nonce' ) ) {
				$remove    = filter_input( INPUT_GET, 'remove', FILTER_VALIDATE_BOOLEAN );
				$remove_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
				if ( true === $remove && 'google' === $remove_id ) {
					update_user_meta( get_current_user_id(), 'is_link_with_google', false );
					$redirect_url = site_url( 'account/details/' );
					wp_redirect( $redirect_url, 301 );
					exit;
				} else {
					$request_url = filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL );
					wp_redirect( $request_url, 301 );
					exit;
				}
			}
		} else {
			$redirect_url = site_url( 'account/login/' );
			wp_redirect( $redirect_url );
			exit;
		}
	}

	if ( is_page( 'account/login/' ) && is_user_logged_in() ) {
		$redirect_url = site_url( 'account/details/' );
		wp_redirect( $redirect_url, 301 );
		exit;
	}

	if ( is_page( 'profile-hub/edit/' ) ) {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( true !== pi_is_partner_account( $user ) ) {
				$redirect_url = site_url( '/profile-hub/choose-package/' );
				wp_redirect( $redirect_url, 301 );
				exit;
			}
		} else {
			$redirect_url = site_url( 'account/login/' );
			wp_redirect( $redirect_url, 301 );
			exit;
		}
	}

	if ( is_page( 'account/complete-profile/' ) ) {
		$g_code = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( isset( $g_code ) ) {
			try {
				global $wp;
				$pi_google   = new PI_SignIn_With_Google();
				$google_date = $pi_google->getAccessToken( GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_CLIENT_REDIRECT_URL, $g_code );
				$user_info   = $pi_google->getUserInfo( $google_date['access_token'] );
				if ( isset( $user_info ) && ! empty( $user_info ) ) {
					$user = get_user_by( 'email', $user_info['email'] );
					if ( $user && ! is_wp_error( $user ) ) {
						wp_set_auth_cookie( $user->ID );
						$is_link_with_google = get_the_author_meta( 'is_link_with_google', $user->ID );
						if ( empty( $is_link_with_google ) && true !== (bool) $is_link_with_google ) {
							update_user_meta( $user->ID, 'is_link_with_google', true );
						}
						$redirect_url = site_url( 'account/details/' );
						wp_redirect( $redirect_url, 301 );
						exit();
					} else {
						$redirect_url = pi_social_user_registration( $user_info );
						wp_redirect( $redirect_url, 301 );
						exit();
					}
				} else {
					wp_redirect( home_url(), 301 );
					exit;
				}

			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit();
			}
		}
		$performancein_cookie = filter_input( INPUT_COOKIE, 'performancein_cookie', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $performancein_cookie ) ) {
			$redirect_url = site_url( 'account/login/' );
			wp_redirect( $redirect_url );
			exit;
		}

	}
	if ( is_page( 'account/confirm/' ) ) {
		$account   = filter_input( INPUT_GET, 'account', FILTER_SANITIZE_NUMBER_INT );
		$user_code = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( isset( $account ) && isset( $user_code ) ) {
			$data = json_decode( base64_decode( $user_code ), true );
			if ( (int) $data['id'] === (int) $account ) {
				$activation_code = get_user_meta( $data['id'], 'activation_code', true );
				// verify whether the code given is the same as ours
				if ( $data['code'] === $activation_code ) {
					update_user_meta( $data['id'], 'is_confirm', true );
					delete_user_meta( $data['id'], 'activation_code' );
					$user = get_userdata( $data['id'] );
					wp_set_current_user( $data['id'], $user->user_login );
					wp_set_auth_cookie( $data['id'] );
					setcookie( "performancein_cookie", "", time(), '/', $_SERVER['HTTP_HOST'] );
					$performancein_order  = get_user_meta( $account, 'performancein_user_order', true );
					$performancein_order  = json_decode( $performancein_order, true );
					$redirect_to_checkout = false;
					foreach ( $performancein_order as $product_id => $quantity ) {
						WC()->cart->add_to_cart( $product_id, $quantity );
						$redirect_to_checkout = true;
					}
					if ( true === $redirect_to_checkout ) {
						$result['url'] = wc_get_checkout_url();
						wp_redirect( wc_get_checkout_url() );
						exit;
					} else {
						wp_redirect( site_url( 'account/details/' ) );
						exit;
					}

				} else {
					wp_redirect( site_url(), 301 );
					exit;
				}
			} else {
				wp_redirect( site_url(), 301 );
				exit;
			}
		} else {
			wp_redirect( site_url(), 301 );
			exit;
		}
	}
	if ( is_page( 'account/register/check-inbox/' ) ) {
		if ( ! is_user_logged_in() ) {
			$action     = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
			$user_email = filter_input( INPUT_GET, 'user-email', FILTER_SANITIZE_EMAIL );
			if ( ( isset( $user_email ) && ! empty( $user_email ) && 'send_user_activation' === $action ) ) {
				$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
				if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'send_user_activation_nonce' ) ) {
					$user        = get_user_by( 'email', $user_email );
					$request_url = filter_input( INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL );
					if ( ! is_wp_error( $user ) ) {
						pi_wp_new_user_notification( $user->ID );
					} else {
						$request_url = add_query_arg( array( 'msg' => $user->get_error_message(), 'error' => true ), $request_url );
					}
					wp_redirect( $request_url );
					exit;
				} else {
					wp_redirect( add_query_arg( array(
						'msg'   => __( 'Security failed.', 'performancein' ),
						'error' => true
					), site_url( 'account/register/' ) ) );
					exit;
				}
			} else {
				$performancein_cookie = filter_input( INPUT_COOKIE, 'performancein_cookie', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				if ( empty( $performancein_cookie ) ) {
					$redirect_url = site_url( 'account/login/' );
					wp_redirect( $redirect_url );
					exit;
				}
			}
		} else {
			wp_redirect( site_url( 'account/details/' ) );
			exit;
		}
	}
	if ( is_page( 'account/logout/' ) ) {
		$logout = filter_input( INPUT_GET, 'logout', FILTER_VALIDATE_BOOLEAN );
		if ( true === (bool) $logout ) {
			wp_logout();
		}
		wp_redirect( site_url(), 301 );
		exit;
	}
	if ( is_page( 'order/jobs/new/' ) ) {
		$id_encoded = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
		if ( ! is_numeric( base64_decode( $id_encoded ) ) ) {
			wp_safe_redirect( site_url( 'order/jobs/' ) );
			exit;
		} else {
			$job_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
			$nonce  = filter_input( INPUT_GET, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'edit_job_page_nonce' ) ) {
				wp_safe_redirect( site_url( 'order/jobs/' ) );
				exit;
			} else {
				$pi_closing_date = get_field( 'pi_closing_date', $job_id );
				if ( true === pi_is_expired_job( $pi_closing_date ) ) {
					wp_safe_redirect( site_url( 'order/jobs/' ) );
					exit;
				}
			}
		}

	}

	if ( is_page( 'order/jobs-landing/' ) ) {
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( site_url( 'order/jobs/' ) );
			exit;
		} else {
			$user_id        = get_current_user_id();
			$credit_package = pi_get_credit_package( $user_id );
			$credit_package = isset( $credit_package ) ? json_decode( $credit_package, true ) : array();
			if ( ! array_filter( $credit_package ) ) {
				wp_safe_redirect( site_url( 'order/jobs/' ) );
				exit;
			} else {
				wp_safe_redirect( site_url( 'account/details/jobs/#job_credits' ) );
				exit;
			}
		}
	}

	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_cart() ) {
			if ( 0 === WC()->cart->cart_contents_count ) {
				wp_safe_redirect( site_url( 'order/jobs/' ) );
				exit;
			} else {
				wp_safe_redirect( wc_get_checkout_url() );
				exit;
			}
		}
//		if ( is_product() ) {
//			wp_safe_redirect( site_url( 'order/jobs/' ) );
//			exit;
//		}
	}
}

/**
 * Add image size for recruiter logo.
 */
function performancein_theme_setup() {
	add_image_size( 'recruiter-logo', 218, 97 );
	add_image_size( 'company-logo', 380, 185 );
	add_image_size( 'company-header', 750, 250 );
	add_image_size( 'event-header', 870, 504, true );
	add_image_size( 'article-image', 600, 338, true );
	add_image_size( 'article-image-srcset', 640, 480 );
}

/**
 * Remove the job package transient with product update.
 *
 * @param $post_id
 */
function performancein_remove_transient( $post_id ) {
	if ( 'product' === get_post_type( $post_id ) ) {
		delete_transient( 'performancein_job_package_transient' );
	}
}

/**
 * User profile fields adding.
 *
 * @param $user
 */
function performancein_user_profile_fields_html( $user ) {
	$pi_credit_package = pi_get_credit_package( $user->ID );

	?>
	<h2><?php esc_html_e( 'Profile Status', 'performancein' ); ?></h2>
	<table class="form-table">
		<?php $is_confirm = get_the_author_meta( 'is_confirm', $user->ID ); ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Is confirm', 'performancein' ); ?></th>
			<td>
				<label for="is_confirm">
					<input type="checkbox" name="is_confirm" id="is_confirm" value="true" <?php checked( $is_confirm, true, true ); ?>/>
				</label>
			</td>
		</tr>
		<?php $is_link_with_google = get_the_author_meta( 'is_link_with_google', $user->ID ); ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Is link with google', 'performancein' ); ?></th>
			<td>
				<label for="is_confirm">
					<input type="checkbox" name="is_link_with_google" id="is_link_with_google" value="true" <?php checked( $is_link_with_google, true, true ); ?>/>
				</label>
			</td>
		</tr>
	</table>
	<h2><?php esc_html_e( 'User Credit details', 'performancein' ); ?></h2>
	<table class="form-table">
		<tbody>
		<?php
		$args              = array(
			'post_type'      => 'product',
			'posts_per_page' => 10,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array( 'job-package' )
				),
			),
			'orderby'        => 'date',
			'order'          => 'ASC',
		);
		$job_package_query = new WP_Query( $args );

		foreach ( $job_package_query->posts as $pi_product ) {
			$credit = pi_get_credit( $pi_credit_package, $pi_product->ID );
			?>
			<tr>
				<th class="label">
					<label for="pi_credit_package_<?php echo esc_attr( $pi_product->ID ); ?>"><?php echo esc_html( $pi_product->post_title ); ?></label>
				</th>
				<td>
					<input type="number" class="regular-text" id="pi_credit_package_<?php echo esc_attr( $pi_product->ID ); ?>" name="pi_credit_package[<?php echo esc_attr( $pi_product->ID ); ?>]" value="<?php echo esc_attr( $credit ); ?>" min="0">
				</td>
			</tr>

		<?php } ?>
		</tbody>
	</table>
	<?php
}

/**
 * Update profile info.
 *
 * @param $user_id
 *
 * @return bool
 */
function performancein_update_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$is_confirm = filter_input( INPUT_POST, 'is_confirm', FILTER_VALIDATE_BOOLEAN );
	$is_confirm = isset( $is_confirm ) ? $is_confirm : false;
	update_user_meta( $user_id, 'is_confirm', $is_confirm );

	$filter       = array(
		'pi_credit_package' => array(
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'flags'  => FILTER_REQUIRE_ARRAY
		),
	);
	$_credit_data = filter_input_array( INPUT_POST, $filter );
	update_user_meta( $user_id, 'pi_credit_package', wp_json_encode( $_credit_data['pi_credit_package'] ) );

	return true;
}

/**
 * Add credit cart logo.
 */
function performancein_wc_credit_card_logo() {
	printf( '<img src="%s" alt="%s" class="creditcards">',
		esc_url( get_template_directory_uri() . '/assets/images/credit-card-logos.jpg' ),
		esc_attr__( 'Credit card Logos', 'performancein' )
	);
}

/**
 * Add condition pages link based on product.
 *
 * @param $order_id
 */
function performancein_wc_thankyou( $order_id ) {
	?>
	<h4><?php esc_html_e( 'Next Steps', 'performancein' ) ?></h4>
	<ul>
		<li>
			<a href="<?php echo esc_url( site_url( '/account/details/jobs/#job_credits' ) ); ?>"><?php esc_html_e( 'Post a Job', 'performancein' ); ?></a>
		</li>
		<li><a href="<?php echo esc_url( site_url( '/account/details/' ) ); ?>"><?php esc_html_e( 'View Your Account', 'performancein' ); ?></a></li>
		<li><a href="<?php echo esc_url( site_url( '/jobs/' ) ); ?>"><?php esc_html_e( 'Back to Jobs', 'performancein' ); ?></a></li>
		<li><a href="/"><?php esc_html_e( 'Back to PerformanceIN', 'performancein' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Once the order payment complete then credit adding on user account.
 *
 * @param $order_id
 */
function performancein_wc_payment_complete( $order_id ) {
	$order             = wc_get_order( $order_id );
	$items             = $order->get_items();
	$user_id           = (int) $order->get_user_id();
	$pi_credit_package = pi_get_credit_package( $user_id );
	$credit_package    = pi_add_credit( $pi_credit_package, $items );
	update_user_meta( $user_id, 'pi_credit_package', wp_json_encode( $credit_package ) );
}


/**
 * Register meta box(es).
 */
function performancein_register_meta_boxes() {
	add_meta_box( 'applied-job-meta-box-id', __( 'Applied Jobs', 'performancein' ), 'performancein_applied_job_display_callback', 'pi_jobs' );
	add_meta_box( 'jobs-meta-box-id', __( 'Jobs', 'performancein' ), 'performancein_jobs_display_callback', 'pi_applied_jobs' );
}


/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function performancein_applied_job_display_callback( $post ) {
	$args      = array(
		'post_parent' => $post->ID,
		'post_type'   => 'pi_applied_jobs',
	);
	$the_query = new WP_Query( $args );
	?>
	<table style="border: solid 1px #b0b0b0; width: 100%">
		<tbody>
		<tr style="background: #e0e0e0;">
			<th><?php esc_html_e( 'ID', 'performancein' ); ?></th>
			<th><?php esc_html_e( 'Name', 'performancein' ); ?></th>
			<th><?php esc_html_e( 'Action', 'performancein' ); ?></th>
		</tr>
		<?php
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				?>
				<tr style="text-align: center">
					<td><?php the_ID(); ?></td>
					<td><?php the_title() ?></td>
					<td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>"><?php esc_html_e( 'View', 'performancein' ); ?></a>
					</td>
				</tr>
				<?php
			} // end while
		} // endif
		?>
		</tbody>
	</table>
	<?php
// Reset Post Data
	wp_reset_postdata();
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function performancein_jobs_display_callback( $post ) {
	$post_parent = $post->post_parent;

	?>
	<table style="border: solid 1px #b0b0b0; width: 100%">
		<tbody>
		<tr style="background: #e0e0e0;">
			<th><?php esc_html_e( 'ID', 'performancein' ); ?></th>
			<th><?php esc_html_e( 'Job', 'performancein' ); ?></th>
			<th><?php esc_html_e( 'Action', 'performancein' ); ?></th>
		</tr>
		<tr style="text-align: center">
			<td><?php echo esc_html( $post_parent ); ?></td>
			<td>
				<a href="<?php echo esc_url( get_permalink( $post_parent ) ); ?>"><?php echo esc_html( get_the_title( $post_parent ) ) ?></a>

			</td>
			<td>
				<a href="<?php echo esc_url( get_edit_post_link( $post_parent ) ); ?>"><?php esc_html_e( 'Edit', 'performancein' ); ?></a>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
// Reset Post Data
	wp_reset_postdata();
}

function pi_get_search_partner_result() {
	$result            = array();
	$result['success'] = true;
	$result['html']    = '';
	$nonce             = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_action    = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$current_class     = filter_input( INPUT_POST, 'class', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	// Verify nonce.
	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'pagination_nonce' ) ) {
		$result['msg'] = esc_html__( 'Security check failed.', 'performancein' );
		echo wp_json_encode( $result );
		wp_die();
	}
	$page_number = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_STRING );
	$search_text = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );

	$args = array(
		'posts_per_page' => 48,
		'post_type'      => 'pi_partner_networks',
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'pi_package_selection',
				'value'   => pi_get_available_packages(),
				'compare' => 'IN'
			)
		),
		'orderby' => array('meta_value_num'=>'desc','title'=>'asc'), // Just enter 2 parameters here, seprated by comma
		'order'=>'DESC',
		'paged'          => $page_number
	);

	if ( term_exists( $search_text, 'partner_network_tag' ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'partner_network_tag',
				'terms'    => $search_text,
				'field'    => 'name',
			),
		);
	} else {
		$args['s'] = $search_text;
	}

	$the_query = new WP_Query( $args );
	ob_start();
	if ( $the_query->have_posts() ) {
		$today = date( 'd/m/Y' );
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			get_template_part( 'template-parts/partner-network/content', 'partner-search-single' );
		}
	} else {
		echo '';
	}
	$result['html'] = ob_get_clean();
	ob_start();
	if ( $the_query->max_num_pages !== 0 ) {
		pi_pagination_html( $the_query, $current_action, $current_class );
	}
	$result['pagination_html'] = ob_get_clean();
	$result['success']         = true;
	echo wp_json_encode( $result );
	wp_die();
}

/**
 * Customer role to recruiter role name change.
 */
function performancein_change_role_name() {
	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	//You can replace "administrator" with any other role "editor", "author", "contributor" or "subscriber"...
	$wp_roles->roles['customer']['name'] = 'Recruiter';
	$wp_roles->role_names['customer']    = 'Recruiter';
}

/**
 * WooCommerce my account redirect to account.
 */
function pi_redirect() {
	if ( is_page( 'my-account' ) ) {
		wp_redirect( site_url( '/account/details' ) );
		die();
	}
}

/**
 * Change the default post name to news.
 */
function performancein_change_post_label() {
	global $menu;
	global $submenu;
	$menu[5][0]                 = __( 'Article', 'performancein' );
	$submenu['edit.php'][5][0]  = __( 'Article', 'performancein' );
	$submenu['edit.php'][10][0] = __( 'New Article', 'performancein' );
	$submenu['edit.php'][16][0] = __( 'Article Tags', 'performancein' );
}

/**
 * Change the default post name to news.
 */
function performancein_change_post_object() {
	global $wp_post_types;
	$labels                     = &$wp_post_types['post']->labels;
	$labels->name               = __( 'Article', 'performancein' );
	$labels->singular_name      = __( 'Article', 'performancein' );
	$labels->add_new            = __( 'New Article', 'performancein' );
	$labels->edit_item          = __( 'Edit Article', 'performancein' );
	$labels->add_new_item       = __( 'New Article', 'performancein' );
	$labels->new_item           = __( 'Article', 'performancein' );
	$labels->view_item          = __( 'View Article', 'performancein' );
	$labels->search_items       = __( 'Search Article', 'performancein' );
	$labels->not_found          = __( 'No News found', 'performancein' );
	$labels->not_found_in_trash = __( 'No News found in Trash', 'performancein' );
	$labels->all_items          = __( 'All Articles', 'performancein' );
	$labels->menu_name          = __( 'Article', 'performancein' );
	$labels->name_admin_bar     = __( 'Article', 'performancein' );
}


/**
 * Revert the credit limit in user account if job is not expired.
 *
 * @param $post_id
 * @param $post_after
 * @param $post_before
 */
function performancein_job_revert_credit( $post_id ) {
	$deleted_post = get_post( $post_id );
	if ( $deleted_post->post_type == 'pi_jobs' && is_user_logged_in() ) {
		$pi_closing_date = get_field( 'pi_closing_date', $post_id );
		if ( true !== (bool) pi_is_expired_job( $pi_closing_date ) ) {
			$pi_jobs_packages_id   = get_field( 'pi_jobs_packages', $post_id );
			$_user_id              = $deleted_post->post_author;
			$pi_credit_package     = pi_get_credit_package( $_user_id );
			$user_credit           = pi_get_credit( $pi_credit_package, $pi_jobs_packages_id );
			$update_credit_package = pi_update_credit( $pi_credit_package, ( $user_credit + 1 ), $pi_jobs_packages_id );
			update_post_meta( $post_id, 'job_credit_reverted', true );
			update_field( 'pi_credit_package', $update_credit_package, "user_{$_user_id}" );
		}
	}
}

/**
 * Job credit revert.
 *
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function performancein_job_revert_credit_draft( $new_status, $old_status, $post ) {

	if ( get_post_type( $post ) !== 'pi_jobs' ) {
		return;
	}

	$_user_id            = $post->post_author;
	$pi_closing_date     = get_field( 'pi_closing_date', $post->ID );
	$pi_jobs_packages_id = get_field( 'pi_jobs_packages', $post->ID );
	//If some variety of a draft is being published, dispatch an email
	if ( 'draft' === $old_status && 'publish' === $new_status ) {
		if ( true !== (bool) pi_is_expired_job( $pi_closing_date ) ) {
			$job_moved_trash = get_post_meta( $post->ID, 'job_credit_reverted', true );
			if ( true === (bool) $job_moved_trash ) {
				$pi_credit_package     = pi_get_credit_package( $_user_id );
				$user_credit           = pi_get_credit( $pi_credit_package, $pi_jobs_packages_id );
				$update_credit_package = pi_update_credit( $pi_credit_package, ( $user_credit - 1 ), $pi_jobs_packages_id );
				update_field( 'pi_credit_package', $update_credit_package, "user_{$_user_id}" );
				delete_post_meta( $post->ID, 'job_credit_reverted' );
			}
		}
	} elseif ( 'publish' === $old_status && 'draft' === $new_status ) {
		if ( true !== (bool) pi_is_expired_job( $pi_closing_date ) ) {
			$job_moved_trash = get_post_meta( $post->ID, 'job_credit_reverted', true );
			if ( true !== (bool) $job_moved_trash ) {
				$pi_credit_package     = pi_get_credit_package( $_user_id );
				$user_credit           = pi_get_credit( $pi_credit_package, $pi_jobs_packages_id );
				$update_credit_package = pi_update_credit( $pi_credit_package, ( $user_credit + 1 ), $pi_jobs_packages_id );
				update_post_meta( $post->ID, 'job_credit_reverted', true );
				update_field( 'pi_credit_package', $update_credit_package, "user_{$_user_id}" );
			}
		}

	}
}

/**
 * Decrease credit limit.
 *
 * @param $post_id
 */
function performancein_job_decrease_credit_manage( $post_id ) {
	$deleted_post = get_post( $post_id );
	if ( $deleted_post->post_type == 'pi_jobs' && is_user_logged_in() ) {
		$pi_closing_date = get_field( 'pi_closing_date', $post_id );
		if ( true !== (bool) pi_is_expired_job( $pi_closing_date ) ) {
			$job_moved_trash = get_post_meta( $post_id, 'job_credit_reverted', true );
			if ( true === (bool) $job_moved_trash ) {
				delete_post_meta( $post_id, 'job_credit_reverted' );
				$pi_jobs_packages_id   = get_field( 'pi_jobs_packages', $post_id );
				$_user_id              = $deleted_post->post_author;
				$pi_credit_package     = pi_get_credit_package( $_user_id );
				$user_credit           = pi_get_credit( $pi_credit_package, $pi_jobs_packages_id );
				$update_credit_package = pi_update_credit( $pi_credit_package, ( $user_credit - 1 ), $pi_jobs_packages_id );
				update_field( 'pi_credit_package', $update_credit_package, "user_{$_user_id}" );
			}
		}
	}
}

add_action( 'admin_menu', 'pi_remove_sub_menus' );

function pi_remove_sub_menus() {
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
}

/**
 * function to add custom css and js for select2 in aythor section
 */
function pi_select2jquery_inline() {
	global $pagenow;
	$postType = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
	if ( 'post' === get_post_type() ) {
		if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) { ?>
			<style type="text/css">
				.select2-container {
					margin: 0 2px 0 2px;
				}

				.tablenav.top #doaction, #doaction2, #post-query-submit {
					margin: 0px 4px 0 4px;
				}
			</style>
			<script type='text/javascript'>
				jQuery( document ).ready( function( $ ) {
					jQuery( document ).on( 'click', '.editor-post-author__select', function() {
						jQuery( '.editor-post-author__select' ).select2();
					} );
					jQuery( '.editor-post-author__select' ).live( 'change', function() {
						var data = jQuery( '.editor-post-author__select option:selected' ).val();
						jQuery('#article_author').remove();
						jQuery('.metabox-base-form').append('<input type="hidden" id="article_author" name="article_author" value="'+data+'"/>');
			
								var d = new Date();
								d.setTime(d.getTime() + (365*24*60*60*1000));
								var expires = "expires="+ d.toUTCString();
								document.cookie = 'article_author' + "=" + data + ";" + expires + ";path=/";

										} );
				} );
			</script>
		<?php }
	}

	?>

	<?php
}
