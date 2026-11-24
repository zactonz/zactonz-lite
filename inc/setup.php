<?php
/**
 * Theme setup: translations, the editor stylesheet, pattern category and block styles.
 *
 * @package Zactonz_Lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'zactonz_lite_setup' );

/**
 * Registers the theme's translations and its editor stylesheet.
 *
 * @return void
 */
function zactonz_lite_setup() {
	load_theme_textdomain( 'zactonz-lite', ZACTONZ_LITE_DIR . '/languages' );
	add_editor_style( 'assets/css/main.min.css' );
}

add_action( 'init', 'zactonz_lite_register_pattern_category' );

/**
 * Registers the category the theme's own patterns are filed under.
 *
 * Guarded because the function arrived in WordPress 5.5 and a theme should not fatal
 * on an older install that can still read its templates.
 *
 * @return void
 */
function zactonz_lite_register_pattern_category() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'zactonz-lite',
		array(
			'label'       => _x( 'Zactonz Lite', 'block pattern category', 'zactonz-lite' ),
			'description' => __( 'Patterns supplied by the Zactonz Lite theme.', 'zactonz-lite' ),
		)
	);
}

add_action( 'init', 'zactonz_lite_register_block_styles' );

/**
 * Registers the two block styles the theme adds to core blocks.
 *
 * Both carry their rules inline rather than in the stylesheet, so a style costs nothing
 * on a page that does not use it.
 *
 * @return void
 */
function zactonz_lite_register_block_styles() {
	register_block_style(
		'core/group',
		array(
			'name'         => 'zactonz-lite-boxed',
			'label'        => __( 'Boxed', 'zactonz-lite' ),
			'inline_style' => '.wp-block-group.is-style-zactonz-lite-boxed{background-color:var(--wp--preset--color--surface);border-radius:.375rem;padding:var(--wp--preset--spacing--40)}',
		)
	);

	register_block_style(
		'core/image',
		array(
			'name'         => 'zactonz-lite-framed',
			'label'        => __( 'Framed', 'zactonz-lite' ),
			'inline_style' => '.wp-block-image.is-style-zactonz-lite-framed img{border:1px solid var(--wp--preset--color--accent-soft);border-radius:.375rem}',
		)
	);
}

add_filter( 'comment_form_defaults', 'zactonz_lite_comment_form_heading' );

/**
 * Makes the comment form's reply title a heading rather than a div.
 *
 * Core wraps it in an h3 by default, which skips a level on a post whose title is the
 * only h1 and whose sections are h2.
 *
 * @param array $defaults Comment form arguments.
 * @return array Comment form arguments.
 */
function zactonz_lite_comment_form_heading( $defaults ) {
	$defaults['title_reply_before'] = '<h2 id="reply-title" class="comment-reply-title">';
	$defaults['title_reply_after']  = '</h2>';

	return $defaults;
}
