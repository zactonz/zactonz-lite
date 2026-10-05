<?php

$sources = glob( __DIR__ . '/../assets/css/*.un-compressed.css' );

if ( ! $sources ) {
	fwrite( STDERR, "no *.un-compressed.css sources found\n" );
	exit( 1 );
}

foreach ( $sources as $src ) {
	$out = preg_replace( '~\.un-compressed\.css$~', '.css', $src );
	$css = file_get_contents( $src );
	$css = preg_replace( '~/\*.*?\*/~s', '', $css );
	$css = preg_replace( '~\s+~', ' ', $css );
	$css = preg_replace( '~\s*([{}>,])\s*~', '$1', $css );
	$css = preg_replace_callback( '~\{[^{}]*\}~', fn( $m ) => preg_replace( '~\s*([:;])\s*~', '$1', $m[0] ), $css );
	$css = str_replace( ';}', '}', $css );
	file_put_contents( $out, trim( $css ) . "\n" );
	printf( "%s: %d bytes -> %d bytes\n", basename( $out ), strlen( file_get_contents( $src ) ), strlen( file_get_contents( $out ) ) );
}
