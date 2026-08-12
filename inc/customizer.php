<?php
/**
 * Customizer: hero video + poster for the homepage (Apparence → Personnaliser).
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function springcard_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'springcard_hero',
		array(
			'title'    => __( 'Accueil : vidéo du hero', 'springcard' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'springcard_hero_video',
		array(
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'springcard_hero_video',
			array(
				'label'       => __( 'Vidéo de fond (lecture en boucle, muette)', 'springcard' ),
				'description' => __( 'Sans fichier, une animation CSS de secours s’affiche à la place.', 'springcard' ),
				'section'     => 'springcard_hero',
				'mime_type'   => 'video',
			)
		)
	);

	$wp_customize->add_setting(
		'springcard_hero_poster',
		array(
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'springcard_hero_poster',
			array(
				'label'     => __( 'Image "poster" affichée avant lecture (optionnel)', 'springcard' ),
				'section'   => 'springcard_hero',
				'mime_type' => 'image',
			)
		)
	);

	springcard_register_home_text_controls( $wp_customize );
	springcard_register_footer_controls( $wp_customize );
}
add_action( 'customize_register', 'springcard_customize_register' );

/**
 * Editable controls for the footer's address and social network links.
 */
function springcard_register_footer_controls( $wp_customize ) {
	$wp_customize->add_section(
		'springcard_footer',
		array(
			'title'    => __( 'Pied de page', 'springcard' ),
			'priority' => 32,
		)
	);

	$wp_customize->add_setting(
		'springcard_footer_address',
		array(
			'default'           => springcard_footer_defaults()['springcard_footer_address'],
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'springcard_footer_address',
		array(
			'label'   => __( 'Adresse', 'springcard' ),
			'section' => 'springcard_footer',
			'type'    => 'text',
		)
	);

	$social_labels = array(
		'springcard_footer_facebook' => __( 'Facebook (URL, laisser vide pour masquer)', 'springcard' ),
		'springcard_footer_twitter'  => __( 'Twitter / X (URL, laisser vide pour masquer)', 'springcard' ),
		'springcard_footer_linkedin' => __( 'LinkedIn (URL, laisser vide pour masquer)', 'springcard' ),
		'springcard_footer_youtube'  => __( 'YouTube (URL, laisser vide pour masquer)', 'springcard' ),
	);
	foreach ( $social_labels as $setting => $label ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => springcard_footer_defaults()[ $setting ],
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $label,
				'section' => 'springcard_footer',
				'type'    => 'url',
			)
		);
	}
}

/**
 * Editable controls for the home page's hero title, lead and 3 stat sentences.
 */
function springcard_register_home_text_controls( $wp_customize ) {
	$wp_customize->add_section(
		'springcard_home_texts',
		array(
			'title'    => __( 'Accueil : textes', 'springcard' ),
			'priority' => 31,
		)
	);

	$labels = array(
		'springcard_home_hero_title' => __( 'Titre du hero', 'springcard' ),
		'springcard_home_hero_lead'  => __( 'Accroche sous le titre', 'springcard' ),
		'springcard_home_stat_1'     => __( 'Bande de stats, phrase 1', 'springcard' ),
		'springcard_home_stat_2'     => __( 'Bande de stats, phrase 2', 'springcard' ),
		'springcard_home_stat_3'     => __( 'Bande de stats, phrase 3', 'springcard' ),
	);

	foreach ( springcard_home_text_defaults() as $setting => $default ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => $default,
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $labels[ $setting ],
				'section' => 'springcard_home_texts',
				'type'    => ( 'springcard_home_hero_title' === $setting ) ? 'text' : 'textarea',
			)
		);
	}
}
