<?php
/**
 * Single technical article, with an optional link to the related gamme/produit.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$gamme_id   = (int) get_post_meta( get_the_ID(), '_gamme_id', true );
	$produit_id = (int) get_post_meta( get_the_ID(), '_produit_id', true );
	$related_id = $produit_id ? $produit_id : $gamme_id;
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php esc_html_e( 'Blog technique', 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:720px; margin-bottom:14px;"><?php the_title(); ?></h1>
		<div class="single-meta">
			<span class="tag tag-tech"><?php esc_html_e( 'Technique', 'springcard' ); ?></span>
			<span><?php echo esc_html( get_the_date() ); ?></span>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="single-hero-img"><?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) ); ?></div>
		<?php else : ?>
			<div style="margin-top:20px;"></div>
		<?php endif; ?>

		<div class="prose"><?php the_content(); ?></div>

		<?php if ( $related_id && get_post( $related_id ) ) : ?>
			<div class="single-tags">
				<a class="tag tag-tech" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
					<?php echo esc_html( get_the_title( $related_id ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>

	<?php
endwhile;

get_footer();
