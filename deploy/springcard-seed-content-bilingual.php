<?php
/**
 * Plugin Name: SpringCard - Contenu de demonstration (FR/EN)
 * Description: Cree ou complete le contenu bilingue du site SpringCard (pages, gamme M519, produits, secteurs, cas d'usage, expertises, articles, formulaire de contact, reglages SEO) en francais ET en anglais via Polylang. Necessite Polylang installe et actif. Sans danger a relancer plusieurs fois : le contenu et les menus sont nettoyes puis reconstruits a l'identique a chaque lancement, jamais dupliques.
 * Version: 1.5
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'admin_menu',
	function () {
		add_management_page(
			'Contenu SpringCard (FR/EN)',
			'Contenu SpringCard (FR/EN)',
			'manage_options',
			'springcard-seed-bilingual',
			'springcard_seed_bilingual_admin_page'
		);
	}
);

function springcard_seed_bilingual_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$ran = false;
	$polylang_active = function_exists( 'PLL' );
	if ( $polylang_active && isset( $_POST['springcard_seed_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['springcard_seed_nonce'] ) ), 'springcard_seed_bilingual_run' ) ) {
		springcard_seed_bilingual_run();
		$ran = true;
	}
	?>
	<div class="wrap">
		<h1>Contenu SpringCard (FR/EN)</h1>
		<p>Version du plugin : <strong>1.5</strong> — verifie que ce numero correspond bien a la derniere version avant de relancer (retelecharge le fichier si besoin, un zip deja telecharge peut etre perime).</p>
		<?php if ( ! $polylang_active ) : ?>
			<div class="notice notice-error"><p>Polylang n'est pas actif. Installe et active Polylang (Extensions > Ajouter, rechercher "Polylang") avant de lancer la creation du contenu bilingue.</p></div>
		<?php elseif ( $ran ) : ?>
			<div class="notice notice-success"><p>Fait. Le contenu deja present a ete conserve (pas duplique), les menus ont ete entierement reconstruits, et le reste du contenu/reglages a ete complete.</p></div>
		<?php endif; ?>
		<p>Ce bouton met en place les langues (francais/anglais) dans Polylang si besoin, puis cree ou complete en bilingue : les pages, la gamme M519 et ses variantes, les secteurs, le cas d'usage AFCare/Doctolib, les expertises, l'article SpringPass, les pages legales, le menu principal, et les reglages SEO correspondants.</p>
		<p><strong>Sans danger a relancer</strong> : le contenu existant n'est jamais duplique, et les menus sont entierement nettoyes puis reconstruits a chaque lancement.</p>
		<form method="post">
			<?php wp_nonce_field( 'springcard_seed_bilingual_run', 'springcard_seed_nonce' ); ?>
			<?php submit_button( 'Lancer / relancer la creation du contenu bilingue', 'primary', 'submit', true, $polylang_active ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>
	</div>
	<?php
}

function springcard_seed_bilingual_run() {
	/**
	 * Create (or find) a FR + EN pair of posts for a given post type, link them
	 * via Polylang (no-ops gracefully if Polylang isn't active), and return
	 * their IDs keyed by language code.
	 *
	 * Looked up by a dedicated tracking meta key rather than by slug: while the
	 * FR/EN pair share the same intended slug, WordPress can't tell them apart
	 * by post_name alone until Polylang's language-aware uniqueness filter is
	 * active (which only happens after pll_set_post_language() runs below) —
	 * get_page_by_path() would otherwise randomly match either translation.
	 *
	 * @param string $post_type
	 * @param array  $fr array with 'slug', 'title', optional 'excerpt', 'content'.
	 * @param array  $en same shape as $fr, in English.
	 * @return array{fr?:int,en?:int}
	 */
	function springcard_seed_bilingual( $post_type, $fr, $en ) {
		$ids = array();
		foreach ( array( 'fr' => $fr, 'en' => $en ) as $lang => $data ) {
			$seed_key = $post_type . ':' . $lang . ':' . $data['slug'];
			$found    = get_posts(
				array(
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'post_status'    => 'any',
					'meta_key'       => '_springcard_seed_key',
					'meta_value'     => $seed_key,
				)
			);
			$existing_by_slug = ( ! $found && 'fr' === $lang ) ? get_page_by_path( $data['slug'], OBJECT, $post_type ) : null;
			if ( $found ) {
				$id = $found[0]->ID;
			} elseif ( $existing_by_slug ) {
				// Adopte un contenu français déjà créé avant l'activation de Polylang
				// (premier déploiement francophone sur l'hébergement réel) plutôt que
				// d'en recréer un doublon avec un slug suffixé « -2 ».
				$id = $existing_by_slug->ID;
				update_post_meta( $id, '_springcard_seed_key', $seed_key );
			} else {
				$post_args = array(
					'post_type'   => $post_type,
					'post_title'  => $data['title'],
					'post_name'   => $data['slug'],
					'post_status' => 'publish',
				);
				if ( isset( $data['excerpt'] ) ) {
					$post_args['post_excerpt'] = $data['excerpt'];
				}
				if ( isset( $data['content'] ) ) {
					$post_args['post_content'] = $data['content'];
				}
				$id = wp_insert_post( $post_args );
				if ( $id && ! is_wp_error( $id ) ) {
					update_post_meta( $id, '_springcard_seed_key', $seed_key );
				}
			}
			if ( $id && ! is_wp_error( $id ) ) {
				$ids[ $lang ] = $id;
				if ( function_exists( 'pll_set_post_language' ) ) {
					pll_set_post_language( $id, $lang );
					// Re-assert the intended slug now that Polylang knows the post's
					// language, in case WordPress auto-suffixed it (e.g. "-2") when
					// the FR/EN pair briefly shared the same slug just above.
					if ( get_post_field( 'post_name', $id ) !== $data['slug'] ) {
						wp_update_post( array( 'ID' => $id, 'post_name' => $data['slug'] ) );
					}
				}
			}
		}
		if ( isset( $ids['fr'], $ids['en'] ) && function_exists( 'pll_save_post_translations' ) ) {
			pll_save_post_translations( $ids );
		}
		return $ids;
	}

	// Configure les langues Polylang (français par défaut, anglais en second) —
	// doit tourner avant tout le reste : pll_set_post_language() plus bas est un
	// no-op tant qu'aucune langue n'existe. S'appuie sur PLL()->model->add_language(),
	// la même méthode que l'assistant de configuration natif de Polylang utilise
	// (voir modules/wizard/wizard.php::save_step_languages() dans le plugin) ;
	// les réglages d'URL par défaut de Polylang (force_lang=1, hide_default=true)
	// correspondent déjà à ce qu'on veut : français à la racine, /en/ en préfixe.
	if ( function_exists( 'PLL' ) ) {
		if ( ! PLL()->model->get_language( 'fr' ) ) {
			PLL()->model->add_language(
				array(
					'locale'     => 'fr_FR',
					'slug'       => 'fr',
					'name'       => 'Français',
					'flag'       => 'fr',
					'rtl'        => false,
					'term_group' => 0,
				)
			);
		}
		if ( ! PLL()->model->get_language( 'en' ) ) {
			PLL()->model->add_language(
				array(
					'locale'     => 'en_GB',
					'slug'       => 'en',
					'name'       => 'English',
					'flag'       => 'gb',
					'rtl'        => false,
					'term_group' => 1,
				)
			);
		}
	}

	// Pages "gabarit" (Solutions, Bureau d'études, À propos), avec un texte d'intro.
	// Traductions EN reprises du contenu réel publié sur springcard.com quand il
	// existe (M519, cas d'usage, article), sinon traduction fidèle du FR approuvé.
	$pages = array(
		array(
			'slug'     => 'solutions',
			'template' => 'page-solutions.php',
			'fr'       => array(
				'title'   => 'Solutions',
				'slug'    => 'solutions',
				'content' => "Chaque secteur impose ses propres contraintes d'intégration. Découvrez comment M519 s'adapte à vos usages, du contrôle d'accès à l'industrie.",
			),
			'en'       => array(
				'title'   => 'Solutions',
				'slug'    => 'solutions',
				'content' => 'Every sector comes with its own integration constraints. Discover how M519 adapts to your use case, from access control to industrial applications.',
			),
		),
		array(
			'slug'     => 'bureau-etudes',
			'template' => 'page-bureau-etudes.php',
			'fr'       => array(
				'title'   => "Bureau d'études",
				'slug'    => 'bureau-etudes',
				'content' => "Autour du module OEM SpringSeed M519 et d'un savoir-faire tourné vers la haute sécurité et les performances, notre bureau d'études conçoit des lecteurs d'identification et de contrôle d'accès adaptés à votre produit, à vos protocoles et à vos contraintes industrielles.\n\nNous intervenons là où une expertise spécialisée fait gagner du temps : intégration électronique, antenne RFID HF/NFC, firmware, Linux embarqué, cartes à puce, SAM et secure elements, conformité réglementaire (CRA, RED).",
			),
			'en'       => array(
				'title'   => 'Engineering Office',
				'slug'    => 'engineering-office',
				'content' => "Built around the SpringSeed M519 OEM module and expertise focused on high security and performance, our engineering office develops identification and access control readers tailored to your product, your protocols and your industrial constraints.\n\nWe step in wherever specialised expertise saves time: electronic integration, RFID HF/NFC antenna, firmware, embedded Linux, smart cards, SAM and secure elements, regulatory compliance (CRA, RED).",
			),
		),
		array(
			'slug'     => 'a-propos',
			'template' => 'page-a-propos.php',
			'fr'       => array(
				'title'   => 'À propos',
				'slug'    => 'a-propos',
				'content' => "SpringCard conçoit et fabrique en France des modules RFID/NFC OEM depuis plus de 20 ans. Aujourd'hui recentrée sur la gamme M519 et son bureau d'études, l'entreprise accompagne ses clients de l'intégration standard au développement sur mesure.",
			),
			'en'       => array(
				'title'   => 'About',
				'slug'    => 'about',
				'content' => 'SpringCard has been designing and manufacturing OEM RFID/NFC modules in France for over 20 years. Now focused on the M519 range and its engineering office, the company supports its clients from standard integration through to fully custom development.',
			),
		),
	);
	$page_ids = array();
	foreach ( $pages as $p ) {
		$ids = springcard_seed_bilingual( 'page', $p['fr'], $p['en'] );
		foreach ( $ids as $lang => $id ) {
			update_post_meta( $id, '_wp_page_template', $p['template'] );
		}
		$page_ids[ $p['slug'] ] = $ids;
	}


	// Pages légales : françaises uniquement pour l'instant (le contenu
	// juridique n'a pas d'équivalent anglais validé) — on leur assigne quand
	// même la langue FR explicitement, sinon Polylang les masque de tout
	// listing filtré par langue (footer, etc.) une fois le plugin actif.
	$legal_pages = array(
		array(
			'title'    => 'Copyright',
			'slug'     => 'copyright',
			'template' => 'page-legal.php',
			'content'  => "<h2>Site web</h2>\n<p>Tous les éléments présents sur le site web de SpringCard (documents, images, textes, logiciels…) sont protégés par les droits d'auteur de SpringCard et/ou de ses fournisseurs et/ou de ses clients, selon les dispositions légales en vigueur en France et dans les autres pays.</p>\n<p>SpringCard autorise la copie et l'utilisation de ces éléments à condition que chaque copie soit exclusivement destinée à un usage informatif et non commercial en relation avec ses produits, qu'elle ne soit ni modifiée ni révisée de quelque manière que ce soit, et qu'elle conserve tous les avertissements sur les droits réservés dans une forme identique au document d'origine.</p>\n<p>Cette autorisation n'inclut pas la conception ou la présentation de ce site web, ni tout autre élément téléchargeable depuis ce site mais régi par les stipulations d'un contrat de licence spécifique.</p>\n<p>Pour toute information, n'hésitez pas à <a href=\"/a-propos/#contact\">nous contacter</a>.</p>\n\n<h2>Marques déposées</h2>\n<p>Tous les noms de produits ou de sociétés mentionnés sur ces pages peuvent être des marques déposées appartenant à leurs propriétaires respectifs.</p>\n\n<h2>Logiciels, éléments sous licence</h2>\n<p>Tous les éléments sous licence (tels que les logiciels, kits de développement ou firmwares de produits) que vous téléchargez à partir de ce site sont exclusivement régis par les termes du contrat de licence qui les accompagne, et leur téléchargement implique que vous acceptez ces termes. Toute reproduction ou redistribution non conforme à ces stipulations est expressément interdite par la loi.</p>\n\n<h2>Liens</h2>\n<p>SpringCard autorise les liens vers ce site web à condition que le site qui les propose :</p>\n<ul>\n<li>dirige vers un contenu de ce site sans le copier ;</li>\n<li>ne crée pas d'environnement ou de cadre (frame) autour de ce contenu ;</li>\n<li>ne fournisse pas d'informations erronées ou mensongères sur les produits et services de SpringCard ;</li>\n<li>ne donne pas une image trompeuse du rapport entre SpringCard et le créateur du site ;</li>\n<li>ne sous-entende pas que SpringCard cautionne ou soutient ses services et produits ;</li>\n<li>n'utilise pas les logos ou l'image commerciale de SpringCard sans accord écrit préalable ;</li>\n<li>ne présente pas de contenu obscène, diffamatoire, ou contraire à la loi française.</li>\n</ul>\n<p>SpringCard se réserve le droit de demander la suppression de ce lien à tout moment.</p>",
		),
		array(
			'title'    => 'Informations légales',
			'slug'     => 'informations-legales',
			'template' => 'page-legal.php',
			'content'  => "<h2>À propos du contenu de ce site</h2>\n<p>Toutes les informations contenues sur ce site web sont mises à disposition des utilisateurs à titre indicatif. Elles ne peuvent en aucun cas être interprétées comme une offre commerciale, une licence, un conseil ou une relation professionnelle entre l'utilisateur et SpringCard. Le contenu fourni sur ce site n'exonère pas l'utilisateur de procéder par lui-même au contrôle de l'information fournie.</p>\n<p>Il est précisé que le contenu de ce site web peut faire référence à des produits ou services qui ne sont pas disponibles dans tous les pays.</p>\n\n<h2>Utilisation des données</h2>\n<p>Les données que vous nous confiez peuvent être modifiées, supprimées et restituées à votre demande. Chaque donnée enregistrée dans nos bases n'est et ne sera jamais vendue à un tiers ou à des partenaires.</p>\n\n<h2>Garanties et responsabilités</h2>\n<p>Le contenu du site web de SpringCard — y compris les logiciels et documents pouvant y être téléchargés — est fourni « en l'état », sans garantie d'aucune sorte, ni expresse ni tacite, autre que celle prévue par la loi en vigueur, et notamment sans garantie que le contenu réponde aux besoins de l'utilisateur ni qu'il soit à jour.</p>\n<p>Bien que SpringCard s'efforce de fournir un contenu fiable, nous ne pouvons garantir qu'il soit exempt d'inexactitudes, d'erreurs typographiques ou d'omissions. SpringCard se réserve à tout moment et sans préavis le droit d'apporter des améliorations et/ou des modifications au contenu de son site web.</p>\n<p>SpringCard ne pourra être tenue responsable des dommages indirects résultant de l'usage de ce site web ou d'autres sites qui lui sont liés, notamment et sans limitation, tout préjudice financier ou commercial, ou perte de données, même si SpringCard a eu connaissance de la possibilité de survenance de tels dommages.</p>\n\n<h2>Informations éditeur</h2>\n<p>Ce site web est édité par SPRINGCARD SAS, 2 voie La Cardon, 91120 Palaiseau, France.<br>\nR.C.S. Évry B 429 665 482 — Code APE 722 Z</p>\n<p>Pour toute information, n'hésitez pas à <a href=\"/a-propos/#contact\">nous contacter</a>.</p>\n\n<h2>Conditions générales de vente</h2>\n<p>Vous pouvez consulter nos conditions générales de vente dans ce <a href=\"https://files.springcard.com/pub/%5BvcgZ011-cb%5D_condition_generales_de_ventes_FR.pdf\" target=\"_blank\" rel=\"noopener noreferrer\">document</a>.</p>\n\n<h2>Commander chez SpringCard</h2>\n<p>Nos clients en compte peuvent commander par téléphone, e-mail, fax ou courrier. Votre commande sera confirmée et traitée dans les délais les plus brefs.</p>\n<p>Notre accueil téléphonique est ouvert de 9h00 à 18h00, du lundi au vendredi.</p>\n\n<h3>Tarifs et devis</h3>\n<p>Les tarifs et délais annoncés dans nos devis sont valides 30 jours, sauf exception précisée. Nos tarifs sont normalement établis en euros, mais vous pouvez demander un devis dans la devise de votre choix.</p>\n<p>Nos clients enregistrés peuvent demander un devis (ou une facture pro-forma) en <a href=\"/a-propos/#contact\">contactant notre service commercial</a>.</p>\n\n<h3>Commander</h3>\n<p>Toute commande est soumise à nos <a href=\"https://files.springcard.com/pub/%5BvcgZ011-cb%5D_condition_generales_de_ventes_FR.pdf\" target=\"_blank\" rel=\"noopener noreferrer\">conditions générales de vente</a>. Les délais de livraison varient selon le produit et la quantité commandée ; le devis ou l'offre de prix précise toujours ce délai.</p>\n\n<h3>Transport</h3>\n<p>Nous privilégions, dans la mesure du possible, la livraison via votre transporteur habituel, sur votre propre compte chez lui. Merci de nous préciser toutes les informations requises lors de la commande.</p>\n\n<h3>Conditions de paiement</h3>\n<p>Nos conditions de paiement normales sont à 30 jours nets, pour nos comptes clients confirmés. Merci de vous assurer que votre paiement nous parvienne dans la devise prévue, sans déduction de frais de change ou de transfert.</p>\n<p>Pour les clients pas encore en compte ou pour les achats individuels, nous acceptons les paiements par virement SWIFT en euros ou en dollars US, ou par chèque bancaire d'une banque française. Dans ces deux cas, le paiement est comptant à la commande.</p>",
		),
		array(
			'title'    => 'Politique de confidentialité',
			'slug'     => 'politique-de-confidentialite',
			'template' => 'page-legal.php',
			'content'  => "<h2>Notre engagement</h2>\n<p>SpringCard respecte votre vie privée conformément à la réglementation française et européenne (RGPD). Notre site est conçu pour que vous puissiez le consulter et accéder à la majorité de son contenu sans avoir à nous transmettre de données personnelles.</p>\n\n<h2>Données collectées</h2>\n<p>Nous ne disposons que des informations que vous choisissez volontairement de nous transmettre, notamment via nos formulaires ou par courrier électronique, dans le cadre d'une relation professionnelle normale. Ces informations ne visent qu'à nous permettre de vous offrir un service de qualité — par exemple en vous fournissant un support technique pertinent ou en vous tenant informé de l'évolution de nos produits.</p>\n<p>Ces données ne sont jamais vendues à un tiers. Elles sont strictement réservées à SpringCard et, le cas échéant, à ses sous-traitants ou revendeurs agissant pour son compte, sauf accord explicite de votre part ou obligation légale.</p>\n\n<h2>Cookies</h2>\n<p>Ce site n'utilise actuellement aucun cookie de mesure d'audience ni traceur publicitaire. Cette politique sera mise à jour si des outils de suivi venaient à être ajoutés, avec le recueil de votre consentement préalable conformément aux recommandations de la CNIL.</p>\n\n<h2>Vos droits</h2>\n<p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression des informations vous concernant. Pour exercer ce droit, contactez-nous via notre <a href=\"/a-propos/#contact\">formulaire de contact</a>.</p>\n\n<h2>Sécurité</h2>\n<p>SpringCard protège dans la mesure du possible les informations que vous lui confiez contre la consultation ou la modification par des tiers non autorisés. L'Internet étant un espace naturellement ouvert et non sécurisé, SpringCard ne peut néanmoins garantir une protection absolue des informations transmises.</p>\n<p>Pour en savoir plus sur notre politique de protection des données, vous pouvez consulter notre <a href=\"https://www.springcard.com/uploads/chartes/%5BRGPD%5D_charte_donnees_clients.pdf\" target=\"_blank\" rel=\"noopener noreferrer\">charte RGPD</a>.</p>",
		),
	);
	// Migration : l'ancienne page "Mentions légales" (qui combinait Copyright
	// et Informations légales en un seul texte) est remplacée par les deux
	// pages distinctes ci-dessus, qui reprennent la structure réelle du site
	// actuel. On la met à la corbeille plutôt que de la laisser orpheline.
	$old_mentions_legales = get_page_by_path( 'mentions-legales' );
	if ( $old_mentions_legales ) {
		wp_trash_post( $old_mentions_legales->ID );
	}

	foreach ( $legal_pages as $p ) {
		$existing = get_page_by_path( $p['slug'] );
		if ( $existing ) {
			$id = $existing->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_status'  => 'publish',
					'post_content' => $p['content'],
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $p['template'] );
			if ( function_exists( 'pll_set_post_language' ) && ! pll_get_post_language( $id ) ) {
				pll_set_post_language( $id, 'fr' );
			}
		}
	}

	// Gamme M519 — l'excerpt/le contenu EN reprend le sous-titre et l'intro réels
	// du produit M519 sur springcard.com (avant sa scission en 3 variantes ici).
	$gamme_ids = springcard_seed_bilingual(
		'gamme',
		array(
			'title'   => 'SpringSeed M519',
			'slug'    => 'm519',
			'excerpt' => "Module RFID/NFC OEM haut de gamme et polyvalent, décliné en trois configurations selon votre besoin d'intégration.",
			'content' => "M519 est un module de lecture RFID/NFC 13.56 MHz conçu pour être intégré directement dans vos machines et équipements. Trois configurations d'antenne (externe, déportée ou intégrée) permettent d'adapter le module à votre boîtier, sans jamais reconcevoir votre produit.",
		),
		array(
			'title'   => 'SpringSeed M519',
			'slug'    => 'm519',
			'excerpt' => 'High-end, versatile OEM RFID/NFC module, available in three configurations to match your integration needs.',
			'content' => "SpringSeed M519 is a 13.56 MHz RFID/NFC reader module designed to be integrated directly into your machines and equipment. Three antenna configurations (external, remote or integrated) let you adapt the module to your housing, without ever having to redesign your product.",
		)
	);
	$gamme_id = isset( $gamme_ids['fr'] ) ? $gamme_ids['fr'] : 0;

	// Sideloaded once (checked against the FR post) and reused for both language
	// posts, rather than re-uploading the same file once per language.
	$hero_id = has_post_thumbnail( $gamme_id ) ? get_post_thumbnail_id( $gamme_id ) : springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-hero-web.jpg' ), 'M519' );
	$fab_id  = get_post_meta( $gamme_id, '_visuel_fabrication_id', true );
	if ( ! $fab_id ) {
		$fab_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-fabrication-web.jpg' ), 'M519 fabrication' );
	}
	$kit_id = get_post_meta( $gamme_id, '_visuel_kit_id', true );
	if ( ! $kit_id ) {
		$kit_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-kit-web.jpg' ), 'M519 kit de développement' );
	}
	foreach ( $gamme_ids as $lang => $id ) {
		update_post_meta( $id, '_statut', 'actif' );

		if ( $hero_id && ! has_post_thumbnail( $id ) ) {
			set_post_thumbnail( $id, $hero_id );
		}
		if ( $fab_id && ! get_post_meta( $id, '_visuel_fabrication_id', true ) ) {
			update_post_meta( $id, '_visuel_fabrication_id', $fab_id );
		}
		if ( $kit_id && ! get_post_meta( $id, '_visuel_kit_id', true ) ) {
			update_post_meta( $id, '_visuel_kit_id', $kit_id );
		}
	}

	// Variantes (produit) de la gamme M519 — FR repris de springcard.com/fr/products/,
	// EN repris tel quel de springcard.com/en/products/ (m519, m519-suv, m519-sam-u-b).
	$produits_seed = array(
		array(
			'slug'         => 'm519',
			'type_antenne' => 'non_fournie',
			'image'        => 'm519-thumbnail-web.jpg',
			'fr'           => array(
				'title'   => 'M519',
				'slug'    => 'm519',
				'excerpt' => 'Module OEM RFID/NFC HF compact, à intégrer avec une antenne externe. Compatible cartes sans contact, tags NFC et smartphones (Apple/Google Wallet).',
			),
			'en'           => array(
				'title'   => 'M519',
				'slug'    => 'm519',
				'excerpt' => 'Compact OEM RFID/NFC HF module, designed to be integrated with an external antenna. Compatible with contactless cards, NFC tags and smartphones (Apple/Google Wallet).',
			),
			'specs_fr'     => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : externe, non fournie\nInterface : série ou coupleur USB\nModes : PC/SC coupleur, SpringProx Legacy",
			'specs_en'     => "Frequency: 13.56 MHz (HF RFID, NFC), NXP PN5190 chip\nRF standards: ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenna: external, not supplied\nInterface: serial or USB coupler\nModes: PC/SC coupler, SpringProx Legacy",
		),
		array(
			'slug'         => 'm519-sam',
			'type_antenne' => 'separee',
			'image'        => '',
			'fr'           => array(
				'title'   => 'M519-SAM',
				'slug'    => 'm519-sam',
				'excerpt' => "Disponible en deux versions : SAM(B) (antenne déportée symétrique) et SAM(U) (antenne déportée asymétrique), toutes deux avec un slot SAM intégré pour une sécurité matérielle renforcée.",
			),
			'en'           => array(
				'title'   => 'M519-SAM',
				'slug'    => 'm519-sam',
				'excerpt' => 'Available in two versions: SAM(B) (symmetric remote antenna) and SAM(U) (asymmetric remote antenna), both with an integrated SAM slot for enhanced hardware security.',
			),
			'specs_fr'     => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : déportée (symétrique SAM(B) / asymétrique SAM(U))\nInterface : USB PC/SC (CCID), identique entre SAM(B) et SAM(U)\nModes : PC/SC coupleur, émulation carte ISO/IEC 14443 A, peer-to-peer ISO/IEC 18092\nSécurité : slot SAM intégré (NXP TDA8035, ISO/IEC 7816-2 & -3, T=0/T=1)",
			'specs_en'     => "Frequency: 13.56 MHz (HF RFID, NFC), NXP PN5190 chip\nRF standards: ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenna: remote (symmetric SAM(B) / asymmetric SAM(U))\nInterface: USB PC/SC (CCID), identical between SAM(B) and SAM(U)\nModes: PC/SC coupler, ISO/IEC 14443 A card emulation, ISO/IEC 18092 peer-to-peer\nSecurity: integrated SAM slot (NXP TDA8035, ISO/IEC 7816-2 & -3, T=0/T=1)",
		),
		array(
			'slug'         => 'm519-suv',
			'type_antenne' => 'integree',
			'image'        => 'm519-suv-thumbnail-web.jpg',
			'fr'           => array(
				'title'   => 'M519-SUV',
				'slug'    => 'm519-suv',
				'excerpt' => 'Module coupleur à antenne intégrée, interfaces USB et série, pour des transactions rapides et sécurisées (AES/ECC).',
			),
			'en'           => array(
				'title'   => 'M519-SUV',
				'slug'    => 'm519-suv',
				'excerpt' => 'Coupler module with integrated antenna, USB and serial interfaces, for fast and secure transactions (AES/ECC).',
			),
			'specs_fr'     => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : intégrée, symétrique, diamètre 7 cm (portée 0–10 cm selon carte/antenne)\nInterface : USB PC/SC (CCID), série RS232/RS485/TTL\nModes : PC/SC coupleur, émulation carte ISO/IEC 14443 A, peer-to-peer ISO/IEC 18092\nSécurité : AES/ECC, stockage de clés sécurisé, composant sécurisé Microchip ATECC",
			'specs_en'     => "Frequency: 13.56 MHz (HF RFID, NFC), NXP PN5190 chip\nRF standards: ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenna: integrated, symmetric, 7 cm diameter (0-10 cm range depending on card/antenna)\nInterface: USB PC/SC (CCID), RS232/RS485/TTL serial\nModes: PC/SC coupler, ISO/IEC 14443 A card emulation, ISO/IEC 18092 peer-to-peer\nSecurity: AES/ECC, secure key storage, Microchip ATECC secure component",
		),
	);
	$produit_ids = array();
	foreach ( $produits_seed as $p ) {
		$ids = springcard_seed_bilingual( 'produit', $p['fr'], $p['en'] );

		$fr_id  = isset( $ids['fr'] ) ? $ids['fr'] : 0;
		$img_id = $p['image'] && ! has_post_thumbnail( $fr_id )
			? springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/' . $p['image'] ), $p['fr']['title'] )
			: ( $fr_id ? get_post_thumbnail_id( $fr_id ) : 0 );

		foreach ( $ids as $lang => $id ) {
			update_post_meta( $id, '_statut', 'actif' );
			update_post_meta( $id, '_gamme_id', isset( $gamme_ids[ $lang ] ) ? $gamme_ids[ $lang ] : $gamme_id );
			update_post_meta( $id, '_type_antenne', $p['type_antenne'] );
			update_post_meta( $id, '_specs', 'en' === $lang ? $p['specs_en'] : $p['specs_fr'] );

			if ( $img_id && ! has_post_thumbnail( $id ) ) {
				set_post_thumbnail( $id, $img_id );
			}
		}
		$produit_ids[ $p['slug'] ] = $ids;
	}

	// Secteurs — catégorisation propre au nouveau site (pas d'équivalent sur
	// l'ancien springcard.com), traduction fidèle du FR approuvé.
	$secteurs_seed = array(
		array(
			'icone' => 'dashicons-car',
			'fr'    => array( 'title' => 'Mobilité', 'slug' => 'mobilite', 'excerpt' => "Titres de transport, contrôle d'accès véhicules et bornes de validation sans contact." ),
			'en'    => array( 'title' => 'Mobility', 'slug' => 'mobility', 'excerpt' => 'Transport tickets, vehicle access control and contactless validation terminals.' ),
		),
		array(
			'icone' => 'dashicons-plus-alt',
			'fr'    => array( 'title' => 'Santé', 'slug' => 'sante', 'excerpt' => 'Cartes professionnelles et lecteurs sécurisés pour les professionnels de santé en mobilité.' ),
			'en'    => array( 'title' => 'Healthcare', 'slug' => 'healthcare', 'excerpt' => 'Professional cards and secure readers for healthcare professionals on the move.' ),
		),
		array(
			'icone' => 'dashicons-tickets-alt',
			'fr'    => array( 'title' => 'Loisirs', 'slug' => 'loisirs', 'excerpt' => "Billetterie et contrôle d'accès pour parcs, salles de spectacle et stades." ),
			'en'    => array( 'title' => 'Leisure', 'slug' => 'leisure', 'excerpt' => 'Ticketing and access control for parks, venues and stadiums.' ),
		),
		array(
			'icone' => 'dashicons-cart',
			'fr'    => array( 'title' => 'Retail', 'slug' => 'retail', 'excerpt' => 'Programmes de fidélité et paiement sans contact en point de vente.' ),
			'en'    => array( 'title' => 'Retail', 'slug' => 'retail', 'excerpt' => 'Loyalty programmes and contactless payment at the point of sale.' ),
		),
		array(
			'icone' => 'dashicons-shield',
			'fr'    => array( 'title' => 'Sécurité', 'slug' => 'securite', 'excerpt' => "Badges et lecteurs pour le contrôle d'accès aux bâtiments et zones sensibles." ),
			'en'    => array( 'title' => 'Security', 'slug' => 'security', 'excerpt' => 'Badges and readers for access control to buildings and sensitive areas.' ),
		),
		array(
			'icone' => 'dashicons-archive',
			'fr'    => array( 'title' => 'Logistique', 'slug' => 'logistique', 'excerpt' => 'Traçabilité des flux et identification des colis sur toute la chaîne logistique.' ),
			'en'    => array( 'title' => 'Logistics', 'slug' => 'logistics', 'excerpt' => 'Flow traceability and parcel identification across the entire logistics chain.' ),
		),
	);
	$secteur_ids = array();
	foreach ( $secteurs_seed as $s ) {
		$ids = springcard_seed_bilingual( 'secteur', $s['fr'], $s['en'] );
		foreach ( $ids as $lang => $id ) {
			update_post_meta( $id, '_icone', $s['icone'] );
		}
		$secteur_ids[ $s['fr']['slug'] ] = $ids;
	}

	// Cas d'usage — repris de springcard.com/fr/blog/news et de sa version
	// anglaise /en/blog/news (AFCare / Doctolib, lecteur "DoctoLecteur"/"DoctoReader").
	$cas_ids = springcard_seed_bilingual(
		'cas_usage',
		array(
			'title'   => 'Comment SpringCard a accompagné AFCare et Doctolib dans une solution de mobilité pour les professionnels de santé',
			'slug'    => 'afcare-doctolib-mobilite-sante',
			'excerpt' => "SpringCard a conçu sur mesure le DoctoLecteur, un lecteur de carte à puce PC/SC et Bluetooth pour les professionnels de santé, aujourd'hui utilisé par près de 600 praticiens avec AFCare et Doctolib.",
			'content' => "<p>Notre client François Sendra, Co-fondateur en 2017 de la start up AFCare est une entreprise qui accompagne les éditeurs de logiciels pour les professionnels de santé libéraux (médecins, infirmières, kinés, etc) qui souhaitent développer leur projet de mobilité lié au SESAM-Vitale.</p>\n<p>Dans un projet mené pour Doctolib, AFCare s'est tourné vers SpringCard pour concevoir un lecteur de carte à puce PC/SC et Bluetooth, répondant à des problématiques d'usage et de sécurité, dont l'intelligence est pilotée par une application mobile sous iOS et Android.</p>\n<p>Les principaux challenges rencontrés par AFCare ont notamment été de répondre aux différentes exigences d'usage et de sécurité des professionnels de santé, soit :</p>\n<ul>\n<li>un lecteur bi-fentes qui sache lire les cartes vitales ainsi que les cartes CPS,</li>\n<li>un lecteur qui puisse fonctionner en USB comme en Bluetooth,</li>\n<li>un lecteur de petite taille et léger, facilement transportable avec le plus d'autonomie possible,</li>\n<li>un lecteur sécurisé pour l'encryption des données BLE,</li>\n<li>un lecteur homologué et répondant aux besoins de sécurité du GIE SESAM-Vitale.</li>\n</ul>\n<p>C'est en étroite collaboration avec notre bureau R&amp;D que le lecteur a été conçu sur mesure et testé dans son environnement pendant plusieurs mois jusqu'à arriver à une solution parfaitement adaptée.</p>\n<p>Une fois ces étapes réalisées, le DoctoLecteur a été redesigné pour répondre à la guideline des produits Doctolib.</p>\n<p>Le lecteur est aujourd'hui utilisé par près de 600 professionnels de santé, qui en sont équipés à la fois, en cabinet en mode PC/SC, mais aussi et surtout, en mobilité grâce à son mode BLE, pour les médecins généralistes qui effectuent des visites à domicile.</p>\n<p>Depuis plus d'un an après sa commercialisation, les retours des médecins sont très positifs, aucune correction n'a été nécessaire tant au niveau électronique qu'au niveau firmware.</p>\n<p>D'après François Sendra : « Les prévisions sont à la hausse pour 2022 et 2023, avec pour stratégie d'étendre le réseau du DoctoLecteur aux kinésithérapeutes et aux infirmier(e)s. »</p>\n<p>La preuve d'un projet rondement mené par les équipes Doctolib, AFCare et SpringCard.</p>\n<p>AFCare s'est tourné vers SpringCard pour son savoir-faire de plus de 20 ans dans la conception de produits électroniques et pour la qualité de ses services.</p>\n<p>Dès les premiers échanges avec l'équipe du bureau d'étude, François Sendra raconte comment les équipes SpringCard ont su s'adapter pour répondre aux problématiques d'un produit sur mesure : « Une approche professionnelle dans le livrable tout en étant agile sur le développement du projet. »</p>\n<p>A ce jour, AFCare a été rachetée par Doctolib. SpringCard continue de collaborer avec son fidèle partenaire François Sendra, lui-même Directeur de l'Ingénierie Hardware chez Doctolib.</p>",
		),
		array(
			'title'   => 'How SpringCard supported AFCare and Doctolib with a mobility solution for healthcare professionals',
			'slug'    => 'afcare-doctolib-healthcare-mobility',
			'excerpt' => 'SpringCard custom-designed the DoctoReader, a PC/SC and Bluetooth smart card reader for healthcare professionals, now used by nearly 600 practitioners with AFCare and Doctolib.',
			'content' => "<p>Our client François Sendra, co-founder in 2017 of the AFCare start-up, runs a company that supports software publishers serving self-employed health professionals (doctors, nurses, physiotherapists, etc.) who want to develop a mobility project linked to SESAM-Vitale.</p>\n<p>In a project carried out for Doctolib, AFCare turned to SpringCard to design a PC/SC and Bluetooth smart card reader, addressing usage and security requirements, with its intelligence driven by a mobile application on iOS and Android.</p>\n<p>The main challenges for AFCare were to meet the different usage and security requirements of healthcare professionals, namely:</p>\n<ul>\n<li>a two-slot reader able to read Vitale cards as well as CPS cards,</li>\n<li>a reader that works over both USB and Bluetooth,</li>\n<li>a small, light reader, easy to carry with as much autonomy as possible,</li>\n<li>a secure reader for the encryption of BLE data,</li>\n<li>an approved reader meeting the security requirements of GIE SESAM-Vitale.</li>\n</ul>\n<p>It was in close collaboration with our R&amp;D office that the reader was custom-designed and tested in its environment for several months, until reaching a perfectly adapted solution.</p>\n<p>Once these steps were completed, the DoctoReader was redesigned to meet Doctolib's product guidelines.</p>\n<p>The reader is now used by nearly 600 healthcare professionals, equipped both in the office in PC/SC mode and, above all, on the move thanks to its BLE mode, for general practitioners making home visits.</p>\n<p>More than a year after its commercial launch, feedback from doctors has been very positive, with no correction needed at either the electronic or firmware level.</p>\n<p>According to François Sendra: “The forecasts are trending upward for 2022 and 2023, with a strategy to extend the DoctoReader network to physiotherapists and nurses.”</p>\n<p>Proof of a project smoothly delivered by the Doctolib, AFCare and SpringCard teams.</p>\n<p>AFCare turned to SpringCard for its 20-plus years of expertise in designing electronic products and for the quality of its services.</p>\n<p>From the very first exchanges with our engineering office team, François Sendra describes how the SpringCard teams adapted to meet the challenges of a custom product: “A professional approach to the deliverable while staying agile on the project's development.”</p>\n<p>AFCare has since been acquired by Doctolib. SpringCard continues to collaborate with its loyal partner François Sendra, now Director of Hardware Engineering at Doctolib.</p>",
		)
	);
	$cas_fr_id  = isset( $cas_ids['fr'] ) ? $cas_ids['fr'] : 0;
	$cas_img_id = has_post_thumbnail( $cas_fr_id )
		? get_post_thumbnail_id( $cas_fr_id )
		: springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/cas-usage/doctolib-afcare.png' ), 'DoctoLecteur (AFCare / Doctolib)' );

	foreach ( $cas_ids as $lang => $cas_id ) {
		update_post_meta( $cas_id, '_client', 'AFCare / Doctolib' );

		if ( ! empty( $secteur_ids['sante'][ $lang ] ) ) {
			update_post_meta( $cas_id, '_secteurs', array( $secteur_ids['sante'][ $lang ] ) );
		}

		if ( $cas_img_id && ! has_post_thumbnail( $cas_id ) ) {
			set_post_thumbnail( $cas_id, $cas_img_id );
		}
	}

	// Autres cas d'usage repris de l'ancien blog de springcard.com, traduits en
	// français. Contrairement à la plupart des anciennes études de cas — qui
	// mettent en avant un produit aujourd'hui abandonné (CrazyWriter, K663,
	// Prox'N'Drive, PUCK...) et ne peuvent donc pas être reprises sans travestir
	// la réalité technique — ces deux-là racontent un partenariat ou la
	// réalisation du client sans s'appuyer sur un modèle SpringCard précis.
	$cas_usage_seed = array(
		array(
			'slug'         => "itn-international-identification-sans-contact-evenementiel",
			'title'        => "ITN International : l'identification sans contact au service de l'événementiel",
			'excerpt'      => "Depuis ses débuts avec un lecteur compact flash jusqu'à l'identification sans contact généralisée, comment ITN International s'appuie sur SpringCard pour équiper les salons professionnels.",
			'content'      => "<p>Fondée par Ivan Lazarev en 1999, ITN International s'est fait un nom en révolutionnant l'identification professionnelle grâce aux technologies sans contact. L'entreprise a connu une évolution remarquable, passant des cartes magnétiques aux cartes à puce, puis aux cartes sans contact (NFC/RFID).</p>
	<p>La rencontre entre ITN et SpringCard remonte aux premiers temps de l'entreprise : Ivan Lazarev découvre alors l'un des tout premiers lecteurs à contact au format compact flash (CF) de SpringCard, conçu pour s'intégrer dans des assistants numériques (PDA). Cette innovation transforme l'expérience client d'ITN sur les salons professionnels : grâce à ce lecteur, l'entreprise propose une approche inédite pour l'identification des visiteurs, intégrée directement dans les PDA, qui fait gagner un temps précieux pendant ces événements.</p>
	<p>D'abord concentrée sur les cartes à puce « à contact », ITN fait le choix stratégique, en 2004, de basculer vers le sans contact, à l'occasion d'un salon dédié au Bluetooth. Ce succès propulse naturellement ITN comme spécialiste de l'identification sans contact sur les salons professionnels. Un partenariat déterminant avec Philips (devenu NXP) leur ouvre alors de nouvelles perspectives, notamment vers les cartes de transport et de paiement sans contact.</p>
	<p>Pour rester leader sur ce marché très exigeant en réactivité et en délais, ITN s'appuie sur des lecteurs sans contact SpringCard fiables et sur un support technique réactif, garantissant la continuité de service pour les exposants pendant toute la durée des salons. En quête de solutions toujours plus qualitatives, ITN associe également les lecteurs sans contact SpringCard aux imprimantes Evolis, permettant d'éditer des badges d'identification à la fois rapides à produire et agréables à utiliser.</p>
	<p>« C'est un domaine très spécifique, mais les gens ne réalisent pas à quel point toutes les entreprises professionnelles du monde entier participent à des salons. »</p>
	<p>ITN International a été un véritable pionnier dans la démocratisation des technologies sans contact, contribuant à les diffuser auprès d'usagers très divers : salons du bâtiment, de la technologie, du secteur médical, et bien d'autres encore.</p>",
			'client'       => "ITN International",
			'secteur_slug' => "loisirs",
		),
		array(
			'slug'         => "thinfilm-sous-bock-nfc-engagement-consommateurs",
			'title'        => "ThinFilm : un sous-bock NFC pour engager les consommateurs",
			'excerpt'      => "Comment notre client norvégien ThinFilm Electronics a développé, pour le brasseur Coronado Brewing Co., un sous-bock NFC générant un taux de conversion jusqu'à 17,5 % supérieur à leurs autres canaux marketing.",
			'content'      => "<p>Notre client norvégien ThinFilm Electronics ASA, leader mondial du marketing mobile NFC, a développé un sous-bock de bière équipé de puces NFC pour le brasseur californien Coronado Brewing Co.</p>
	<p>Le marché de la bière est extrêmement concurrentiel : plus de 3 900 nouveaux produits sont lancés chaque année sur le seul marché américain. Il devient alors difficile de se démarquer et de capter l'attention des consommateurs. Coronado Brewing Co. a choisi la technologie NFC pour sortir du lot et renforcer l'engagement de ses clients.</p>
	<p>ThinFilm a conçu des sous-bocks intégrant ses tags SpeedTap™ : d'un simple tapotement de smartphone, le client accède à une page web dédiée à une nouvelle bière traditionnelle, la CoastWise IPA Session. Cette page met en avant le partenariat avec l'ONG Surfrider, à laquelle une partie des revenus de la vente de cette bière est reversée.</p>
	<p>La conception du produit intégrait dès le départ le besoin de mesurer précisément son impact : chaque sous-bock avait un cycle de vie d'une semaine, chaque boîte de sous-bocks une durée de vie moyenne de 35 jours, et l'ensemble a été distribué sur une période d'un mois.</p>
	<p>Les résultats ont été à la hauteur des attentes : alors que les campagnes habituelles de Coronado enregistrent un taux de clic de 0,2 %, cette nouvelle campagne a atteint une moyenne de 1,65 % (avec un pic à 2,24 %). Comparé aux autres canaux marketing de Coronado, le taux de conversion s'est révélé supérieur de 13 à 17,5 %.</p>
	<p>De nombreux clients ont ramené leur sous-bock chez eux et demandé des informations pour en acheter. Coronado envisage depuis de reconfigurer les sous-bocks restants pour rediriger directement vers sa page e-commerce.</p>",
			'client'       => "ThinFilm Electronics ASA",
			'secteur_slug' => null,
		),
	);
	foreach ( $cas_usage_seed as $cu ) {
		$existing_cu = get_page_by_path( $cu['slug'], OBJECT, 'cas_usage' );
		if ( $existing_cu ) {
			$cu_id = $existing_cu->ID;
		} else {
			$cu_id = wp_insert_post(
				array(
					'post_type'    => 'cas_usage',
					'post_title'   => $cu['title'],
					'post_name'    => $cu['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $cu['excerpt'],
					'post_content' => $cu['content'],
				)
			);
		}
		if ( $cu_id && ! is_wp_error( $cu_id ) ) {
			if ( function_exists( 'pll_set_post_language' ) && ! pll_get_post_language( $cu_id ) ) {
				pll_set_post_language( $cu_id, 'fr' );
			}
			update_post_meta( $cu_id, '_client', $cu['client'] );
			if ( $cu['secteur_slug'] && ! empty( $secteur_ids[ $cu['secteur_slug'] ]['fr'] ) ) {
				update_post_meta( $cu_id, '_secteurs', array( $secteur_ids[ $cu['secteur_slug'] ]['fr'] ) );
			}
			if ( ! get_post_meta( $cu_id, 'rank_math_title', true ) ) {
				update_post_meta( $cu_id, 'rank_math_title', $cu['title'] . ' | SpringCard' );
			}
			if ( ! get_post_meta( $cu_id, 'rank_math_description', true ) ) {
				update_post_meta( $cu_id, 'rank_math_description', $cu['excerpt'] );
			}
		}
	}

	// Expertises (bureau d'études) — contenu fourni par le directeur technique,
	// sans équivalent sur l'ancien site : traduction fidèle du FR approuvé.
	$expertises_seed = array(
		array(
			'code' => 'DP',
			'fr'   => array( 'title' => 'Développement produit', 'slug' => 'developpement-produit', 'excerpt' => "Intégration du module M519 dans un lecteur, un terminal ou un équipement de contrôle d'accès : électronique, firmware, mécanique, interfaces et accompagnement jusqu'au prototype industrialisable." ),
			'en'   => array( 'title' => 'Product development', 'slug' => 'product-development', 'excerpt' => "Integrating the M519 module into a reader, terminal or access control device: electronics, firmware, mechanics, interfaces and support through to an industrialisable prototype." ),
		),
		array(
			'code' => 'RF',
			'fr'   => array( 'title' => 'RFID HF & NFC', 'slug' => 'rfid-hf-nfc', 'excerpt' => "Conception, simulation, mesure et optimisation d'antennes 13,56 MHz. Mise au point de l'accord RF dans l'environnement réel du produit et préparation des essais de qualification." ),
			'en'   => array( 'title' => 'RFID HF & NFC', 'slug' => 'rfid-hf-nfc', 'excerpt' => "Design, simulation, measurement and optimisation of 13.56 MHz antennas. RF matching tuned within the product's real-world environment, and preparation of qualification testing." ),
		),
		array(
			'code' => 'CT',
			'fr'   => array( 'title' => 'Cartes & transactions sécurisées', 'slug' => 'cartes-transactions-securisees', 'excerpt' => 'Expertise en DESFire, MIFARE DUOX, MIFARE Plus, Calypso et autres cartes ISO/IEC 14443 ou ISO/IEC 15693. Conception des applications, personnalisation, gestion des clés et sécurisation des transactions.' ),
			'en'   => array( 'title' => 'Cards & secure transactions', 'slug' => 'cards-secure-transactions', 'excerpt' => 'Expertise in DESFire, MIFARE DUOX, MIFARE Plus, Calypso and other ISO/IEC 14443 or ISO/IEC 15693 cards. Application design, personalisation, key management and transaction security.' ),
		),
		array(
			'code' => 'WA',
			'fr'   => array( 'title' => 'Wallets', 'slug' => 'wallets', 'excerpt' => "Intégration des protocoles VAS, ECP1 et ECP2, SmartTap et de toutes les transactions NFC avec des passes dématérialisés ou identifiants sur mobiles. Accompagnement vers l'approbation par Apple et Google." ),
			'en'   => array( 'title' => 'Wallets', 'slug' => 'wallets', 'excerpt' => 'Integration of VAS, ECP1 and ECP2 protocols, SmartTap, and all NFC transactions with digital passes or mobile credentials. Support through to Apple and Google approval.' ),
		),
		array(
			'code' => 'PI',
			'fr'   => array( 'title' => 'Protocoles & interfaces', 'slug' => 'protocoles-interfaces', 'excerpt' => "Lecteurs et équipements connectés en OSDP ou SSCP, avec intégration PC/SC sur USB ou interfaces série lorsque le projet l'exige. Architecture des échanges, sécurité de bout en bout et interopérabilité avec le système hôte." ),
			'en'   => array( 'title' => 'Protocols & interfaces', 'slug' => 'protocols-interfaces', 'excerpt' => "Readers and equipment connected over OSDP or SSCP, with PC/SC integration over USB or serial interfaces when the project requires it. Exchange architecture, end-to-end security and interoperability with the host system." ),
		),
		array(
			'code' => 'PA',
			'fr'   => array( 'title' => "Contrôle d'accès physique (PACS)", 'slug' => 'controle-acces-physique', 'excerpt' => 'Identification sécurisée en mode transparent ou en mode autonome (smart reader), prise en compte de la sécurité physique du produit (tampers), ergonomie du lecteur complet, interopérabilité et certification SPAC.' ),
			'en'   => array( 'title' => 'Physical access control (PACS)', 'slug' => 'physical-access-control', 'excerpt' => "Secure identification in transparent or standalone (smart reader) mode, accounting for the product's physical security (tamper protection), full reader ergonomics, interoperability and SPAC certification." ),
		),
		array(
			'code' => 'FW',
			'fr'   => array( 'title' => 'Firmware, logiciels & Linux embarqué', 'slug' => 'firmware-logiciels-linux', 'excerpt' => 'Développements bas niveau sur microcontrôleur, bibliothèques C et C#, pilotes et outils côté hôte. Conception de systèmes Linux embarqués, BSP, services de communication et intégration sécurisée des périphériques.' ),
			'en'   => array( 'title' => 'Firmware, software & embedded Linux', 'slug' => 'firmware-software-embedded-linux', 'excerpt' => 'Low-level microcontroller development, C and C# libraries, host-side drivers and tools. Design of embedded Linux systems, BSPs, communication services and secure peripheral integration.' ),
		),
		array(
			'code' => 'SC',
			'fr'   => array( 'title' => 'Sécurité & conformité', 'slug' => 'securite-conformite', 'excerpt' => 'Intégration des SAM NXP, des composants CryptoAuthentication / Crypto Companion Atmel-Microchip et du secure element NXP SE052. Architecture cryptographique, démarrage et mise à jour sécurisés, gestion des secrets, SBOM et accompagnement transversal vers la conformité CRA.' ),
			'en'   => array( 'title' => 'Security & compliance', 'slug' => 'security-compliance', 'excerpt' => 'Integration of NXP SAMs, Atmel-Microchip CryptoAuthentication / Crypto Companion components and the NXP SE052 secure element. Cryptographic architecture, secure boot and updates, secrets management, SBOM, and cross-cutting support towards CRA compliance.' ),
		),
	);
	foreach ( $expertises_seed as $e ) {
		$ids = springcard_seed_bilingual( 'expertise', $e['fr'], $e['en'] );
		foreach ( $ids as $lang => $id ) {
			update_post_meta( $id, '_code', $e['code'] );
		}
	}

	// Article de blog — repris tel quel de springcard.com/fr/blog/news et de sa
	// version anglaise /en/blog/news (SpringPass).
	$article_ids = springcard_seed_bilingual(
		'article',
		array(
			'title'   => 'SpringPass : une expérience client simplifiée et fluide',
			'slug'    => 'springpass-experience-client-simplifiee',
			'excerpt' => "Avec SpringPass, stockez cartes de fidélité, billets de transport et coupons directement dans votre smartphone. Une expérience client fluide et sécurisée, appuyée sur Apple et Google Wallet.",
			'content' => "<p>Avec SpringPass, vos clients bénéficient d'une expérience utilisateur intuitive et simplifiée. Ils peuvent stocker leurs cartes de fidélité, billets de transport, coupons et autres titres numériques directement dans leur smartphone, pour les avoir toujours à portée de main.</p>\n<p><strong>Comment SpringPass apporte une valeur ajoutée à votre entreprise ?</strong></p>\n<p><strong>Fidélisation client</strong> : proposez des offres et des promotions personnalisées directement via les pass numériques, encourageant ainsi la rétention des clients.</p>\n<p><strong>Augmentation de l'engagement</strong> : offrez une expérience client fluide et sans friction, favorisant une image de marque positive et moderne.</p>\n<p><strong>Optimisation des opérations</strong> : simplifiez la gestion des pass numériques et réduisez les coûts liés aux supports physiques.</p>\n<p><strong>Données précieuses</strong> : collectez des données précieuses sur les habitudes de consommation de vos clients pour mieux cibler vos offres marketing.</p>\n<p><strong>SpringPass : une solution sécurisée et polyvalente</strong></p>\n<p>SpringPass s'appuie sur les technologies Apple et Google Wallet pour garantir la sécurité des données de vos clients.</p>\n<p>De plus, SpringPass offre une grande flexibilité d'utilisation. Vous pouvez créer une variété de pass numériques pour répondre à vos besoins spécifiques, tels que des cartes de fidélité, des billets de transport, des coupons, des cartes d'accès et bien plus encore.</p>\n<p><strong>Prêt à offrir une expérience digitale exceptionnelle à vos clients ?</strong></p>\n<p>Contactez-nous dès aujourd'hui pour découvrir comment SpringPass peut vous aider à atteindre vos objectifs commerciaux.</p>",
		),
		array(
			'title'   => 'SpringPass: a simplified, seamless customer experience',
			'slug'    => 'springpass-simplified-seamless-customer-experience',
			'excerpt' => 'With SpringPass, store loyalty cards, transport tickets and coupons directly on your smartphone. A smooth, secure customer experience built on Apple and Google Wallet.',
			'content' => "<p>With SpringPass, your customers benefit from an intuitive, streamlined user experience. They can store their loyalty cards, transport tickets, coupons and other digital passes directly on their smartphone, always within reach.</p>\n<p><strong>How does SpringPass add value to your business?</strong></p>\n<p><strong>Customer loyalty</strong>: offer personalised deals and promotions directly through digital passes, encouraging customer retention.</p>\n<p><strong>Increased engagement</strong>: provide a smooth, frictionless customer experience that fosters a positive, modern brand image.</p>\n<p><strong>Operational optimisation</strong>: simplify the management of digital passes and reduce costs linked to physical media.</p>\n<p><strong>Valuable data</strong>: collect valuable data on your customers' consumption habits to better target your marketing offers.</p>\n<p><strong>SpringPass: a secure, versatile solution</strong></p>\n<p>SpringPass leverages Apple and Google Wallet technologies to ensure the security of your customers' data.</p>\n<p>SpringPass also offers great flexibility in use. You can create a variety of digital passes to meet your specific needs, such as loyalty cards, transport tickets, coupons, access cards and much more.</p>\n<p>Ready to offer your customers an exceptional digital experience?</p>\n<p>Contact us today to find out how SpringPass can help you reach your business goals.</p>",
		)
	);
	$article_fr_id  = isset( $article_ids['fr'] ) ? $article_ids['fr'] : 0;
	$article_img_id = has_post_thumbnail( $article_fr_id )
		? get_post_thumbnail_id( $article_fr_id )
		: springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/blog/springpass.jpg' ), 'SpringPass' );

	foreach ( $article_ids as $lang => $article_id ) {
		if ( $article_img_id && ! has_post_thumbnail( $article_id ) ) {
			set_post_thumbnail( $article_id, $article_img_id );
		}
	}

	// Articles techniques repris de l'ancien blog de springcard.com (table
	// "posts", modèle "Posts" de seo_uris) : une sélection d'articles
	// pédagogiques intemporels sur les technologies RFID/NFC/Bluetooth/PC-SC,
	// traduits en français (les articles originaux n'existaient qu'en anglais,
	// même sous leur URL /fr/) et nettoyés des mentions de produits abandonnés
	// et des liens morts vers l'ancien site.
	$articles_techniques_seed = array(
		array(
			'slug'    => "quelle-est-la-difference-entre-rfid-et-nfc",
			'title'   => "Quelle est la différence entre RFID et NFC ?",
			'excerpt' => "RFID active, RFID passive, champ proche, champ lointain : comprendre ce qui distingue et relie les technologies RFID et NFC.",
			'keyword' => "différence RFID NFC",
			'content' => "<p>Derrière ces deux mots, RFID et NFC, se cachent deux technologies à la fois très proches et complémentaires, mais aussi deux familles d'usages qui n'ont pas grand-chose en commun. Essayons d'y voir plus clair.</p>

	<h2>RFID</h2>
	<p>Commençons par la définition de la RFID, acronyme anglais de Radio Frequency IDentification (identification par radiofréquence). Il s'agit de tout système dans lequel un dispositif embarque un mécanisme électronique que l'on peut interroger à distance, par communication radio, afin de reconnaître et d'identifier l'objet qui le porte.</p>
	<p>Ce dispositif peut être de grande taille : le transpondeur aéronautique est en quelque sorte l'ancêtre commun de tous les systèmes RFID actuels. L'évolution, la miniaturisation de l'électronique et la maîtrise des ondes électromagnétiques ont ensuite fait émerger deux branches distinctes.</p>
	<p>Dans la branche RFID active, on trouve les transpondeurs aéronautiques, les badges de télépéage, mais aussi tous les systèmes fondés sur le Bluetooth Low Energy (BLE) ou équivalent. Selon les cas, on parlera de transpondeur, de balise (beacon) ou de tag actif, mais le principe reste le même : le dispositif embarque un émetteur radio et génère lui-même l'onde qui permettra au lecteur de détecter sa présence et de recevoir les données d'identification.</p>
	<p>Dans la branche RFID passive, le dispositif ne dispose d'aucun émetteur et ne peut générer la moindre onde. C'est le plus souvent une puce microscopique, sans alimentation propre, associée à une antenne de quelques centimètres pour former une étiquette ou un tag électronique.</p>
	<p>Comment ce tag peut-il alors se signaler au lecteur, s'il est totalement passif ? Tout simplement en se comportant comme le petit miroir qu'utilisait un naufragé pour se faire repérer par un bateau : le miroir n'a pas de source de lumière, tout comme le tag n'a pas d'émetteur radio. Mais en réfléchissant l'onde lumineuse, il permet au naufragé d'être vu, et même de communiquer en morse. Le tag baigne dans l'onde émise par le lecteur et, en la rétromodulant, il parvient à transmettre un message binaire.</p>
	<p>Derrière ce principe se cachent deux réalisations techniques possibles, dont le choix dépend de la fréquence utilisée.</p>
	<p>En haute fréquence, notamment dans la bande UHF utilisée par certains systèmes RFID (autour de 868 MHz en Europe et 910 MHz aux États-Unis), le lecteur et le tag exploitent la composante électrique de l'onde radio, comme la plupart des systèmes radio du quotidien : c'est ce que les physiciens appellent le champ lointain.</p>
	<p>À l'inverse, en basse fréquence — pour la RFID, de 125 à 135 kHz, ou à 13,56 MHz — le lecteur et le tag exploitent la composante magnétique de l'onde radio. C'est le champ proche.</p>
	<p>Et c'est précisément là que le lien avec le NFC apparaît.</p>

	<h2>NFC</h2>
	<p>NFC signifie Near Field Communication, communication en champ proche. Le NFC est un sous-ensemble spécifique de la RFID : celui qui repose sur une communication passive en champ magnétique, plus précisément à 13,56 MHz. Cette fréquence offre de nombreux avantages : elle est libre de droits partout dans le monde, permet un débit de communication intéressant, tout en restant technique raisonnablement simple — donc accessible et facile à miniaturiser.</p>
	<p>Mais du point de vue de l'utilisateur, les choses sont moins simples. Pour quelqu'un travaillant en logistique ou dans la grande distribution, une étiquette RFID n'est rien de plus qu'un code-barres lisible à distance : le lecteur se contente de lire un numéro d'article et l'ensemble des informations associées, stockées dans une base de données ou un service cloud. Pour quelqu'un travaillant dans l'événementiel et proposant des badges RFID à ses visiteurs, la logique est différente : le badge ne porte qu'une faible quantité de données — le numéro identifiant le visiteur, par exemple — et c'est le système d'information qui conserve le reste.</p>
	<p>À l'autre extrémité du spectre des usages, un opérateur de transport ou de paiement ne peut pas tout faire reposer sur un système d'information central. Les transactions doivent pouvoir se dérouler hors ligne, pour garantir la robustesse du réseau, un débit suffisant ou respecter des contraintes de confidentialité et de cloisonnement des données. Le client dispose alors d'une carte à puce sans contact capable de stocker un volume de données relativement important et de garantir un haut niveau de sécurité grâce à la cryptographie.</p>
	<p>D'un point de vue purement communication, la RFID à 13,56 MHz et la carte à puce sans contact, c'est la même chose. D'un point de vue applicatif, l'écart est important.</p>
	<p>Le NFC vient combler cet écart, ou du moins le rendre plus facile à appréhender pour le grand public, en unifiant l'ergonomie et en favorisant le développement des usages.</p>
	<p>Avec un smartphone NFC, on peut par exemple payer ou valider un titre de transport, tout simplement parce que le smartphone émule la carte à puce sans contact à 13,56 MHz attendue par le terminal de paiement ou de validation.</p>
	<p>Avec un smartphone NFC, on peut aussi lire l'étiquette RFID à 13,56 MHz apposée sur un produit que l'on vient d'acheter : en ouvrant l'application du fabricant, celle-ci interroge son système d'information pour confirmer l'authenticité du produit, voire proposer un coupon de réduction sur le prochain achat.</p>
	<p>En résumé, le vocabulaire du NFC et de la RFID à 13,56 MHz recouvre des concepts très proches, mais dans des contextes d'usage complètement différents. Tous les produits SpringCard opérant sur cette bande de fréquence peuvent être utilisés dans les deux contextes. La RFID reste un domaine plus large, avec d'autres fréquences comme l'UHF, et le monde de la RFID active où, dans de nombreuses situations, les anciens systèmes propriétaires peuvent aujourd'hui être remplacés par des systèmes BLE.</p>",
		),
		array(
			'slug'    => "nfc-rfid-le-guide-complet",
			'title'   => "NFC, RFID : le guide complet",
			'excerpt' => "Origines de la RFID, fonctionnement actif/passif, normes NFC Forum et EMV : les bases pour comprendre les technologies sans contact.",
			'keyword' => "guide RFID NFC",
			'content' => "<p>La RFID est née d'un besoin essentiel : identifier à distance les avions amis ou ennemis pendant la Seconde Guerre mondiale. Le système IFF (Identification Friend or Foe) utilisait des ondes radio pour identifier un objet mobile — c'est ainsi qu'est né le premier système d'identification par radiofréquence, la Radio Frequency IDentification (RFID).</p>
	<p>Aujourd'hui, grâce à la miniaturisation des composants électroniques et à la baisse des coûts, la RFID est utilisée dans de très nombreux domaines d'application.</p>
	<p>Un système RFID se compose de deux éléments : une station de base (l'interrogateur) et une cible (le transpondeur). Ces deux éléments communiquent par ondes radio, et leur mode de communication détermine s'il s'agit de RFID active ou passive.</p>
	<p>La RFID est active lorsque la cible dispose d'un émetteur et génère sa propre onde radio. Elle est passive lorsque seul l'interrogateur est capable d'émettre : la cible ne dispose d'aucun émetteur et se contente de modifier l'onde qu'elle reçoit — un principe appelé rétromodulation.</p>
	<p>La RFID utilise le plus souvent des bandes de fréquences libres de droits. Les cartes à puce sans contact fonctionnent sur la bande ISM des 13,56 MHz (HF), qui est précisément au cœur de l'expertise de SpringCard.</p>

	<h2>Le NFC en détail</h2>
	<p>La communication en champ proche, ou NFC, correspond à l'extension de cette technologie à de nouveaux usages, le plus souvent associés à une dimension grand public.</p>
	<p>Plusieurs normes encadrent les produits intégrant NFC ou RFID à 13,56 MHz : le NFC Forum pour l'ensemble des usages grand public et télécoms, l'EMV pour le paiement, ou encore, pour le transport, le RCTIF — un référentiel francilien aujourd'hui progressivement remplacé par la norme européenne ISO CEN TS 16794.</p>",
		),
		array(
			'slug'    => "tout-savoir-sur-le-nfc-la-rfid-et-le-bluetooth",
			'title'   => "Tout savoir sur le NFC, la RFID et le Bluetooth",
			'excerpt' => "NFC, RFID, Bluetooth et Bluetooth Low Energy : définitions, fonctionnement et usages sur smartphone, expliqués simplement.",
			'keyword' => "NFC RFID Bluetooth",
			'content' => "<h2>Le NFC : qu'est-ce que c'est ?</h2>
	<p>Le NFC est l'étiquette sous laquelle se regroupent trois usages, tous fondés sur un champ magnétique à 13,56 MHz :</p>
	<ul>
	<li>le mode lecteur + carte « classique », mais aussi lecteur + émulation de carte</li>
	<li>l'usage RFID</li>
	<li>l'usage pair-à-pair (peer-to-peer)</li>
	</ul>
	<p>Un intégrateur se concentre le plus souvent sur un seul de ces modes, mais il arrive d'en combiner plusieurs.</p>

	<h3>À quoi ça sert ?</h3>
	<p>Le NFC permet de communiquer en pair-à-pair, tout comme il permet la communication NFC entre un tag et un lecteur.</p>

	<h3>Le NFC et les smartphones</h3>
	<p>Le NFC est aujourd'hui intégré dans la plupart des smartphones, sous forme d'une puce active (qui émet des données) d'un côté, et d'une puce passive (qui les reçoit) de l'autre. La communication s'établit lorsque les deux puces se rapprochent l'une de l'autre.</p>
	<p>Le rapprochement de ces deux puces permet l'échange de données, comme des fichiers. Le NFC permet aussi d'identifier des appareils : c'est lui qui sert, par exemple, à appairer un périphérique Bluetooth. Autre usage courant : la lecture de tags, ces petits objets intégrant une puce NFC réveillée lorsqu'un smartphone en mode NFC s'en approche.</p>

	<h2>La RFID : qu'est-ce que c'est ?</h2>
	<p>La technologie RFID est apparue en réponse au besoin d'identifier à distance des avions pendant la Seconde Guerre mondiale.</p>
	<p>Un système IFF (Identification Friend or Foe) a été rapidement développé durant ce conflit. L'IFF utilise des ondes radio pour identifier des objets mobiles : c'est le premier système d'identification par radiofréquence, la Radio Frequency IDentification (RFID).</p>

	<h3>Comment ça fonctionne ?</h3>
	<p>Un système RFID se compose d'une station de base, aussi appelée interrogateur, et d'une cible, aussi appelée transpondeur. Ces deux éléments communiquent par ondes radio, et c'est leur mode de communication qui détermine le type de RFID utilisé.</p>
	<p>La RFID peut être active ou passive. En RFID active, la cible embarque un émetteur qui génère sa propre onde radio. En RFID passive, l'interrogateur envoie une onde vers la cible et attend un écho en retour : la cible n'a pas d'émetteur et se contente de modifier l'onde reçue. Lorsque l'onde est modifiée, l'interrogateur sait qu'il y a eu communication.</p>

	<h3>À quoi ça sert ?</h3>
	<p>La technologie RFID permet d'identifier des personnes ou des objets, de tracer des produits, ou encore de lire des titres de transport et des passeports.</p>

	<h2>Le Bluetooth : qu'est-ce que c'est ?</h2>
	<p>Le Bluetooth est une technologie de réseau sans fil qui permet aux appareils électroniques d'échanger des données à courte distance. Cette technologie équipe la plupart des smartphones.</p>
	<p>Il existe une variante du Bluetooth classique appelée Bluetooth Low Energy (BLE), qui conserve la même portée de communication tout en réduisant le coût et la consommation énergétique. Le BLE reste compatible avec les autres technologies Bluetooth.</p>

	<h3>À quoi ça sert ?</h3>
	<p>La technologie Bluetooth permet aux utilisateurs de transférer des données entre appareils rapidement et efficacement.</p>

	<h3>Le Bluetooth et les smartphones</h3>
	<p>Utilisée sur les smartphones, la technologie Bluetooth a permis d'améliorer la connectivité entre appareils, mais aussi de favoriser le développement rapide d'accessoires sans fil comme les écouteurs.</p>",
		),
		array(
			'slug'    => "comment-concevoir-son-antenne-rfid-nfc",
			'title'   => "Comment concevoir son antenne RFID/NFC ?",
			'excerpt' => "Portée, normes, symétrie et compatibilité électromagnétique : les étapes clés pour concevoir une antenne RFID/NFC sur mesure.",
			'keyword' => "conception antenne RFID NFC",
			'content' => "<p>Les modules OEM RFID/NFC de SpringCard sont proposés avec une antenne standard qui couvre une grande diversité d'usages.</p>
	<p>Certaines situations peuvent toutefois conduire à concevoir une antenne spécifique : contraintes d'encombrement, ou besoin d'optimiser la communication avec des cartes non standards, plus petites ou plus grandes que d'ordinaire.</p>
	<p><strong>Mais concevoir une antenne n'est pas un exercice simple.</strong> Il faut savoir l'accorder, mais aussi la tester et la valider — un vrai métier d'expert.</p>
	<p>Voici les grandes étapes à connaître pour qui souhaite se lancer dans cet exercice.</p>

	<h2>Les étapes de développement</h2>
	<p>La première étape consiste à déterminer le volume de portée opérationnelle attendu, en fonction des cartes et des tags qui seront utilisés.</p>
	<p>Il faut ensuite veiller au respect des normes et protocoles, afin que l'antenne reste compatible avec le plus grand nombre d'objets mobiles — sauf à se limiter volontairement à un format d'objet spécifique, auquel cas il est possible de s'affranchir en partie de ces normes.</p>
	<p>Dans tous les cas, il faut respecter les niveaux de champ et la symétrie de l'antenne afin de garantir la compatibilité électromagnétique (CEM).</p>
	<p>L'environnement dans lequel l'antenne sera intégrée joue également un rôle déterminant : la proximité de métal, d'autres composants électroniques ou de matériaux absorbants modifie sensiblement le comportement de l'antenne par rapport à un environnement de laboratoire, et doit être prise en compte dès la conception.</p>
	<p>Vient ensuite la phase d'accord (tuning) : ajuster les valeurs des composants passifs du circuit d'antenne pour atteindre la fréquence de résonance et l'impédance ciblées, dans l'environnement réel du produit fini — et non plus seulement sur un prototype de laboratoire.</p>
	<p>Enfin, la validation consiste à mesurer les performances réelles de lecture (portée, robustesse, répétabilité) avec les cartes et tags cibles, avant de figer la conception.</p>
	<p>C'est exactement ce type d'accompagnement — conception, accord et validation d'antenne RFID/NFC — que le bureau d'études de SpringCard propose aux projets qui ne peuvent pas se satisfaire d'une antenne standard.</p>",
		),
		array(
			'slug'    => "tout-savoir-sur-le-pc-sc",
			'title'   => "Tout savoir sur le PC/SC",
			'excerpt' => "Standard d'interopérabilité entre ordinateurs et cartes à puce, architecture en couches, APDU : comprendre le PC/SC.",
			'keyword' => "PC/SC carte à puce",
			'content' => "<h2>Qu'est-ce que c'est ?</h2>
	<p>Le PC/SC est une norme d'interopérabilité qui garantit le dialogue entre un ordinateur et une carte à puce. Cette norme est disponible sur la plupart des systèmes d'exploitation, dont Windows et Linux. PC/SC (Personal Computer/Smart Card) désigne à la fois une spécification et une bibliothèque logicielle d'accès aux cartes à puce sous Microsoft Windows. Une implémentation libre, PC/SC Lite, est disponible sous GNU/Linux et distribuée avec macOS. La spécification de cette bibliothèque est portée par le PC/SC Workgroup, qui réunit fabricants de cartes à puce et constructeurs informatiques. Son objectif : garantir un socle commun de commandes pour assurer une meilleure interopérabilité entre PC, lecteurs de cartes et cartes à puce.</p>

	<h2>Comment ça fonctionne ?</h2>
	<p>Le PC/SC repose sur une architecture en couches. Elle commence par le coupleur et son pilote (driver), se poursuit par un middleware qui assure l'abstraction du matériel et isole fortement les applications clientes du détail des cartes. Ce middleware maintient la liste des coupleurs connectés, notifie les applications lorsqu'une carte est insérée, et empêche plusieurs applications d'accéder simultanément à la même carte.</p>
	<p>Cette architecture en couches se termine par une interface de programmation (API) de haut niveau, destinée aux applications qui exploitent les cartes à puce : une bibliothèque dynamique qui invoque les services fournis par le middleware PC/SC. Le PC/SC ne fait aucune différence entre un coupleur à contact et un coupleur sans contact : il masque l'essentiel des spécificités propres à chaque technologie, et permet même de piloter des cartes à logique câblée via des APDU, comme s'il s'agissait de cartes à microcontrôleur.</p>

	<h2>L'APDU</h2>
	<p>APDU signifie Application Protocol Data Unit : c'est le message échangé entre une carte à puce et un lecteur. Ce type de message est décrit par la norme ISO 7816. Il existe deux types d'APDU : ceux qui transmettent des commandes, et ceux qui transmettent des réponses. Le rôle de l'APDU est de permettre la communication entre le lecteur et la carte à puce, et de faire remonter les informations de la carte vers le lecteur.</p>",
		),
		array(
			'slug'    => "lecteur-ou-coupleur-quelle-difference",
			'title'   => "Lecteur ou coupleur : quelle différence ?",
			'excerpt' => "« Lire » une carte à puce n'a pas vraiment de sens : comprendre pourquoi SpringCard parle de coupleurs plutôt que de lecteurs.",
			'keyword' => "coupleur carte à puce",
			'content' => "<h2>Qu'est-ce qu'un lecteur « intelligent » (smart reader) ?</h2>
	<p>Chez SpringCard, les lecteurs « intelligents » sont des appareils qui combinent un dispositif de couplage avec un logiciel applicatif embarqué, là où d'autres dispositifs nécessitent un système hôte externe. Ces lecteurs intelligents sont capables de mener seuls une transaction simple avec une carte, et de retrouver un identifiant conforme à ce qu'attend le système de traitement.</p>
	<p>Une carte à puce est, au fond, un microcontrôleur qui communique avec l'extérieur via une liaison série. Il faut donc trouver un terme plus juste que « lecteur » pour désigner le dispositif qui assure la connectivité et donne accès aux instructions de la carte à puce.</p>

	<h2>Que signifie « lire » une carte à puce ?</h2>
	<p>« Lire » une carte à puce n'a pas vraiment plus de sens que « lire » une base de données SQL : on ne « lit » pas une mémoire brute. L'application envoie des commandes pour s'authentifier, d'autres pour explorer un système de fichiers, etc. — et elle lit, bien sûr, mais pas seulement.</p>
	<p>Le lecteur dans lequel la carte à puce est insérée joue donc le rôle d'une passerelle transparente entre le logiciel qui s'exécute sur l'ordinateur hôte et le logiciel qui s'exécute dans le microcontrôleur de la carte.</p>
	<p>Cette passerelle traduit les commandes envoyées par l'application hôte, quelle que soit la technologie d'interconnexion utilisée, en signaux électriques. Mais elle n'ajoute aucune logique de traitement : son rôle se limite à coupler la carte à puce avec l'ordinateur — c'est pour cela qu'on parle de « coupleur ».</p>",
		),
		array(
			'slug'    => "nfc-p2p-vs-hce-quelles-differences",
			'title'   => "NFC P2P vs Host Card Emulation : quelles différences ?",
			'excerpt' => "HCE et NFC pair-à-pair reposent sur la même technologie NFC mais répondent à des usages très différents. Explications.",
			'keyword' => "HCE NFC pair à pair",
			'content' => "<p>Voyons pas à pas les différences entre le NFC HCE (Host Card Emulation) et le NFC P2P (pair-à-pair).</p>
	<p>Ce sont deux schémas de communication fondés sur la même technologie NFC, mais répondant à des cas d'usage très différents.</p>

	<h2>Host Card Emulation (HCE)</h2>
	<p>HCE signifie Host Card Emulation : c'est une implémentation particulière du mode d'émulation de carte en NFC.</p>
	<p>Émuler une carte signifie qu'un appareil électronique — typiquement un smartphone — se comporte exactement comme une carte à puce sans contact. C'est le principe fondateur de la virtualisation des cartes de paiement, de transport, des badges de contrôle d'accès, des cartes de fidélité et d'autres identifiants, directement sur smartphone.</p>
	<p>Il existe ici deux implémentations différentes, selon le niveau de sécurité recherché.</p>
	<p>Les cartes de paiement et de transport, sensibles, sont généralement virtualisées dans un composant sécurisé inviolable, où toutes les clés cryptographiques sont sérieusement protégées contre les attaques connues. Selon les architectures, ce composant sécurisé peut être la carte SIM (ou UICC) du smartphone, gérée par l'opérateur mobile, ou une puce dédiée sur la carte mère, appelée secure element, dont le fabricant du smartphone garde le contrôle. Or, de nombreuses applications de cartes sans contact peuvent se satisfaire d'un niveau de sécurité inférieur à celui des systèmes bancaires. Et virtualiser une carte dans un composant sécurisé est un processus complexe et coûteux, qui suppose de s'associer à l'opérateur ou au fabricant qui contrôle la SIM ou le secure element.</p>
	<p>Pour toutes ces applications, le Host Card Emulation est la solution de choix. La logique de la carte à puce ne s'exécute plus dans un composant sécurisé mais directement dans le processeur du smartphone, comme n'importe quelle application classique. Cela réduit considérablement la complexité, raccourcit les délais de développement, et permet de déployer les cartes virtualisées via les stores d'applications.</p>
	<p>En résumé, le NFC HCE permet d'émuler une carte à puce sans contact en ajoutant seulement quelques lignes de code à une application mobile — pour peu que la plateforme le permette. Android implémente cette technologie depuis la version 4.4 ; iOS, de son côté, reste plus restrictif sur ce point.</p>
	<p>De nombreux lecteurs SpringCard communiquent avec une application HCE exactement comme ils communiqueraient avec une véritable carte à puce sans contact. Certains de nos dispositifs sont également capables d'inverser le schéma, et de permettre à un PC Windows ou Linux de devenir lui-même un hôte HCE.</p>

	<h2>NFC pair-à-pair (P2P)</h2>
	<p>Le NFC pair-à-pair, à l'opposé, n'a que peu à voir avec les transactions de carte à puce. Le NFC P2P n'est rien d'autre qu'un canal de communication courte distance qui, comme tout canal de communication, permet de véhiculer pratiquement n'importe quel protocole réseau et n'importe quelle donnée applicative entre deux appareils.</p>
	<p>Le NFC Forum a spécifié un protocole réseau dédié au NFC P2P, nommé LLCP. Au-dessus de cette pile protocolaire se trouve le service NFC SNEP, conçu pour transmettre le contenu d'un tag NFC d'un appareil à l'autre.</p>
	<p>En résumé, plutôt que d'émuler un tag NFC pour partager un contenu — URL, carte de visite, paramètres Wi-Fi ou Bluetooth —, un smartphone peut simplement utiliser SNEP pour pousser ce même contenu vers un autre smartphone. C'est la technologie qui se cachait derrière « Android Beam ». Grâce à une implémentation logicielle LLCP/SNEP côté hôte, de nombreux dispositifs SpringCard sont également capables de pousser des données NFC vers un smartphone.</p>
	<p>LLCP, la pile protocolaire réseau du NFC, a été conçue pour rester ouverte et extensible. Elle s'avère toutefois relativement complexe et, compte tenu des limites intrinsèques de la communication NFC — portée très courte, débit limité à quelques kilo-octets par seconde —, son intérêt s'estompe face au Bluetooth Low Energy.</p>
	<p>Aujourd'hui, on peut considérer que le NFC pair-à-pair se limite globalement à SNEP, le service de push NDEF. C'est un moyen pratique d'envoyer une information courte d'un appareil à un autre, mais qui ne tire pas pleinement parti du caractère bidirectionnel de ce canal de communication.</p>",
		),
	);
	foreach ( $articles_techniques_seed as $at ) {
		$existing_at = get_page_by_path( $at['slug'], OBJECT, 'article' );
		if ( $existing_at ) {
			$at_id = $existing_at->ID;
		} else {
			$at_id = wp_insert_post(
				array(
					'post_type'    => 'article',
					'post_title'   => $at['title'],
					'post_name'    => $at['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $at['excerpt'],
					'post_content' => $at['content'],
				)
			);
		}
		if ( $at_id && ! is_wp_error( $at_id ) ) {
			if ( function_exists( 'pll_set_post_language' ) && ! pll_get_post_language( $at_id ) ) {
				pll_set_post_language( $at_id, 'fr' );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_title', true ) ) {
				update_post_meta( $at_id, 'rank_math_title', $at['title'] . ' | SpringCard' );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_description', true ) ) {
				update_post_meta( $at_id, 'rank_math_description', $at['excerpt'] );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_focus_keyword', true ) ) {
				update_post_meta( $at_id, 'rank_math_focus_keyword', $at['keyword'] );
			}
		}
	}

	// Second lot d'articles techniques repris de l'ancien blog de springcard.com
	// (mêmes principes que le lot précédent : contenu réel traduit en français,
	// nettoyé des mentions de produits abandonnés et des liens/chiffres datés).
	$articles_techniques_seed_2 = array(
		array(
			'slug'    => "tout-savoir-sur-les-cartes-nxp",
			'title'   => "Tout savoir sur les cartes NXP",
			'excerpt' => "MIFARE Classic, Plus, DESFire, Ultralight, NTAG, ICODE : panorama des familles de cartes NXP et de leurs usages RFID/NFC.",
			'keyword' => "cartes NXP MIFARE",
			'content' => "<h3>Cartes à microcontrôleur et cartes à mémoire</h3>
	<p>Pour un projet RFID/NFC, plusieurs familles de cartes sans contact sont disponibles. Selon vos besoins, votre capacité à investir du temps de développement, et votre préférence pour une solution prête à l'emploi ou plus progressive, la carte à choisir ne sera pas la même.</p>
	<p>Voici un panorama des principaux produits NXP susceptibles de répondre à vos besoins.</p>
	<p>Il existe de nombreuses familles de cartes, mais avant de les présenter, deux points méritent d'être soulignés.</p>
	<p>Le premier : il existe plus de 400 types de cartes haute fréquence différentes (à 13,56 MHz, incluant un large éventail de tags NFC).</p>
	<p>Le second : il existe une différence fondamentale entre les cartes à microcontrôleur et les cartes à mémoire.</p>
	<p>Les cartes à microcontrôleur (ou cartes à puce) offrent un niveau de sécurité très élevé, en contrepartie d'un coût et d'une complexité de mise en œuvre plus importants que les cartes à mémoire. Les cartes à mémoire (ou à logique câblée) stockent les données dans une mémoire plus ou moins structurée, avec un niveau de sécurité variable. Elles consomment aussi moins que les cartes à microcontrôleur et s'adaptent plus facilement à des formats réduits — bracelet, porte-clés, bague, etc.</p>

	<h3>Les cartes NXP</h3>
	<p>NXP est le leader du marché des cartes sans contact. Anciennement Philips Semiconductors, l'entreprise a créé la marque de cartes sans contact MIFARE®, devenue la référence du marché.</p>
	<p>La norme ISO 14443 type A a été en grande partie écrite autour des caractéristiques de ces cartes. Cette gamme comprend quatre familles principales : MIFARE Classic®, MIFARE Plus®, Ultralight et MIFARE DESFire®.</p>
	<p>La MIFARE Classic®, première génération de cartes à mémoire, ne devrait plus être utilisée dans un nouveau projet en raison de failles de sécurité connues. Elle est remplacée par la MIFARE Plus®.</p>
	<p>La MIFARE Plus® permet de stocker de 2 à 4 Ko de données selon les versions. Sa mémoire est divisée en zones que l'on peut sécuriser grâce à deux clés AES.</p>
	<p>La MIFARE DESFire® est une carte à microcontrôleur. Sa mémoire (jusqu'à 8 Ko) est livrée non structurée : il faut créer un système de fichiers et de répertoires avant de pouvoir y stocker des données. L'architecte de la solution dispose d'une grande liberté sur sa politique de sécurité, avec plusieurs clés AES ou 3DES possibles par répertoire.</p>
	<p>L'entrée de gamme est assurée par la carte à mémoire MIFARE Ultralight, qui ne stocke jamais plus d'une dizaine d'octets et n'offre pas de sécurité particulière. Elle suffit pour des applications d'identification RFID/NFC dont le fonctionnement repose sur une base de données plutôt que sur le tag lui-même.</p>
	<p>NXP propose également les NTAG, une évolution des MIFARE Ultralight offrant davantage de capacités, conçues pour les usages tags du NFC Forum.</p>
	<p>Toujours chez NXP, la famille ICODE regroupe des cartes à mémoire conformes à la norme ISO 15693. Comparée à la norme ISO 14443, la norme ISO 15693 est plus lente, mais consomme moins — ce qui permet des applications de lecture « mains libres ».</p>
	<p>Nos lecteurs SpringCard sont capables de lire l'ensemble de ces types de cartes, ainsi que tous les protocoles de communication non propriétaires.</p>",
		),
		array(
			'slug'    => "ndef-format-echange-donnees-nfc",
			'title'   => "NDEF : le format d'échange de données du NFC",
			'excerpt' => "NDEF, la spécification du NFC Forum qui structure les échanges de données entre tags et appareils NFC : définition et fonctionnement.",
			'keyword' => "NDEF NFC",
			'content' => "<p>NDEF signifie NFC Data Exchange Format : une spécification créée par le NFC Forum qui définit un format de données commun pour les appareils et les tags conformes aux standards NFC Forum.</p>
	<p>Le NDEF est une norme de standardisation pour les échanges entre deux appareils NFC, ou entre un appareil NFC et un tag. Un tag NDEF peut contenir un ou plusieurs messages, appelés enregistrements. Chaque enregistrement comporte un en-tête, lui-même composé d'un identifiant, d'une longueur et d'un type.</p>
	<p>Le NDEF fixe également les règles de création d'un message NFC valide, et établit les règles d'enregistrement de ces messages.</p>
	<p>Enfin, la spécification NDEF définit le mécanisme qui permet de préciser quel type de donnée applicative est encodé dans un enregistrement NFC.</p>
	<p>Le format NDEF sert principalement à stocker et échanger des informations — le plus souvent des URL ou du texte — grâce à un format largement reconnu et utilisé. Des tags NFC comme les cartes MIFARE Classic® peuvent être configurés en tags NDEF : une donnée écrite par un appareil NFC devient alors lisible et compréhensible par tout autre appareil conforme au NDEF. Les messages NDEF peuvent aussi être échangés en mode pair-à-pair entre deux appareils NFC. En adhérant au format d'échange NDEF, des appareils qui ne se connaissent pas entre eux sont capables de partager des données de manière organisée et compréhensible pour les deux parties.</p>
	<p>La grande majorité des appareils NFC — lecteurs, smartphones, tablettes — prennent en charge la lecture des messages NDEF issus des tags NFC. Chaque type d'action NDEF peut être encodé sur une puce NFC par un lecteur NFC. En fonction du volume de données nécessaire ou des limites de mémoire propres à chaque type de puce, il est préférable de choisir le tag NFC en fonction du type de donnée à encoder.</p>
	<p>Un enregistrement NDEF comporte deux composantes :</p>
	<ul>
	<li>un type d'enregistrement, qui contextualise la donnée transportée</li>
	<li>la donnée elle-même (payload)</li>
	</ul>
	<p>Ensemble, ces deux éléments représentent une action à réaliser par l'appareil NFC lorsque le tag est approché. Le NDEF ne prend en charge qu'un nombre limité d'actions ; des actions plus complexes peuvent être implémentées en personnalisant le logiciel embarqué dans l'appareil.</p>
	<p>Membre du NFC Forum, SpringCard respecte la spécification NDEF sur l'ensemble de ses lecteurs.</p>",
		),
		array(
			'slug'    => "comprendre-l-anti-collision-en-rfid-nfc",
			'title'   => "Comprendre l'anti-collision en RFID/NFC",
			'excerpt' => "Comment un lecteur RFID/NFC distingue plusieurs cartes présentes en même temps dans son champ : principes et méthodes de l'anti-collision.",
			'keyword' => "anti-collision RFID",
			'content' => "<h3>Qu'est-ce que l'anti-collision ?</h3>
	<p>La plupart des systèmes RFID, y compris les cartes sans contact à 13,56 MHz et le NFC, reposent sur le principe RTF (Reader Talk First) : le lecteur envoie périodiquement des trames de recherche, et chaque carte présente dans son champ y répond en transmettant son identifiant.</p>
	<p>Mais que se passe-t-il lorsque deux cartes — ou plus — sont présentes en même temps dans le champ du lecteur ? Elles répondent simultanément à la trame de recherche : c'est la collision. Sauf cas très particuliers, le lecteur est alors incapable de décoder la réponse.</p>
	<p>Le mécanisme d'anti-collision résout ce problème : il permet au lecteur de savoir précisément combien de cartes se trouvent dans son champ, et d'obtenir, l'une après l'autre, l'identifiant de chacune. Le nom est un peu trompeur : il ne s'agit pas d'empêcher la collision — elle a déjà eu lieu lorsque le mécanisme s'active — mais au contraire de la résoudre, en faisant répéter leur réponse aux cartes, mais de manière non simultanée.</p>

	<h3>Les différentes méthodes de résolution</h3>
	<p>Plusieurs méthodes permettent de résoudre une collision.</p>
	<p>La partie A de la norme ISO 14443 repose sur un principe bien adapté aux cartes à logique câblée : les cartes répondent de manière synchrone, et le lecteur est capable de déterminer précisément sur quel bit se produit la première collision. Il génère alors une nouvelle trame de recherche indiquant le début de la réponse qu'il a reçue, en fixant aléatoirement ce bit de collision à 0.</p>
	<p>S'il n'y a que deux cartes, celle dont le bit vaut 1 ne répond pas, et le lecteur peut recevoir l'identifiant complet de la première carte. Il génère ensuite une nouvelle trame en fixant le bit à 1 pour obtenir l'identifiant de la seconde carte. S'il y a davantage de cartes, il suffit de répéter ce mécanisme autant de fois que nécessaire pour explorer l'ensemble des identifiants présents.</p>
	<p>La partie B de la norme ISO 14443 repose sur un principe plus simple à implémenter en logiciel. Dans sa première trame de recherche, le lecteur annonce un nombre de créneaux temporels (slots). Chaque carte choisit aléatoirement le numéro de créneau dans lequel elle répondra, ce qui réduit la probabilité de collision. Si une collision se produit malgré tout dans un créneau, le lecteur demande aux cartes dont il a déjà obtenu l'identifiant de rester silencieuses, puis génère une nouvelle trame de recherche, jusqu'à résoudre toutes les collisions.</p>
	<p>Enfin, la norme ISO 15693 repose sur un principe hybride entre les deux précédents. Comme pour le type B, l'anti-collision s'appuie sur des créneaux, mais le numéro de créneau choisi par la carte n'est pas aléatoire : il correspond à une partie de son identifiant. Comme pour le type A, le lecteur réalise une exploration en profondeur en annonçant la longueur d'identifiant qu'il connaît déjà, ce qui revient à faire défiler la portion d'identifiant que les cartes utilisent pour choisir leur créneau.</p>
	<p>Même si les lecteurs qui intègrent ces algorithmes les exécutent très rapidement, l'anti-collision représente toujours un temps de traitement supplémentaire par rapport à une situation sans collision. Ce temps reste heureusement impercep­tible pour un utilisateur humain — c'est tout l'intérêt de l'anti-collision dans de nombreuses applications.</p>
	<p>Un lecteur qui prend en charge l'anti-collision peut, sous le contrôle d'une application, sélectionner la « bonne » carte parmi plusieurs. Pour un lecteur de contrôle d'accès par exemple, l'utilisateur peut présenter son portefeuille contenant sa carte de transport, une ou deux cartes de paiement, le badge d'accès à son immeuble et sa carte d'accès à l'entreprise. L'anti-collision permet au système de contrôle d'accès de tester l'authenticité de chaque carte, une par une, jusqu'à trouver celle qui ouvre la porte — un vrai gain d'ergonomie pour l'utilisateur.</p>

	<h3>Quand l'anti-collision n'est pas souhaitable</h3>
	<p>Certains cas rendent l'anti-collision peu pertinente, voire proscrite par principe.</p>
	<p>C'est le cas d'un terminal de paiement : un utilisateur possédant plusieurs cartes bancaires doit choisir explicitement celle avec laquelle il souhaite payer. Si une collision est détectée, le terminal refuse la transaction et invite l'utilisateur à résoudre lui-même la situation.</p>
	<p>C'est aussi le cas des lecteurs sans contact conformes au standard PC/SC pour la connexion à un ordinateur. Le PC/SC a été conçu pour les cartes à contact et repose sur le principe d'une seule carte insérée dans le lecteur : il ne peut pas gérer la présence simultanée de deux cartes sans contact sur un même lecteur. Un lecteur sans contact PC/SC peut implémenter l'anti-collision via des mécanismes propriétaires, mais dans une implémentation respectueuse du standard, il ne présentera aux applications que la première carte détectée.</p>",
		),
		array(
			'slug'    => "anti-tearing-principe-et-mecanismes",
			'title'   => "L'anti-tearing : principe et mécanismes",
			'excerpt' => "Comment protéger les données d'une carte à puce sans contact contre une écriture interrompue : le principe de l'anti-tearing.",
			'keyword' => "anti-tearing carte à puce",
			'content' => "<h3>Tearing et anti-tearing</h3>
	<p>Une carte à puce sans contact est un objet électronique dépourvu de sa propre source d'alimentation : elle ne fonctionne que lorsqu'elle est téléalimentée par le champ RF du lecteur.</p>
	<p>Or l'utilisateur qui tient la carte peut, à tout moment, l'éloigner du lecteur. C'est ce que l'on appelle le tearing (l'arrachement). La source d'alimentation qui fait fonctionner la puce est par nature fragile, puisque l'utilisateur peut la faire disparaître à tout instant. Si l'alimentation disparaît pendant que la puce écrit dans une mémoire non volatile (E2PROM ou flash), la zone mémoire concernée peut se retrouver écrite partiellement, voire corrompue.</p>
	<p>Cette problématique existe aussi pour les cartes à contact, mais elle y est moins gênante : l'utilisateur a l'habitude de laisser sa carte dans le lecteur jusqu'à la fin de la transaction. Les distributeurs automatiques de billets ou les horodateurs disposent d'ailleurs souvent d'un lecteur motorisé qui « retient » la carte le temps de la transaction.</p>
	<p>L'anti-tearing désigne l'ensemble des contre-mesures, matérielles et/ou logicielles, qui évitent qu'un arrachement pendant une écriture ne corrompe le contenu stocké en mémoire.</p>
	<p>Prenons l'exemple d'une carte stockant un nombre de points ou de jetons, dont la valeur initiale est 12345. Le terminal souhaite ajouter 1 pour obtenir 12346. Si la carte ne dispose d'aucune mesure d'anti-tearing et qu'elle est arrachée pendant que sa logique interne réécrit la valeur en mémoire, la valeur finale pourrait devenir 12300, 123FF, 00000, FFFFF, ou toute autre valeur incohérente.</p>
	<p>Cette valeur est généralement associée à une somme de contrôle ou à un contrôle d'intégrité et d'authenticité cryptographique (CMAC), qui permet de déterminer plus tard si elle est correcte. Mais si elle ne l'est pas, comment retrouver la valeur qui aurait dû être écrite ?</p>

	<h3>Les méthodes d'anti-tearing</h3>
	<p>La méthode la plus simple consiste à utiliser une technique de « miroir » : doubler la mémoire de stockage et écrire successivement dans une zone puis dans l'autre.</p>
	<p>Si l'écriture est interrompue, l'ancienne valeur reste disponible dans l'une des deux zones, utilisée comme sauvegarde, ce qui permet de restaurer un état cohérent au prochain rallumage. En contrepartie de cette simplicité, cette méthode peut augmenter le coût de la carte, puisque la taille de la mémoire de stockage doit être doublée.</p>
	<p>Pour les cartes qui ne disposent pas d'anti-tearing natif, il est possible d'implémenter le même principe de « miroir » au niveau applicatif, en stockant chaque donnée en double. En contrepartie, la mémoire utile est divisée par deux et le temps de transaction est doublé.</p>
	<p>Une autre méthode consiste à doter la puce d'un condensateur et d'une mémoire tampon. Lorsque le terminal demande une écriture, celle-ci n'est pas réalisée directement dans la mémoire de stockage non volatile, mais placée en attente dans la mémoire tampon volatile. Une fois toutes les données attendues reçues, la carte vérifie sa source d'alimentation et le niveau de charge de son condensateur. Si celui-ci a accumulé suffisamment d'énergie, l'écriture peut débuter car la carte sait qu'elle pourra la terminer même en cas d'arrachement. Si l'alimentation n'est plus présente et que le condensateur n'a pas assez d'énergie, l'écriture n'a tout simplement pas lieu.</p>
	<p>Ces deux méthodes peuvent se combiner pour aboutir, sur certaines cartes, à un mécanisme transactionnel puissant. Plutôt que de protéger chaque écriture individuellement, l'anti-tearing couplé à un mécanisme transactionnel garantit l'atomicité d'une chaîne de plusieurs écritures : soit elles réussissent toutes, soit le contenu précédent est préservé.</p>
	<p>L'intérêt d'un tel mécanisme transactionnel est aussi d'accélérer la reprise sur erreur, ce qui compte pour des applications comme le transport. Si l'utilisateur retire sa carte pendant la transaction, le terminal signale l'erreur et refuse l'accès ; l'utilisateur représente alors sa carte et le terminal doit relancer un traitement complet, ce qui prend du temps et risque de compter deux trajets au lieu d'un. Si la carte dispose d'un anti-tearing transactionnel, le terminal peut simplement relire le dernier événement inscrit dans son journal de transactions : s'il est cohérent avec son propre historique, il peut immédiatement laisser passer l'utilisateur.</p>
	<p>Les cartes sans contact de milieu et de haut de gamme intègrent le plus souvent un mécanisme d'anti-tearing qui protège leurs métadonnées : zones de configuration, clés cryptographiques, organisation du système de fichiers. En revanche, l'anti-tearing appliqué aux zones de données n'est pas systématique : il doit, le cas échéant, être activé fichier par fichier dès le formatage initial.</p>",
		),
		array(
			'slug'    => "tout-savoir-sur-le-marquage-ce",
			'title'   => "Tout savoir sur le marquage CE",
			'excerpt' => "Ce que garantit le marquage CE sur un lecteur RFID/NFC, et comment un fabricant établit sa conformité aux exigences européennes.",
			'keyword' => "marquage CE lecteur RFID",
			'content' => "<p>Le marquage CE, apposé sur nos produits, signifie que le produit a été évalué et qu'il répond aux exigences de l'Union européenne.</p>
	<p>De nombreux règlements et normes se cachent derrière ces exigences. Leur objectif : garantir qu'un produit vendu dans l'UE respecte la santé et la sécurité de ses utilisateurs, ainsi que l'environnement. La plupart des produits SpringCard sont des lecteurs RFID ou NFC HF, c'est-à-dire des appareils émettant des ondes radio. La compatibilité électromagnétique constitue donc une part importante des exigences CE : elle garantit que l'appareil ne cause pas d'interférences et respecte les limitations de puissance radio imposées par la réglementation.</p>
	<p>Pour appliquer le marquage CE sur un produit, SpringCard, comme tout autre fabricant, constitue un dossier technique rassemblant l'ensemble des justificatifs démontrant que le produit respecte les exigences européennes. Les données et essais peuvent être réalisés en interne ou sous-traités à un laboratoire. Au final, c'est le fabricant qui reste responsable de l'évaluation de conformité. Une fois apposé, le marquage CE permet au produit de circuler librement sur le territoire de l'UE.</p>
	<p>Il est important de noter que le marquage CE n'indique en rien l'origine géographique d'un produit : c'est uniquement la preuve qu'il répond aux exigences européennes. Les appareils SpringCard sont conçus et fabriqués en France ; la plupart portent également le marquage FCC une fois testés pour une commercialisation sur le marché américain.</p>",
		),
		array(
			'slug'    => "mtbf-qu-est-ce-que-c-est",
			'title'   => "MTBF : qu'est-ce que c'est ?",
			'excerpt' => "MTBF, MTTF, MTTR : comprendre les indicateurs de fiabilité utilisés sur les fiches techniques des lecteurs RFID/NFC.",
			'keyword' => "MTBF fiabilité lecteur",
			'content' => "<p>Le MTBF est une information que l'on retrouve régulièrement sur les fiches techniques de produits électroniques professionnels. Voyons ce qu'il signifie, et surtout à quoi il sert.</p>

	<h2>MTBF : qu'est-ce que c'est ?</h2>
	<p>MTBF signifie Mean Time Between Failures (temps moyen entre pannes). C'est un indicateur de la fiabilité d'un produit ou d'un système réparable.</p>
	<p>Il est calculé à partir de la probabilité de défaillance technique de chacun des composants d'un système, en considérant qu'une défaillance de n'importe lequel de ces composants entraînera la défaillance de l'ensemble.</p>
	<p>En électronique moderne, la probabilité de défaillance d'un composant est extrêmement faible. Pour rendre le chiffre parlant, le MTBF s'exprime en nombre d'heures pendant lesquelles le produit est susceptible de fonctionner sans panne. Pour un lecteur professionnel typique, le MTBF calculé se compte généralement en centaines de milliers d'heures, soit plusieurs dizaines d'années d'utilisation continue.</p>
	<p>À noter que la probabilité de défaillance varie avec les conditions d'utilisation : le MTBF est calculé pour des conditions d'usage typiques. Exposer un produit à des conditions anormales, comme une température très élevée, peut modifier considérablement ce chiffre. C'est pourquoi la plage de température de fonctionnement garantie fait partie des caractéristiques essentielles à vérifier sur la fiche technique d'un lecteur.</p>

	<h2>Les autres indicateurs</h2>
	<p>Le MTBF se distingue du MTTF (Mean Time To Failure), qui ne considère que la première panne — les deux valeurs sont identiques si le système est trop ancien, ou trop peu coûteux, pour être réparé. L'autre différence : le MTBF s'utilise pour des produits réparables, tandis que le MTTF s'utilise pour des produits non réparables.</p>
	<p>Le MTBF se distingue aussi du MTTR (Mean Time To Recovery), le temps moyen nécessaire pour réparer une panne sur un produit ou un système donné.</p>
	<p>En résumé, le MTBF et les autres indicateurs de fiabilité (MTTF, MTTR) sont des éléments essentiels pour évaluer la fiabilité d'un produit ou d'un système.</p>",
		),
		array(
			'slug'    => "certification-rctif-qu-est-ce-que-c-est",
			'title'   => "La certification RCTIF, qu'est-ce que c'est ?",
			'excerpt' => "RCTIF, le Référentiel Commun de Télébillettique d'Île-de-France : à quoi il sert et pourquoi il structure les lecteurs de transport sans contact.",
			'keyword' => "certification RCTIF",
			'content' => "<h2>Qu'est-ce que le RCTIF ?</h2>
	<p>Le « Référentiel Commun de Télébillettique d'Île-de-France » décrit les exigences définies par Île-de-France Mobilités (ex-STIF) pour le protocole de communication sans contact des équipements de billettique déployés en région parisienne, dans le cadre du projet RCTIF 5.0.</p>
	<p>Dans le périmètre du RCTIF 5.0, le principe de détection des PICC évolue, en raison de la multiplication des supports acceptés par les PCD. Les PCD s'assurent désormais qu'un seul PICC est présent dans leur champ avant de réaliser une transaction — l'objectif étant d'amener l'utilisateur à ne présenter qu'un seul objet : celui avec lequel il souhaite réaliser sa transaction.</p>
	<p>Le PCD (Proximity Coupling Device), c'est tout simplement le lecteur sans contact, aussi appelé terminal ou coupleur. Le PICC (Proximity Integrated Circuit Card), c'est la carte sans contact (par exemple le Pass Navigo), que le jargon du sans-contact appelle parfois « tag ».</p>
	<p>Le RCTIF 5.0 a été développé en considérant que les titres sans contact et les cartes bas coût sont conformes à la norme CEN/TS 16794-1. Cette conformité assure par ailleurs l'interopérabilité avec les appareils NFC conformes aux spécifications techniques NFC Forum Analog 2.0 et NFC Forum Digital 1.1.</p>
	<p>CEN TS 16794 est une norme européenne d'interopérabilité née des travaux de l'AFIMB (agence française pour l'intermodalité billettique), auxquels SpringCard a participé. Son rôle : garantir la compatibilité des lecteurs avec tous les types d'« objets sans contact » susceptibles de servir de titre de transport — cartes de transport, bien sûr, mais aussi téléphone NFC et carte bancaire EMV. CEN TS 16794 est donc une norme compatible avec l'implémentation EMV contactless L1, et alignée avec les spécifications NFC du monde mobile (GSMA, ETSI, NFC Forum).</p>

	<h2>À quoi ça sert ?</h2>
	<p>La généralisation des lecteurs à la version RCTIF 5.0 est nécessaire pour permettre à la région Île-de-France d'envisager la dématérialisation de ses titres de transport.</p>
	<p>Par rapport à la version précédente du RCTIF, les objets sans contact suivants sont susceptibles d'être acceptés par le PCD :</p>
	<ul>
	<li>cartes Navigo et cartes de paiement sans contact</li>
	<li>cartes bas coût</li>
	<li>objets connectés (bracelets)</li>
	<li>titres sans contact (tickets)</li>
	<li>smartphones NFC</li>
	</ul>

	<h2>Quelle différence entre les versions 4.0 et 5.0 ?</h2>
	<p>Historiquement, les premières cartes Navigo sont apparues avant la standardisation ISO. Le RCTIF 4 organisait la cohabitation entre les cartes récentes, conformes à l'ISO 14443-B, et les cartes historiques, utilisant l'ancien protocole propriétaire Innovatron. Toutes les cartes historiques ayant depuis été retirées de la circulation, le RCTIF 5 ne s'intéresse plus qu'aux cartes ISO — c'est la principale différence entre les deux versions.</p>

	<h2>Pourquoi le RCTIF 5.0 ?</h2>
	<p>En termes de mobilité, cette nouvelle version représente une évolution majeure. Le RCTIF 5.0 facilite l'accès à une mobilité multimodale, maillon essentiel du parcours des usagers, en élargissant le champ des objets acceptés et en optimisant la compatibilité des lecteurs.</p>
	<p>SpringCard participe depuis de nombreuses années aux travaux autour du RCTIF et a fait évoluer ses lecteurs pour les adapter aux nouveaux mécanismes de détection introduits par le RCTIF 5.0.</p>",
		),
		array(
			'slug'    => "quel-type-de-tag-nfc-choisir",
			'title'   => "Quel type de tag NFC choisir ?",
			'excerpt' => "Les 5 types de tags définis par le NFC Forum, leurs normes et leurs usages : billetterie, cartes de fidélité, identification, santé...",
			'keyword' => "types de tags NFC",
			'content' => "<p>Le NFC Forum a organisé les tags en cinq types, chacun correspondant à des usages différents. Voici les caractéristiques de chaque type et les usages associés.</p>

	<h3>Tags NFC type 1</h3>
	<p>Ce type de tag peut être lu et écrit. Il fonctionne selon la norme ISO/IEC 14443-3A (NFC-A).</p>
	<p>La simplicité du protocole permet de réduire le nombre de composants électroniques, et donc le coût du tag. En contrepartie, le débit de communication reste limité et seules les fonctionnalités de base sont disponibles (lecture / écriture / verrouillage).</p>
	<p>Les tags de type 1 sont utilisés pour :</p>
	<ul>
	<li>les cartes de visite</li>
	<li>l'appairage d'appareils Bluetooth</li>
	<li>la lecture d'un tag précis lorsque plusieurs tags sont présents</li>
	</ul>

	<h3>Tags NFC type 2</h3>
	<p>Ce type de tag est conforme à NFC-A. Il fonctionne en mode lecture/écriture ou en mode lecture seule, selon la norme ISO/IEC 14443-3A.</p>
	<p>Le tag NFC Forum de type 2 est le plus répandu : il répond à un large éventail de besoins et d'usages, à un prix accessible. Plus rapides que les tags de type 1, les tags de type 2 conviennent aux applications qui exigent une communication quasi instantanée.</p>
	<p>Les tags de type 2 sont utilisés pour :</p>
	<ul>
	<li>les transactions de faible montant</li>
	<li>les titres de transport journaliers</li>
	<li>la billetterie événementielle</li>
	<li>les redirections vers une URL</li>
	</ul>

	<h3>Tags NFC type 3</h3>
	<p>Ce type de tag est conforme à NFC-F. Il peut être lu et écrit, ou utilisé en lecture seule, selon la norme JIS 6319-4.</p>
	<p>Les tags NFC Forum de type 3 reposent sur une norme différente des autres types. Leurs composants sont plus sophistiqués, ce qui leur permet de proposer de nombreuses fonctionnalités (identification, portefeuille numérique...), mais à un coût relativement élevé.</p>
	<p>Les tags de type 3 sont utilisés pour :</p>
	<ul>
	<li>les titres de transport</li>
	<li>la monnaie électronique</li>
	<li>l'identification électronique</li>
	<li>les cartes de fidélité/adhésion</li>
	<li>la billetterie électronique</li>
	<li>les dispositifs de santé</li>
	<li>l'électronique grand public</li>
	</ul>

	<h3>Tags NFC type 4</h3>
	<p>Ce type de tag est conforme à NFC-A et NFC-B. Il peut être lu, écrit, ou utilisé en lecture seule, selon la norme ISO/IEC 14443-4 A/B.</p>
	<p>Le tag de type 4 offre une grande flexibilité d'usage et une mémoire importante. Son prix, modéré à élevé, dépend de la capacité mémoire choisie. La sécurité est la caractéristique principale de ce type de tag, qui permet une véritable identification.</p>
	<p>C'est le seul type de tag capable de supporter le niveau de sécurité requis par l'ISO 7816, tout en permettant la modification du contenu NDEF — des capacités qui répondent par exemple aux exigences des applications de titre de transport.</p>

	<h3>Tags NFC type 5</h3>
	<p>Ce type de tag est conforme à NFC-V. Il peut être lu, écrit, ou utilisé en lecture seule, selon la norme ISO/IEC 15693.</p>
	<p>Il permet de transférer des données entre les technologies déjà prises en charge par le NFC Forum et la spécification technique ISO/IEC 15693. Sa distance de lecture est meilleure que celle des autres types de tags.</p>
	<p>Les tags de type 5 sont utilisés pour :</p>
	<ul>
	<li>les livres de bibliothèque, produits et emballages</li>
	<li>la billetterie (forfaits de ski, par exemple)</li>
	<li>la santé (emballages de médicaments)</li>
	</ul>",
		),
		array(
			'slug'    => "lora-et-lorawan-comprendre-cette-technologie",
			'title'   => "LoRa et LoRaWAN : comprendre cette technologie",
			'excerpt' => "Portée, fréquence, consommation, réseaux LoRaWAN : présentation de la technologie LoRa et de ses usages pour l'IoT.",
			'keyword' => "technologie LoRa LoRaWAN",
			'content' => "<h2>Qu'est-ce que LoRa ?</h2>
	<p>LoRa est une technologie de communication radio, développée par une start-up grenobloise rachetée en 2012 par l'entreprise américaine Semtech.</p>
	<p>Les quatre lettres de LoRa signifient Long Range et décrivent l'objectif de cette technologie : assurer un transfert d'information longue distance.</p>
	<p>LoRa utilise la bande de fréquence des 868 MHz (UHF), libre de droits. Sa modulation spécifique permet d'atteindre une portée de plusieurs kilomètres (jusqu'à 20 km) avec une puissance d'émission très faible.</p>
	<p>Cette caractéristique en fait la solution idéale pour le monde des objets connectés ou des villes intelligentes, permettant de concevoir des capteurs fonctionnant plusieurs années sur une simple pile. En contrepartie de cette faible consommation, le débit de communication reste limité à quelques bits par heure en moyenne.</p>

	<h2>Qu'est-ce que LoRaWAN ?</h2>
	<p>Le protocole LoRa se prête particulièrement bien à la conception de réseaux étendus, appelés LoRaWAN. Les données émises par les objets LoRa sont reçues par des antennes déployées sur un large territoire, puis centralisées dans un service cloud.</p>
	<p>Chacun est libre de déployer son propre réseau LoRaWAN, mais compte tenu de l'investissement que cela représente, il est courant de passer par un opérateur, moyennant un abonnement de l'ordre d'une dizaine d'euros par an et par objet.</p>
	<p>Les mécanismes de sécurité de LoRa (clé d'authentification réseau et clé de chiffrement des données) garantissent la confidentialité des données à chaque étape de leur cycle de vie : d'abord sous forme d'onde radio, puis sous forme de données informatiques sur le réseau et sur le serveur de l'opérateur.</p>

	<h2>Notre expérience</h2>
	<p>Une entreprise spécialisée dans les aménagements et services autour de la pratique du vélo a sollicité SpringCard pour développer un capteur permettant de suivre l'occupation de parkings à vélos.</p>
	<p>Le nombre important de capteurs nécessaires rendait une solution opérée coûteuse. Dans le même temps, la concentration des capteurs sur un territoire restreint permettait d'envisager le déploiement d'un nombre limité d'antennes.</p>
	<p>Au-delà de la conception du capteur, SpringCard a également conçu un système LoRa indépendant, fondé sur une passerelle LoRa 3G+ et une solution logicielle open-source pour la centralisation des données.</p>

	<h2>Un projet LoRa ou IoT ?</h2>
	<p>Notre bureau d'études vous accompagne dans la conception d'objets LoRa comme dans la mise en place d'un système de centralisation des données, opéré ou non, adapté à vos contraintes opérationnelles et à vos objectifs de coût total de possession (TCO).</p>",
		),
		array(
			'slug'    => "ce-que-l-iot-peut-faire-pour-vous",
			'title'   => "Ce que l'IoT peut faire pour vous",
			'excerpt' => "Objets connectés, jumeaux numériques, plateformes d'hypervision : comprendre l'intérêt de l'IoT pour les industriels.",
			'keyword' => "IoT industriel",
			'content' => "<h2>Qu'est-ce que l'IoT ?</h2>
	<p>L'IoT (Internet of Things, internet des objets) est un sujet en constante évolution, sans définition parfaitement figée.</p>
	<p>Pour simplifier, on peut dire que l'IoT regroupe l'ensemble des technologies qui permettent d'associer un objet physique à des données accessibles uniquement depuis Internet, ou de réaliser des actions sur cet objet, à distance, via Internet.</p>
	<p>Avec l'IoT, chaque objet qui nous entoure est associé à un jumeau numérique, que l'on peut manipuler et dont on peut connaître l'état via une application et un service cloud.</p>
	<p>Mais l'IoT va au-delà de ces jumeaux numériques. La force d'Internet repose en effet sur l'interconnexion des réseaux, la mise en relation de serveurs et de services. Grâce à l'internet des objets, l'objet connecté devient un objet interconnecté : il s'affranchit de son application ou de son implémentation propriétaire pour mutualiser ses données et ses fonctions au sein de schémas de coopération complexes.</p>
	<p>Les grands acteurs d'Internet — Amazon, Microsoft, IBM, Google — font aujourd'hui de leurs offres d'IoT en tant que service un axe essentiel de leur stratégie de développement.</p>

	<h2>Le principal avantage de l'IoT</h2>
	<p>Pour les industriels et les fournisseurs de solutions professionnelles, l'IoT représente une source d'opportunités nouvelles ou de gains de productivité.</p>
	<p>Avec des objets connectés plus petits, moins coûteux et qui communiquent davantage, il devient possible d'optimiser des processus et des flux, d'augmenter des capacités ou de prévenir des pannes.</p>
	<p>Avec l'IoT, l'interopérabilité et les interfaces ouvertes favorisent l'émergence de plateformes d'hypervision et le développement d'algorithmes de machine learning capables d'exploiter des volumes de données considérables au service de la performance.</p>
	<p>Les offres d'IoT en tant que service permettent d'accéder à des technologies innovantes tout en évitant les investissements lourds qu'elles représentent. À l'inverse, les organisations qui souhaitent garder la maîtrise de leurs données et contrôler précisément l'accès à leurs objets peuvent capitaliser sur leurs propres services et entrepôts de données, tout en faisant pleinement partie de l'internet des objets grâce à des API ouvertes.</p>
	<p>Dans le monde grand public, cette question de la maîtrise des données fait partie intégrante de la réflexion sur le modèle à adopter : il faut savoir gagner la confiance des utilisateurs sans renoncer à la simplicité de mise en œuvre et d'usage.</p>

	<h2>Ce que SpringCard peut faire pour vous</h2>
	<p>Fort de son expérience dans les communications sans contact, les systèmes basse consommation et la sécurité des transactions, SpringCard est le bureau d'études et le partenaire de standardisation qui vous accompagnera dans le monde de l'IoT.</p>",
		),
	);
	foreach ( $articles_techniques_seed_2 as $at ) {
		$existing_at = get_page_by_path( $at['slug'], OBJECT, 'article' );
		if ( $existing_at ) {
			$at_id = $existing_at->ID;
		} else {
			$at_id = wp_insert_post(
				array(
					'post_type'    => 'article',
					'post_title'   => $at['title'],
					'post_name'    => $at['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $at['excerpt'],
					'post_content' => $at['content'],
				)
			);
		}
		if ( $at_id && ! is_wp_error( $at_id ) ) {
			if ( function_exists( 'pll_set_post_language' ) && ! pll_get_post_language( $at_id ) ) {
				pll_set_post_language( $at_id, 'fr' );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_title', true ) ) {
				update_post_meta( $at_id, 'rank_math_title', $at['title'] . ' | SpringCard' );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_description', true ) ) {
				update_post_meta( $at_id, 'rank_math_description', $at['excerpt'] );
			}
			if ( ! get_post_meta( $at_id, 'rank_math_focus_keyword', true ) ) {
				update_post_meta( $at_id, 'rank_math_focus_keyword', $at['keyword'] );
			}
		}
	}

	// Formulaire de contact (Contact Form 7) — remplace le lien mailto: de la page
	// À propos. Créé via l'API du plugin (WPCF7_ContactForm), pas de dépendance à
	// un id post fixe : le thème le retrouve via son _springcard_seed_key.
	if ( class_exists( 'WPCF7_ContactForm' ) ) {
		$existing_cf7 = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => 1,
				'post_status'    => 'any',
				'meta_key'       => '_springcard_seed_key',
				'meta_value'     => 'contact_form',
			)
		);
		if ( ! $existing_cf7 ) {
			$cf7 = WPCF7_ContactForm::get_template( array( 'title' => 'Contact', 'locale' => 'fr_FR' ) );
			$cf7->set_properties(
				array(
					// Structure des champs reprise du vrai formulaire de contact de
					// springcard.com (Contacts::main, formulaire "General enquiry") :
					// prénom/nom séparés, société, téléphone, email, sujet, message,
					// case de consentement RGPD. Les champs propres à l'ancien système
					// (intégration Salesforce, sélecteur de motif à 8 formulaires
					// différents, champs produit/n° de série du formulaire support)
					// n'ont pas été repris : hors périmètre du nouveau site recentré
					// sur M519 et le bureau d'études.
					//
					// Le routage commercial/support reprend la fonctionnalité "Selectable
					// recipient with pipes" native de Contact Form 7 : chaque option du
					// champ "Motif" porte un libellé et, après le "|", l'adresse email
					// réelle utilisée comme valeur soumise (aucun code PHP de routage
					// n'est nécessaire, CF7 gère nativement les valeurs "pipées").
					'form'     => "<div class=\"form-row\">\n<label> Prénom *\n    [text* your-firstname autocomplete:given-name] </label>\n\n<label> Nom *\n    [text* your-lastname autocomplete:family-name] </label>\n</div>\n\n<div class=\"form-row\">\n<label> Société\n    [text your-company autocomplete:organization] </label>\n\n<label> Téléphone\n    [tel your-phone autocomplete:tel] </label>\n</div>\n\n<label> Email professionnel *\n    [email* your-email autocomplete:email] </label>\n\n<label> Motif de la demande *\n[radio your-department default:1 \"Demande commerciale|sales@springcard.com\" \"Support technique (FAE)|support@springcard.com\"]\n</label>\n\n<label> Sujet *\n    [text* your-subject] </label>\n\n<label> Message *\n    [textarea* your-message] </label>\n\n[acceptance rgpd] J'accepte que les informations saisies dans ce formulaire soient utilisées pour traiter ma demande, conformément à la <a href=\"/politique-de-confidentialite/\" target=\"_blank\" rel=\"noopener noreferrer\">politique de confidentialité</a>. [/acceptance]\n\n[submit \"Envoyer le message\"]",
					'mail'     => array(
						'subject'            => '[_site_title] — Nouveau message de [your-firstname] [your-lastname]',
						'sender'             => '[_site_title] <wordpress@springcard.com>',
						'body'               => "Nouvelle demande de contact via le site SpringCard.\n\nPrénom : [your-firstname]\nNom : [your-lastname]\nSociété : [your-company]\nTéléphone : [your-phone]\nEmail : [your-email]\nSujet : [your-subject]\n\nMessage :\n[your-message]\n\n--\nEnvoyé depuis [_site_url]",
						'recipient'          => '[your-department]',
						'additional_headers' => 'Reply-To: [your-email]',
						'attachments'        => '',
						'use_html'           => 0,
						'exclude_blank'      => 0,
					),
					'mail_2'   => array(
						'active'             => true,
						'subject'            => 'Votre message a bien été reçu — [_site_title]',
						'sender'             => '[_site_title] <wordpress@springcard.com>',
						'body'               => "Bonjour [your-firstname],\n\nNous avons bien reçu votre message et reviendrons vers vous rapidement.\n\nRécapitulatif de votre demande :\n[your-message]\n\n--\nL'équipe SpringCard",
						'recipient'          => '[your-email]',
						'additional_headers' => 'Reply-To: [_site_admin_email]',
						'attachments'        => '',
						'use_html'           => 0,
						'exclude_blank'      => 0,
					),
					'messages' => array(
						'mail_sent_ok'      => 'Merci pour votre message, il a bien été envoyé.',
						'mail_sent_ng'      => "Une erreur est survenue lors de l'envoi de votre message. Merci de réessayer plus tard.",
						'validation_error'  => 'Un ou plusieurs champs contiennent une erreur. Merci de vérifier votre saisie.',
						'spam'              => "Votre message a été détecté comme indésirable et n'a pas pu être envoyé.",
						'accept_terms'      => 'Vous devez accepter la politique de confidentialité avant d\'envoyer votre message.',
						'invalid_required'  => 'Merci de renseigner ce champ.',
						'invalid_too_long'  => 'Ce champ contient un texte trop long.',
						'invalid_too_short' => 'Ce champ contient un texte trop court.',
					),
				)
			);
			$cf7_id = $cf7->save();
			if ( $cf7_id ) {
				update_post_meta( $cf7_id, '_springcard_seed_key', 'contact_form' );
			}
		}
	}

	// SEO (Rank Math) — titres, méta-descriptions et mot-clé principal pour les
	// contenus clés, en français ET en anglais quand une traduction existe,
	// construits autour de nos technologies (RFID, NFC) plutôt que de laisser
	// Rank Math générer des valeurs par défaut génériques. Les clés de post meta
	// (rank_math_title, rank_math_description, rank_math_focus_keyword) sont
	// vérifiées dans le code du plugin ; elles sont écrites indépendamment de
	// l'activation du plugin et n'ont donc aucun effet tant que Rank Math n'est
	// pas actif. La homepage n'a pas de post dédié (front-page.php) : son
	// titre/méta se règlent une fois, à la main, dans Rank Math > Titres & Méta
	// > Accueil (pas de version EN pour l'instant, faute de front-page bilingue).
	$seo_seed = array();

	if ( ! empty( $page_ids['solutions']['fr'] ) ) {
		$seo_seed[ $page_ids['solutions']['fr'] ] = array(
			'title'   => "Solutions RFID, NFC, IoT & contrôle d'accès | SpringCard",
			'desc'    => "SpringCard équipe vos projets RFID et NFC : mobilité, santé, retail, sécurité, logistique. Modules OEM M519, intégration sur mesure par notre bureau d'études.",
			'keyword' => 'solutions RFID NFC',
		);
	}
	if ( ! empty( $page_ids['solutions']['en'] ) ) {
		$seo_seed[ $page_ids['solutions']['en'] ] = array(
			'title'   => 'RFID, NFC & IoT Solutions by Sector | SpringCard',
			'desc'    => 'SpringCard powers your RFID and NFC projects: mobility, healthcare, retail, security, logistics. OEM M519 modules, custom integration by our engineering office.',
			'keyword' => 'RFID NFC solutions',
		);
	}

	if ( ! empty( $page_ids['bureau-etudes']['fr'] ) ) {
		$seo_seed[ $page_ids['bureau-etudes']['fr'] ] = array(
			'title'   => "Bureau d'études RFID/NFC sur mesure | SpringCard",
			'desc'    => "Conception de lecteurs RFID/NFC sur mesure : antennes HF, firmware, Linux embarqué, sécurité CRA/RED. Plus de 20 ans d'expertise électronique en France.",
			'keyword' => "bureau d'études RFID NFC",
		);
	}
	if ( ! empty( $page_ids['bureau-etudes']['en'] ) ) {
		$seo_seed[ $page_ids['bureau-etudes']['en'] ] = array(
			'title'   => 'Custom RFID/NFC Engineering Office | SpringCard',
			'desc'    => 'Custom RFID/NFC reader design: HF antennas, firmware, embedded Linux, CRA/RED compliance. Over 20 years of electronics expertise, made in France.',
			'keyword' => 'RFID NFC engineering office',
		);
	}

	if ( ! empty( $page_ids['a-propos']['fr'] ) ) {
		$seo_seed[ $page_ids['a-propos']['fr'] ] = array(
			'title'   => 'À propos de SpringCard | Fabricant français de modules RFID/NFC',
			'desc'    => 'SpringCard conçoit et fabrique en France des modules RFID/NFC OEM depuis plus de 20 ans. Découvrez notre équipe, notre histoire et nos valeurs.',
			'keyword' => 'fabricant modules RFID NFC France',
		);
	}
	if ( ! empty( $page_ids['a-propos']['en'] ) ) {
		$seo_seed[ $page_ids['a-propos']['en'] ] = array(
			'title'   => 'About SpringCard | French Manufacturer of RFID/NFC Modules',
			'desc'    => 'SpringCard has designed and manufactured OEM RFID/NFC modules in France for over 20 years. Discover our team, our story and our values.',
			'keyword' => 'RFID NFC module manufacturer France',
		);
	}

	if ( ! empty( $gamme_ids['fr'] ) ) {
		$seo_seed[ $gamme_ids['fr'] ] = array(
			'title'   => 'M519 — Module RFID/NFC OEM 13.56 MHz | SpringCard',
			'desc'    => "Module de lecture RFID/NFC OEM 13.56 MHz, 3 configurations d'antenne. Intégrez la technologie sans contact dans vos équipements avec SpringCard.",
			'keyword' => 'module RFID NFC OEM',
		);
	}
	if ( ! empty( $gamme_ids['en'] ) ) {
		$seo_seed[ $gamme_ids['en'] ] = array(
			'title'   => 'M519 — 13.56 MHz OEM RFID/NFC Module | SpringCard',
			'desc'    => '13.56 MHz OEM RFID/NFC reader module, 3 antenna configurations. Integrate contactless technology into your equipment with SpringCard.',
			'keyword' => 'OEM RFID NFC module',
		);
	}

	if ( ! empty( $produit_ids['m519']['fr'] ) ) {
		$seo_seed[ $produit_ids['m519']['fr'] ] = array(
			'title'   => 'M519 — Module RFID/NFC OEM antenne externe | SpringCard',
			'desc'    => 'Module RFID/NFC compact NXP PN5190, antenne externe, compatible ISO 14443/15693, NFC et Apple/Google Wallet. Fiche technique complète.',
			'keyword' => 'module RFID NFC antenne externe',
		);
	}
	if ( ! empty( $produit_ids['m519']['en'] ) ) {
		$seo_seed[ $produit_ids['m519']['en'] ] = array(
			'title'   => 'M519 — OEM RFID/NFC Module, External Antenna | SpringCard',
			'desc'    => 'Compact RFID/NFC module with NXP PN5190 chip, external antenna, compatible with ISO 14443/15693, NFC and Apple/Google Wallet. Full datasheet.',
			'keyword' => 'RFID NFC module external antenna',
		);
	}

	if ( ! empty( $produit_ids['m519-sam']['fr'] ) ) {
		$seo_seed[ $produit_ids['m519-sam']['fr'] ] = array(
			'title'   => 'M519-SAM — Module RFID/NFC OEM avec slot SAM sécurisé | SpringCard',
			'desc'    => 'Module RFID/NFC 13.56 MHz avec slot SAM intégré pour une sécurité renforcée. Versions SAM(B) et SAM(U), interface USB PC/SC.',
			'keyword' => 'module RFID NFC SAM sécurisé',
		);
	}
	if ( ! empty( $produit_ids['m519-sam']['en'] ) ) {
		$seo_seed[ $produit_ids['m519-sam']['en'] ] = array(
			'title'   => 'M519-SAM — OEM RFID/NFC Module with Secure SAM Slot | SpringCard',
			'desc'    => '13.56 MHz RFID/NFC module with integrated SAM slot for enhanced security. SAM(B) and SAM(U) versions, USB PC/SC interface.',
			'keyword' => 'RFID NFC SAM secure module',
		);
	}

	if ( ! empty( $produit_ids['m519-suv']['fr'] ) ) {
		$seo_seed[ $produit_ids['m519-suv']['fr'] ] = array(
			'title'   => 'M519-SUV — Coupleur RFID/NFC à antenne intégrée | SpringCard',
			'desc'    => 'Coupleur RFID/NFC OEM à antenne intégrée, transactions sécurisées AES/ECC, interfaces USB et série. Le module SpringCard prêt à intégrer.',
			'keyword' => 'coupleur RFID NFC antenne intégrée',
		);
	}
	if ( ! empty( $produit_ids['m519-suv']['en'] ) ) {
		$seo_seed[ $produit_ids['m519-suv']['en'] ] = array(
			'title'   => 'M519-SUV — RFID/NFC Coupler with Integrated Antenna | SpringCard',
			'desc'    => 'OEM RFID/NFC coupler with integrated antenna, secure AES/ECC transactions, USB and serial interfaces. The SpringCard module ready to integrate.',
			'keyword' => 'RFID NFC coupler integrated antenna',
		);
	}

	$secteurs_seo = array(
		'mobilite'   => array(
			'fr' => array(
				'title'   => "RFID/NFC pour la mobilité : titres de transport, accès véhicules | SpringCard",
				'desc'    => "Solutions RFID/NFC SpringCard pour la mobilité : titres de transport, contrôle d'accès véhicules, bornes de validation sans contact.",
				'keyword' => 'RFID NFC mobilité',
			),
			'en' => array(
				'title'   => 'RFID/NFC for Mobility: Transport Tickets & Vehicle Access | SpringCard',
				'desc'    => 'SpringCard RFID/NFC solutions for mobility: transport tickets, vehicle access control, contactless validation terminals.',
				'keyword' => 'RFID NFC mobility',
			),
		),
		'sante'      => array(
			'fr' => array(
				'title'   => 'RFID/NFC pour la santé : cartes professionnelles sécurisées | SpringCard',
				'desc'    => 'Modules RFID/NFC SpringCard pour le secteur santé : cartes professionnelles, lecteurs sécurisés et mobiles pour les praticiens.',
				'keyword' => 'RFID NFC santé',
			),
			'en' => array(
				'title'   => 'RFID/NFC for Healthcare: Secure Professional Cards | SpringCard',
				'desc'    => 'SpringCard RFID/NFC modules for healthcare: professional cards, secure and mobile readers for practitioners.',
				'keyword' => 'RFID NFC healthcare',
			),
		),
		'loisirs'    => array(
			'fr' => array(
				'title'   => "RFID/NFC pour les loisirs : billetterie et contrôle d'accès | SpringCard",
				'desc'    => "SpringCard équipe la billetterie et le contrôle d'accès des parcs, salles de spectacle et stades avec des modules RFID/NFC OEM.",
				'keyword' => 'RFID NFC billetterie',
			),
			'en' => array(
				'title'   => 'RFID/NFC for Leisure: Ticketing & Access Control | SpringCard',
				'desc'    => 'SpringCard powers ticketing and access control for parks, venues and stadiums with OEM RFID/NFC modules.',
				'keyword' => 'RFID NFC ticketing',
			),
		),
		'retail'     => array(
			'fr' => array(
				'title'   => 'RFID/NFC pour le retail : fidélité et paiement sans contact | SpringCard',
				'desc'    => 'Modules RFID/NFC SpringCard pour le retail : programmes de fidélité et paiement sans contact en point de vente.',
				'keyword' => 'RFID NFC retail',
			),
			'en' => array(
				'title'   => 'RFID/NFC for Retail: Loyalty & Contactless Payment | SpringCard',
				'desc'    => 'SpringCard RFID/NFC modules for retail: loyalty programmes and contactless payment at the point of sale.',
				'keyword' => 'RFID NFC retail',
			),
		),
		'securite'   => array(
			'fr' => array(
				'title'   => "RFID/NFC pour le contrôle d'accès et la sécurité | SpringCard",
				'desc'    => "Badges et lecteurs RFID/NFC SpringCard pour le contrôle d'accès aux bâtiments et zones sensibles.",
				'keyword' => "RFID NFC contrôle d'accès",
			),
			'en' => array(
				'title'   => 'RFID/NFC for Access Control & Security | SpringCard',
				'desc'    => 'SpringCard RFID/NFC badges and readers for access control to buildings and sensitive areas.',
				'keyword' => 'RFID NFC access control',
			),
		),
		'logistique' => array(
			'fr' => array(
				'title'   => 'RFID/NFC pour la logistique : traçabilité et identification | SpringCard',
				'desc'    => "Modules RFID/NFC SpringCard pour la traçabilité des flux et l'identification des colis sur toute la chaîne logistique.",
				'keyword' => 'RFID NFC logistique',
			),
			'en' => array(
				'title'   => 'RFID/NFC for Logistics: Traceability & Identification | SpringCard',
				'desc'    => 'SpringCard RFID/NFC modules for flow traceability and parcel identification across the logistics chain.',
				'keyword' => 'RFID NFC logistics',
			),
		),
	);
	foreach ( $secteurs_seo as $slug => $meta_by_lang ) {
		foreach ( $meta_by_lang as $lang => $meta ) {
			if ( ! empty( $secteur_ids[ $slug ][ $lang ] ) ) {
				$seo_seed[ $secteur_ids[ $slug ][ $lang ] ] = $meta;
			}
		}
	}

	if ( ! empty( $cas_ids['fr'] ) ) {
		$seo_seed[ $cas_ids['fr'] ] = array(
			'title'   => 'AFCare & Doctolib : un lecteur RFID/NFC Bluetooth sur mesure | SpringCard',
			'desc'    => 'Comment SpringCard a conçu un lecteur de carte à puce PC/SC et Bluetooth sur mesure pour AFCare et Doctolib, utilisé par 600 professionnels de santé.',
			'keyword' => 'lecteur carte à puce Bluetooth sur mesure',
		);
	}
	if ( ! empty( $cas_ids['en'] ) ) {
		$seo_seed[ $cas_ids['en'] ] = array(
			'title'   => 'AFCare & Doctolib: a Custom Bluetooth RFID/NFC Reader | SpringCard',
			'desc'    => 'How SpringCard designed a custom PC/SC and Bluetooth smart card reader for AFCare and Doctolib, used by 600 healthcare professionals.',
			'keyword' => 'custom Bluetooth smart card reader',
		);
	}

	if ( ! empty( $article_ids['fr'] ) ) {
		$seo_seed[ $article_ids['fr'] ] = array(
			'title'   => 'SpringPass : cartes de fidélité et billets dans Apple & Google Wallet | SpringCard',
			'desc'    => 'SpringPass permet de stocker cartes de fidélité, billets et coupons directement dans Apple Wallet et Google Wallet, pour une expérience client fluide.',
			'keyword' => 'Apple Wallet Google Wallet fidélité',
		);
	}
	if ( ! empty( $article_ids['en'] ) ) {
		$seo_seed[ $article_ids['en'] ] = array(
			'title'   => 'SpringPass: Loyalty Cards and Tickets in Apple & Google Wallet | SpringCard',
			'desc'    => 'SpringPass lets you store loyalty cards, tickets and coupons directly in Apple Wallet and Google Wallet, for a seamless customer experience.',
			'keyword' => 'Apple Wallet Google Wallet loyalty',
		);
	}

	foreach ( $seo_seed as $seo_post_id => $seo_meta ) {
		if ( ! get_post_meta( $seo_post_id, 'rank_math_title', true ) ) {
			update_post_meta( $seo_post_id, 'rank_math_title', $seo_meta['title'] );
		}
		if ( ! get_post_meta( $seo_post_id, 'rank_math_description', true ) ) {
			update_post_meta( $seo_post_id, 'rank_math_description', $seo_meta['desc'] );
		}
		if ( ! get_post_meta( $seo_post_id, 'rank_math_focus_keyword', true ) ) {
			update_post_meta( $seo_post_id, 'rank_math_focus_keyword', $seo_meta['keyword'] );
		}
	}

	// Titre / description de la page d'accueil : pas de post dédié derrière
	// front-page.php (aucune page statique n'est assignée en accueil), donc ce
	// réglage ne passe pas par un post meta mais par l'option globale de Rank
	// Math. Clé vérifiée dans la documentation développeur Rank Math (page de
	// compatibilité Polylang) : rank-math-options-titles['homepage_title'].
	$rank_math_titles = get_option( 'rank-math-options-titles', array() );
	if ( ! is_array( $rank_math_titles ) ) {
		$rank_math_titles = array();
	}
	if ( empty( $rank_math_titles['homepage_title'] ) ) {
		$rank_math_titles['homepage_title'] = "SpringCard — Modules RFID/NFC OEM & bureau d'études";
	}
	if ( empty( $rank_math_titles['homepage_description'] ) ) {
		$rank_math_titles['homepage_description'] = "Fabricant français de modules RFID/NFC OEM depuis plus de 20 ans. Découvrez la gamme M519 et notre bureau d'études pour vos projets sur mesure.";
	}

	// Type de schema.org par défaut, par type de contenu : gamme/produit en
	// Product, article/cas_usage en Article. Champ et valeurs vérifiés dans le
	// code source de Rank Math (includes/settings/titles/post-types.php, id
	// pt_{post_type}_default_rich_snippet ; valeurs listées dans
	// Helper::choices_rich_snippet_types()). secteur/expertise ne sont pas de
	// bons candidats pour Article ou Product (pages de type "hub"/catégorie) et
	// restent donc sur la valeur par défaut de Rank Math.
	$schema_defaults = array(
		'gamme'     => 'product',
		'produit'   => 'product',
		'article'   => 'article',
		'cas_usage' => 'article',
	);
	foreach ( $schema_defaults as $schema_post_type => $snippet_type ) {
		$schema_key = 'pt_' . $schema_post_type . '_default_rich_snippet';
		if ( empty( $rank_math_titles[ $schema_key ] ) ) {
			$rank_math_titles[ $schema_key ] = $snippet_type;
		}
	}

	// Organisation / Knowledge Graph — logo et profils sociaux affichés dans le
	// panneau de connaissance Google. Champs et valeurs vérifiés dans le code
	// source de Rank Math (includes/settings/titles/local.php :
	// knowledgegraph_type, knowledgegraph_name, knowledgegraph_logo ;
	// includes/modules/schema/class-jsonld.php::get_social_profiles() pour
	// social_url_facebook, twitter_author_names, social_additional_profiles).
	if ( empty( $rank_math_titles['knowledgegraph_type'] ) ) {
		$rank_math_titles['knowledgegraph_type'] = 'company';
	}
	if ( empty( $rank_math_titles['knowledgegraph_name'] ) ) {
		$rank_math_titles['knowledgegraph_name'] = 'SpringCard';
	}
	if ( empty( $rank_math_titles['website_name'] ) ) {
		$rank_math_titles['website_name'] = 'SpringCard';
	}
	if ( empty( $rank_math_titles['knowledgegraph_logo'] ) ) {
		$logo_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/logo-square.png' ), 'SpringCard' );
		if ( $logo_id ) {
			$rank_math_titles['knowledgegraph_logo']    = wp_get_attachment_url( $logo_id );
			$rank_math_titles['knowledgegraph_logo_id'] = $logo_id;
		}
	}
	if ( empty( $rank_math_titles['social_url_facebook'] ) ) {
		$rank_math_titles['social_url_facebook'] = 'https://www.facebook.com/Springcard/';
	}
	if ( empty( $rank_math_titles['twitter_author_names'] ) ) {
		$rank_math_titles['twitter_author_names'] = 'sc_rfid';
	}
	if ( empty( $rank_math_titles['social_additional_profiles'] ) ) {
		$rank_math_titles['social_additional_profiles'] = "https://www.linkedin.com/company/springcard/\nhttps://www.youtube.com/channel/UChkfP_eFhSFndcPYombOLmg";
	}

	update_option( 'rank-math-options-titles', $rank_math_titles );

	// Menu principal (FR + EN) : Accueil · Produits · Bureau d'études · Solutions
	// · À propos (architecture verrouillée du projet), avec sous-menu
	// Blog / Blog technique / Contact sous "À propos".
	$menu_labels = array(
		'fr' => array(
			'menu_name' => 'Menu principal',
			'home'      => 'Accueil',
			'products'  => 'Produits',
			'contact'   => 'Contact',
			'blog'      => 'Blog',
			'blog_tech' => 'Blog technique',
		),
		'en' => array(
			'menu_name' => 'Main Menu',
			'home'      => 'Home',
			'products'  => 'Products',
			'contact'   => 'Contact',
			'blog'      => 'Blog',
			'blog_tech' => 'Technical Blog',
		),
	);

	$menu_ids = array();
	foreach ( $menu_labels as $lang => $labels ) {
		$menu_id = wp_create_nav_menu( $labels['menu_name'] );
		if ( is_wp_error( $menu_id ) ) {
			$term    = get_term_by( 'name', $labels['menu_name'], 'nav_menu' );
			$menu_id = $term ? $term->term_id : 0;
		}
		if ( ! $menu_id ) {
			continue;
		}

		if ( function_exists( 'pll_set_term_language' ) ) {
			pll_set_term_language( $menu_id, $lang );
		}

		// wp_update_nav_menu_item() with a 0 db id always INSERTS a new item.
		// Rather than a fragile "only add if the menu looks empty" guard, clear
		// out any existing items first and rebuild from scratch every time —
		// the menu always ends up with exactly the items below, however messy
		// (or duplicated) its previous state was.
		foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $existing_menu_item ) {
			wp_delete_post( $existing_menu_item->ID, true );
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $labels['home'],
				'menu-item-url'    => home_url( '/' ),
				'menu-item-status' => 'publish',
			)
		);

		if ( ! empty( $gamme_ids[ $lang ] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $labels['products'],
					'menu-item-object'    => 'gamme',
					'menu-item-object-id' => $gamme_ids[ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['bureau-etudes'][ $lang ] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $pages[1][ $lang ]['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['bureau-etudes'][ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['solutions'][ $lang ] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $pages[0][ $lang ]['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['solutions'][ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['a-propos'][ $lang ] ) ) {
			$apropos_item_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $pages[2][ $lang ]['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['a-propos'][ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);

			if ( $apropos_item_id && ! is_wp_error( $apropos_item_id ) ) {
				$blog_link    = get_post_type_archive_link( 'article' );
				$contact_link = trailingslashit( get_permalink( $page_ids['a-propos'][ $lang ] ) ) . '#contact';

				if ( $blog_link ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $labels['blog'],
							'menu-item-url'       => $blog_link,
							'menu-item-status'    => 'publish',
							'menu-item-parent-id' => $apropos_item_id,
						)
					);
				}
				// Blog technique : reste hébergé séparément, lien externe.
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $labels['blog_tech'],
						'menu-item-url'       => 'https://tech.springcard.com/',
						'menu-item-status'    => 'publish',
						'menu-item-parent-id' => $apropos_item_id,
						'menu-item-target'    => '_blank',
					)
				);
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $labels['contact'],
						'menu-item-url'       => $contact_link,
						'menu-item-status'    => 'publish',
						'menu-item-parent-id' => $apropos_item_id,
					)
				);
			}
		}

		$menu_ids[ $lang ] = $menu_id;
	}

	// Position "primary" (déclarée dans functions.php) : le menu FR sert de menu
	// WordPress générique, et si Polylang est actif, on lui indique en plus le
	// menu propre à chaque langue pour cette position — même mécanisme que la
	// page Apparence > Menus le ferait, via l'option 'nav_menus' que Polylang
	// consulte pour servir le bon menu selon la langue courante.
	if ( ! empty( $menu_ids['fr'] ) ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}
		$locations['primary'] = $menu_ids['fr'];
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	if ( function_exists( 'PLL' ) && ! empty( $menu_ids ) ) {
		$theme     = get_option( 'stylesheet' );
		$nav_menus = PLL()->options->get( 'nav_menus' );
		if ( ! is_array( $nav_menus ) ) {
			$nav_menus = array();
		}
		foreach ( $menu_ids as $lang => $menu_id ) {
			$nav_menus[ $theme ]['primary'][ $lang ] = $menu_id;
		}
		PLL()->options->set( 'nav_menus', $nav_menus );
	}

}
