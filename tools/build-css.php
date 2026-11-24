<?php

$sources = array_values(
	array_filter(
		glob( __DIR__ . '/../assets/css/*.css' ),
		static fn( $path ) => ! str_ends_with( $path, '.min.css' )
	)
);

if ( ! $sources ) {
	fwrite( STDERR, "no stylesheet sources found\n" );
	exit( 1 );
}

foreach ( $sources as $src ) {
	$out = preg_replace( '~\.css$~', '.min.css', $src );
	$css = file_get_contents( $src );
	$css = preg_replace( '~/\*.*?\*/~s', '', $css );
	$css = preg_replace( '~\s+~', ' ', $css );
	$css = preg_replace( '~\s*([{}>,])\s*~', '$1', $css );
	$css = preg_replace_callback( '~\{[^{}]*\}~', fn( $m ) => preg_replace( '~\s*([:;])\s*~', '$1', $m[0] ), $css );
	$css = str_replace( ';}', '}', $css );
	file_put_contents( $out, trim( $css ) . "\n" );
	printf( "%s: %d bytes -> %d bytes\n", basename( $out ), strlen( file_get_contents( $src ) ), strlen( file_get_contents( $out ) ) );
}
