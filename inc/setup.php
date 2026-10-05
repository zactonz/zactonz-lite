<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'zactonz_lite_setup' );

function zactonz_lite_setup() {
	load_theme_textdomain( 'zactonz-lite', ZACTONZ_LITE_DIR . '/languages' );
	add_editor_style( 'assets/css/main.css' );
}

add_action( 'init', 'zactonz_lite_register_pattern_category' );

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
