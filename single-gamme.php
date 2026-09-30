<?php
/**
 * Single "gamme" template: full-bleed hero, variant "at a glance" cards,
 * feature rows, comparison table, resources, related cas d'usage.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$gamme_id       = get_the_ID();
	$bureau_url     = springcard_get_page_url_by_template( 'page-bureau-etudes.php' );
	$contact_url    = springcard_get_contact_url();
	$variantes      = springcard_get_produits_actifs( $gamme_id );

	// Version féminine de "_statut" pour l'accord avec "gamme" (les libellés
	// partagés avec "produit" dans springcard_statut_options() sont neutres/masculins).
	$statut_value    = get_post_meta( $gamme_id, '_statut', true );
	$statut_labels_f = array(
		'actif'   => springcard_t( 'active' ),
		'archive' => springcard_t( 'archivée' ),
		'a_venir' => springcard_t( 'à venir' ),
	);
	$statut_label = isset( $statut_labels_f[ $statut_value ] ) ? $statut_labels_f[ $statut_value ] : $statut_labels_f['actif'];

	$hero_url        = has_post_thumbnail( $gamme_id ) ? get_the_post_thumbnail_url( $gamme_id, 'springcard-hero' ) : '';
	$fabrication_url = springcard_get_meta_image_url( $gamme_id, '_visuel_fabrication_id', 'springcard-feature' );
	$kit_url         = springcard_get_meta_image_url( $gamme_id, '_visuel_kit_id', 'springcard-feature' );

	// Première fiche technique disponible parmi les variantes, pour le CTA secondaire du hero.
	$hero_pdf_url = '';
	foreach ( $variantes as $variante ) {
		$hero_pdf_url = springcard_get_fiche_technique_url( $variante->ID );
		if ( $hero_pdf_url ) {
			break;
		}
	}

	// Union des libellés de caractéristiques présents sur au moins une variante,
	// dans leur ordre de première apparition.
	$specs_by_variant = array();
	$all_labels        = array();
	foreach ( $variantes as $variante ) {
		$pairs = array();
		foreach ( springcard_get_produit_specs( $variante->ID ) as $spec ) {
			$pairs[ $spec['label'] ] = $spec['value'];
			if ( ! in_array( $spec['label'], $all_labels, true ) ) {
				$all_labels[] = $spec['label'];
			}
		}
		$specs_by_variant[ $variante->ID ] = $pairs;
	}

	// Cas d'usage liés à au moins une des variantes actives de cette gamme.
	$cas_usage = array();
	foreach ( $variantes as $variante ) {
		foreach ( springcard_get_cas_usage_by( '_produits', $variante->ID ) as $cas ) {
			$cas_usage[ $cas->ID ] = $cas;
		}
	}
	?>

	<nav class="gamme-nav" aria-label="<?php springcard_attr_e( 'Navigation rapide de la gamme' ); ?>">
		<div class="gamme-nav-inner">
			<div class="gamme-nav-name"><?php the_title(); ?></div>
			<ul class="gamme-nav-links">
				<?php if ( ! empty( $variantes ) ) : ?>
					<li><a href="#variantes"><?php springcard_e( 'Variantes' ); ?></a></li>
					<li><a href="#comparer"><?php springcard_e( 'Comparer' ); ?></a></li>
				<?php endif; ?>
				<li><a href="#ressources"><?php springcard_e( 'Ressources' ); ?></a></li>
			</ul>
			<a class="gamme-nav-cta" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>"><?php springcard_e( "Bureau d'études" ); ?> →</a>
		</div>
	</nav>

	<section class="gamme-hero">
		<?php if ( $hero_url ) : ?>
			<div class="gamme-hero-media" style="background-image:url('<?php echo esc_url( $hero_url ); ?>');"></div>
		<?php endif; ?>
		<div class="gamme-hero-scrim"></div>
		<div class="gamme-hero-inner">
			<div class="eyebrow eyebrow-on-dark">
				<?php
				/* translators: %s: statut label, e.g. "active", "archivée", "à venir". */
				printf( esc_html( springcard_t( 'Gamme %s' ) ), esc_html( $statut_label ) );
				?>
			</div>
			<h1 class="gamme-hero-title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="gamme-hero-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<div class="btn-row">
				<?php if ( ! empty( $variantes ) ) : ?>
					<a class="btn btn-primary" href="#comparer"><?php springcard_e( 'Comparer les variantes' ); ?></a>
				<?php else : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>"><?php springcard_e( "Découvrir le bureau d'études" ); ?></a>
				<?php endif; ?>
				<?php if ( $hero_pdf_url ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( $hero_pdf_url ); ?>" target="_blank" rel="noopener noreferrer"><?php springcard_e( 'Télécharger la fiche technique' ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $variantes ) ) : ?>
				<div class="gamme-hero-facts">
					<?php foreach ( $variantes as $variante ) : ?>
						<div>
							<div class="k"><?php echo esc_html( get_the_title( $variante ) ); ?></div>
							<div class="v"><?php echo esc_html( springcard_antenne_label( get_post_meta( $variante->ID, '_type_antenne', true ) ) ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( get_the_content() ) : ?>
	<div class="section tight">
		<div class="prose reveal" style="max-width:640px; margin:0 auto; text-align:center;"><?php the_content(); ?></div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $variantes ) ) : ?>
	<div class="section tight" id="variantes">
		<div class="section-head-center reveal">
			<div class="eyebrow-x"><?php springcard_e( "D'un coup d'œil" ); ?></div>
			<h2>
				<?php
				/* translators: %s: gamme name, e.g. "M519". */
				printf( esc_html( springcard_t( "Trois façons d'intégrer %s" ) ), esc_html( get_the_title( $gamme_id ) ) );
				?>
			</h2>
		</div>
		<div class="glance-grid">
			<?php foreach ( $variantes as $variante ) :
				$type_antenne = get_post_meta( $variante->ID, '_type_antenne', true );
				$pdf_url      = springcard_get_fiche_technique_url( $variante->ID );
				?>
				<div class="glance-card reveal" id="produit-<?php echo esc_attr( $variante->post_name ); ?>">
					<div class="glance-media">
						<?php if ( $type_antenne ) : ?>
							<span class="tag-overlay"><?php echo esc_html( springcard_antenne_label( $type_antenne ) ); ?></span>
						<?php endif; ?>
						<?php if ( has_post_thumbnail( $variante ) ) : ?>
							<?php echo get_the_post_thumbnail( $variante, 'springcard-feature', array( 'alt' => get_the_title( $variante ) ) ); ?>
						<?php else : ?>
							<div class="glance-placeholder" aria-hidden="true"></div>
						<?php endif; ?>
					</div>
					<div class="glance-body">
						<h3><?php echo esc_html( get_the_title( $variante ) ); ?></h3>
						<?php if ( has_excerpt( $variante ) ) : ?>
							<p><?php echo esc_html( get_the_excerpt( $variante ) ); ?></p>
						<?php endif; ?>
						<?php if ( $pdf_url ) : ?>
							<a class="go" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer"><?php springcard_e( 'Voir la fiche technique →' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $fabrication_url ) : ?>
	<div class="section surface">
		<div class="feature-row">
			<div class="feature-media reveal"><img src="<?php echo esc_url( $fabrication_url ); ?>" alt="" /></div>
			<div class="feature-copy reveal">
				<div class="num"><?php springcard_e( 'Fabrication' ); ?></div>
				<h3><?php springcard_e( 'Du prototype à la série, sans rupture de forme' ); ?></h3>
				<p><?php springcard_e( 'Même module, du premier essai en laboratoire jusqu\'aux volumes de production. Aucune reconception nécessaire quand vous passez à l\'échelle.' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $kit_url ) : ?>
	<div class="section">
		<div class="feature-row reverse">
			<div class="feature-media reveal"><img src="<?php echo esc_url( $kit_url ); ?>" alt="" /></div>
			<div class="feature-copy reveal">
				<div class="num"><?php springcard_e( "Bureau d'études" ); ?></div>
				<h3><?php springcard_e( 'Un kit pour démarrer en un après-midi' ); ?></h3>
				<p><?php springcard_e( 'SDK, exemples de code et kit de développement pour valider votre intégration rapidement, puis notre bureau d\'études prend le relais pour le sur-mesure.' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $variantes ) ) : ?>
	<div class="section tight" id="comparer">
		<div class="section-head-center reveal">
			<div class="eyebrow-x"><?php springcard_e( 'Comparer' ); ?></div>
			<h2><?php springcard_e( 'Toutes les caractéristiques, côte à côte' ); ?></h2>
		</div>
		<div class="compare-scroll reveal">
			<table class="compare">
				<thead>
				<tr>
					<th><?php springcard_e( 'Caractéristique' ); ?></th>
					<?php foreach ( $variantes as $variante ) : ?>
						<th><?php echo esc_html( get_the_title( $variante ) ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $all_labels as $label ) : ?>
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<?php foreach ( $variantes as $variante ) : ?>
							<td><?php echo esc_html( isset( $specs_by_variant[ $variante->ID ][ $label ] ) ? $specs_by_variant[ $variante->ID ][ $label ] : '-' ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>

	<div class="section" id="ressources">
		<div class="section-head reveal">
			<div class="eyebrow"><?php springcard_e( 'Ressources' ); ?></div>
			<h2 style="font-size:1.25rem;"><?php springcard_e( 'De quoi démarrer' ); ?></h2>
		</div>
		<div class="grid grid-4">
			<a class="card reveal" href="<?php echo ! empty( $variantes ) ? '#comparer' : '#'; ?>">
				<h3><?php springcard_e( 'Documentation technique' ); ?></h3>
				<p><?php springcard_e( 'Datasheets et guides d\'intégration, par variante.' ); ?></p>
				<span class="go"><?php springcard_e( 'Voir les fiches →' ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<h3><?php springcard_e( 'SDK &amp; outils' ); ?></h3>
				<p><?php springcard_e( 'Librairies et exemples de code.' ); ?></p>
				<span class="go"><?php springcard_e( "Voir le bureau d'études →" ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $contact_url ); ?>">
				<h3><?php springcard_e( 'Kit de démarrage' ); ?></h3>
				<p><?php springcard_e( 'Pour prototyper rapidement.' ); ?></p>
				<span class="go"><?php springcard_e( 'Demander un kit →' ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $contact_url ); ?>">
				<h3><?php springcard_e( 'Support technique' ); ?></h3>
				<p><?php springcard_e( "Une équipe d'ingénieurs disponible." ); ?></p>
				<span class="go"><?php springcard_e( 'Contacter →' ); ?></span>
			</a>
		</div>
	</div>

	<?php if ( ! empty( $cas_usage ) ) : ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow">
			<?php
			/* translators: %s: gamme name. */
			printf( esc_html( springcard_t( 'Construit avec %s' ) ), esc_html( get_the_title( $gamme_id ) ) );
			?>
		</div>
		<div class="grid grid-3">
			<?php foreach ( $cas_usage as $cas ) : ?>
				<a class="card reveal" href="<?php echo esc_url( get_permalink( $cas ) ); ?>">
					<h3><?php echo esc_html( get_the_title( $cas ) ); ?></h3>
					<p><?php echo esc_html( get_the_excerpt( $cas ) ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php springcard_e( 'Besoin d\'une configuration spécifique ?' ); ?></h3>
				<p>
					<?php
					/* translators: %s: gamme name. */
					printf( esc_html( springcard_t( "Notre bureau d'études peut adapter %s à vos contraintes." ) ), esc_html( get_the_title( $gamme_id ) ) );
					?>
				</p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<?php springcard_e( "Découvrir le bureau d'études" ); ?>
			</a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
