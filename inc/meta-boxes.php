<?php
/**
 * Native meta boxes (no ACF / no CPT UI) for SpringCard custom post types.
 *
 * Meta keys:
 * - gamme:             _statut, _visuel_fabrication_id, _visuel_kit_id
 * - produit:           _statut, _type_antenne, _fiche_technique_id, _specs, _gamme_id
 * - secteur:           _icone
 * - cas_usage:         _client, _secteurs (array), _produits (array)
 * - expertise:         _code
 * - article_technique: _gamme_id, _produit_id
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed "statut" values shared by gamme and produit.
 */
function springcard_statut_options() {
	return array(
		'actif'    => __( 'Actif', 'springcard' ),
		'archive'  => __( 'Archivé', 'springcard' ),
		'a_venir'  => __( 'À venir', 'springcard' ),
	);
}

/**
 * Allowed "type d'antenne" values for produit.
 */
function springcard_antenne_options() {
	return array(
		'non_fournie' => __( 'Module seul', 'springcard' ),
		'separee'     => __( 'Antenne séparée', 'springcard' ),
		'integree'    => __( 'Antenne intégrée', 'springcard' ),
	);
}

/**
 * Register meta boxes per post type.
 */
function springcard_register_meta_boxes() {
	add_meta_box( 'springcard_statut', __( 'Statut', 'springcard' ), 'springcard_render_statut_metabox', 'gamme', 'side', 'default' );
	add_meta_box( 'springcard_gamme_visuels', __( 'Visuels de présentation', 'springcard' ), 'springcard_render_gamme_visuels_metabox', 'gamme', 'normal', 'default' );

	add_meta_box( 'springcard_statut', __( 'Statut', 'springcard' ), 'springcard_render_statut_metabox', 'produit', 'side', 'default' );
	add_meta_box( 'springcard_produit_details', __( 'Détails produit', 'springcard' ), 'springcard_render_produit_metabox', 'produit', 'normal', 'default' );

	add_meta_box( 'springcard_secteur_details', __( 'Icône du secteur', 'springcard' ), 'springcard_render_secteur_metabox', 'secteur', 'side', 'default' );

	add_meta_box( 'springcard_cas_usage_details', __( "Détails du cas d'usage", 'springcard' ), 'springcard_render_cas_usage_metabox', 'cas_usage', 'normal', 'default' );

	add_meta_box( 'springcard_expertise_details', __( 'Code affiché', 'springcard' ), 'springcard_render_expertise_metabox', 'expertise', 'side', 'default' );

	add_meta_box( 'springcard_article_technique_details', __( 'Ressource associée', 'springcard' ), 'springcard_render_article_technique_metabox', 'article_technique', 'side', 'default' );

	add_meta_box( 'springcard_logo_client_details', __( 'Lien du client', 'springcard' ), 'springcard_render_logo_client_metabox', 'logo_client', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'springcard_register_meta_boxes' );

/**
 * Enqueue the media uploader on edit screens that have a media-picker button
 * (produit: fiche technique PDF; gamme: visuels de présentation).
 */
function springcard_admin_enqueue( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	global $post_type;
	if ( ! in_array( $post_type, array( 'produit', 'gamme' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'springcard-admin',
		get_theme_file_uri( 'assets/js/admin.js' ),
		array( 'jquery' ),
		SPRINGCARD_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'springcard_admin_enqueue' );

/* ---------------------------------------------------------------------
 * Render callbacks
 * ------------------------------------------------------------------ */

function springcard_render_statut_metabox( $post ) {
	wp_nonce_field( 'springcard_save_statut', 'springcard_statut_nonce' );
	$current = get_post_meta( $post->ID, '_statut', true );
	if ( ! $current ) {
		$current = 'actif';
	}
	foreach ( springcard_statut_options() as $value => $label ) {
		printf(
			'<p><label><input type="radio" name="springcard_statut" value="%1$s" %2$s /> %3$s</label></p>',
			esc_attr( $value ),
			checked( $current, $value, false ),
			esc_html( $label )
		);
	}
}

function springcard_render_produit_metabox( $post ) {
	wp_nonce_field( 'springcard_save_produit', 'springcard_produit_nonce' );

	$type_antenne = get_post_meta( $post->ID, '_type_antenne', true );
	$fiche_id     = (int) get_post_meta( $post->ID, '_fiche_technique_id', true );
	$specs        = get_post_meta( $post->ID, '_specs', true );
	$gamme_id     = (int) get_post_meta( $post->ID, '_gamme_id', true );

	$gammes = get_posts(
		array(
			'post_type'      => 'gamme',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	echo '<p><label for="springcard_gamme_id"><strong>' . esc_html__( 'Gamme parente', 'springcard' ) . '</strong></label><br />';
	echo '<select name="springcard_gamme_id" id="springcard_gamme_id" style="width:100%;">';
	echo '<option value="0">' . esc_html__( 'Aucune', 'springcard' ) . '</option>';
	foreach ( $gammes as $gamme ) {
		printf(
			'<option value="%1$d" %2$s>%3$s</option>',
			esc_attr( $gamme->ID ),
			selected( $gamme_id, $gamme->ID, false ),
			esc_html( get_the_title( $gamme ) )
		);
	}
	echo '</select></p>';

	echo '<p><label for="springcard_type_antenne"><strong>' . esc_html__( "Type d'antenne", 'springcard' ) . '</strong></label><br />';
	echo '<select name="springcard_type_antenne" id="springcard_type_antenne" style="width:100%;">';
	foreach ( springcard_antenne_options() as $value => $label ) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $value ),
			selected( $type_antenne, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select></p>';

	echo '<p><label for="springcard_specs"><strong>' . esc_html__( 'Caractéristiques (une par ligne, format "Libellé : Valeur")', 'springcard' ) . '</strong></label><br />';
	printf(
		'<textarea name="springcard_specs" id="springcard_specs" rows="5" style="width:100%%;">%s</textarea></p>',
		esc_textarea( $specs )
	);

	echo '<p><label><strong>' . esc_html__( 'Fiche technique (PDF)', 'springcard' ) . '</strong></label><br />';
	echo '<input type="hidden" name="springcard_fiche_technique_id" id="springcard_fiche_technique_id" value="' . esc_attr( $fiche_id ) . '" />';
	printf(
		'<button type="button" class="button" data-media-picker="springcard_fiche_technique" data-media-title="%1$s" data-media-button="%2$s" data-media-type="application/pdf">%3$s</button> ',
		esc_attr__( 'Choisir la fiche technique (PDF)', 'springcard' ),
		esc_attr__( 'Utiliser ce fichier', 'springcard' ),
		esc_html__( 'Choisir un fichier PDF', 'springcard' )
	);
	echo '<span id="springcard_fiche_technique_filename">';
	if ( $fiche_id ) {
		echo esc_html( basename( get_attached_file( $fiche_id ) ) );
	}
	echo '</span></p>';
}

/**
 * Two optional single-image fields used by the gamme hero/feature rows:
 * a "fabrication" shot and a "kit de développement" shot.
 */
function springcard_render_gamme_visuels_metabox( $post ) {
	wp_nonce_field( 'springcard_save_gamme_visuels', 'springcard_gamme_visuels_nonce' );

	$fields = array(
		'springcard_visuel_fabrication' => array(
			'meta'  => '_visuel_fabrication_id',
			'label' => __( 'Fabrication / production', 'springcard' ),
		),
		'springcard_visuel_kit'         => array(
			'meta'  => '_visuel_kit_id',
			'label' => __( "Bureau d'études / kit de développement", 'springcard' ),
		),
	);

	foreach ( $fields as $target => $field ) {
		$attachment_id = (int) get_post_meta( $post->ID, $field['meta'], true );
		echo '<p style="margin-bottom:6px;"><strong>' . esc_html( $field['label'] ) . '</strong></p>';
		if ( $attachment_id ) {
			echo wp_get_attachment_image( $attachment_id, array( 160, 120 ), false, array( 'style' => 'border-radius:6px; display:block; margin-bottom:8px; object-fit:cover;' ) );
		}
		echo '<p>';
		echo '<input type="hidden" name="' . esc_attr( $target ) . '_id" id="' . esc_attr( $target ) . '_id" value="' . esc_attr( $attachment_id ) . '" />';
		printf(
			'<button type="button" class="button" data-media-picker="%1$s" data-media-title="%2$s" data-media-button="%3$s" data-media-type="image">%4$s</button> ',
			esc_attr( $target ),
			esc_attr__( 'Choisir une image', 'springcard' ),
			esc_attr__( 'Utiliser cette image', 'springcard' ),
			esc_html__( 'Choisir une image', 'springcard' )
		);
		echo '<span id="' . esc_attr( $target ) . '_filename"></span>';
		echo '</p>';
	}
}

function springcard_render_secteur_metabox( $post ) {
	wp_nonce_field( 'springcard_save_secteur', 'springcard_secteur_nonce' );
	$icone = get_post_meta( $post->ID, '_icone', true );
	printf(
		'<p><label for="springcard_icone">%1$s</label><br /><input type="text" name="springcard_icone" id="springcard_icone" value="%2$s" style="width:100%%;" placeholder="dashicons-car" /></p>',
		esc_html__( 'Classe Dashicon (ex. dashicons-car)', 'springcard' ),
		esc_attr( $icone )
	);
}

function springcard_render_cas_usage_metabox( $post ) {
	wp_nonce_field( 'springcard_save_cas_usage', 'springcard_cas_usage_nonce' );

	$client    = get_post_meta( $post->ID, '_client', true );
	$secteurs  = (array) get_post_meta( $post->ID, '_secteurs', true );
	$produits  = (array) get_post_meta( $post->ID, '_produits', true );

	printf(
		'<p><label for="springcard_client"><strong>%1$s</strong></label><br /><input type="text" name="springcard_client" id="springcard_client" value="%2$s" style="width:100%%;" /></p>',
		esc_html__( 'Client', 'springcard' ),
		esc_attr( $client )
	);

	echo '<p><strong>' . esc_html__( 'Secteurs concernés', 'springcard' ) . '</strong></p>';
	foreach ( get_posts( array( 'post_type' => 'secteur', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $secteur ) {
		printf(
			'<label style="display:block;"><input type="checkbox" name="springcard_secteurs[]" value="%1$d" %2$s /> %3$s</label>',
			esc_attr( $secteur->ID ),
			checked( in_array( $secteur->ID, $secteurs, true ), true, false ),
			esc_html( get_the_title( $secteur ) )
		);
	}

	echo '<p style="margin-top:14px;"><strong>' . esc_html__( 'Produits concernés', 'springcard' ) . '</strong></p>';
	foreach ( get_posts( array( 'post_type' => 'produit', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $produit ) {
		printf(
			'<label style="display:block;"><input type="checkbox" name="springcard_produits[]" value="%1$d" %2$s /> %3$s</label>',
			esc_attr( $produit->ID ),
			checked( in_array( $produit->ID, $produits, true ), true, false ),
			esc_html( get_the_title( $produit ) )
		);
	}
}

function springcard_render_expertise_metabox( $post ) {
	wp_nonce_field( 'springcard_save_expertise', 'springcard_expertise_nonce' );
	$code = get_post_meta( $post->ID, '_code', true );
	printf(
		'<p><label for="springcard_code">%1$s</label><br /><input type="text" name="springcard_code" id="springcard_code" value="%2$s" maxlength="4" style="width:100%%;" placeholder="HW" /></p>',
		esc_html__( 'Code court (ex. HW, FW, SW)', 'springcard' ),
		esc_attr( $code )
	);
}

function springcard_render_article_technique_metabox( $post ) {
	wp_nonce_field( 'springcard_save_article_technique', 'springcard_article_technique_nonce' );

	$gamme_id   = (int) get_post_meta( $post->ID, '_gamme_id', true );
	$produit_id = (int) get_post_meta( $post->ID, '_produit_id', true );

	echo '<p><label for="springcard_at_gamme_id">' . esc_html__( 'Gamme liée (optionnel)', 'springcard' ) . '</label><br />';
	echo '<select name="springcard_gamme_id" id="springcard_at_gamme_id" style="width:100%;">';
	echo '<option value="0">' . esc_html__( 'Aucune', 'springcard' ) . '</option>';
	foreach ( get_posts( array( 'post_type' => 'gamme', 'posts_per_page' => -1 ) ) as $gamme ) {
		printf(
			'<option value="%1$d" %2$s>%3$s</option>',
			esc_attr( $gamme->ID ),
			selected( $gamme_id, $gamme->ID, false ),
			esc_html( get_the_title( $gamme ) )
		);
	}
	echo '</select></p>';

	echo '<p><label for="springcard_at_produit_id">' . esc_html__( 'Produit lié (optionnel)', 'springcard' ) . '</label><br />';
	echo '<select name="springcard_produit_id" id="springcard_at_produit_id" style="width:100%;">';
	echo '<option value="0">' . esc_html__( 'Aucun', 'springcard' ) . '</option>';
	foreach ( get_posts( array( 'post_type' => 'produit', 'posts_per_page' => -1 ) ) as $produit ) {
		printf(
			'<option value="%1$d" %2$s>%3$s</option>',
			esc_attr( $produit->ID ),
			selected( $produit_id, $produit->ID, false ),
			esc_html( get_the_title( $produit ) )
		);
	}
	echo '</select></p>';
}

function springcard_render_logo_client_metabox( $post ) {
	wp_nonce_field( 'springcard_save_logo_client', 'springcard_logo_client_nonce' );
	$url = get_post_meta( $post->ID, '_url_client', true );
	printf(
		'<p><label for="springcard_url_client">%1$s</label><br /><input type="url" name="springcard_url_client" id="springcard_url_client" value="%2$s" style="width:100%%;" placeholder="https://…" /></p>',
		esc_html__( "Site du client (optionnel, rend le logo cliquable)", 'springcard' ),
		esc_attr( $url )
	);
	echo '<p class="description">' . esc_html__( "Utilise l'image mise en avant pour le logo, et l'ordre (page-attributes) pour le classement dans le défilement.", 'springcard' ) . '</p>';
}

/* ---------------------------------------------------------------------
 * Save — une fonction courte par type de contenu, accrochée à son hook
 * dédié "save_post_{type}" plutôt qu'un save_post générique + gros switch.
 * ------------------------------------------------------------------ */

/**
 * Garde commune : autosave, révision, permissions.
 */
function springcard_meta_save_allowed( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return false;
	}
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Champ "_statut", partagé par gamme et produit.
 */
function springcard_save_statut_field( $post_id ) {
	if ( ! isset( $_POST['springcard_statut_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_statut_nonce'] ), 'springcard_save_statut' )
		|| ! isset( $_POST['springcard_statut'] )
	) {
		return;
	}
	$statut = sanitize_text_field( wp_unslash( $_POST['springcard_statut'] ) );
	if ( array_key_exists( $statut, springcard_statut_options() ) ) {
		update_post_meta( $post_id, '_statut', $statut );
	}
}

function springcard_save_gamme_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	springcard_save_statut_field( $post_id );

	if ( ! isset( $_POST['springcard_gamme_visuels_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_gamme_visuels_nonce'] ), 'springcard_save_gamme_visuels' )
	) {
		return;
	}
	if ( isset( $_POST['springcard_visuel_fabrication_id'] ) ) {
		update_post_meta( $post_id, '_visuel_fabrication_id', absint( $_POST['springcard_visuel_fabrication_id'] ) );
	}
	if ( isset( $_POST['springcard_visuel_kit_id'] ) ) {
		update_post_meta( $post_id, '_visuel_kit_id', absint( $_POST['springcard_visuel_kit_id'] ) );
	}
}
add_action( 'save_post_gamme', 'springcard_save_gamme_meta' );

function springcard_save_produit_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	springcard_save_statut_field( $post_id );

	if ( ! isset( $_POST['springcard_produit_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_produit_nonce'] ), 'springcard_save_produit' )
	) {
		return;
	}
	if ( isset( $_POST['springcard_gamme_id'] ) ) {
		update_post_meta( $post_id, '_gamme_id', absint( $_POST['springcard_gamme_id'] ) );
	}
	if ( isset( $_POST['springcard_type_antenne'] ) ) {
		$type_antenne = sanitize_text_field( wp_unslash( $_POST['springcard_type_antenne'] ) );
		if ( array_key_exists( $type_antenne, springcard_antenne_options() ) ) {
			update_post_meta( $post_id, '_type_antenne', $type_antenne );
		}
	}
	if ( isset( $_POST['springcard_specs'] ) ) {
		update_post_meta( $post_id, '_specs', sanitize_textarea_field( wp_unslash( $_POST['springcard_specs'] ) ) );
	}
	if ( isset( $_POST['springcard_fiche_technique_id'] ) ) {
		update_post_meta( $post_id, '_fiche_technique_id', absint( $_POST['springcard_fiche_technique_id'] ) );
	}
}
add_action( 'save_post_produit', 'springcard_save_produit_meta' );

function springcard_save_secteur_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['springcard_secteur_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_secteur_nonce'] ), 'springcard_save_secteur' )
		|| ! isset( $_POST['springcard_icone'] )
	) {
		return;
	}
	update_post_meta( $post_id, '_icone', sanitize_text_field( wp_unslash( $_POST['springcard_icone'] ) ) );
}
add_action( 'save_post_secteur', 'springcard_save_secteur_meta' );

function springcard_save_cas_usage_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['springcard_cas_usage_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_cas_usage_nonce'] ), 'springcard_save_cas_usage' )
	) {
		return;
	}
	if ( isset( $_POST['springcard_client'] ) ) {
		update_post_meta( $post_id, '_client', sanitize_text_field( wp_unslash( $_POST['springcard_client'] ) ) );
	}
	$secteurs = isset( $_POST['springcard_secteurs'] ) ? array_map( 'absint', (array) $_POST['springcard_secteurs'] ) : array();
	update_post_meta( $post_id, '_secteurs', $secteurs );
	$produits = isset( $_POST['springcard_produits'] ) ? array_map( 'absint', (array) $_POST['springcard_produits'] ) : array();
	update_post_meta( $post_id, '_produits', $produits );
}
add_action( 'save_post_cas_usage', 'springcard_save_cas_usage_meta' );

