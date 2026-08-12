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
		array(
			'post_type'      => 'article',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$articles_techniques = get_posts(
		array(
			'post_type'      => 'article_technique',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$contact_email = get_option( 'admin_email' );
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php esc_html_e( 'À propos', 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:560px; margin-bottom:24px;"><?php the_title(); ?></h1>

		<?php if ( get_the_content() ) : ?>
			<div class="prose" style="margin-bottom:24px;"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="pillbar" data-tabs role="tablist" aria-label="<?php esc_attr_e( 'Sections de la page À propos', 'springcard' ); ?>">
			<button type="button" class="active" data-tab-trigger="blog" role="tab" aria-selected="true"><?php esc_html_e( 'Blog', 'springcard' ); ?></button>
			<button type="button" data-tab-trigger="blog-technique" role="tab" aria-selected="false"><?php esc_html_e( 'Blog technique', 'springcard' ); ?></button>
			<button type="button" data-tab-trigger="contact" role="tab" aria-selected="false"><?php esc_html_e( 'Contact', 'springcard' ); ?></button>
		</div>

		<div data-tab-panel="blog" role="tabpanel">
			<?php if ( ! empty( $articles ) ) : ?>
				<div class="grid grid-3">
					<?php foreach ( $articles as $article ) : ?>
						<a class="card reveal" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
							<span class="tag"><?php esc_html_e( 'Actualité', 'springcard' ); ?></span>
							<h3 style="margin-top:10px;"><?php echo esc_html( get_the_title( $article ) ); ?></h3>
							<p><?php echo esc_html( get_the_excerpt( $article ) ); ?></p>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="prose"><?php esc_html_e( 'Aucun article pour le moment.', 'springcard' ); ?></p>
			<?php endif; ?>
		</div>

		<div data-tab-panel="blog-technique" role="tabpanel" style="display:none;">
			<?php if ( ! empty( $articles_techniques ) ) : ?>
				<div class="grid grid-3">
					<?php foreach ( $articles_techniques as $article ) : ?>
						<a class="card reveal" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
							<span class="tag tag-tech"><?php esc_html_e( 'Technique', 'springcard' ); ?></span>
							<h3 style="margin-top:10px;"><?php echo esc_html( get_the_title( $article ) ); ?></h3>
							<p><?php echo esc_html( get_the_excerpt( $article ) ); ?></p>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="prose"><?php esc_html_e( 'Aucun article technique pour le moment.', 'springcard' ); ?></p>
			<?php endif; ?>
		</div>

		<div data-tab-panel="contact" role="tabpanel" style="display:none;">
			<div class="card reveal" style="max-width:420px;">
				<h3><?php esc_html_e( 'Nous contacter', 'springcard' ); ?></h3>
				<p style="margin-bottom:14px;"><?php esc_html_e( 'Une question technique, commerciale, ou un projet à décrire, écrivez-nous.', 'springcard' ); ?></p>
				<a class="btn btn-primary" href="<?php echo esc_url( 'mailto:' . antispambot( $contact_email ) ); ?>">
					<?php esc_html_e( 'Envoyer un message', 'springcard' ); ?>
				</a>
			</div>
		</div>
	</div>

	<?php
endwhile;

get_footer();
