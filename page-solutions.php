<?php
/**
 * Template Name: Solutions
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$bureau_url = springcard_get_page_url_by_template( 'page-bureau-etudes.php' );
	$secteurs   = get_posts(
		array(
			'post_type'      => 'secteur',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	$case_study = get_posts(
		array(
			'post_type'      => 'cas_usage',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php esc_html_e( 'Solutions', 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:560px; margin-bottom:14px;"><?php the_title(); ?></h1>
		<?php if ( get_the_content() ) : ?>
			<div class="prose" style="max-width:540px;"><?php the_content(); ?></div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $secteurs ) ) : ?>
	<div class="section">
		<div class="grid grid-3">
			<?php
			foreach ( $secteurs as $secteur ) :
				$icone = get_post_meta( $secteur->ID, '_icone', true );
				?>
				<div class="card reveal" id="sector-<?php echo esc_attr( $secteur->post_name ); ?>">
					<div class="ic">
						<?php if ( $icone ) : ?>
							<span class="dashicons <?php echo esc_attr( $icone ); ?>" aria-hidden="true"></span>
						<?php else : ?>
							◆
						<?php endif; ?>
					</div>
					<h3><?php echo esc_html( get_the_title( $secteur ) ); ?></h3>
					<p><?php echo esc_html( get_the_excerpt( $secteur ) ); ?></p>
					<span class="go"><?php esc_html_e( 'Voir le défi →', 'springcard' ); ?></span>
				</div>
				<?php
			endforeach;
			?>
			<div class="card reveal ghost"><?php esc_html_e( '+ Futur secteur', 'springcard' ); ?></div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $case_study ) ) : $cas = $case_study[0]; $cas_secteurs = (array) get_post_meta( $cas->ID, '_secteurs', true ); ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow"><?php esc_html_e( 'Cas d\'usage à la une', 'springcard' ); ?></div>
		<div class="case reveal">
			<?php if ( has_post_thumbnail( $cas ) ) : ?>
				<div class="case-img"><?php echo get_the_post_thumbnail( $cas, 'medium', array( 'alt' => get_the_title( $cas ) ) ); ?></div>
			<?php else : ?>
				<div class="case-img"><?php esc_html_e( '[ Visuel client ]', 'springcard' ); ?></div>
			<?php endif; ?>
			<div class="case-body">
				<h3><?php echo esc_html( get_the_title( $cas ) ); ?></h3>
				<p>
					<?php
					if ( ! empty( $cas_secteurs ) ) {
						$secteur_titre = get_the_title( (int) $cas_secteurs[0] );
						/* translators: %s: nom du secteur. */
						printf( esc_html__( 'Secteur %s : ', 'springcard' ), esc_html( $secteur_titre ) );
					}
					echo esc_html( get_the_excerpt( $cas ) );
					?>
				</p>
				<a class="go" href="<?php echo esc_url( get_permalink( $cas ) ); ?>"><?php esc_html_e( "Lire le cas d'usage →", 'springcard' ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php esc_html_e( "Votre secteur n'est pas listé ?", 'springcard' ); ?></h3>
				<p><?php esc_html_e( "Notre bureau d'études étudie tout projet d'intégration, même hors des cas standards.", 'springcard' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<?php esc_html_e( 'Parler à un ingénieur', 'springcard' ); ?>
			</a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
