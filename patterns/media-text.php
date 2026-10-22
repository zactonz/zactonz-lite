<?php
/**
 * Title: Image beside text
 * Slug: zactonz-lite/media-text
 * Categories: zactonz-lite, featured
 * Keywords: image, media, about
 * Description: An image on one side with a heading and paragraph on the other.
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"72rem"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<?php $zactonz_lite_placeholder = get_template_directory_uri() . '/assets/images/placeholder.webp'; ?>
	<!-- wp:media-text {"mediaType":"image","mediaUrl":"<?php echo esc_url( $zactonz_lite_placeholder ); ?>","verticalAlignment":"center"} -->
	<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center">
		<figure class="wp-block-media-text__media"><img src="<?php echo esc_url( $zactonz_lite_placeholder ); ?>" alt="<?php echo esc_attr__( 'A circle, a triangle and a rectangle standing in for your picture', 'zactonz-lite' ); ?>" /></figure>
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":2,"fontSize":"large"} -->
			<h2 class="wp-block-heading has-large-font-size"><?php echo esc_html__( 'A heading beside a picture', 'zactonz-lite' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Replace the image and this text. The image keeps its aspect ratio reserved while it loads, so nothing on the page jumps once it arrives.', 'zactonz-lite' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
	</div>
	<!-- /wp:media-text -->
</div>
<!-- /wp:group -->
