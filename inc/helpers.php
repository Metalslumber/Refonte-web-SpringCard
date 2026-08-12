<?php
/**
 * Query and formatting helpers built around the "_statut" field.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gammes with statut = actif, ordered by menu_order/title.
 *
 * @return WP_Post[]
 */
function springcard_get_gammes_actives() {
	return get_posts(
		array(
			'post_type'      => 'gamme',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'meta_key'       => '_statut',
			'meta_value'     => 'actif',
		)
	);
}

/**
 * Produits with statut = actif, optionally restricted to one gamme.
 *
 * @param int|null $gamme_id Optional parent gamme post ID.
 * @return WP_Post[]
 */
function springcard_get_produits_actifs( $gamme_id = null ) {
	$args = array(
		'post_type'      => 'produit',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'   => '_statut',
				'value' => 'actif',
			),
		),
	);

	if ( $gamme_id ) {
		$args['meta_query'][] = array(
			'key'   => '_gamme_id',
			'value' => (int) $gamme_id,
		);
	}

	return get_posts( $args );
}

/**
 * All produits belonging to a gamme, regardless of statut (used on the gamme
 * comparison table, which should keep showing archived/upcoming variants too).
 *
 * @param int $gamme_id Parent gamme post ID.
 * @return WP_Post[]
 */
function springcard_get_produits_de_gamme( $gamme_id ) {
	return get_posts(
		array(
			'post_type'      => 'produit',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'meta_key'       => '_gamme_id',
			'meta_value'     => (int) $gamme_id,
		)
	);
}

/**
 * Cas d'usage linked to a given produit or secteur ID.
 *
 * @param string $relation "_produits" or "_secteurs".
 * @param int    $id       Related post ID.
 * @return WP_Post[]
 */
function springcard_get_cas_usage_by( $relation, $id ) {
	if ( ! in_array( $relation, array( '_produits', '_secteurs' ), true ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'cas_usage',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => $relation,
					'value'   => sprintf( ':%d;', (int) $id ),
					'compare' => 'LIKE',
				),
			),
		)
	);
}

/**
 * Human-readable label for a "_statut" value.
 */
function springcard_statut_label( $statut ) {
	$options = springcard_statut_options();
	return isset( $options[ $statut ] ) ? $options[ $statut ] : '';
}

/**
 * Human-readable label for a "_type_antenne" value.
 */
function springcard_antenne_label( $type ) {
	$options = springcard_antenne_options();
	return isset( $options[ $type ] ) ? $options[ $type ] : '';
}

/**
 * Parse the "_specs" textarea ("Libellé : Valeur" per line) into pairs.
 *
 * @param int $post_id Produit post ID.
 * @return array<int, array{label: string, value: string}>
 */
