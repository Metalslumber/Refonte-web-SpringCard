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
		<nav aria-label="<?php esc_attr_e( 'Liens de pied de page', 'springcard' ); ?>">
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
		<span>
			<?php
			printf(
				/* translators: %s: current year. */
				esc_html__( '© %s SpringCard', 'springcard' ),
				esc_html( gmdate( 'Y' ) )
			);
			?>
		</span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
