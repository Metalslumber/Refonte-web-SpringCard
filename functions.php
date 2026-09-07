<?php
/**
 * SpringCard theme bootstrap.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPRINGCARD_VERSION', '0.1.0' );

/**
 * Theme setup: text domain, supports, nav menus.
 */
function springcard_setup() {
	load_theme_textdomain( 'springcard', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' )
	);

	// Full-bleed gamme hero background and feature-row photography.
	add_image_size( 'springcard-hero', 1920, 960, true );
	add_image_size( 'springcard-feature', 1200, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'springcard' ),
		)
	);
}
add_action( 'after_setup_theme', 'springcard_setup' );

/**
 * Preconnect to Google Fonts origins.
 */
function springcard_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'springcard_resource_hints', 10, 2 );

/**
 * Enqueue styles and scripts.
 */
function springcard_enqueue_assets() {
	wp_enqueue_style( 'dashicons' );

	wp_enqueue_style(
		'springcard-fonts',
		'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=IBM+Plex+Mono:wght@500&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'springcard-style', get_stylesheet_uri(), array(), SPRINGCARD_VERSION );

	wp_enqueue_style(
		'springcard-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array( 'springcard-fonts', 'springcard-style' ),
		SPRINGCARD_VERSION
	);

	wp_enqueue_script(
		'springcard-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		SPRINGCARD_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'springcard_enqueue_assets' );

require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/class-nav-walker.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/seed-content.php';
