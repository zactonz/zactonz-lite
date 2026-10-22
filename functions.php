<?php
/**
 * Zactonz Lite theme entry point.
 *
 * Defines the theme's constants and loads its modules. Each module can be switched off
 * with a zactonz_lite_load_{module} filter, so a child theme can replace one without
 * having to unhook everything inside it.
 *
 * @package Zactonz_Lite
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ZACTONZ_LITE_VERSION' ) ) {
	define( 'ZACTONZ_LITE_VERSION', '1.0.2' );
}

if ( ! defined( 'ZACTONZ_LITE_DIR' ) ) {
	define( 'ZACTONZ_LITE_DIR', get_template_directory() );
}

if ( ! defined( 'ZACTONZ_LITE_URI' ) ) {
	define( 'ZACTONZ_LITE_URI', get_template_directory_uri() );
}

foreach ( array( 'setup', 'assets' ) as $zactonz_lite_module ) {
	if ( apply_filters( "zactonz_lite_load_{$zactonz_lite_module}", true ) ) {
		require_once ZACTONZ_LITE_DIR . '/inc/' . $zactonz_lite_module . '.php';
	}
}

unset( $zactonz_lite_module );
