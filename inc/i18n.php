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
	foreach ( array( 'gamme', 'produit', 'secteur', 'cas_usage', 'expertise', 'article', 'article_technique' ) as $post_type ) {
		$post_types[ $post_type ] = $post_type;
	}
	return $post_types;
}
add_filter( 'pll_get_post_types', 'springcard_pll_get_post_types' );
