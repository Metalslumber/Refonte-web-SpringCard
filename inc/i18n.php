<?php
/**
 * Polylang integration: makes the theme's custom post types translatable
 * without requiring a manual toggle in Languages > Settings after activation.
 * No-ops entirely when Polylang isn't active.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register springcard_post_types() with Polylang as translatable.
 *
 * @param array $post_types Post type names already managed by Polylang.
 * @return array
 */
function springcard_pll_get_post_types( $post_types ) {
	foreach ( array( 'gamme', 'produit', 'secteur', 'cas_usage', 'expertise', 'article' ) as $post_type ) {
		$post_types[ $post_type ] = $post_type;
	}
	return $post_types;
}
add_filter( 'pll_get_post_types', 'springcard_pll_get_post_types' );

/**
 * Current Polylang language slug, or '' when Polylang isn't active.
 *
 * Used to explicitly filter the theme's secondary get_posts() queries
 * (listings inside a template, as opposed to the main query) by the
 * language of the page being viewed. Polylang only auto-filters the main
 * query; without this, every listing (secteurs, expertises, articles...)
 * pulls every language at once as soon as translations exist.
 *
 * @return string
 */
function springcard_current_lang() {
	return function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
}

/**
 * Adds a 'lang' arg to a get_posts()/WP_Query args array when Polylang is
 * active, leaving it untouched otherwise.
 *
 * @param array $args get_posts()/WP_Query args.
 * @return array
 */
function springcard_lang_filter( $args ) {
	$lang = springcard_current_lang();
	if ( $lang ) {
		$args['lang'] = $lang;
	}
	return $args;
}
