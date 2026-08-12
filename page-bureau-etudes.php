<?php
/**
 * Template Name: Bureau d'études
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$contact_url = springcard_get_contact_url();
	$expertises  = get_posts(
		array(
			'post_type'      => 'expertise',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	$case_study  = get_posts(
		array(
			'post_type'      => 'cas_usage',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>

	<div class="section reveal" style="padding-top:20px;">
		<div class="eyebrow"><?php esc_html_e( "Bureau d'études", 'springcard' ); ?></div>
		<h1 style="font-size:1.875rem; max-width:580px; margin-bottom:14px;"><?php the_title(); ?></h1>
		<?php if ( get_the_content() ) : ?>
			<div class="prose" style="max-width:560px; margin-bottom:22px;"><?php the_content(); ?></div>
		<?php endif; ?>
		<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Décrire mon projet', 'springcard' ); ?></a>
	</div>

	<?php if ( ! empty( $expertises ) ) : ?>
	<div class="section">
		<div class="section-head reveal"><div class="eyebrow"><?php esc_html_e( 'Expertises', 'springcard' ); ?></div></div>
		<div class="grid grid-3">
			<?php foreach ( $expertises as $expertise ) : ?>
				<div class="card reveal">
					<?php $code = get_post_meta( $expertise->ID, '_code', true ); ?>
					<?php if ( $code ) : ?>
						<div class="ic"><?php echo esc_html( $code ); ?></div>
					<?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $expertise ) ); ?></h3>
					<p><?php echo esc_html( get_the_excerpt( $expertise ) ); ?></p>
				</div>
			<?php endforeach; ?>
			<div class="card reveal ghost"><?php esc_html_e( '+ Future expertise', 'springcard' ); ?></div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="section-head reveal"><div class="eyebrow"><?php esc_html_e( 'Méthode', 'springcard' ); ?></div></div>
		<div class="steps">
			<div class="reveal">
				<div class="num">01</div>
				<h3><?php esc_html_e( 'Étude de faisabilité', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Cadrage technique et contraintes projet.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">02</div>
				<h3><?php esc_html_e( 'Prototypage', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Preuve de concept sur module existant ou nouveau.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">03</div>
				<h3><?php esc_html_e( 'Développement', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Industrialisation hardware, firmware, software.', 'springcard' ); ?></p>
			</div>
			<div class="reveal">
				<div class="num">04</div>
				<h3><?php esc_html_e( 'Qualification', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Tests, certification, mise en production.', 'springcard' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $case_study ) ) : $cas = $case_study[0]; ?>
	<div class="section" style="background:var(--sc-surface); border-radius:14px; padding:28px;">
		<div class="eyebrow"><?php esc_html_e( 'Ils nous ont confié leur développement sur mesure', 'springcard' ); ?></div>
		<div class="case reveal">
			<?php if ( has_post_thumbnail( $cas ) ) : ?>
				<div class="case-img"><?php echo get_the_post_thumbnail( $cas, 'medium', array( 'alt' => get_the_title( $cas ) ) ); ?></div>
			<?php else : ?>
				<div class="case-img"><?php esc_html_e( '[ Visuel projet ]', 'springcard' ); ?></div>
			<?php endif; ?>
			<div class="case-body">
				<h3><?php echo esc_html( get_the_title( $cas ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $cas ) ); ?></p>
				<a class="go" href="<?php echo esc_url( get_permalink( $cas ) ); ?>"><?php esc_html_e( "Lire le cas d'usage →", 'springcard' ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="section">
		<div class="cta-banner reveal">
			<div>
				<h3><?php esc_html_e( 'Discutons de votre projet', 'springcard' ); ?></h3>
				<p><?php esc_html_e( 'Un premier échange avec un ingénieur, sans engagement.', 'springcard' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Prendre rendez-vous', 'springcard' ); ?></a>
		</div>
	</div>

	<?php
endwhile;

get_footer();
