<?php
/**
 * Custom post types: gamme, produit, secteur, cas_usage, expertise, article, article_technique.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a standard WP labels array from a singular/plural pair.
 */
function springcard_cpt_labels( $singular, $plural ) {
	return array(
		'name'                  => $plural,
		'singular_name'         => $singular,
		/* translators: %s: post type plural label. */
		'add_new'               => __( 'Ajouter', 'springcard' ),
		/* translators: %s: post type singular label. */
		'add_new_item'          => sprintf( __( 'Ajouter %s', 'springcard' ), $singular ),
		/* translators: %s: post type singular label. */
		'edit_item'             => sprintf( __( 'Modifier %s', 'springcard' ), $singular ),
		/* translators: %s: post type singular label. */
		'new_item'              => sprintf( __( 'Nouveau %s', 'springcard' ), $singular ),
		/* translators: %s: post type singular label. */
		'view_item'             => sprintf( __( 'Voir %s', 'springcard' ), $singular ),
		/* translators: %s: post type plural label. */
		'search_items'          => sprintf( __( 'Rechercher %s', 'springcard' ), $plural ),
		'not_found'             => __( 'Aucun résultat', 'springcard' ),
		'not_found_in_trash'    => __( 'Aucun résultat dans la corbeille', 'springcard' ),
		/* translators: %s: post type plural label. */
		'all_items'             => sprintf( __( 'Tous les %s', 'springcard' ), strtolower( $plural ) ),
		'menu_name'             => $plural,
	);
}

/**
 * Register all SpringCard custom post types.
 */
function springcard_register_post_types() {

	register_post_type(
		'gamme',
		array(
			'labels'        => springcard_cpt_labels( __( 'Gamme', 'springcard' ), __( 'Gammes', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-category',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'produits', 'with_front' => false ),
			'menu_position' => 20,
		)
	);

	register_post_type(
		'produit',
		array(
			'labels'        => springcard_cpt_labels( __( 'Produit', 'springcard' ), __( 'Produits', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-admin-generic',
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'produit', 'with_front' => false ),
			'menu_position' => 21,
		)
	);

	register_post_type(
		'secteur',
		array(
			'labels'        => springcard_cpt_labels( __( 'Secteur', 'springcard' ), __( 'Secteurs', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-building',
			'supports'      => array( 'title', 'editor' ),
			'rewrite'       => array( 'slug' => 'solutions', 'with_front' => false ),
			'menu_position' => 22,
		)
	);

	register_post_type(
		'cas_usage',
		array(
			'labels'        => springcard_cpt_labels( __( "Cas d'usage", 'springcard' ), __( "Cas d'usage", 'springcard' ) ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-portfolio',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'cas-usage', 'with_front' => false ),
			'menu_position' => 23,
		)
	);

	register_post_type(
		'expertise',
		array(
			'labels'        => springcard_cpt_labels( __( 'Expertise', 'springcard' ), __( 'Expertises', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-lightbulb',
			'supports'      => array( 'title', 'editor' ),
			'rewrite'       => array( 'slug' => 'expertise', 'with_front' => false ),
			'menu_position' => 24,
		)
	);

	register_post_type(
		'article',
		array(
			'labels'        => springcard_cpt_labels( __( 'Article', 'springcard' ), __( 'Blog', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-admin-post',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'blog', 'with_front' => false ),
			'menu_position' => 25,
		)
	);

	register_post_type(
		'article_technique',
		array(
			'labels'        => springcard_cpt_labels( __( 'Article technique', 'springcard' ), __( 'Blog technique', 'springcard' ) ),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-editor-code',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'blog-technique', 'with_front' => false ),
			'menu_position' => 26,
		)
	);

	register_post_type(
		'logo_client',
		array(
			'labels'        => springcard_cpt_labels( __( 'Logo client', 'springcard' ), __( 'Logos clients', 'springcard' ) ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'exclude_from_search' => true,
			'menu_icon'     => 'dashicons-format-image',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'menu_position' => 27,
		)
	);
}
add_action( 'init', 'springcard_register_post_types' );
