<?php
/**
 * Front-end assets: the theme's stylesheet and the fonts worth preloading.
 *
 * @package Zactonz_Lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'zactonz_lite_enqueue_styles' );

/**
 * Registers and enqueues the theme's single stylesheet.
 *
 * The path is handed to WordPress as well as the URL so core can inline the file rather
 * than make a request for it, which is why the theme ships no stylesheet request of its
 * own.
 *
 * @return void
 */
function zactonz_lite_enqueue_styles() {
	$relative = 'assets/css/main.css';
	$absolute = ZACTONZ_LITE_DIR . '/' . $relative;

	if ( ! file_exists( $absolute ) ) {
		return;
	}

	wp_register_style( 'zactonz-lite', ZACTONZ_LITE_URI . '/' . $relative, array(), ZACTONZ_LITE_VERSION );
	wp_style_add_data( 'zactonz-lite', 'path', $absolute );
	wp_enqueue_style( 'zactonz-lite' );
}

add_filter( 'wp_preload_resources', 'zactonz_lite_preload_fonts' );

/**
 * Preloads the web fonts the active style variation actually uses.
 *
 * A variation set in the reader's system font uses none, in which case nothing is
 * preloaded and nothing is downloaded.
 *
 * @param array $resources Resources WordPress will preload.
 * @return array Resources.
 */
function zactonz_lite_preload_fonts( $resources ) {
	if ( ! apply_filters( 'zactonz_lite_enable_font_preload', true ) ) {
		return $resources;
	}

	foreach ( zactonz_lite_active_font_urls() as $url ) {
		$resources[] = array(
			'href'          => $url,
			'as'            => 'font',
			'type'          => 'font/woff2',
			'crossorigin'   => 'anonymous',
			'fetchpriority' => 'high',
		);
	}

	return $resources;
}

/**
 * Returns the font family slugs referenced anywhere in the resolved global styles.
 *
 * Read out of the serialised styles rather than walked, because a family can be named
 * by preset reference or by custom property and both spellings have to count.
 *
 * @return array Font family slugs.
 */
function zactonz_lite_used_font_slugs() {
	$styles = wp_get_global_styles();
	$encoded = wp_json_encode( $styles );

	if ( ! is_string( $encoded ) ) {
		return array();
	}

	preg_match_all( '~font-family\\|([a-z0-9-]+)~i', $encoded, $presets );
	preg_match_all( '~--wp--preset--font-family--([a-z0-9-]+)~i', $encoded, $vars );

	return array_values( array_unique( array_merge( $presets[1], $vars[1] ) ) );
}

/**
 * Returns the URLs of the font files belonging to families the styles actually use.
 *
 * Only files that live inside this theme are returned; a family pointing somewhere else
 * is left for whoever registered it to preload.
 *
 * @return array Absolute font file URLs.
 */
function zactonz_lite_active_font_urls() {
	$families = wp_get_global_settings( array( 'typography', 'fontFamilies' ) );

	if ( ! is_array( $families ) ) {
		return array();
	}

	$flat = array();

	foreach ( $families as $entry ) {
		if ( ! is_array( $entry ) ) {
			continue;
		}

		if ( isset( $entry['fontFamily'] ) || isset( $entry['slug'] ) ) {
			$flat[] = $entry;
			continue;
		}

		foreach ( $entry as $nested ) {
			if ( is_array( $nested ) ) {
				$flat[] = $nested;
			}
		}
	}

	$used = zactonz_lite_used_font_slugs();
	$base = ZACTONZ_LITE_URI . '/assets/fonts/';
	$urls = array();

	foreach ( $flat as $family ) {
		if ( empty( $family['fontFace'] ) || ! is_array( $family['fontFace'] ) ) {
			continue;
		}

		if ( empty( $family['slug'] ) || ! in_array( $family['slug'], $used, true ) ) {
			continue;
		}

		foreach ( $family['fontFace'] as $face ) {
			if ( empty( $face['src'] ) ) {
				continue;
			}

			foreach ( (array) $face['src'] as $src ) {
				if ( ! is_string( $src ) ) {
					continue;
				}

				$src = str_replace( 'file:./', ZACTONZ_LITE_URI . '/', $src );

				if ( 0 === strpos( $src, $base ) ) {
					$urls[] = $src;
				}
			}
		}
	}

	return array_values( array_unique( $urls ) );
}
