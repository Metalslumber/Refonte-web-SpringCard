<?php
/**
 * Template Name: Bureau d'études
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$contact_url   = springcard_get_contact_url();
	$solutions_url = springcard_get_page_url_by_template( 'page-solutions.php' );
	$gammes        = springcard_get_gammes_actives();
	$gamme_url     = ! empty( $gammes ) ? get_permalink( $gammes[0] ) : '';
	$expertises  = get_posts(
		springcard_lang_filter(
			array(
				'post_type'      => 'expertise',
				'posts_per_page' => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		)
	);
	$case_study  = get_posts(
		springcard_lang_filter(
			array(
				'post_type'      => 'cas_usage',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		)
	);
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="hero-split">
			<div class="hero-split-copy">
				<div class="eyebrow"><?php springcard_e( "Bureau d'études" ); ?></div>
				<h1 style="font-size:1.875rem; max-width:580px; margin-bottom:14px;"><?php springcard_e( 'Votre prochain produit est déjà en germe' ); ?></h1>
				<?php if ( get_the_content() ) : ?>
					<div class="prose" style="max-width:560px; margin-bottom:22px;"><?php the_content(); ?></div>
				<?php endif; ?>
				<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php springcard_e( 'Parler de votre projet' ); ?></a>
			</div>
			<div class="hero-split-visual reveal">
				<div class="hero-visual-panel">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/bureau-etudes/hero-module.png' ) ); ?>" alt="<?php springcard_attr_e( 'Module M519' ); ?>">
				</div>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $expertises ) ) : ?>
	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php springcard_e( 'Expertises' ); ?></div>
			<?php if ( $solutions_url ) : ?>
				<p class="prose">
					<?php
					printf(
						/* translators: %s: lien vers la page Solutions. */
						wp_kses_post( springcard_t( 'Ces expertises sont mobilisées sur des projets RFID/NFC de %s.' ) ),
						'<a href="' . esc_url( $solutions_url ) . '">' . esc_html( springcard_t( 'tous secteurs' ) ) . '</a>'
					);
					?>
				</p>
			<?php endif; ?>
		</div>
		<div class="grid grid-3">
			<?php foreach ( $expertises as $expertise ) : ?>
				<div class="card reveal" id="expertise-<?php echo esc_attr( $expertise->post_name ); ?>">
					<?php $code = get_post_meta( $expertise->ID, '_code', true ); ?>
					<?php if ( $code ) : ?>
						<div class="ic"><?php echo esc_html( $code ); ?></div>
					<?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $expertise ) ); ?></h3>
					<p><?php echo esc_html( get_the_excerpt( $expertise ) ); ?></p>
				</div>
			<?php endforeach; ?>
			<div class="card reveal ghost"><?php springcard_e( '+ Future expertise' ); ?></div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php springcard_e( 'Innovation' ); ?></div>
			<h2><?php springcard_e( 'Des projets qui ouvrent la voie' ); ?></h2>
		</div>
		<div class="prose reveal" style="margin-bottom:22px;">
			<p><?php springcard_e( "Ancrée dans une démarche d'innovation agile, l'équipe SpringCard peut prendre en charge le projet le plus atypique qui doit valider une technologie, convaincre un client stratégique ou rendre visible une nouvelle direction." ); ?></p>
			<p><?php springcard_e( "Démonstrateur pour le prochain salon, preuve de concept, prototype fonctionnel ou première version d'un futur produit prêt à être industrialisé : nous réunissons rapidement le hardware, le firmware, le logiciel et la sécurité qui démontreront votre valeur ajoutée et convaincront vos clients ou les décideurs." ); ?></p>
			<p><?php springcard_e( "Notre démarche ? Lever les inconnues techniques, élaguer la complexité inutile, raccourcir le chemin vers une démonstration crédible afin de transmettre à votre équipe une base solide qu'elle pourra maîtriser pleinement." ); ?></p>
		</div>
		<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php springcard_e( 'Construire un démonstrateur' ); ?></a>
	</div>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php springcard_e( 'Collaboration' ); ?></div>
			<h2><?php springcard_e( 'Trois façons de travailler avec nous' ); ?></h2>
		</div>
		<div class="grid grid-3">
			<div class="card reveal">
				<h3><?php springcard_e( 'Accélérer votre produit' ); ?></h3>
				<p>
						<?php
						if ( $gamme_url ) {
							printf(
								/* translators: %s: lien vers la gamme M519. */
								wp_kses_post( springcard_t( "Vous partez du %s ou d'une architecture existante. Nous traitons les points spécialisés : antenne, intégration RF, protocole, carte sécurisée, cryptographie, pilote ou logiciel embarqué." ) ),
								'<a href="' . esc_url( $gamme_url ) . '">' . esc_html( springcard_t( 'M519' ) ) . '</a>'
							);
						} else {
							springcard_e( "Vous partez du M519 ou d'une architecture existante. Nous traitons les points spécialisés : antenne, intégration RF, protocole, carte sécurisée, cryptographie, pilote ou logiciel embarqué." );
						}
						?>
					</p>
			</div>
			<div class="card reveal">
				<h3><?php springcard_e( 'Explorer une nouvelle voie' ); ?></h3>
				<p><?php springcard_e( "Nous réalisons un prototype ou un démonstrateur complet pour tester un usage, préparer un salon, sécuriser un choix d'architecture ou convaincre avant d'engager l'industrialisation." ); ?></p>
			</div>
			<div class="card reveal">
				<h3><?php springcard_e( 'Acquérir une base éprouvée' ); ?></h3>
				<p><?php springcard_e( 'Vous pouvez acquérir une licence sur une bibliothèque logicielle, une IP ou un dossier de définition de produit conçu par SpringCard, puis fabriquer et faire évoluer la solution dans le cadre convenu.' ); ?></p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="section-head reveal"><div class="eyebrow"><?php springcard_e( 'Méthode' ); ?></div></div>
		<div class="steps">
			<div class="reveal">
				<div class="num">01</div>
				<h3><?php springcard_e( 'Étude de faisabilité' ); ?></h3>
				<p><?php springcard_e( 'Cadrage technique et contraintes projet.' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">02</div>
				<h3><?php springcard_e( 'Prototypage' ); ?></h3>
				<p><?php springcard_e( 'Preuve de concept sur module existant ou nouveau.' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">03</div>
				<h3><?php springcard_e( 'Développement' ); ?></h3>
				<p><?php springcard_e( 'Industrialisation hardware, firmware, software.' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">04</div>
				<h3><?php springcard_e( 'Qualification' ); ?></h3>
				<p><?php springcard_e( 'Tests, certification, mise en production.' ); ?></p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php springcard_e( 'Livrables' ); ?></div>
			<h2><?php springcard_e( 'Des briques techniques maîtrisées' ); ?></h2>
		</div>
		<div class="prose reveal">
			<p><?php springcard_e( "Selon le projet, SpringCard livre un prototype, un dossier de conception, du code source, une bibliothèque documentée, des outils de test ou un transfert de compétences. Le périmètre, la propriété intellectuelle, les conditions de licence et le niveau d'accompagnement sont définis dès le départ." ); ?></p>
			<p><?php springcard_e( 'Les projets peuvent être conduits et documentés à 100 % en français ou en anglais, avec des interlocuteurs techniques capables de travailler directement avec vos équipes internationales.' ); ?></p>
		</div>
	</div>

	<?php if ( ! empty( $case_study ) ) : $cas = $case_study[0]; ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow"><?php springcard_e( 'Ils nous ont confié leur développement sur mesure' ); ?></div>
		<div class="case reveal">
			<?php if ( has_post_thumbnail( $cas ) ) : ?>
				<div class="case-img"><?php echo get_the_post_thumbnail( $cas, 'medium', array( 'alt' => get_the_title( $cas ) ) ); ?></div>
			<?php else : ?>
				<div class="case-img"><?php springcard_e( '[ Visuel projet ]' ); ?></div>
			<?php endif; ?>
			<div class="case-body">
				<h3><?php echo esc_html( get_the_title( $cas ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $cas ) ); ?></p>
				<a class="go" href="<?php echo esc_url( get_permalink( $cas ) ); ?>"><?php springcard_e( "Lire le cas d'usage →" ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php springcard_e( 'Discutons de votre projet' ); ?></h3>
				<p><?php springcard_e( 'Un premier échange avec un ingénieur, sans engagement.' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php springcard_e( 'Décrire votre besoin' ); ?></a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
