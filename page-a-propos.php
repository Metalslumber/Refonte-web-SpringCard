<?php
/**
 * Template Name: À propos
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$articles           = get_posts(
		springcard_lang_filter(
			array(
				'post_type'      => 'article',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		)
	);
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php springcard_e( 'À propos' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:560px; margin-bottom:24px;"><?php the_title(); ?></h1>

		<?php if ( get_the_content() ) : ?>
			<div class="prose" style="margin-bottom:24px;"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="pillbar-row">
			<div class="pillbar" data-tabs role="tablist" aria-label="<?php springcard_attr_e( 'Sections de la page À propos' ); ?>">
				<button type="button" class="active" data-tab-trigger="blog" role="tab" aria-selected="true"><?php springcard_e( 'Blog' ); ?></button>
				<button type="button" data-tab-trigger="contact" role="tab" aria-selected="false"><?php springcard_e( 'Contact' ); ?></button>
			</div>
			<a class="external-link" href="https://tech.springcard.com/" target="_blank" rel="noopener noreferrer">
				<?php springcard_e( 'Blog technique' ); ?> ↗
			</a>
		</div>

		<div data-tab-panel="blog" role="tabpanel">
			<?php if ( ! empty( $articles ) ) : ?>
				<div class="grid grid-3">
					<?php foreach ( $articles as $article ) : ?>
						<a class="card reveal" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
							<span class="tag"><?php springcard_e( 'Actualité' ); ?></span>
							<h3 style="margin-top:10px;"><?php echo esc_html( get_the_title( $article ) ); ?></h3>
							<p><?php echo esc_html( get_the_excerpt( $article ) ); ?></p>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="prose"><?php springcard_e( 'Aucun article pour le moment.' ); ?></p>
			<?php endif; ?>
		</div>

		<div data-tab-panel="contact" role="tabpanel" style="display:none;">
			<div class="card reveal contact-card">
				<h3><?php springcard_e( 'Nous contacter' ); ?></h3>
				<p style="margin-bottom:14px;"><?php springcard_e( 'Une question technique, commerciale, ou un projet à décrire, écrivez-nous.' ); ?></p>
				<?php echo springcard_get_contact_form_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns either the CF7 shortcode markup or an already-escaped mailto fallback. ?>
			</div>
		</div>
	</div>

	<?php
endwhile;

get_footer();
