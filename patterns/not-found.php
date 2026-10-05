<?php
/**
 * Title: Page not found
 * Slug: zactonz-lite/not-found
 * Categories: zactonz-lite
 * Inserter: no
 */

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"level":1,"fontSize":"x-large"} -->
	<h1 class="wp-block-heading has-x-large-font-size"><?php echo esc_html__( 'That page has moved on', 'zactonz-lite' ); ?></h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'The address you followed does not match anything here. Try a search instead.', 'zactonz-lite' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:search {"label":"<?php echo esc_attr__( 'Search', 'zactonz-lite' ); ?>","showLabel":false,"buttonText":"<?php echo esc_attr__( 'Search', 'zactonz-lite' ); ?>"} /-->
</div>
<!-- /wp:group -->
