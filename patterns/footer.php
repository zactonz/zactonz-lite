<?php
/**
 * Title: Footer
 * Slug: zactonz-lite/footer
 * Categories: zactonz-lite
 * Block Types: core/template-part/footer
 * Inserter: no
 */

?>
<!-- wp:group {"className":"zlite-bar zlite-rule","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<div class="wp-block-group zlite-bar zlite-rule" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:paragraph {"fontSize":"x-small"} -->
	<p class="has-x-small-font-size"><?php
	echo esc_html(
		sprintf(
			/* translators: 1: current year, 2: site name. */
			__( '© %1$s %2$s', 'zactonz-lite' ),
			gmdate( 'Y' ),
			get_bloginfo( 'name' )
		)
	);
	?></p>
	<!-- /wp:paragraph -->
	<!-- wp:site-tagline {"fontSize":"x-small"} /-->
</div>
<!-- /wp:group -->
