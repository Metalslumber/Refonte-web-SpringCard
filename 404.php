<?php
/**
 * 404 (not found).
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="section reveal" style="padding-top:60px; padding-bottom:60px; text-align:center;">
	<div class="eyebrow" style="justify-content:center;"><?php esc_html_e( 'Erreur 404', 'springcard' ); ?></div>
	<h1 style="margin-bottom:14px;"><?php esc_html_e( "Cette page n'existe pas", 'springcard' ); ?></h1>
	<p class="prose" style="margin:0 auto 24px;"><?php esc_html_e( "La page que vous cherchez a peut-être été déplacée ou n'existe plus.", 'springcard' ); ?></p>
	<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( "Retour à l'accueil", 'springcard' ); ?></a>
</div>

<?php
get_footer();
