<?php
/**
 * Title: Three columns of features
 * Slug: zactonz-lite/features-three-column
 * Categories: zactonz-lite, featured, columns
 * Keywords: features, columns, services
 * Description: Three short feature columns, each with a heading and a paragraph.
 */

$zactonz_lite_features = array(
	array(
		'title' => __( 'Nothing render-blocking', 'zactonz-lite' ),
		'body'  => __( 'Styles are inlined by WordPress, so the browser never waits on a stylesheet request before it can paint.', 'zactonz-lite' ),
	),
	array(
		'title' => __( 'Your reader’s own font', 'zactonz-lite' ),
		'body'  => __( 'The default style uses the system font stack, which costs nothing to download and never flashes while it loads.', 'zactonz-lite' ),
	),
	array(
		'title' => __( 'Out of the way', 'zactonz-lite' ),
		'body'  => __( 'Structured data, meta tags and caching stay with the plugins that specialise in them, so nothing is emitted twice.', 'zactonz-lite' ),
	),
);

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"72rem"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns">
	<?php foreach ( $zactonz_lite_features as $zactonz_lite_feature ) : ?>
		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"medium"} -->
			<h3 class="wp-block-heading has-medium-font-size"><?php echo esc_html( $zactonz_lite_feature['title'] ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><?php echo esc_html( $zactonz_lite_feature['body'] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
