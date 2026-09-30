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

/**
 * FR -> EN dictionary for the theme's own hardcoded UI strings (headings,
 * buttons, labels). Polylang translates database content (pages, products,
 * articles...) natively; this covers what the templates themselves
 * hardcode, which Polylang has no way to know about. Keyed by the French
 * source text, one entry per distinct string regardless of how many
 * templates reuse it.
 *
 * @return array<string, string>
 */
function springcard_ui_translations() {
	return array(
		// functions.php
		'Menu principal' => 'Main Menu',

		// Shared across several templates
		'Blog'                          => 'Blog',
		'Contact'                       => 'Contact',
		"Bureau d'études"               => 'Engineering Office',
		'Actualité'                     => 'News',
		'Aucun article pour le moment.' => 'No articles yet.',
		"Lire le cas d'usage →"         => 'Read the case study →',
		'Décrire votre besoin'          => 'Describe your need',
		'Parler à un ingénieur'         => 'Talk to an engineer',
		"Découvrir le bureau d'études"  => 'Discover our engineering office',
		'M519'                          => 'M519',

		// header.php
		'Aller au contenu principal'      => 'Skip to main content',
		"Retour à l'accueil SpringCard"   => 'Back to SpringCard homepage',
		'Changer de langue'               => 'Change language',
		'Ouvrir le menu'                  => 'Open menu',

		// footer.php
		'Liens de pied de page' => 'Footer links',
		'© %s SpringCard'       => '© %s SpringCard',
		'Liens légaux'          => 'Legal links',

		// front-page.php
		'Module RFID / NFC OEM'                                                                            => 'OEM RFID/NFC Module',
		'Découvrir la gamme M519'                                                                          => 'Discover the M519 range',
		'La gamme'                                                                                         => 'The range',
		'Trois configurations, une même liberté'                                                           => 'Three configurations, the same freedom',
		'Chaque variante correspond à un niveau différent de liberté sur le design du produit final.'      => 'Each variant gives you a different level of freedom over your final product design.',
		'Voir la fiche →'                                                                                  => 'View datasheet →',
		'Un besoin plus spécifique ?'                                                                      => 'A more specific need?',
		"Notre bureau d'études conçoit le hardware, le firmware et le software autour de M519, sur mesure." => 'Our engineering office designs custom hardware, firmware and software around M519.',
		'Secteurs'                                                                                         => 'Sectors',
		'Vos défis, par secteur'                                                                           => 'Your challenges, by sector',
		'Secteur précédent'                                                                                => 'Previous sector',
		'Secteur suivant'                                                                                  => 'Next sector',
		'Ils intègrent nos modules'                                                                        => 'They integrate our modules',

		// page-solutions.php
		'Solutions'                                                                             => 'Solutions',
		"Toutes ces solutions s'appuient sur %s, notre module RFID/NFC OEM."                    => 'All these solutions are built on %s, our OEM RFID/NFC module.',
		'la gamme M519'                                                                         => 'the M519 range',
		'Voir le défi →'                                                                        => 'See the challenge →',
		'+ Futur secteur'                                                                       => '+ Future sector',
		"Cas d'usage à la une"                                                                  => 'Featured case study',
		'[ Visuel client ]'                                                                     => '[ Client visual ]',
		'Secteur %s : '                                                                         => 'Sector %s: ',
		"Votre secteur n'est pas listé ?"                                                       => "Don't see your sector?",
		"Notre bureau d'études étudie tout projet d'intégration, même hors des cas standards."  => 'Our engineering office reviews any integration project, even outside the standard cases.',

		// page-bureau-etudes.php
		'Votre prochain produit est déjà en germe'                                                                                                                                                                    => 'Your next product is already taking shape',
		'Parler de votre projet'                                                                                                                                                                                      => 'Talk about your project',
		'Module M519'                                                                                                                                                                                                 => 'M519 module',
		'Expertises'                                                                                                                                                                                                  => 'Expertise',
		'Ces expertises sont mobilisées sur des projets RFID/NFC de %s.'                                                                                                                                             => 'This expertise is put to work on RFID/NFC projects across %s.',
		'tous secteurs'                                                                                                                                                                                               => 'all sectors',
		'+ Future expertise'                                                                                                                                                                                          => '+ Future expertise',
		'Innovation'                                                                                                                                                                                                  => 'Innovation',
		'Des projets qui ouvrent la voie'                                                                                                                                                                             => 'Projects that break new ground',
		"Ancrée dans une démarche d'innovation agile, l'équipe SpringCard peut prendre en charge le projet le plus atypique qui doit valider une technologie, convaincre un client stratégique ou rendre visible une nouvelle direction." => 'Rooted in an agile innovation approach, the SpringCard team can take on the most unconventional project — one that needs to validate a technology, win over a strategic client, or showcase a new direction.',
		"Démonstrateur pour le prochain salon, preuve de concept, prototype fonctionnel ou première version d'un futur produit prêt à être industrialisé : nous réunissons rapidement le hardware, le firmware, le logiciel et la sécurité qui démontreront votre valeur ajoutée et convaincront vos clients ou les décideurs." => 'A demonstrator for your next trade show, a proof of concept, a working prototype, or the first version of a future product ready for industrialisation: we quickly bring together the hardware, firmware, software and security that will demonstrate your added value and win over your clients or decision-makers.',
		"Notre démarche ? Lever les inconnues techniques, élaguer la complexité inutile, raccourcir le chemin vers une démonstration crédible afin de transmettre à votre équipe une base solide qu'elle pourra maîtriser pleinement." => 'Our approach? Resolve the technical unknowns, cut out unnecessary complexity, and shorten the path to a credible demonstration — so your team is left with a solid foundation it can fully master.',
		'Construire un démonstrateur'                                                                                                                                                                                 => 'Build a demonstrator',
		'Collaboration'                                                                                                                                                                                               => 'Collaboration',
		'Trois façons de travailler avec nous'                                                                                                                                                                       => 'Three ways to work with us',
		'Accélérer votre produit'                                                                                                                                                                                     => 'Accelerate your product',
		"Vous partez du %s ou d'une architecture existante. Nous traitons les points spécialisés : antenne, intégration RF, protocole, carte sécurisée, cryptographie, pilote ou logiciel embarqué."                 => 'You start from %s or an existing architecture. We handle the specialised parts: antenna, RF integration, protocol, secure card, cryptography, driver or embedded software.',
		'Explorer une nouvelle voie'                                                                                                                                                                                  => 'Explore a new direction',
		"Nous réalisons un prototype ou un démonstrateur complet pour tester un usage, préparer un salon, sécuriser un choix d'architecture ou convaincre avant d'engager l'industrialisation."                      => 'We build a prototype or a complete demonstrator to test a use case, prepare for a trade show, de-risk an architecture choice, or make the case before committing to industrialisation.',
		'Acquérir une base éprouvée'                                                                                                                                                                                  => 'Acquire a proven foundation',
		'Vous pouvez acquérir une licence sur une bibliothèque logicielle, une IP ou un dossier de définition de produit conçu par SpringCard, puis fabriquer et faire évoluer la solution dans le cadre convenu.'   => 'You can license a software library, an IP block, or a product design package created by SpringCard, then manufacture and evolve the solution within the agreed framework.',
		'Méthode'                                                                                                                                                                                                     => 'Method',
		'Étude de faisabilité'                                                                                                                                                                                       => 'Feasibility study',
		'Cadrage technique et contraintes projet.'                                                                                                                                                                   => 'Technical scoping and project constraints.',
		'Prototypage'                                                                                                                                                                                                 => 'Prototyping',
		'Preuve de concept sur module existant ou nouveau.'                                                                                                                                                          => 'Proof of concept on an existing or new module.',
		'Développement'                                                                                                                                                                                               => 'Development',
		'Industrialisation hardware, firmware, software.'                                                                                                                                                            => 'Hardware, firmware and software industrialisation.',
		'Qualification'                                                                                                                                                                                               => 'Qualification',
		'Tests, certification, mise en production.'                                                                                                                                                                  => 'Testing, certification, production rollout.',
		'Livrables'                                                                                                                                                                                                   => 'Deliverables',
		'Des briques techniques maîtrisées'                                                                                                                                                                          => 'Technical building blocks, fully mastered',
		"Selon le projet, SpringCard livre un prototype, un dossier de conception, du code source, une bibliothèque documentée, des outils de test ou un transfert de compétences. Le périmètre, la propriété intellectuelle, les conditions de licence et le niveau d'accompagnement sont définis dès le départ." => 'Depending on the project, SpringCard delivers a prototype, a design package, source code, a documented library, test tools, or a skills transfer. Scope, intellectual property, licensing terms and the level of support are all defined upfront.',
		'Les projets peuvent être conduits et documentés à 100 % en français ou en anglais, avec des interlocuteurs techniques capables de travailler directement avec vos équipes internationales.'                 => 'Projects can be run and documented entirely in French or English, with technical contacts able to work directly with your international teams.',
		'Ils nous ont confié leur développement sur mesure'                                                                                                                                                          => 'They trusted us with their custom development',
		'[ Visuel projet ]'                                                                                                                                                                                           => '[ Project visual ]',
		'Discutons de votre projet'                                                                                                                                                                                   => "Let's discuss your project",
		'Un premier échange avec un ingénieur, sans engagement.'                                                                                                                                                     => 'An initial conversation with an engineer, no strings attached.',

		// single-gamme.php
		'active'                                                                    => 'active',
		'archivée'                                                                  => 'discontinued',
		'à venir'                                                                   => 'upcoming',
		'Navigation rapide de la gamme'                                             => 'Quick range navigation',
		'Variantes'                                                                 => 'Variants',
		'Comparer'                                                                  => 'Compare',
		'Ressources'                                                                => 'Resources',
		'Gamme %s'                                                                  => '%s range',
		'Comparer les variantes'                                                    => 'Compare variants',
		'Télécharger la fiche technique'                                            => 'Download datasheet',
		"D'un coup d'œil"                                                           => 'At a glance',
		"Trois façons d'intégrer %s"                                                => 'Three ways to integrate %s',
		'Voir la fiche technique →'                                                 => 'View datasheet →',
		'Fabrication'                                                               => 'Manufacturing',
		'Du prototype à la série, sans rupture de forme'                            => 'From prototype to volume production, same form factor',
		"Même module, du premier essai en laboratoire jusqu'aux volumes de production. Aucune reconception nécessaire quand vous passez à l'échelle." => 'Same module, from the first lab trial through to production volumes. No redesign needed as you scale up.',
		'Un kit pour démarrer en un après-midi'                                     => 'A kit to get started in an afternoon',
		"SDK, exemples de code et kit de développement pour valider votre intégration rapidement, puis notre bureau d'études prend le relais pour le sur-mesure." => 'SDK, code samples and a development kit to validate your integration quickly — then our engineering office takes over for custom work.',
		'Toutes les caractéristiques, côte à côte'                                  => 'Every specification, side by side',
		'Caractéristique'                                                           => 'Specification',
		'De quoi démarrer'                                                          => 'Everything to get started',
		'Documentation technique'                                                   => 'Technical documentation',
		"Datasheets et guides d'intégration, par variante."                        => 'Datasheets and integration guides, per variant.',
		'Voir les fiches →'                                                         => 'View datasheets →',
		'SDK &amp; outils'                                                          => 'SDK &amp; tools',
		'Librairies et exemples de code.'                                           => 'Libraries and code samples.',
		"Voir le bureau d'études →"                                                 => 'Visit the engineering office →',
		'Kit de démarrage'                                                          => 'Starter kit',
		'Pour prototyper rapidement.'                                               => 'To prototype quickly.',
		'Demander un kit →'                                                         => 'Request a kit →',
		'Support technique'                                                         => 'Technical support',
		"Une équipe d'ingénieurs disponible."                                       => 'A team of engineers on hand.',
		'Contacter →'                                                               => 'Contact us →',
		'Construit avec %s'                                                         => 'Built with %s',
		"Besoin d'une configuration spécifique ?"                                   => 'Need a specific configuration?',
		"Notre bureau d'études peut adapter %s à vos contraintes."                  => 'Our engineering office can adapt %s to your constraints.',

		// single-cas_usage.php
		"Cas d'usage"                                    => 'Case study',
		'Un projet similaire ?'                          => 'A similar project?',
		"Parlons-en avec notre bureau d'études."          => "Let's talk about it with our engineering office.",

		// single-article.php
		'Gamme M519'                                                                        => 'M519 range',
		'Solutions RFID/NFC par secteur'                                                    => 'RFID/NFC solutions by sector',
		'Un projet RFID/NFC en tête ?'                                                      => 'Got an RFID/NFC project in mind?',
		"Notre bureau d'études conçoit des lecteurs sur mesure autour de la gamme M519."    => 'Our engineering office designs custom readers built around the M519 range.',

		// archive-article.php
		'Actualités SpringCard' => 'SpringCard News',

		// page-a-propos.php
		'À propos'                                                              => 'About',
		'Sections de la page À propos'                                         => 'About page sections',
		'Blog technique'                                                       => 'Technical Blog',
		'Nous contacter'                                                       => 'Contact us',
		'Une question technique, commerciale, ou un projet à décrire, écrivez-nous.' => 'A technical or commercial question, or a project to describe — write to us.',

		// page-legal.php
		'Informations légales' => 'Legal information',

		// 404.php
		'Erreur 404'                                                              => 'Error 404',
		"Cette page n'existe pas"                                                => "This page doesn't exist",
		"La page que vous cherchez a peut-être été déplacée ou n'existe plus."   => 'The page you are looking for may have been moved or no longer exists.',
		"Retour à l'accueil"                                                     => 'Back to homepage',

		// index.php (search results)
		'Résultats pour « %s »' => 'Results for "%s"',
		'Aucun résultat.'       => 'No results.',

		// inc/helpers.php — springcard_home_text_defaults() (hero, front-page.php)
		'Un module, votre lecteur sur mesure'                                                                          => 'One module, your custom-made reader',
		'Intégrez M519 dans vos machines. Vous gardez la main sur le design de votre propre lecteur RFID/NFC.'        => 'Integrate M519 into your equipment. You stay in control of the design of your own RFID/NFC reader.',
		"Le module s'intègre directement dans vos équipements, le boîtier et l'antenne restent les vôtres."           => 'The module integrates directly into your equipment — the housing and the antenna stay yours.',
		"Notre bureau d'études vous accompagne de l'idée jusqu'au produit fini."                                       => 'Our engineering office supports you from the idea through to the finished product.',
		"20 ans d'expérience, fabriqué en France, dans des secteurs qui ne laissent pas de place à l'approximation."   => '20 years of experience, made in France, in sectors that leave no room for approximation.',

		// inc/helpers.php — springcard_get_contact_form_html() mailto fallback
		'Envoyer un message' => 'Send a message',

		// inc/meta-boxes.php — springcard_antenne_options()/springcard_statut_options(),
		// shared between the admin edit screens and the frontend badges
		// (front-page.php, single-gamme.php "MODULE SEUL" tag, hero facts...).
		'Module seul'       => 'Module only',
		'Antenne intégrée'  => 'Integrated antenna',
		'Antenne déportée'  => 'Remote antenna',
		'Actif'             => 'Active',
		'Archivé'           => 'Discontinued',
		'À venir'           => 'Coming soon',
	);
}

/**
 * Translates one of the theme's hardcoded UI strings to English when the
 * current Polylang language is English. Returns the French source text
 * unchanged otherwise, or if no translation is registered.
 *
 * @param string $text French source text (used as the lookup key).
 * @return string
 */
function springcard_t( $text ) {
	if ( 'en' !== springcard_current_lang() ) {
		return $text;
	}
	$translations = springcard_ui_translations();
	return isset( $translations[ $text ] ) ? $translations[ $text ] : $text;
}

/**
 * Echoes a translated UI string, HTML-escaped. Drop-in replacement for
 * esc_html_e( $text, 'springcard' ).
 *
 * @param string $text French source text.
 */
function springcard_e( $text ) {
	echo esc_html( springcard_t( $text ) );
}

/**
 * Echoes a translated UI string, attribute-escaped. Drop-in replacement for
 * esc_attr_e( $text, 'springcard' ).
 *
 * @param string $text French source text.
 */
function springcard_attr_e( $text ) {
	echo esc_attr( springcard_t( $text ) );
}
