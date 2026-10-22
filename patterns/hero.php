<?php
/**
 * Title: Hero
 * Slug: zactonz-lite/hero
 * Categories: zactonz-lite, banner, call-to-action
 * Keywords: hero, intro, headline
 * Description: A large headline with a short paragraph and two buttons.
 */

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"level":2,"fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html__( 'Words first, weight last', 'zactonz-lite' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"medium"} -->
	<p class="has-medium-font-size"><?php echo esc_html__( 'A short line of introduction that sets out what this site is for and why anyone should keep reading past the first screen.', 'zactonz-lite' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Start reading', 'zactonz-lite' ); ?></a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'About', 'zactonz-lite' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
