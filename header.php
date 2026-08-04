<?php
/**
 * Header template: doctype, site header/nav, opens <main>.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main-content"><?php esc_html_e( 'Aller au contenu principal', 'springcard' ); ?></a>

<header class="site-header">
	<div class="header-inner">
		<div class="logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( "Retour à l'accueil SpringCard", 'springcard' ); ?>">
				<?php springcard_the_logo(); ?>
			</a>
		</div>

		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'primary',
				'container'       => 'nav',
				'container_id'    => 'mainnav',
				'container_class' => 'mainnav',
				'items_wrap'      => '<ul>%3$s</ul>',
				'walker'          => new SpringCard_Nav_Walker(),
				'fallback_cb'     => false,
				'depth'           => 2,
			)
		);
		?>

		<div class="header-actions">
			<a class="header-cta" href="<?php echo esc_url( springcard_get_contact_url() ); ?>"><?php esc_html_e( 'Contact', 'springcard' ); ?></a>
			<button type="button" class="navtoggle" id="navtoggle" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'springcard' ); ?>" aria-expanded="false" aria-controls="mainnav">☰</button>
		</div>
	</div>
</header>

<main id="main-content">
