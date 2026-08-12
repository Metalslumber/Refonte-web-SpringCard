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
		'actif'   => __( 'active', 'springcard' ),
		'archive' => __( 'archivée', 'springcard' ),
		'a_venir' => __( 'à venir', 'springcard' ),
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

	<nav class="gamme-nav" aria-label="<?php esc_attr_e( 'Navigation rapide de la gamme', 'springcard' ); ?>">
		<div class="gamme-nav-inner">
			<div class="gamme-nav-name"><?php the_title(); ?></div>
			<ul class="gamme-nav-links">
				<?php if ( ! empty( $variantes ) ) : ?>
					<li><a href="#variantes"><?php esc_html_e( 'Variantes', 'springcard' ); ?></a></li>
					<li><a href="#comparer"><?php esc_html_e( 'Comparer', 'springcard' ); ?></a></li>
				<?php endif; ?>
				<li><a href="#ressources"><?php esc_html_e( 'Ressources', 'springcard' ); ?></a></li>
			</ul>
			<a class="gamme-nav-cta" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>"><?php esc_html_e( "Bureau d'études", 'springcard' ); ?> →</a>
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
				printf( esc_html__( 'Gamme %s', 'springcard' ), esc_html( $statut_label ) );
				?>
			</div>
			<h1 class="gamme-hero-title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="gamme-hero-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<div class="btn-row">
				<?php if ( ! empty( $variantes ) ) : ?>
					<a class="btn btn-primary" href="#comparer"><?php esc_html_e( 'Comparer les variantes', 'springcard' ); ?></a>
				<?php else : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>"><?php esc_html_e( "Découvrir le bureau d'études", 'springcard' ); ?></a>
				<?php endif; ?>
				<?php if ( $hero_pdf_url ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( $hero_pdf_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Télécharger la fiche technique', 'springcard' ); ?></a>
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
			<div class="eyebrow-x"><?php esc_html_e( "D'un coup d'œil", 'springcard' ); ?></div>
			<h2>
				<?php
				/* translators: %s: gamme name, e.g. "M519". */
				printf( esc_html__( "Trois façons d'intégrer %s", 'springcard' ), esc_html( get_the_title( $gamme_id ) ) );
				?>
			</h2>
		</div>
		<div class="glance-grid">
			<?php foreach ( $variantes as $variante ) :
				$type_antenne = get_post_meta( $variante->ID, '_type_antenne', true );
				$pdf_url      = springcard_get_fiche_technique_url( $variante->ID );
				?>
				<div class="glance-card reveal">
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
							<a class="go" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Voir la fiche technique →', 'springcard' ); ?></a>
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
				<div class="num"><?php esc_html_e( 'Fabrication', 'springcard' ); ?></div>
				<h3><?php esc_html_e( 'Du prototype à la série, sans rupture de forme', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Même module, du premier essai en laboratoire jusqu\'aux volumes de production. Aucune reconception nécessaire quand vous passez à l\'échelle.', 'springcard' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $kit_url ) : ?>
	<div class="section">
		<div class="feature-row reverse">
			<div class="feature-media reveal"><img src="<?php echo esc_url( $kit_url ); ?>" alt="" /></div>
			<div class="feature-copy reveal">
				<div class="num"><?php esc_html_e( "Bureau d'études", 'springcard' ); ?></div>
				<h3><?php esc_html_e( 'Un kit pour démarrer en un après-midi', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'SDK, exemples de code et kit de développement pour valider votre intégration rapidement, puis notre bureau d\'études prend le relais pour le sur-mesure.', 'springcard' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $variantes ) ) : ?>
	<div class="section tight" id="comparer">
		<div class="section-head-center reveal">
			<div class="eyebrow-x"><?php esc_html_e( 'Comparer', 'springcard' ); ?></div>
			<h2><?php esc_html_e( 'Toutes les caractéristiques, côte à côte', 'springcard' ); ?></h2>
		</div>
		<div class="compare-scroll reveal">
			<table class="compare">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Caractéristique', 'springcard' ); ?></th>
					<?php foreach ( $variantes as $variante ) : ?>
						<th><?php echo esc_html( get_the_title( $variante ) ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php esc_html_e( 'Antenne', 'springcard' ); ?></td>
					<?php foreach ( $variantes as $variante ) : ?>
						<td><?php echo esc_html( springcard_antenne_label( get_post_meta( $variante->ID, '_type_antenne', true ) ) ); ?></td>
					<?php endforeach; ?>
				</tr>
				<?php foreach ( $all_labels as $label ) : ?>
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<?php foreach ( $variantes as $variante ) : ?>
							<td><?php echo esc_html( isset( $specs_by_variant[ $variante->ID ][ $label ] ) ? $specs_by_variant[ $variante->ID ][ $label ] : '-' ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td><?php esc_html_e( 'Fiche technique', 'springcard' ); ?></td>
					<?php foreach ( $variantes as $variante ) : $pdf_url = springcard_get_fiche_technique_url( $variante->ID ); ?>
						<td>
							<?php if ( $pdf_url ) : ?>
								<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">PDF</a>
							<?php else : ?>
								-
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>

	<div class="section" id="ressources">
		<div class="section-head reveal">
			<div class="eyebrow"><?php esc_html_e( 'Ressources', 'springcard' ); ?></div>
			<h2 style="font-size:1.25rem;"><?php esc_html_e( 'De quoi démarrer', 'springcard' ); ?></h2>
		</div>
		<div class="grid grid-4">
			<a class="card reveal" href="<?php echo ! empty( $variantes ) ? '#comparer' : '#'; ?>">
				<h3><?php esc_html_e( 'Documentation technique', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Datasheets et guides d\'intégration, par variante.', 'springcard' ); ?></p>
				<span class="go"><?php esc_html_e( 'Voir les fiches →', 'springcard' ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<h3><?php esc_html_e( 'SDK &amp; outils', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Librairies et exemples de code.', 'springcard' ); ?></p>
				<span class="go"><?php esc_html_e( "Voir le bureau d'études →", 'springcard' ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $contact_url ); ?>">
				<h3><?php esc_html_e( 'Kit de démarrage', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Pour prototyper rapidement.', 'springcard' ); ?></p>
				<span class="go"><?php esc_html_e( 'Demander un kit →', 'springcard' ); ?></span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( $contact_url ); ?>">
				<h3><?php esc_html_e( 'Support technique', 'springcard' ); ?></h3>
				<p><?php esc_html_e( "Une équipe d'ingénieurs disponible.", 'springcard' ); ?></p>
				<span class="go"><?php esc_html_e( 'Contacter →', 'springcard' ); ?></span>
			</a>
		</div>
	</div>

	<?php if ( ! empty( $cas_usage ) ) : ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow">
			<?php
			/* translators: %s: gamme name. */
			printf( esc_html__( 'Construit avec %s', 'springcard' ), esc_html( get_the_title( $gamme_id ) ) );
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
				<h3><?php esc_html_e( 'Besoin d\'une configuration spécifique ?', 'springcard' ); ?></h3>
				<p>
					<?php
					/* translators: %s: gamme name. */
					printf( esc_html__( "Notre bureau d'études peut adapter %s à vos contraintes.", 'springcard' ), esc_html( get_the_title( $gamme_id ) ) );
					?>
				</p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<?php esc_html_e( "Découvrir le bureau d'études", 'springcard' ); ?>
			</a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
