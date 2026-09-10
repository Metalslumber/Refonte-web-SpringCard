<?php
/**
 * Single blog article.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$bureau_url    = springcard_get_page_url_by_template( 'page-bureau-etudes.php' );
	$solutions_url = springcard_get_page_url_by_template( 'page-solutions.php' );
	$gammes        = springcard_get_gammes_actives();
	$gamme_url     = ! empty( $gammes ) ? get_permalink( $gammes[0] ) : '';
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php esc_html_e( 'Blog', 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:720px; margin-bottom:14px;"><?php the_title(); ?></h1>
		<div class="single-meta">
			<span class="tag"><?php esc_html_e( 'Actualité', 'springcard' ); ?></span>
			<span><?php echo esc_html( get_the_date() ); ?></span>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="single-hero-img"><?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) ); ?></div>
		<?php else : ?>
			<div style="margin-top:20px;"></div>
		<?php endif; ?>

		<div class="prose"><?php the_content(); ?></div>

		<?php if ( $gamme_url || $solutions_url || $bureau_url ) : ?>
			<div class="single-tags">
				<?php if ( $gamme_url ) : ?>
					<a class="tag" href="<?php echo esc_url( $gamme_url ); ?>"><?php esc_html_e( 'Gamme M519', 'springcard' ); ?></a>
				<?php endif; ?>
				<?php if ( $solutions_url ) : ?>
					<a class="tag" href="<?php echo esc_url( $solutions_url ); ?>"><?php esc_html_e( 'Solutions RFID/NFC par secteur', 'springcard' ); ?></a>
				<?php endif; ?>
				<?php if ( $bureau_url ) : ?>
					<a class="tag" href="<?php echo esc_url( $bureau_url ); ?>"><?php esc_html_e( "Bureau d'études", 'springcard' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $bureau_url ) : ?>
	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php esc_html_e( 'Un projet RFID/NFC en tête ?', 'springcard' ); ?></h3>
				<p><?php esc_html_e( "Notre bureau d'études conçoit des lecteurs sur mesure autour de la gamme M519.", 'springcard' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ); ?>">
				<?php esc_html_e( 'Parler à un ingénieur', 'springcard' ); ?>
			</a>
		</div>
	</div>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
