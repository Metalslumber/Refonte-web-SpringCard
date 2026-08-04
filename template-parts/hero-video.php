<?php
/**
 * Home hero visual: looping muted background video if set in the Customizer,
 * otherwise falls back to the CSS grid/sweep/chip animation from the prototype.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$video_id   = get_theme_mod( 'springcard_hero_video' );
$video_url  = $video_id ? wp_get_attachment_url( $video_id ) : '';
$poster_id  = get_theme_mod( 'springcard_hero_poster' );
$poster_url = $poster_id ? wp_get_attachment_image_url( $poster_id, 'large' ) : '';
?>
<div class="hero-visual">
	<?php if ( $video_url ) : ?>
		<video
			class="hero-video"
			autoplay muted loop playsinline
			<?php echo $poster_url ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>
		>
			<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
		</video>
	<?php else : ?>
		<div class="bg-grid" aria-hidden="true"></div>
		<div class="sweep" aria-hidden="true"></div>
		<div class="chip" aria-hidden="true"></div>
	<?php endif; ?>
</div>
