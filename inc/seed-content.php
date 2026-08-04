<?php
/**
 * Default homepage media, seeded once from the assets bundled with the theme:
 * the `logo_client` posts (assets/images/clients/) and the hero background
 * video (assets/video/). Both stay fully editable afterwards from wp-admin —
 * logos like any other logo_client post, the video via Apparence > Personnaliser.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bundled client logos. Order here sets the initial marquee order (menu_order).
 *
 * @return array[]
 */
function springcard_client_logo_seeds() {
	return array(
		array( 'title' => 'IDEMIA', 'file' => 'logo-idemia.webp' ),
		array( 'title' => 'IER', 'file' => 'logo-ier.webp' ),
		array( 'title' => 'Xerox', 'file' => 'logo-xerox.webp' ),
		array( 'title' => 'Conduent', 'file' => 'logo-conduent.webp' ),
		array( 'title' => 'Bolloré', 'file' => 'logo-bollore.webp' ),
		array( 'title' => 'Evolis', 'file' => 'logo-evolis.webp' ),
		array( 'title' => 'Doctolib', 'file' => 'logo-doctolib.webp' ),
		array( 'title' => 'Socket Mobile', 'file' => 'logo-socket-mobile.webp' ),
		array( 'title' => 'Designa', 'file' => 'logo-designa.webp' ),
		array( 'title' => 'Omnitech', 'file' => 'logo-omnitech.webp' ),
		array( 'title' => 'Thinfilm', 'file' => 'logo-thinfilm.webp' ),
	);
}

/**
 * Sideload one bundled theme asset (image or video) into the media library.
 *
 * @param string $src_path Absolute path to the theme asset.
 * @param string $title    Attachment/post title (image alt text for images).
 * @return int|false
 */
function springcard_sideload_theme_asset( $src_path, $title ) {
	if ( ! file_exists( $src_path ) ) {
		return false;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$mime_types = array(
		'webp' => 'image/webp',
		'mp4'  => 'video/mp4',
	);
	$ext        = strtolower( pathinfo( $src_path, PATHINFO_EXTENSION ) );
	if ( ! isset( $mime_types[ $ext ] ) ) {
		return false;
	}

	$filename = wp_unique_filename( wp_upload_dir()['path'], basename( $src_path ) );
	$upload   = wp_upload_bits( $filename, null, file_get_contents( $src_path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a theme-bundled asset, not a remote/user file.

	if ( ! empty( $upload['error'] ) ) {
		return false;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $mime_types[ $ext ],
			'post_title'     => $title,
			'post_status'    => 'inherit',
			'guid'           => $upload['url'],
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return false;
	}

	if ( 'webp' === $ext ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
	}
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

	return $attachment_id;
}

/**
 * Create the bundled `logo_client` posts, once, if none exist yet.
 */
function springcard_seed_client_logos() {
	if ( wp_count_posts( 'logo_client' )->publish > 0 ) {
		return;
	}

	foreach ( springcard_client_logo_seeds() as $order => $logo ) {
		$src_path      = get_theme_file_path( 'assets/images/clients/' . $logo['file'] );
		$attachment_id = springcard_sideload_theme_asset( $src_path, $logo['title'] );

		if ( ! $attachment_id ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'logo_client',
				'post_title'  => $logo['title'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);

		if ( ! is_wp_error( $post_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}
}
add_action( 'init', 'springcard_seed_client_logos' );

/**
 * Set the homepage background video, once, from the bundled asset, if no
 * hero video has been chosen yet (via the Customizer or this seeder).
 */
function springcard_seed_hero_video() {
	if ( get_theme_mod( 'springcard_hero_video' ) ) {
		return;
	}

	$src_path      = get_theme_file_path( 'assets/video/hero-springcard.mp4' );
	$attachment_id = springcard_sideload_theme_asset( $src_path, 'Vidéo hero SpringCard' );

	if ( $attachment_id ) {
		set_theme_mod( 'springcard_hero_video', $attachment_id );
	}
}
add_action( 'init', 'springcard_seed_hero_video' );
