<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package performancein
 */

if ( ! function_exists( 'performancein_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function performancein_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )

		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( '%s', 'post date', 'performancein' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'performancein_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function performancein_posted_by() {
		$authorID= get_the_author_meta( 'ID' );
		$userAvtarImg = get_avatar_url($authorID);
		$userMeta = get_user_meta($authorID,'pi_company_name', true);
		$userInfo = get_the_author();
		if(!empty($userMeta)) {
			$userInfo = $userMeta;
		}
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '%s', 'post author', 'performancein' ),
			'<img src="' . esc_url($userAvtarImg) . '" class="pi-author-image"> <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( $userInfo ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'performancein_posted_by_single_article' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function performancein_posted_by_single_article($partnerID) {
		$authorID= get_the_author_meta( 'ID' );
		$userAvtarImg = get_avatar_url($authorID);
		$userCustomAvtar = get_user_meta($authorID,'author_avtar_image', true);
		$userCustomAvtar  = wp_get_attachment_image_src( $userCustomAvtar, array( 245, 245 )  );
		$userAvtarImg = ! empty($userCustomAvtar) ? $userCustomAvtar[0] : $userAvtarImg;
		$userInfo = get_user_meta($authorID,'pi_user_name', true);
		$userInfo = ! empty($userInfo) ? $userInfo : get_the_author();
		$byline = sprintf(
		/* translators: %s: post author. */
			esc_html_x( '%s', 'post author', 'performancein' ),
			'<img src="' . esc_url($userAvtarImg) . '" class="pi-author-image greyscale"> <a id="author_info" class="author_info url fn n" href="#pi-authorProfile"><span class="author vcard" itemprop="name">' . esc_html( $userInfo ) . '</span></a>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'performancein_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function performancein_entry_footer() {
		// Hide category and tag text for pages.
		/*if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( esc_html__( ', ', 'performancein' ) );
			if ( $categories_list ) {
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'performancein' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'performancein' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'performancein' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}*/

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'performancein' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'performancein' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'performancein_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function performancein_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;
