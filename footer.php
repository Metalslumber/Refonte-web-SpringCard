<?php
/**
 * Footer template: closes <main>, site footer.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-col footer-brand">
			<div class="footer-logo"><?php springcard_the_logo(); ?></div>
			<?php $footer_address = springcard_footer_text( 'springcard_footer_address' ); ?>
			<?php if ( $footer_address ) : ?>
				<address><?php echo esc_html( $footer_address ); ?></address>
			<?php endif; ?>
		</div>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Liens de pied de page', 'springcard' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<div class="footer-col footer-social">
			<?php
			$socials = array(
				'springcard_footer_facebook' => array( 'icon' => 'dashicons-facebook', 'label' => 'Facebook' ),
				'springcard_footer_twitter'  => array( 'icon' => 'dashicons-twitter', 'label' => 'Twitter / X' ),
				'springcard_footer_linkedin' => array( 'icon' => 'dashicons-linkedin', 'label' => 'LinkedIn' ),
				'springcard_footer_youtube'  => array( 'icon' => 'dashicons-youtube', 'label' => 'YouTube' ),
			);
			foreach ( $socials as $setting => $social ) :
				$social_url = springcard_footer_text( $setting );
				if ( ! $social_url ) {
					continue;
				}
				?>
				<a href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>">
					<span class="dashicons <?php echo esc_attr( $social['icon'] ); ?>" aria-hidden="true"></span>
				</a>
				<?php
			endforeach;
			?>
		</div>
	</div>

	<div class="footer-bottom">
		<?php
		printf(
			/* translators: %s: current year. */
			esc_html__( '© %s SpringCard', 'springcard' ),
			esc_html( gmdate( 'Y' ) )
		);
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
