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
		'title' => __( 'Your first point', 'zactonz-lite' ),
		'body'  => __( 'A sentence or two about whatever a visitor should notice first. Replace this text with your own.', 'zactonz-lite' ),
	),
	array(
		'title' => __( 'Your second point', 'zactonz-lite' ),
		'body'  => __( 'Keep the three columns to a similar length so they line up neatly. Replace this text with your own.', 'zactonz-lite' ),
	),
	array(
		'title' => __( 'Your third point', 'zactonz-lite' ),
		'body'  => __( 'Put a link in the paragraph if a column should lead somewhere. Replace this text with your own.', 'zactonz-lite' ),
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
