<?php
/**
 * Template Name: Page légale
 *
 * Gabarit minimal partagé par les pages légales (mentions légales, politique
 * de confidentialité) : pas de hero produit ni de CTA, juste le texte.
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
		<div class="eyebrow"><?php esc_html_e( 'Informations légales', 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:640px; margin-bottom:22px;"><?php the_title(); ?></h1>
		<div class="prose" style="max-width:720px;"><?php the_content(); ?></div>
	</div>

	<?php
endwhile;

get_footer();
