<?php
/**
 * Technical blog archive.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="section reveal" style="padding-top:20px;">
	<div class="eyebrow"><?php esc_html_e( 'Blog technique', 'springcard' ); ?></div>
	<h1 style="font-size:1.875rem; margin-bottom:14px;"><?php esc_html_e( 'Ressources techniques', 'springcard' ); ?></h1>
</div>

<div class="section">
	<?php if ( have_posts() ) : ?>
		<div class="grid grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a class="card reveal" href="<?php the_permalink(); ?>">
					<span class="tag tag-tech"><?php esc_html_e( 'Technique', 'springcard' ); ?></span>
					<h3 style="margin-top:10px;"><?php the_title(); ?></h3>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
				<?php
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="prose"><?php esc_html_e( 'Aucun article technique pour le moment.', 'springcard' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
