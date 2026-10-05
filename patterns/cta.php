<?php
/**
 * Title: Call to action
 * Slug: zactonz-lite/cta
 * Categories: zactonz-lite, call-to-action
 * Keywords: cta, subscribe, contact
 * Description: A boxed call to action with a heading, a line of text and a button.
 */

?>
<!-- wp:group {"className":"is-style-zactonz-lite-boxed","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|60","right":"var:preset|spacing|50","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50"},"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-zactonz-lite-boxed" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"level":2,"fontSize":"large"} -->
	<h2 class="wp-block-heading has-large-font-size"><?php echo esc_html__( 'Keep up with what gets published', 'zactonz-lite' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"small"} -->
	<p class="has-small-font-size"><?php echo esc_html__( 'One short line explaining what someone gets if they follow along, and how often.', 'zactonz-lite' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Subscribe', 'zactonz-lite' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
