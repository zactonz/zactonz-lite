<?php
/**
 * Title: Post footer
 * Slug: zactonz-lite/post-footer
 * Categories: zactonz-lite
 * Inserter: no
 */

?>
<!-- wp:group {"className":"zlite-rule","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"},"padding":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group zlite-rule" style="margin-top:var(--wp--preset--spacing--60);padding-top:var(--wp--preset--spacing--40)">
	<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:post-navigation-link {"type":"previous","label":"<?php echo __( 'Previous', 'zactonz-lite' ); ?>","fontSize":"x-small"} /-->
		<!-- wp:post-navigation-link {"label":"<?php echo __( 'Next', 'zactonz-lite' ); ?>","fontSize":"x-small"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:comments -->
	<div class="wp-block-comments">
		<!-- wp:comments-title {"level":2,"fontSize":"large"} /-->

		<!-- wp:comment-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:group {"style":{"spacing":{"blockGap":"0.75rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:avatar {"size":36,"style":{"border":{"radius":"50%"}}} /-->
					<!-- wp:comment-author-name {"fontSize":"x-small"} /-->
					<!-- wp:comment-date {"fontSize":"x-small"} /-->
				</div>
				<!-- /wp:group -->
				<!-- wp:comment-content {"fontSize":"small"} /-->
				<!-- wp:comment-reply-link {"fontSize":"x-small"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:comment-template -->

		<!-- wp:comments-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
			<!-- wp:comments-pagination-previous {"label":"<?php echo __( 'Older comments', 'zactonz-lite' ); ?>"} /-->
			<!-- wp:comments-pagination-next {"label":"<?php echo __( 'Newer comments', 'zactonz-lite' ); ?>"} /-->
		<!-- /wp:comments-pagination -->

		<!-- wp:post-comments-form /-->
	</div>
	<!-- /wp:comments -->
</div>
<!-- /wp:group -->
