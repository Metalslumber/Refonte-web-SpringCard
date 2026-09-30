<?php
/**
 * Single cas d'usage: client, secteurs concernés, contenu détaillé.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$client       = get_post_meta( get_the_ID(), '_client', true );
	$secteur_ids  = array_filter( array_map( 'absint', (array) get_post_meta( get_the_ID(), '_secteurs', true ) ) );
	$solutions_url = springcard_get_page_url_by_template( 'page-solutions.php' );
	$contact_url  = springcard_get_contact_url();
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow">
			<?php springcard_e( "Cas d'usage" ); ?>
			<?php if ( $client ) : ?> · <?php echo esc_html( $client ); ?><?php endif; ?>
		</div>
		<h1 style="font-size:1.875rem; max-width:720px; margin-bottom:18px;"><?php the_title(); ?></h1>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="single-hero-img"><?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) ); ?></div>
		<?php endif; ?>

		<div class="prose"><?php the_content(); ?></div>

		<?php if ( $secteur_ids && $solutions_url ) : ?>
			<div class="single-tags">
				<?php foreach ( $secteur_ids as $secteur_id ) : $secteur = get_post( $secteur_id ); if ( ! $secteur ) continue; ?>
					<a class="tag" href="<?php echo esc_url( trailingslashit( $solutions_url ) . '#sector-' . $secteur->post_name ); ?>">
						<?php echo esc_html( get_the_title( $secteur ) ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php springcard_e( 'Un projet similaire ?' ); ?></h3>
				<p><?php springcard_e( "Parlons-en avec notre bureau d'études." ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>">
				<?php springcard_e( 'Décrire votre besoin' ); ?>
			</a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
