<?php


# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * My awesome closure command
 *
 * <message>
 * : An awesome message to display
 *
 * --append=<message>
 * : An awesome message to append to the original message.
 *
 * @when before_wp_load
 */

if (defined('WP_CLI') && WP_CLI) {

	class ArticlePatchScript
	{

		public function __construct()
		{

			// example constructor called when plugin loads

		}

        function patch_change_excerpt() {
            $start_time = microtime(true);
            global $wpdb;
	        $get_all_article = 'SELECT * FROM `content_article`';
            $content_article_list = $wpdb->get_results($get_all_article, ARRAY_A);
            $success_count = 0;
            $failed_count = 0;
            foreach ($content_article_list as $article) {
//				if ($success_count === 3) {
//					break;
//				}
                $ArticleID = $article['id'];
                $ArticleWPID = $this->pi_get_post_id_by_djongo_pid( $ArticleID );

                $postAttribute = array(
                    'ID'           => $ArticleWPID,
                    'post_excerpt' => $article['intro'],
                );

                $posts_ID = wp_update_post( $postAttribute );

                if(! empty( $posts_ID ) ) {

                    if($posts_ID === $ArticleWPID) {
                        WP_CLI::success( 'The post is successfully updated and post id  = ' . $ArticleWPID );
                    } else {
                        WP_CLI::warning( 'A new post is inserted. Post ID  = ' . $posts_ID . ' existing post ID = '. $ArticleWPID );
                    }

                } else {
                    WP_CLI::warning( 'Post excerpt is not updated = ' . $ArticleWPID );
                }
                $success_count++;
            }

            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            WP_CLI::success($execution_time . " Execution time of script in sec.");
        }

        function patch_change_author() {
            $start_time = microtime(true);
            global $wpdb;
            $get_all_article = 'SELECT * FROM `content_article`';
            $content_article_list = $wpdb->get_results($get_all_article, ARRAY_A);
            $success_count = 0;
            $failed_count = 0;
            foreach ($content_article_list as $article) {
//				if ($success_count === 3) {
//					break;
//				}
                $ArticleID = $article['id'];
                $ArticleWPID = $this->pi_get_post_id_by_djongo_pid( $ArticleID );

                $postAttribute = array(
                    'ID'           => $ArticleWPID,
                    'post_excerpt' => $article->intro,
                );

                $posts_ID = wp_update_post( $postAttribute );

                if(! empty( $posts_ID ) ) {

                    if($posts_ID === $ArticleWPID) {
                        WP_CLI::success( 'The post is successfully updated and post id  = ' . $ArticleWPID );
                    } else {
                        WP_CLI::warning( 'A new post is inserted. Post ID  = ' . $posts_ID . ' existing post ID = '. $ArticleWPID );
                    }

                } else {
                    WP_CLI::warning( 'Post excerpt is not updated = ' . $ArticleWPID );
                }
                $success_count++;
            }

            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            WP_CLI::success($execution_time . " Execution time of script in sec.");
        }

        function pi_get_post_id_by_djongo_pid( $ArticleID ) {
            $params = array(
                'post_type' => 'post',
                'meta_query' => array(
                    array('key' => 'django_id',
                        'value' => $ArticleID,
                        'compare' => '=',
                    )
                ),
                'fields' => 'ids'
            );
            $wc_query = new WP_Query($params);
            $postsIds = $wc_query->posts;
            wp_reset_postdata();
            wp_reset_query();
            return isset($postsIds[0]) ? $postsIds[0] : false;
        }

	}

	WP_CLI::add_command('performance', 'ArticlePatchScript');

}


