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

	$contact_url = springcard_get_contact_url();
	$expertises  = get_posts(
		array(
			'post_type'      => 'expertise',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	$case_study  = get_posts(
		array(
			'post_type'      => 'cas_usage',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="hero-split">
			<div class="hero-split-copy">
				<div class="eyebrow"><?php esc_html_e( "Bureau d'études", 'springcard' ); ?></div>
				<h1 style="font-size:1.875rem; max-width:580px; margin-bottom:14px;"><?php esc_html_e( 'Votre prochain produit est déjà en germe', 'springcard' ); ?></h1>
				<?php if ( get_the_content() ) : ?>
					<div class="prose" style="max-width:560px; margin-bottom:22px;"><?php the_content(); ?></div>
				<?php endif; ?>
				<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Parler de votre projet', 'springcard' ); ?></a>
			</div>
			<div class="hero-split-visual reveal">
				<div class="hero-visual-panel">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/bureau-etudes/hero-module.png' ) ); ?>" alt="<?php esc_attr_e( 'Module M519', 'springcard' ); ?>">
				</div>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $expertises ) ) : ?>
	<div class="section">
		<div class="section-head reveal"><div class="eyebrow"><?php esc_html_e( 'Expertises', 'springcard' ); ?></div></div>
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
			<div class="card reveal ghost"><?php esc_html_e( '+ Future expertise', 'springcard' ); ?></div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php esc_html_e( 'Innovation', 'springcard' ); ?></div>
			<h2><?php esc_html_e( 'Des projets qui ouvrent la voie', 'springcard' ); ?></h2>
		</div>
		<div class="prose reveal" style="margin-bottom:22px;">
			<p><?php esc_html_e( "Ancrée dans une démarche d'innovation agile, l'équipe SpringCard peut prendre en charge le projet le plus atypique qui doit valider une technologie, convaincre un client stratégique ou rendre visible une nouvelle direction.", 'springcard' ); ?></p>
			<p><?php esc_html_e( "Démonstrateur pour le prochain salon, preuve de concept, prototype fonctionnel ou première version d'un futur produit prêt à être industrialisé : nous réunissons rapidement le hardware, le firmware, le logiciel et la sécurité qui démontreront votre valeur ajoutée et convaincront vos clients ou les décideurs.", 'springcard' ); ?></p>
			<p><?php esc_html_e( "Notre démarche ? Lever les inconnues techniques, élaguer la complexité inutile, raccourcir le chemin vers une démonstration crédible afin de transmettre à votre équipe une base solide qu'elle pourra maîtriser pleinement.", 'springcard' ); ?></p>
		</div>
		<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Construire un démonstrateur', 'springcard' ); ?></a>
	</div>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php esc_html_e( 'Collaboration', 'springcard' ); ?></div>
			<h2><?php esc_html_e( 'Trois façons de travailler avec nous', 'springcard' ); ?></h2>
		</div>
		<div class="grid grid-3">
			<div class="card reveal">
				<h3><?php esc_html_e( 'Accélérer votre produit', 'springcard' ); ?></h3>
				<p><?php esc_html_e( "Vous partez du M519 ou d'une architecture existante. Nous traitons les points spécialisés : antenne, intégration RF, protocole, carte sécurisée, cryptographie, pilote ou logiciel embarqué.", 'springcard' ); ?></p>
			</div>
			<div class="card reveal">
				<h3><?php esc_html_e( 'Explorer une nouvelle voie', 'springcard' ); ?></h3>
				<p><?php esc_html_e( "Nous réalisons un prototype ou un démonstrateur complet pour tester un usage, préparer un salon, sécuriser un choix d'architecture ou convaincre avant d'engager l'industrialisation.", 'springcard' ); ?></p>
			</div>
			<div class="card reveal">
				<h3><?php esc_html_e( 'Acquérir une base éprouvée', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Vous pouvez acquérir une licence sur une bibliothèque logicielle, une IP ou un dossier de définition de produit conçu par SpringCard, puis fabriquer et faire évoluer la solution dans le cadre convenu.', 'springcard' ); ?></p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="section-head reveal"><div class="eyebrow"><?php esc_html_e( 'Méthode', 'springcard' ); ?></div></div>
		<div class="steps">
			<div class="reveal">
				<div class="num">01</div>
				<h3><?php esc_html_e( 'Étude de faisabilité', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Cadrage technique et contraintes projet.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">02</div>
				<h3><?php esc_html_e( 'Prototypage', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Preuve de concept sur module existant ou nouveau.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">03</div>
				<h3><?php esc_html_e( 'Développement', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Industrialisation hardware, firmware, software.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">04</div>
				<h3><?php esc_html_e( 'Qualification', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Tests, certification, mise en production.', 'springcard' ); ?></p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="section-head reveal">
			<div class="eyebrow"><?php esc_html_e( 'Livrables', 'springcard' ); ?></div>
			<h2><?php esc_html_e( 'Des briques techniques maîtrisées', 'springcard' ); ?></h2>
		</div>
		<div class="prose reveal">
			<p><?php esc_html_e( "Selon le projet, SpringCard livre un prototype, un dossier de conception, du code source, une bibliothèque documentée, des outils de test ou un transfert de compétences. Le périmètre, la propriété intellectuelle, les conditions de licence et le niveau d'accompagnement sont définis dès le départ.", 'springcard' ); ?></p>
			<p><?php esc_html_e( 'Les projets peuvent être conduits et documentés à 100 % en français ou en anglais, avec des interlocuteurs techniques capables de travailler directement avec vos équipes internationales.', 'springcard' ); ?></p>
		</div>
	</div>

	<?php if ( ! empty( $case_study ) ) : $cas = $case_study[0]; ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow"><?php esc_html_e( 'Ils nous ont confié leur développement sur mesure', 'springcard' ); ?></div>
		<div class="case reveal">
			<?php if ( has_post_thumbnail( $cas ) ) : ?>
				<div class="case-img"><?php echo get_the_post_thumbnail( $cas, 'medium', array( 'alt' => get_the_title( $cas ) ) ); ?></div>
			<?php else : ?>
				<div class="case-img"><?php esc_html_e( '[ Visuel projet ]', 'springcard' ); ?></div>
			<?php endif; ?>
			<div class="case-body">
				<h3><?php echo esc_html( get_the_title( $cas ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $cas ) ); ?></p>
				<a class="go" href="<?php echo esc_url( get_permalink( $cas ) ); ?>"><?php esc_html_e( "Lire le cas d'usage →", 'springcard' ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php esc_html_e( 'Discutons de votre projet', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Un premier échange avec un ingénieur, sans engagement.', 'springcard' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Décrire votre besoin', 'springcard' ); ?></a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
