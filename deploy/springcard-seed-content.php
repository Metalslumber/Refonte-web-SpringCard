<?php
/**
 * Plugin Name: SpringCard - Contenu de demonstration
 * Description: Cree le contenu du site SpringCard (pages, gamme M519, produits, secteurs, cas d'usage, expertises, articles, formulaire de contact, reglages SEO). Sans danger a relancer plusieurs fois : ne duplique jamais un contenu deja present.
 * Version: 1.0
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
			'Contenu SpringCard',
			'Contenu SpringCard',
			'manage_options',
			'springcard-seed',
			'springcard_seed_admin_page'
		);
	}
);

function springcard_seed_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$ran = false;
	if ( isset( $_POST['springcard_seed_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['springcard_seed_nonce'] ) ), 'springcard_seed_run' ) ) {
		springcard_seed_run();
		$ran = true;
	}
	?>
	<div class="wrap">
		<h1>Contenu SpringCard</h1>
		<?php if ( $ran ) : ?>
			<div class="notice notice-success"><p>Fait. Le contenu deja present n'a pas ete duplique.</p></div>
		<?php endif; ?>
		<p>Ce bouton cree les pages, la gamme M519 et ses variantes, les secteurs, les cas d'usage, les expertises, les articles, le formulaire de contact et les reglages SEO du site.</p>
		<p><strong>Sans danger a relancer</strong> : chaque element est verifie avant creation, rien n'est jamais duplique.</p>
		<form method="post">
			<?php wp_nonce_field( 'springcard_seed_run', 'springcard_seed_nonce' ); ?>
			<?php submit_button( 'Lancer / relancer la creation du contenu' ); ?>
		</form>
	</div>
	<?php
}

function springcard_seed_run() {
	// Pages "gabarit" (Solutions, Bureau d'études, À propos), avec un texte d'intro.
	$pages = array(
		array(
			'title'   => 'Solutions',
			'slug'    => 'solutions',
			'template' => 'page-solutions.php',
			'content' => "Chaque secteur impose ses propres contraintes d'intégration. Découvrez comment M519 s'adapte à vos usages, du contrôle d'accès à l'industrie.",
		),
		array(
			'title'   => "Bureau d'études",
			'slug'    => 'bureau-etudes',
			'template' => 'page-bureau-etudes.php',
			'content' => "Autour du module OEM SpringSeed M519 et d'un savoir-faire tourné vers la haute sécurité et les performances, notre bureau d'études conçoit des lecteurs d'identification et de contrôle d'accès adaptés à votre produit, à vos protocoles et à vos contraintes industrielles.\n\nNous intervenons là où une expertise spécialisée fait gagner du temps : intégration électronique, antenne RFID HF/NFC, firmware, Linux embarqué, cartes à puce, SAM et secure elements, conformité réglementaire (CRA, RED).",
		),
		array(
			'title'   => 'À propos',
			'slug'    => 'a-propos',
			'template' => 'page-a-propos.php',
			'content' => "SpringCard conçoit et fabrique en France des modules RFID/NFC OEM depuis plus de 20 ans. Aujourd'hui recentrée sur la gamme M519 et son bureau d'études, l'entreprise accompagne ses clients de l'intégration standard au développement sur mesure.",
		),
		array(
			'title'   => 'Mentions légales',
			'slug'    => 'mentions-legales',
			'template' => 'page-legal.php',
			// Repris et adapté des mentions légales réelles de springcard.com (pages
			// "Copyright" et "Legal disclaimer") : identité de l'éditeur, propriété
			// intellectuelle, liens, responsabilité. La section Hébergement est à
			// compléter une fois l'hébergement du nouveau site choisi — elle était
			// d'ailleurs déjà absente de l'ancien site.
			'content' => "<h2>Éditeur du site</h2>\n<p>Ce site est édité par SPRINGCARD SAS.<br>\n2, voie La Cardon, 91120 Palaiseau, France<br>\nR.C.S. Évry B 429 665 482 — Code APE 722 Z</p>\n<p>Pour toute question, contactez-nous via notre <a href=\"/a-propos/#contact\">formulaire de contact</a>.</p>\n\n<h2>Hébergement</h2>\n<p><em>[Hébergeur à compléter une fois l'hébergement du nouveau site choisi.]</em></p>\n\n<h2>Propriété intellectuelle</h2>\n<p>Tous les éléments présents sur le site web de SpringCard (documents, images, textes, logiciels…) sont protégés par les droits d'auteur de SpringCard et/ou de ses fournisseurs et/ou de ses clients, selon les dispositions légales en vigueur en France et dans les autres pays.</p>\n<p>SpringCard autorise la copie et l'utilisation de ces éléments à condition que chaque copie soit exclusivement destinée à un usage informatif et non commercial en relation avec ses produits, qu'elle ne soit ni modifiée ni révisée de quelque manière que ce soit, et qu'elle conserve tous les avertissements sur les droits réservés dans une forme identique au document d'origine. Cette autorisation n'inclut pas la conception ou la présentation de ce site, ni tout autre élément téléchargeable régi par les stipulations d'un contrat de licence spécifique.</p>\n<p>Tous les noms de produits ou de sociétés mentionnés sur ce site peuvent être des marques déposées appartenant à leurs propriétaires respectifs.</p>\n<p>Tous les éléments sous licence (logiciels, kits de développement, firmwares) téléchargés depuis ce site sont exclusivement régis par les termes du contrat de licence qui les accompagne ; leur téléchargement implique l'acceptation de ce contrat. Toute reproduction ou redistribution non conforme à ces stipulations est expressément interdite par la loi.</p>\n\n<h2>Liens vers ce site</h2>\n<p>SpringCard autorise les liens vers ce site à condition que le site qui les propose :</p>\n<ul>\n<li>dirige vers un contenu de ce site sans le copier ;</li>\n<li>ne crée pas d'environnement ou de cadre (frame) autour de ce contenu ;</li>\n<li>ne fournisse pas d'informations erronées ou mensongères sur les produits et services de SpringCard ;</li>\n<li>ne donne pas une image trompeuse du rapport entre SpringCard et le créateur du site ;</li>\n<li>ne sous-entende pas que SpringCard cautionne ou soutient ses services et produits ;</li>\n<li>n'utilise pas les logos ou l'image commerciale de SpringCard sans accord écrit préalable ;</li>\n<li>ne présente pas de contenu obscène, diffamatoire, ou contraire à la loi française.</li>\n</ul>\n<p>SpringCard se réserve le droit de demander la suppression de ce lien à tout moment.</p>\n\n<h2>Limitation de responsabilité</h2>\n<p>Le contenu de ce site, y compris les logiciels et documents pouvant y être téléchargés, est fourni « en l'état », sans garantie d'aucune sorte, expresse ou tacite, autre que celle prévue par la loi en vigueur. SpringCard s'efforce de fournir un contenu fiable et à jour, mais ne peut garantir l'absence d'inexactitudes, d'erreurs ou d'omissions, et se réserve le droit de modifier ce contenu à tout moment sans préavis.</p>\n\n<h2>Conditions générales de vente</h2>\n<p>Toute commande est soumise à nos conditions générales de vente, disponibles sur demande auprès de notre service commercial.</p>",
		),
		array(
			'title'   => 'Politique de confidentialité',
			'slug'    => 'politique-de-confidentialite',
			'template' => 'page-legal.php',
			// Adapté de la page "Privacy Statement" réelle de springcard.com : les
			// principes généraux sont repris, mais la mention Google Analytics de
			// l'ancien site a été retirée puisqu'aucun outil de mesure d'audience
			// n'est pour l'instant utilisé sur le nouveau site.
			'content' => "<h2>Notre engagement</h2>\n<p>SpringCard respecte votre vie privée conformément à la réglementation française et européenne (RGPD). Ce site est conçu pour que vous puissiez le consulter et accéder à la majorité de son contenu sans avoir à transmettre de données personnelles.</p>\n\n<h2>Données collectées</h2>\n<p>Nous ne disposons que des informations que vous choisissez volontairement de nous transmettre, notamment via nos formulaires de contact ou par courrier électronique. Ces informations nous permettent de répondre à vos demandes et de vous fournir un support technique pertinent.</p>\n<p>Ces données ne sont jamais vendues à un tiers. Elles sont strictement réservées à SpringCard et, le cas échéant, à ses sous-traitants agissant pour son compte, sauf accord explicite de votre part ou obligation légale.</p>\n\n<h2>Cookies</h2>\n<p>Ce site n'utilise actuellement aucun cookie de mesure d'audience ni traceur publicitaire. Cette politique sera mise à jour si des outils de suivi venaient à être ajoutés, avec le recueil de votre consentement préalable conformément aux recommandations de la CNIL.</p>\n\n<h2>Vos droits</h2>\n<p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression des informations vous concernant. Pour exercer ce droit, contactez-nous via notre <a href=\"/a-propos/#contact\">formulaire de contact</a>.</p>\n\n<h2>Sécurité</h2>\n<p>SpringCard protège dans la mesure du possible les informations que vous lui confiez contre la consultation ou la modification par des tiers non autorisés. L'Internet étant un espace ouvert, SpringCard ne peut néanmoins garantir une protection absolue des informations transmises.</p>",
		),
	);
	$page_ids = array();
	foreach ( $pages as $p ) {
		$existing = get_page_by_path( $p['slug'] );
		if ( $existing ) {
			$page_ids[ $p['slug'] ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'post_status'  => 'publish',
				'post_content' => $p['content'],
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $p['template'] );
			$page_ids[ $p['slug'] ] = $id;
		}
	}

	// Gamme M519
	$gamme = get_page_by_path( 'm519', OBJECT, 'gamme' );
	if ( $gamme ) {
		$gamme_id = $gamme->ID;
	} else {
		$gamme_id = wp_insert_post(
			array(
				'post_type'    => 'gamme',
				'post_title'   => 'SpringSeed M519',
				'post_name'    => 'm519',
				'post_status'  => 'publish',
				'post_excerpt' => "Module RFID/NFC OEM haut de gamme et polyvalent, décliné en trois configurations selon votre besoin d'intégration.",
				'post_content' => "M519 est un module de lecture RFID/NFC 13.56 MHz conçu pour être intégré directement dans vos machines et équipements. Trois configurations d'antenne (externe, déportée ou intégrée) permettent d'adapter le module à votre boîtier, sans jamais reconcevoir votre produit.",
			)
		);
	}
	if ( $gamme_id && ! is_wp_error( $gamme_id ) ) {
		update_post_meta( $gamme_id, '_statut', 'actif' );

		if ( ! has_post_thumbnail( $gamme_id ) ) {
			$hero_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-hero-web.jpg' ), 'M519' );
			if ( $hero_id ) {
				set_post_thumbnail( $gamme_id, $hero_id );
			}
		}
		if ( ! get_post_meta( $gamme_id, '_visuel_fabrication_id', true ) ) {
			$fab_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-fabrication-web.jpg' ), 'M519 fabrication' );
			if ( $fab_id ) {
				update_post_meta( $gamme_id, '_visuel_fabrication_id', $fab_id );
			}
		}
		if ( ! get_post_meta( $gamme_id, '_visuel_kit_id', true ) ) {
			$kit_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/m519-kit-web.jpg' ), "M519 kit de développement" );
			if ( $kit_id ) {
				update_post_meta( $gamme_id, '_visuel_kit_id', $kit_id );
			}
		}
	}

	// Variantes (produit) de la gamme M519 — noms et infos repris de springcard.com/fr/products/
	$produits_seed = array(
		array(
			'slug'         => 'm519',
			'title'        => 'M519',
			'excerpt'      => 'Module OEM RFID/NFC HF compact, à intégrer avec une antenne externe. Compatible cartes sans contact, tags NFC et smartphones (Apple/Google Wallet).',
			'type_antenne' => 'non_fournie',
			'specs'        => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : externe, non fournie\nInterface : série ou coupleur USB\nModes : PC/SC coupleur, SpringProx Legacy",
			'image'        => 'm519-thumbnail-web.jpg',
		),
		array(
			'slug'         => 'm519-sam',
			'title'        => 'M519-SAM',
			'excerpt'      => "Disponible en deux versions : SAM(B) (antenne déportée symétrique) et SAM(U) (antenne déportée asymétrique), toutes deux avec un slot SAM intégré pour une sécurité matérielle renforcée.",
			'type_antenne' => 'separee',
			'specs'        => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : déportée (symétrique SAM(B) / asymétrique SAM(U))\nInterface : USB PC/SC (CCID), identique entre SAM(B) et SAM(U)\nModes : PC/SC coupleur, émulation carte ISO/IEC 14443 A, peer-to-peer ISO/IEC 18092\nSécurité : slot SAM intégré (NXP TDA8035, ISO/IEC 7816-2 & -3, T=0/T=1)",
			'image'        => '',
		),
		array(
			'slug'         => 'm519-suv',
			'title'        => 'M519-SUV',
			'excerpt'      => 'Module coupleur à antenne intégrée, interfaces USB et série, pour des transactions rapides et sécurisées (AES/ECC).',
			'type_antenne' => 'integree',
			'specs'        => "Fréquence : 13.56 MHz (HF RFID, NFC), puce NXP PN5190\nNormes RF : ISO/IEC 14443 A & B (NFC-A, NFC-B), ISO/IEC 15693 (NFC-V), ISO/IEC 18000-3M1 & 3M3, ISO/IEC 18092 (NFCIP-1)\nAntenne : intégrée, symétrique, diamètre 7 cm (portée 0–10 cm selon carte/antenne)\nInterface : USB PC/SC (CCID), série RS232/RS485/TTL\nModes : PC/SC coupleur, émulation carte ISO/IEC 14443 A, peer-to-peer ISO/IEC 18092\nSécurité : AES/ECC, stockage de clés sécurisé, composant sécurisé Microchip ATECC",
			'image'        => 'm519-suv-thumbnail-web.jpg',
		),
	);
	$produit_ids = array();
	foreach ( $produits_seed as $p ) {
		$existing = get_page_by_path( $p['slug'], OBJECT, 'produit' );
		if ( $existing ) {
			$id = $existing->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'produit',
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $p['excerpt'],
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_statut', 'actif' );
			update_post_meta( $id, '_gamme_id', $gamme_id );
			update_post_meta( $id, '_type_antenne', $p['type_antenne'] );
			update_post_meta( $id, '_specs', $p['specs'] );
			$produit_ids[ $p['slug'] ] = $id;

			if ( $p['image'] && ! has_post_thumbnail( $id ) ) {
				$img_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/produits/' . $p['image'] ), $p['title'] );
				if ( $img_id ) {
					set_post_thumbnail( $id, $img_id );
				}
			}
		}
	}

	// Secteurs
	$secteurs_seed = array(
		array(
			'slug'    => 'mobilite',
			'title'   => 'Mobilité',
			'icone'   => 'dashicons-car',
			'excerpt' => 'Titres de transport, contrôle d\'accès véhicules et bornes de validation sans contact.',
		),
		array(
			'slug'    => 'sante',
			'title'   => 'Santé',
			'icone'   => 'dashicons-plus-alt',
			'excerpt' => 'Cartes professionnelles et lecteurs sécurisés pour les professionnels de santé en mobilité.',
		),
		array(
			'slug'    => 'loisirs',
			'title'   => 'Loisirs',
			'icone'   => 'dashicons-tickets-alt',
			'excerpt' => 'Billetterie et contrôle d\'accès pour parcs, salles de spectacle et stades.',
		),
		array(
			'slug'    => 'retail',
			'title'   => 'Retail',
			'icone'   => 'dashicons-cart',
			'excerpt' => 'Programmes de fidélité et paiement sans contact en point de vente.',
		),
		array(
			'slug'    => 'securite',
			'title'   => 'Sécurité',
			'icone'   => 'dashicons-shield',
			'excerpt' => 'Badges et lecteurs pour le contrôle d\'accès aux bâtiments et zones sensibles.',
		),
		array(
			'slug'    => 'logistique',
			'title'   => 'Logistique',
			'icone'   => 'dashicons-archive',
			'excerpt' => 'Traçabilité des flux et identification des colis sur toute la chaîne logistique.',
		),
	);
	$secteur_ids = array();
	foreach ( $secteurs_seed as $s ) {
		$existing = get_page_by_path( $s['slug'], OBJECT, 'secteur' );
		if ( $existing ) {
			$id = $existing->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'secteur',
					'post_title'   => $s['title'],
					'post_name'    => $s['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $s['excerpt'],
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_icone', $s['icone'] );
			$secteur_ids[ $s['slug'] ] = $id;
		}
	}

	// Cas d'usage — repris de springcard.com/fr/blog/news (AFCare / Doctolib, lecteur "DoctoLecteur")
	$cas = get_page_by_path( 'afcare-doctolib-mobilite-sante', OBJECT, 'cas_usage' );
	if ( $cas ) {
		$cas_id = $cas->ID;
	} else {
		$cas_id = wp_insert_post(
			array(
				'post_type'    => 'cas_usage',
				'post_title'   => 'Comment SpringCard a accompagné AFCare et Doctolib dans une solution de mobilité pour les professionnels de santé',
				'post_name'    => 'afcare-doctolib-mobilite-sante',
				'post_status'  => 'publish',
				'post_excerpt' => "SpringCard a conçu sur mesure le DoctoLecteur, un lecteur de carte à puce PC/SC et Bluetooth pour les professionnels de santé, aujourd'hui utilisé par près de 600 praticiens avec AFCare et Doctolib.",
				'post_content' => "<p>Notre client François Sendra, Co-fondateur en 2017 de la start up AFCare est une entreprise qui accompagne les éditeurs de logiciels pour les professionnels de santé libéraux (médecins, infirmières, kinés, etc) qui souhaitent développer leur projet de mobilité lié au SESAM-Vitale.</p>\n<p>Dans un projet mené pour Doctolib, AFCare s'est tourné vers SpringCard pour concevoir un lecteur de carte à puce PC/SC et Bluetooth, répondant à des problématiques d'usage et de sécurité, dont l'intelligence est pilotée par une application mobile sous iOS et Android.</p>\n<p>Les principaux challenges rencontrés par AFCare ont notamment été de répondre aux différentes exigences d'usage et de sécurité des professionnels de santé, soit :</p>\n<ul>\n<li>un lecteur bi-fentes qui sache lire les cartes vitales ainsi que les cartes CPS,</li>\n<li>un lecteur qui puisse fonctionner en USB comme en Bluetooth,</li>\n<li>un lecteur de petite taille et léger, facilement transportable avec le plus d'autonomie possible,</li>\n<li>un lecteur sécurisé pour l'encryption des données BLE,</li>\n<li>un lecteur homologué et répondant aux besoins de sécurité du GIE SESAM-Vitale.</li>\n</ul>\n<p>C'est en étroite collaboration avec notre bureau R&amp;D que le lecteur a été conçu sur mesure et testé dans son environnement pendant plusieurs mois jusqu'à arriver à une solution parfaitement adaptée.</p>\n<p>Une fois ces étapes réalisées, le DoctoLecteur a été redesigné pour répondre à la guideline des produits Doctolib.</p>\n<p>Le lecteur est aujourd'hui utilisé par près de 600 professionnels de santé, qui en sont équipés à la fois, en cabinet en mode PC/SC, mais aussi et surtout, en mobilité grâce à son mode BLE, pour les médecins généralistes qui effectuent des visites à domicile.</p>\n<p>Depuis plus d'un an après sa commercialisation, les retours des médecins sont très positifs, aucune correction n'a été nécessaire tant au niveau électronique qu'au niveau firmware.</p>\n<p>D'après François Sendra : « Les prévisions sont à la hausse pour 2022 et 2023, avec pour stratégie d'étendre le réseau du DoctoLecteur aux kinésithérapeutes et aux infirmier(e)s. »</p>\n<p>La preuve d'un projet rondement mené par les équipes Doctolib, AFCare et SpringCard.</p>\n<p>AFCare s'est tourné vers SpringCard pour son savoir-faire de plus de 20 ans dans la conception de produits électroniques et pour la qualité de ses services.</p>\n<p>Dès les premiers échanges avec l'équipe du bureau d'étude, François Sendra raconte comment les équipes SpringCard ont su s'adapter pour répondre aux problématiques d'un produit sur mesure : « Une approche professionnelle dans le livrable tout en étant agile sur le développement du projet. »</p>\n<p>A ce jour, AFCare a été rachetée par Doctolib. SpringCard continue de collaborer avec son fidèle partenaire François Sendra, lui-même Directeur de l'Ingénierie Hardware chez Doctolib.</p>",
			)
		);
	}
	if ( $cas_id && ! is_wp_error( $cas_id ) ) {
		update_post_meta( $cas_id, '_client', 'AFCare / Doctolib' );

		if ( ! empty( $secteur_ids['sante'] ) ) {
			update_post_meta( $cas_id, '_secteurs', array( $secteur_ids['sante'] ) );
		}

		if ( ! has_post_thumbnail( $cas_id ) ) {
			$cas_img_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/cas-usage/doctolib-afcare.png' ), 'DoctoLecteur (AFCare / Doctolib)' );
			if ( $cas_img_id ) {
				set_post_thumbnail( $cas_id, $cas_img_id );
			}
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
			update_post_meta( $cu_id, '_client', $cu['client'] );
			if ( $cu['secteur_slug'] && ! empty( $secteur_ids[ $cu['secteur_slug'] ] ) ) {
				update_post_meta( $cu_id, '_secteurs', array( $secteur_ids[ $cu['secteur_slug'] ] ) );
			}
			if ( ! get_post_meta( $cu_id, 'rank_math_title', true ) ) {
				update_post_meta( $cu_id, 'rank_math_title', $cu['title'] . ' | SpringCard' );
			}
			if ( ! get_post_meta( $cu_id, 'rank_math_description', true ) ) {
				update_post_meta( $cu_id, 'rank_math_description', $cu['excerpt'] );
			}
		}
	}

	// Expertises (bureau d'études) — contenu fourni par le directeur technique.
	$expertises_seed = array(
		array( 'slug' => 'developpement-produit', 'title' => 'Développement produit', 'code' => 'DP', 'excerpt' => "Intégration du module M519 dans un lecteur, un terminal ou un équipement de contrôle d'accès : électronique, firmware, mécanique, interfaces et accompagnement jusqu'au prototype industrialisable." ),
		array( 'slug' => 'rfid-hf-nfc', 'title' => 'RFID HF & NFC', 'code' => 'RF', 'excerpt' => "Conception, simulation, mesure et optimisation d'antennes 13,56 MHz. Mise au point de l'accord RF dans l'environnement réel du produit et préparation des essais de qualification." ),
		array( 'slug' => 'cartes-transactions-securisees', 'title' => 'Cartes & transactions sécurisées', 'code' => 'CT', 'excerpt' => 'Expertise en DESFire, MIFARE DUOX, MIFARE Plus, Calypso et autres cartes ISO/IEC 14443 ou ISO/IEC 15693. Conception des applications, personnalisation, gestion des clés et sécurisation des transactions.' ),
		array( 'slug' => 'wallets', 'title' => 'Wallets', 'code' => 'WA', 'excerpt' => "Intégration des protocoles VAS, ECP1 et ECP2, SmartTap et de toutes les transactions NFC avec des passes dématérialisés ou identifiants sur mobiles. Accompagnement vers l'approbation par Apple et Google." ),
		array( 'slug' => 'protocoles-interfaces', 'title' => 'Protocoles & interfaces', 'code' => 'PI', 'excerpt' => "Lecteurs et équipements connectés en OSDP ou SSCP, avec intégration PC/SC sur USB ou interfaces série lorsque le projet l'exige. Architecture des échanges, sécurité de bout en bout et interopérabilité avec le système hôte." ),
		array( 'slug' => 'controle-acces-physique', 'title' => "Contrôle d'accès physique (PACS)", 'code' => 'PA', 'excerpt' => 'Identification sécurisée en mode transparent ou en mode autonome (smart reader), prise en compte de la sécurité physique du produit (tampers), ergonomie du lecteur complet, interopérabilité et certification SPAC.' ),
		array( 'slug' => 'firmware-logiciels-linux', 'title' => 'Firmware, logiciels & Linux embarqué', 'code' => 'FW', 'excerpt' => 'Développements bas niveau sur microcontrôleur, bibliothèques C et C#, pilotes et outils côté hôte. Conception de systèmes Linux embarqués, BSP, services de communication et intégration sécurisée des périphériques.' ),
		array( 'slug' => 'securite-conformite', 'title' => 'Sécurité & conformité', 'code' => 'SC', 'excerpt' => 'Intégration des SAM NXP, des composants CryptoAuthentication / Crypto Companion Atmel-Microchip et du secure element NXP SE052. Architecture cryptographique, démarrage et mise à jour sécurisés, gestion des secrets, SBOM et accompagnement transversal vers la conformité CRA.' ),
	);
	foreach ( $expertises_seed as $e ) {
		$existing = get_page_by_path( $e['slug'], OBJECT, 'expertise' );
		if ( $existing ) {
			$id = $existing->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'expertise',
					'post_title'   => $e['title'],
					'post_name'    => $e['slug'],
					'post_status'  => 'publish',
					'post_excerpt' => $e['excerpt'],
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_code', $e['code'] );
		}
	}

	// Article de blog (repris de springcard.com/fr/blog/news, dernier article en date)
	$article = get_page_by_path( 'springpass-experience-client-simplifiee', OBJECT, 'article' );
	if ( $article ) {
		$article_id = $article->ID;
	} else {
		$article_id = wp_insert_post(
			array(
				'post_type'    => 'article',
				'post_title'   => 'SpringPass : une expérience client simplifiée et fluide',
				'post_name'    => 'springpass-experience-client-simplifiee',
				'post_status'  => 'publish',
				'post_excerpt' => "Avec SpringPass, stockez cartes de fidélité, billets de transport et coupons directement dans votre smartphone. Une expérience client fluide et sécurisée, appuyée sur Apple et Google Wallet.",
				'post_content' => "<p>Avec SpringPass, vos clients bénéficient d'une expérience utilisateur intuitive et simplifiée. Ils peuvent stocker leurs cartes de fidélité, billets de transport, coupons et autres titres numériques directement dans leur smartphone, pour les avoir toujours à portée de main.</p>\n<p><strong>Comment SpringPass apporte une valeur ajoutée à votre entreprise ?</strong></p>\n<p><strong>Fidélisation client</strong> : proposez des offres et des promotions personnalisées directement via les pass numériques, encourageant ainsi la rétention des clients.</p>\n<p><strong>Augmentation de l'engagement</strong> : offrez une expérience client fluide et sans friction, favorisant une image de marque positive et moderne.</p>\n<p><strong>Optimisation des opérations</strong> : simplifiez la gestion des pass numériques et réduisez les coûts liés aux supports physiques.</p>\n<p><strong>Données précieuses</strong> : collectez des données précieuses sur les habitudes de consommation de vos clients pour mieux cibler vos offres marketing.</p>\n<p><strong>SpringPass : une solution sécurisée et polyvalente</strong></p>\n<p>SpringPass s'appuie sur les technologies Apple et Google Wallet pour garantir la sécurité des données de vos clients.</p>\n<p>De plus, SpringPass offre une grande flexibilité d'utilisation. Vous pouvez créer une variété de pass numériques pour répondre à vos besoins spécifiques, tels que des cartes de fidélité, des billets de transport, des coupons, des cartes d'accès et bien plus encore.</p>\n<p><strong>Prêt à offrir une expérience digitale exceptionnelle à vos clients ?</strong></p>\n<p>Contactez-nous dès aujourd'hui pour découvrir comment SpringPass peut vous aider à atteindre vos objectifs commerciaux.</p>",
			)
		);
	}
	if ( $article_id && ! is_wp_error( $article_id ) && ! has_post_thumbnail( $article_id ) ) {
		$article_img_id = springcard_sideload_theme_asset( get_theme_file_path( 'assets/images/blog/springpass.jpg' ), 'SpringPass' );
		if ( $article_img_id ) {
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
	// contenus clés, construits autour de nos technologies (RFID, NFC) plutôt que
	// de laisser Rank Math générer des valeurs par défaut génériques. Les clés de
	// post meta (rank_math_title, rank_math_description, rank_math_focus_keyword)
	// sont vérifiées dans le code du plugin ; elles sont écrites indépendamment de
	// l'activation du plugin et n'ont donc aucun effet tant que Rank Math n'est pas
	// actif. La homepage n'a pas de post dédié (front-page.php) : son titre/méta
	// se règlent une fois, à la main, dans Rank Math > Titres & Méta > Accueil.
	$seo_seed = array();

	if ( ! empty( $page_ids['solutions'] ) ) {
		$seo_seed[ $page_ids['solutions'] ] = array(
			'title'   => "Solutions RFID, NFC, IoT & contrôle d'accès | SpringCard",
			'desc'    => "SpringCard équipe vos projets RFID et NFC : mobilité, santé, retail, sécurité, logistique. Modules OEM M519, intégration sur mesure par notre bureau d'études.",
			'keyword' => 'solutions RFID NFC',
		);
	}
	if ( ! empty( $page_ids['bureau-etudes'] ) ) {
		$seo_seed[ $page_ids['bureau-etudes'] ] = array(
			'title'   => "Bureau d'études RFID/NFC sur mesure | SpringCard",
			'desc'    => "Conception de lecteurs RFID/NFC sur mesure : antennes HF, firmware, Linux embarqué, sécurité CRA/RED. Plus de 20 ans d'expertise électronique en France.",
			'keyword' => "bureau d'études RFID NFC",
		);
	}
	if ( ! empty( $page_ids['a-propos'] ) ) {
		$seo_seed[ $page_ids['a-propos'] ] = array(
			'title'   => 'À propos de SpringCard | Fabricant français de modules RFID/NFC',
			'desc'    => 'SpringCard conçoit et fabrique en France des modules RFID/NFC OEM depuis plus de 20 ans. Découvrez notre équipe, notre histoire et nos valeurs.',
			'keyword' => 'fabricant modules RFID NFC France',
		);
	}
	if ( $gamme_id && ! is_wp_error( $gamme_id ) ) {
		$seo_seed[ $gamme_id ] = array(
			'title'   => 'M519 — Module RFID/NFC OEM 13.56 MHz | SpringCard',
			'desc'    => "Module de lecture RFID/NFC OEM 13.56 MHz, 3 configurations d'antenne. Intégrez la technologie sans contact dans vos équipements avec SpringCard.",
			'keyword' => 'module RFID NFC OEM',
		);
	}
	if ( ! empty( $produit_ids['m519'] ) ) {
		$seo_seed[ $produit_ids['m519'] ] = array(
			'title'   => 'M519 — Module RFID/NFC OEM antenne externe | SpringCard',
			'desc'    => 'Module RFID/NFC compact NXP PN5190, antenne externe, compatible ISO 14443/15693, NFC et Apple/Google Wallet. Fiche technique complète.',
			'keyword' => 'module RFID NFC antenne externe',
		);
	}
	if ( ! empty( $produit_ids['m519-sam'] ) ) {
		$seo_seed[ $produit_ids['m519-sam'] ] = array(
			'title'   => 'M519-SAM — Module RFID/NFC OEM avec slot SAM sécurisé | SpringCard',
			'desc'    => 'Module RFID/NFC 13.56 MHz avec slot SAM intégré pour une sécurité renforcée. Versions SAM(B) et SAM(U), interface USB PC/SC.',
			'keyword' => 'module RFID NFC SAM sécurisé',
		);
	}
	if ( ! empty( $produit_ids['m519-suv'] ) ) {
		$seo_seed[ $produit_ids['m519-suv'] ] = array(
			'title'   => 'M519-SUV — Coupleur RFID/NFC à antenne intégrée | SpringCard',
			'desc'    => 'Coupleur RFID/NFC OEM à antenne intégrée, transactions sécurisées AES/ECC, interfaces USB et série. Le module SpringCard prêt à intégrer.',
			'keyword' => 'coupleur RFID NFC antenne intégrée',
		);
	}

	$secteurs_seo = array(
		'mobilite'   => array(
			'title'   => "RFID/NFC pour la mobilité : titres de transport, accès véhicules | SpringCard",
			'desc'    => "Solutions RFID/NFC SpringCard pour la mobilité : titres de transport, contrôle d'accès véhicules, bornes de validation sans contact.",
			'keyword' => 'RFID NFC mobilité',
		),
		'sante'      => array(
			'title'   => 'RFID/NFC pour la santé : cartes professionnelles sécurisées | SpringCard',
			'desc'    => 'Modules RFID/NFC SpringCard pour le secteur santé : cartes professionnelles, lecteurs sécurisés et mobiles pour les praticiens.',
			'keyword' => 'RFID NFC santé',
		),
		'loisirs'    => array(
			'title'   => "RFID/NFC pour les loisirs : billetterie et contrôle d'accès | SpringCard",
			'desc'    => "SpringCard équipe la billetterie et le contrôle d'accès des parcs, salles de spectacle et stades avec des modules RFID/NFC OEM.",
			'keyword' => 'RFID NFC billetterie',
		),
		'retail'     => array(
			'title'   => 'RFID/NFC pour le retail : fidélité et paiement sans contact | SpringCard',
			'desc'    => 'Modules RFID/NFC SpringCard pour le retail : programmes de fidélité et paiement sans contact en point de vente.',
			'keyword' => 'RFID NFC retail',
		),
		'securite'   => array(
			'title'   => "RFID/NFC pour le contrôle d'accès et la sécurité | SpringCard",
			'desc'    => "Badges et lecteurs RFID/NFC SpringCard pour le contrôle d'accès aux bâtiments et zones sensibles.",
			'keyword' => "RFID NFC contrôle d'accès",
		),
		'logistique' => array(
			'title'   => 'RFID/NFC pour la logistique : traçabilité et identification | SpringCard',
			'desc'    => "Modules RFID/NFC SpringCard pour la traçabilité des flux et l'identification des colis sur toute la chaîne logistique.",
			'keyword' => 'RFID NFC logistique',
		),
	);
	foreach ( $secteurs_seo as $slug => $meta ) {
		if ( ! empty( $secteur_ids[ $slug ] ) ) {
			$seo_seed[ $secteur_ids[ $slug ] ] = $meta;
		}
	}

	if ( $cas_id && ! is_wp_error( $cas_id ) ) {
		$seo_seed[ $cas_id ] = array(
			'title'   => 'AFCare & Doctolib : un lecteur RFID/NFC Bluetooth sur mesure | SpringCard',
			'desc'    => 'Comment SpringCard a conçu un lecteur de carte à puce PC/SC et Bluetooth sur mesure pour AFCare et Doctolib, utilisé par 600 professionnels de santé.',
			'keyword' => 'lecteur carte à puce Bluetooth sur mesure',
		);
	}
	if ( $article_id && ! is_wp_error( $article_id ) ) {
		$seo_seed[ $article_id ] = array(
			'title'   => 'SpringPass : cartes de fidélité et billets dans Apple & Google Wallet | SpringCard',
			'desc'    => 'SpringPass permet de stocker cartes de fidélité, billets et coupons directement dans Apple Wallet et Google Wallet, pour une expérience client fluide.',
			'keyword' => 'Apple Wallet Google Wallet fidélité',
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

	update_option( 'rank-math-options-titles', $rank_math_titles );

	// Menu principal : Accueil · Produits · Bureau d'études · Solutions · À propos
	// (architecture verrouillée du projet), avec sous-menu Blog / Blog technique / Contact
	// sous "À propos". "Blog technique" pointe vers tech.springcard.com (site
	// séparé, conservé tel quel) plutôt que vers un contenu géré ici.
	$menu_id = wp_create_nav_menu( 'Menu principal' );
	if ( is_wp_error( $menu_id ) ) {
		$term    = get_term_by( 'name', 'Menu principal', 'nav_menu' );
		$menu_id = $term ? $term->term_id : 0;
	}

	// wp_update_nav_menu_item() with a 0 db id always INSERTS a new item — it
	// isn't idempotent like the rest of this script. Guard the whole block on
	// the menu being empty, otherwise relaunching this seed (e.g. once for the
	// French content, once for the bilingual one) doubles every menu entry.
	if ( $menu_id && ! wp_get_nav_menu_items( $menu_id ) ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Accueil',
				'menu-item-url'    => home_url( '/' ),
				'menu-item-status' => 'publish',
			)
		);

		if ( $gamme_id && ! is_wp_error( $gamme_id ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => 'Produits',
					'menu-item-object'    => 'gamme',
					'menu-item-object-id' => $gamme_id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['bureau-etudes'] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => "Bureau d'études",
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['bureau-etudes'],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['solutions'] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => 'Solutions',
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['solutions'],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $page_ids['a-propos'] ) ) {
			$apropos_item_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => 'À propos',
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_ids['a-propos'],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);

			if ( $apropos_item_id && ! is_wp_error( $apropos_item_id ) ) {
				$blog_link    = get_post_type_archive_link( 'article' );
				$contact_link = trailingslashit( get_permalink( $page_ids['a-propos'] ) ) . '#contact';

				if ( $blog_link ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => 'Blog',
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
						'menu-item-title'     => 'Blog technique',
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
						'menu-item-title'     => 'Contact',
						'menu-item-url'       => $contact_link,
						'menu-item-status'    => 'publish',
						'menu-item-parent-id' => $apropos_item_id,
					)
				);
			}
		}

	}

	if ( $menu_id ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

}
