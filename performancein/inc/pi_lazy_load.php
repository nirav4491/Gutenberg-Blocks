<?php

/**
 * Lazy Load
 */
if ( ! class_exists( 'LazyLoad_Images' ) ) :

	class LazyLoad_Images {

		function __construct() {
			add_action( 'init',array( $this, 'init' ) );
		}

		public function init() {
			add_action( 'wp_head', array( $this, 'setup_filters' ), 9999 ); // we don't really want to modify anything in <head> since it's mostly all metadata, e.g. OG tags
			add_filter( 'wp_kses_allowed_html', array( $this,'allow_lazy_attributes') );
		}

		public function setup_filters() {
			add_filter( 'the_content', array( $this, 'add_image_placeholders' ), 9999 ); // run this later, so other content filters have run, including image_add_wh on WP.com
			add_filter( 'post_thumbnail_html', array( $this, 'add_image_placeholders' ), 11 );
		}

		/**
		 * Ensure that our lazy image attributes are not filtered out of image tags.
		 *
		 * @param array $allowed_tags The allowed tags and their attributes.
		 *
		 * @return array
		 */
		public function allow_lazy_attributes( $allowed_tags ) {
			if ( ! isset( $allowed_tags['img'] ) ) {
				return $allowed_tags;
			}
			// But, if images are allowed, ensure that our attributes are allowed!
			$img_attributes      = array_merge( $allowed_tags['img'], array(
				'data-lazy-src'    => 1,
				'data-lazy-srcset' => 1,
				'data-lazy-sizes'  => 1,
			) );
			$allowed_tags['img'] = $img_attributes;

			return $allowed_tags;
		}

		public function add_image_placeholders( $content ) {

			// Don't lazyload for feeds, previews, mobile
			if ( is_feed() || is_preview() ) {
				return $content;
			}
			// Don't lazyload for amp-wp content
			if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
				return $content;
			}
			// Don't lazy-load if the content has already been run through previously
			if ( false !== strpos( $content, 'data-pisrcset' ) ) {
				return $content;
			}

			// This is a pretty simple regex, but it works
			$i       = 0;
			$content = preg_replace_callback( '#<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si', function ( $matches ) use ( &$i ) {

				if ( is_front_page() ) {
					$imageskipforabovefold = -1;
				} else if ( is_single() ) {
					$imageskipforabovefold = -5;
				} else {
					$imageskipforabovefold = -1;
				}
				if ( $i ++ <= $imageskipforabovefold ) // compares $i to 0 and then increment it
				{
					return $matches[0];
				} // if $i is equal to 0, return the own match

				$old_attributes_str       = $matches[2];
				$old_attributes_kses_hair = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );
				if ( empty( $old_attributes_kses_hair['src'] ) ) {
					return $matches[0];
				}
				$old_attributes = self::flatten_kses_hair_data( $old_attributes_kses_hair );
				$new_attributes = self::process_image_attributes( $old_attributes );
				// If we didn't add lazy attributes, just return the original image source.
				if ( empty( $new_attributes['data-pisrcset'] ) ) {
					return $matches[0];
				}
				$new_attributes_str = self::build_attributes_string( $new_attributes );

				return sprintf( '<img %1$s><noscript>%2$s</noscript>', $new_attributes_str, $matches[0] );
			}, $content );

			return $content;
		}

		static function process_image( $matches ) {
			$old_attributes_str       = $matches[2];
			$old_attributes_kses_hair = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );
			if ( empty( $old_attributes_kses_hair['src'] ) ) {
				return $matches[0];
			}
			$old_attributes = self::flatten_kses_hair_data( $old_attributes_kses_hair );
			$new_attributes = self::process_image_attributes( $old_attributes );
			// If we didn't add lazy attributes, just return the original image source.
			if ( empty( $new_attributes['data-pisrcset'] ) ) {
				return $matches[0];
			}
			$new_attributes_str = self::build_attributes_string( $new_attributes );

			return sprintf( '<img %1$s><noscript>%2$s</noscript>', $new_attributes_str, $matches[0] );
		}

		/**
		 * Given an array of image attributes, updates the `src`, `srcset`, and `sizes` attributes so
		 * that they load lazily.
		 *
		 * @param array $attributes
		 *
		 * @return array The updated image attributes array with lazy load attributes
		 * @since 5.7.0
		 *
		 */
		static function process_image_attributes( $attributes ) {
			if ( empty( $attributes['src'] ) ) {
				return $attributes;
			}
			if ( ! empty( $attributes['class'] ) && self::should_skip_image_with_blacklisted_class( $attributes['class'] ) ) {
				return $attributes;
			}
			/**
			 * Allow plugins and themes to conditionally skip processing an image via its attributes.
			 *
			 * @module-lazy-images
			 *
			 * @param bool  Default to not skip processing the current image.
			 * @param array An array of attributes via wp_kses_hair() for the current image.
			 *
			 * @since 5.9.0
			 *
			 */
			if ( apply_filters( 'jetpack_lazy_images_skip_image_with_atttributes', false, $attributes ) ) {
				return $attributes;
			}
			$old_attributes = $attributes;
			// Set placeholder and lazy-src
			$attributes['src']            = self::get_placeholder_image();
			$attributes['data-pisrcset'] = $old_attributes['src'];
			$attributes['lazyload']       = 'true';
			// Lazyload Handle `srcset`
			if ( ! empty( $attributes['pisrcset'] ) ) {
				$attributes['data-pisrcset'] = $old_attributes['pisrcset'];
				unset( $attributes['pisrcset'] );
			}
			// Lazyload Handle `sizes`
			if ( ! empty( $attributes['sizes'] ) ) {
				$attributes['data-lazy-sizes'] = $old_attributes['sizes'];
				unset( $attributes['sizes'] );
			}

			/**
			 * Allow plugins and themes to override the attributes on the image before the content is updated.
			 *
			 * One potential use of this filter is for themes that set `height:auto` on the `img` tag.
			 * With this filter, the theme could get the width and height attributes from the
			 * $attributes array and then add a style tag that sets those values as well, which could
			 * minimize reflow as images load.
			 *
			 * @module lazy-images
			 *
			 * @param array An array containing the attributes for the image, where the key is the attribute name
			 *              and the value is the attribute value.
			 *
			 * @since  5.6.0
			 *
			 */
			return apply_filters( 'jetpack_lazy_images_new_attributes', $attributes );
		}

		/**
		 * Returns true when a given string of classes contains a class signifying lazy images
		 * should not process the image.
		 *
		 * @param string $classes A string of space-separated classes.
		 *
		 * @return bool
		 * @since 5.9.0
		 *
		 */
		public static function should_skip_image_with_blacklisted_class( $classes ) {
			$blacklisted_classes = array(
				'skip-lazy',
				'abovefoldimage',
				'gazette-featured-content-thumbnail',
				'pt-cv-spinner',
			);
			/**
			 * Allow plugins and themes to tell lazy images to skip an image with a given class.
			 *
			 * @module lazy-images
			 *
			 * @param array An array of strings where each string is a class.
			 *
			 * @since  5.9.0
			 *
			 */
			$blacklisted_classes = apply_filters( 'jetpack_lazy_images_blacklisted_classes', $blacklisted_classes );
			if ( ! is_array( $blacklisted_classes ) || empty( $blacklisted_classes ) ) {
				return false;
			}
			foreach ( $blacklisted_classes as $class ) {
				if ( false !== strpos( $classes, $class ) ) {
					return true;
				}
			}

			return false;
		}

		private static function get_placeholder_image() {
			return apply_filters( 'lazyload_images_placeholder_image', 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' );
		}

		private static function flatten_kses_hair_data( $attributes ) {
			$flattened_attributes = array();
			foreach ( $attributes as $name => $attribute ) {
				$flattened_attributes[ $name ] = $attribute['value'];
			}

			return $flattened_attributes;
		}

		private static function build_attributes_string( $attributes ) {
			$string = array();
			foreach ( $attributes as $name => $value ) {
				if ( '' === $value ) {
					$string[] = sprintf( '%s', $name );
				} else {
					$string[] = sprintf( '%s="%s"', $name, esc_attr( $value ) );
				}
			}

			return implode( ' ', $string );
		}

		static function get_url( $path = '' ) {
			return plugins_url( ltrim( $path, '/' ), __FILE__ );
		}
	}

endif;

$var = new LazyLoad_Images();
