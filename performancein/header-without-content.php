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
	<!-- <meta name="msapplication-config" content="/wp-content/themes/performancein/assets/images/browserconfig.xml"> -->
	<meta name="theme-color" content="#ffffff">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:300,400,400i,500,700" rel="stylesheet">
	<?php wp_head(); ?>
</head>

<body <?php esc_html( body_class() ); ?>>