function springcard_get_produit_specs( $post_id ) {
	$raw = get_post_meta( $post_id, '_specs', true );
	if ( ! $raw ) {
		return array();
	}

	$specs = array();
	foreach ( preg_split( '/\R/', $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = explode( ':', $line, 2 );
		$specs[] = array(
			'label' => trim( $parts[0] ),
			'value' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
		);
	}
	return $specs;
}

/**
 * URL of a produit's fiche technique PDF, if set.
 *
 * @param int $post_id Produit post ID.
 * @return string
 */
function springcard_get_fiche_technique_url( $post_id ) {
	$attachment_id = (int) get_post_meta( $post_id, '_fiche_technique_id', true );
	if ( ! $attachment_id ) {
		return '';
	}
	$url = wp_get_attachment_url( $attachment_id );
	return $url ? $url : '';
}

/**
 * URL of an image stored in a single-attachment meta field (e.g. the gamme's
 * "_visuel_fabrication_id"/"_visuel_kit_id"), or empty string if unset.
 *
 * @param int    $post_id  Post ID the meta field belongs to.
 * @param string $meta_key Meta key holding the attachment ID.
 * @param string $size     Registered image size.
 * @return string
 */
function springcard_get_meta_image_url( $post_id, $meta_key, $size = 'large' ) {
	$attachment_id = (int) get_post_meta( $post_id, $meta_key, true );
	if ( ! $attachment_id ) {
		return '';
	}
	$src = wp_get_attachment_image_src( $attachment_id, $size );
	return $src ? $src[0] : '';
}

/**
 * Home page marketing copy, editable in Apparence → Personnaliser, with the
 * originally-written wording kept as fallback default.
 *
 * @return array<string, string>
 */
function springcard_home_text_defaults() {
	return array(
		'springcard_home_hero_title' => __( 'Un module, votre lecteur sur mesure', 'springcard' ),
		'springcard_home_hero_lead'  => __( 'Intégrez M519 dans vos machines. Vous gardez la main sur le design de votre propre lecteur RFID/NFC.', 'springcard' ),
		'springcard_home_stat_1'     => __( "Le module s'intègre directement dans vos équipements, le boîtier et l'antenne restent les vôtres.", 'springcard' ),
		'springcard_home_stat_2'     => __( "Notre bureau d'études vous accompagne de l'idée jusqu'au produit fini.", 'springcard' ),
		'springcard_home_stat_3'     => __( "20 ans d'expérience, fabriqué en France, dans des secteurs qui ne laissent pas de place à l'approximation.", 'springcard' ),
	);
}

/**
 * Read one of the home page text settings, falling back to its default.
 *
 * @param string $key One of the springcard_home_text_defaults() keys.
 * @return string
 */
function springcard_home_text( $key ) {
	$defaults = springcard_home_text_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Footer site info (address) and social network links, editable in
 * Apparence → Personnaliser, with SpringCard's real public info as default.
 *
 * @return array<string, string>
 */
function springcard_footer_defaults() {
	return array(
		'springcard_footer_address'  => __( '2 voie la Cardon, Parc Gutenberg, 91120 Palaiseau, France', 'springcard' ),
		'springcard_footer_facebook' => 'https://www.facebook.com/Springcard/',
		'springcard_footer_twitter'  => 'https://twitter.com/sc_rfid',
		'springcard_footer_linkedin' => 'https://www.linkedin.com/company/springcard/',
		'springcard_footer_youtube'  => 'https://www.youtube.com/channel/UChkfP_eFhSFndcPYombOLmg',
	);
}

/**
 * Read one of the footer settings, falling back to its default.
 *
 * @param string $key One of the springcard_footer_defaults() keys.
 * @return string
 */
function springcard_footer_text( $key ) {
	$defaults = springcard_footer_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Client logos for the homepage marquee, ordered manually via page-attributes.
 *
 * @return WP_Post[]
 */
function springcard_get_client_logos() {
	return get_posts(
		array(
			'post_type'      => 'logo_client',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
}

/**
 * Echo the theme's logo as inline SVG (trusted, theme-bundled static asset —
 * not user input, so no output escaping applies here).
 */
function springcard_the_logo() {
	$logo_path = get_theme_file_path( 'assets/images/logo.svg' );
	if ( ! file_exists( $logo_path ) ) {
		return;
	}
	$svg = file_get_contents( $logo_path );
	if ( $svg ) {
		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Permalink of the first published page using a given page template file.
 *
 * @param string $template Template file name, e.g. "page-a-propos.php".
 * @return string Empty string if no page uses that template yet.
 */
function springcard_get_page_url_by_template( $template ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
		)
	);
	return $pages ? get_permalink( $pages[0] ) : '';
}

/**
 * URL of the "Contact" tab on the À propos page, used by the header CTA
 * button. Falls back to "#" until a page using page-a-propos.php exists.
 *
 * @return string
 */
function springcard_get_contact_url() {
	$url = springcard_get_page_url_by_template( 'page-a-propos.php' );
	return $url ? trailingslashit( $url ) . '#contact' : '#';
}
