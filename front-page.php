<?php
/**
 * Front page (Accueil).
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gammes_actives   = springcard_get_gammes_actives();
$gamme_principale = ! empty( $gammes_actives ) ? $gammes_actives[0] : null;
$bureau_url       = springcard_get_page_url_by_template( 'page-bureau-etudes.php' );
$solutions_url    = springcard_get_page_url_by_template( 'page-solutions.php' );
$produits_actifs  = springcard_get_produits_actifs();
$secteurs         = get_posts(
	array(
		'post_type'      => 'secteur',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	)
);
$logos_clients    = springcard_get_client_logos();
?>

<section class="hero reveal">
	<?php get_template_part( 'template-parts/hero-video' ); ?>
	<div class="hero-scrim"></div>
	<div class="hero-inner">
		<div class="eyebrow"><?php esc_html_e( 'Module RFID / NFC OEM', 'springcard' ); ?></div>
		<h1><?php echo esc_html( springcard_home_text( 'springcard_home_hero_title' ) ); ?></h1>
		<p class="lead"><?php echo esc_html( springcard_home_text( 'springcard_home_hero_lead' ) ); ?></p>
		<div class="btn-row">
			<a class="btn btn-primary" href="<?php echo esc_url( $gamme_principale ? get_permalink( $gamme_principale ) : '#' ); ?>">
				<?php esc_html_e( 'Découvrir la gamme M519', 'springcard' ); ?>
			</a>
			<a class="btn btn-ghost" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
				<?php esc_html_e( 'Parler à un ingénieur', 'springcard' ); ?>
			</a>
		</div>
	</div>
</section>

<div class="section" style="padding-top:8px;">
	<div class="stat-strip reveal">
		<div>
			<div class="ic">01</div>
			<p><?php echo esc_html( springcard_home_text( 'springcard_home_stat_1' ) ); ?></p>
		</div>
		<div>
			<div class="ic">02</div>
			<p><?php echo esc_html( springcard_home_text( 'springcard_home_stat_2' ) ); ?></p>
		</div>
		<div>
			<div class="ic">03</div>
			<p><?php echo esc_html( springcard_home_text( 'springcard_home_stat_3' ) ); ?></p>
		</div>
	</div>
</div>

<?php if ( ! empty( $produits_actifs ) ) : ?>
<div class="section">
	<div class="section-head reveal">
		<div class="eyebrow"><?php esc_html_e( 'La gamme', 'springcard' ); ?></div>
		<h2><?php esc_html_e( 'Trois configurations, une même liberté', 'springcard' ); ?></h2>
		<p><?php esc_html_e( 'Chaque variante correspond à un niveau différent de liberté sur le design du produit final.', 'springcard' ); ?></p>
	</div>
	<div class="grid grid-3">
		<?php
		foreach ( $produits_actifs as $produit ) :
			$gamme_id  = (int) get_post_meta( $produit->ID, '_gamme_id', true );
			$link      = $gamme_id ? get_permalink( $gamme_id ) : '#';
			$badge_src = ( false !== strpos( $produit->post_title, '-' ) ) ? substr( strrchr( $produit->post_title, '-' ), 1 ) : $produit->post_title;
			$badge     = strtoupper( substr( $badge_src, 0, 2 ) );
			?>
			<a class="card reveal" href="<?php echo esc_url( $link ); ?>">
				<div class="ic"><?php echo esc_html( $badge ); ?></div>
				<h3><?php echo esc_html( get_the_title( $produit ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $produit ) ); ?></p>
				<span class="go"><?php esc_html_e( 'Voir la fiche →', 'springcard' ); ?></span>
			</a>
			<?php
		endforeach;
		?>
	</div>
</div>
<?php endif; ?>

<div class="section">
	<div class="cta-banner reveal">
		<div>
			<h3><?php esc_html_e( 'Un besoin plus spécifique ?', 'springcard' ); ?></h3>
			<p><?php esc_html_e( 'Notre bureau d\'études conçoit le hardware, le firmware et le software autour de M519, sur mesure.', 'springcard' ); ?></p>
		</div>
		<a class="btn btn-primary" href="<?php echo esc_url( $bureau_url ? $bureau_url : '#' ); ?>">
			<?php esc_html_e( "Découvrir le bureau d'études", 'springcard' ); ?>
		</a>
	</div>
</div>

<?php if ( ! empty( $secteurs ) ) : ?>
<div class="section">
	<div class="section-head reveal">
		<div class="eyebrow"><?php esc_html_e( 'Secteurs', 'springcard' ); ?></div>
		<h2><?php esc_html_e( 'Vos défis, par secteur', 'springcard' ); ?></h2>
	</div>
	<div class="grid grid-4">
		<?php
		foreach ( $secteurs as $secteur ) :
			$anchor = 'sector-' . $secteur->post_name;
			$href   = ( $solutions_url ? $solutions_url : '#' ) . '#' . $anchor;
			?>
			<a class="card reveal" href="<?php echo esc_url( $href ); ?>">
				<h3><?php echo esc_html( get_the_title( $secteur ) ); ?></h3>
				<p><?php echo esc_html( get_the_excerpt( $secteur ) ); ?></p>
			</a>
			<?php
		endforeach;
		?>
	</div>
</div>
<?php endif; ?>

<div class="section">
	<div class="eyebrow"><?php esc_html_e( 'Ils intègrent nos modules', 'springcard' ); ?></div>
	<div class="logos-viewport reveal">
		<div class="logos-track">
			<?php if ( ! empty( $logos_clients ) ) : ?>
				<?php
				// Le train de logos est dupliqué deux fois pour boucler sans à-coup
				// (l'animation @keyframes marquee translate de -50%, soit un cycle complet).
				for ( $pass = 0; $pass < 2; $pass++ ) :
					foreach ( $logos_clients as $logo ) :
						$img_html = get_the_post_thumbnail( $logo, 'medium', array( 'alt' => get_the_title( $logo ) ) );
						if ( ! $img_html ) {
							continue;
						}
						$client_url = get_post_meta( $logo->ID, '_url_client', true );
						if ( $client_url ) :
							?>
							<a href="<?php echo esc_url( $client_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( get_the_title( $logo ) ); ?>">
								<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-generated <img>, already escaped by get_the_post_thumbnail(). ?>
							</a>
							<?php
						else :
							echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
					endforeach;
				endfor;
				?>
			<?php else : ?>
				<?php for ( $i = 0; $i < 12; $i++ ) : ?>
					<div></div>
				<?php endfor; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