function springcard_save_expertise_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['springcard_expertise_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_expertise_nonce'] ), 'springcard_save_expertise' )
		|| ! isset( $_POST['springcard_code'] )
	) {
		return;
	}
	update_post_meta( $post_id, '_code', sanitize_text_field( wp_unslash( $_POST['springcard_code'] ) ) );
}
add_action( 'save_post_expertise', 'springcard_save_expertise_meta' );

function springcard_save_article_technique_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['springcard_article_technique_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_article_technique_nonce'] ), 'springcard_save_article_technique' )
	) {
		return;
	}
	if ( isset( $_POST['springcard_gamme_id'] ) ) {
		update_post_meta( $post_id, '_gamme_id', absint( $_POST['springcard_gamme_id'] ) );
	}
	if ( isset( $_POST['springcard_produit_id'] ) ) {
		update_post_meta( $post_id, '_produit_id', absint( $_POST['springcard_produit_id'] ) );
	}
}
add_action( 'save_post_article_technique', 'springcard_save_article_technique_meta' );

function springcard_save_logo_client_meta( $post_id ) {
	if ( ! springcard_meta_save_allowed( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['springcard_logo_client_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['springcard_logo_client_nonce'] ), 'springcard_save_logo_client' )
		|| ! isset( $_POST['springcard_url_client'] )
	) {
		return;
	}
	update_post_meta( $post_id, '_url_client', esc_url_raw( wp_unslash( $_POST['springcard_url_client'] ) ) );
}
add_action( 'save_post_logo_client', 'springcard_save_logo_client_meta' );
