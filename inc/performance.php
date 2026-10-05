<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wp_speculation_rules_configuration', 'zactonz_lite_speculation_configuration' );

function zactonz_lite_speculation_configuration( $config ) {
	if ( null === $config || ! is_array( $config ) ) {
		return $config;
	}

	if ( zactonz_lite_has_transactional_plugin() ) {
		return $config;
	}

	$config['mode']      = 'prerender';
	$config['eagerness'] = 'moderate';

	return apply_filters( 'zactonz_lite_speculation', $config );
}

add_filter( 'wp_speculation_rules_href_exclude_paths', 'zactonz_lite_speculation_exclusions' );

function zactonz_lite_speculation_exclusions( $paths ) {
	$excluded = apply_filters(
		'zactonz_lite_speculation_exclude_paths',
		array( '/cart/*', '/checkout/*', '/my-account/*', '/*add-to-cart*' )
	);

	return array_merge( (array) $paths, (array) $excluded );
}

function zactonz_lite_has_transactional_plugin() {
	return class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' );
}
