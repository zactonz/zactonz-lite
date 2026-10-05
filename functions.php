<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ZACTONZ_LITE_VERSION' ) ) {
	define( 'ZACTONZ_LITE_VERSION', '1.0.0' );
}

if ( ! defined( 'ZACTONZ_LITE_DIR' ) ) {
	define( 'ZACTONZ_LITE_DIR', get_template_directory() );
}

if ( ! defined( 'ZACTONZ_LITE_URI' ) ) {
	define( 'ZACTONZ_LITE_URI', get_template_directory_uri() );
}

foreach ( array( 'setup', 'assets', 'performance' ) as $zactonz_lite_module ) {
	if ( apply_filters( "zactonz_lite_load_{$zactonz_lite_module}", true ) ) {
		require_once ZACTONZ_LITE_DIR . '/inc/' . $zactonz_lite_module . '.php';
	}
}

unset( $zactonz_lite_module );
