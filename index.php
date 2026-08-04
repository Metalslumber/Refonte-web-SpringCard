<?php
/**
 * Fallback template: used whenever no more specific template
 * (front-page, single-*, page-*, archive-*) matches the request.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="crumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'springcard' ); ?></a>
	<?php if ( is_search() ) : ?>
		/ <span aria-current="page"><?php echo esc_html( sprintf( __( 'Résultats pour « %s »', 'springcard' ), get_search_query() ) ); ?></span>
	<?php endif; ?>
</div>

<div class="section reveal" style="padding-top:20px;">
	<?php if ( have_posts() ) : ?>
		<div class="grid grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a class="card reveal" href="<?php the_permalink(); ?>">
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
				<?php
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="prose"><?php esc_html_e( 'Aucun résultat.', 'springcard' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
