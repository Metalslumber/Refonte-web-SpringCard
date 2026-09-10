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
	</div>

	<?php
endwhile;

get_footer();
