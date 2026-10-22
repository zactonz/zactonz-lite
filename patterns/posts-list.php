<?php
/**
 * Title: Posts list
 * Slug: zactonz-lite/posts-list
 * Categories: zactonz-lite, posts
 * Keywords: posts, blog, query, loop
 */

?>
<!-- wp:query {"queryId":0,"query":{"perPage":null,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query">
	<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|60"}}} -->
		<!-- wp:post-featured-image {"isLink":true,"className":"zlite-media","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->
		<!-- wp:post-title {"level":2,"isLink":true,"fontSize":"large"} /-->
		<!-- wp:post-date {"fontSize":"x-small"} /-->
		<!-- wp:post-excerpt {"moreText":"<?php echo _x( 'Continue reading', 'excerpt read-more link', 'zactonz-lite' ); ?>"} /-->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"className":"zlite-rule","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"},"padding":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous {"label":"<?php echo __( 'Newer', 'zactonz-lite' ); ?>"} /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next {"label":"<?php echo __( 'Older', 'zactonz-lite' ); ?>"} /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph -->
		<p><?php echo esc_html__( 'Nothing found here yet.', 'zactonz-lite' ); ?></p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
