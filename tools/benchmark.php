<?php

require_once __DIR__ . '/measure.php';

$wp_root = getenv( 'ZL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/wp-test';
$base    = getenv( 'ZL_BASE_URL' ) ?: 'http://localhost:8734';
$themes  = explode( ',', getenv( 'ZL_THEMES' ) ?: 'zactonz-lite,twentytwentyfive,generatepress,neve' );
$paths   = explode( ',', getenv( 'ZL_PATHS' ) ?: '/,/?p=1' );

function zl_wp( $root, $args ) {
	$cmd = sprintf(
		'cd %s && php -d display_errors=0 -d error_reporting=0 wp-cli.phar %s 2>&1',
		escapeshellarg( $root ),
		$args
	);
	return trim( (string) shell_exec( $cmd ) );
}

$original = zl_wp( $wp_root, 'theme list --status=active --field=name' );
$original = trim( preg_replace( '~^.*\n~s', '', $original ) ?: $original );

$results = array();

foreach ( $themes as $theme ) {
	$out = zl_wp( $wp_root, 'theme activate ' . escapeshellarg( $theme ) );

	if ( false === stripos( $out, 'Success' ) && false === stripos( $out, 'already active' ) ) {
		fwrite( STDERR, "skip {$theme}: {$out}\n" );
		continue;
	}

	foreach ( $paths as $path ) {
		$results[] = zl_measure( $base . $path, $theme ) + array( 'path' => $path );
	}
}

if ( $original ) {
	zl_wp( $wp_root, 'theme activate ' . escapeshellarg( $original ) );
}

$paths_seen = array_values( array_unique( array_column( $results, 'path' ) ) );

foreach ( $paths_seen as $path ) {
	printf( "\n=== %s ===\n", $path );
	printf( "%-20s %10s %10s %8s %8s %10s %7s\n", 'theme', 'document', 'inline-css', 'css-req', 'js-req', 'total', 'dom' );

	$rows = array_values( array_filter( $results, fn( $r ) => $r['path'] === $path ) );
	usort( $rows, fn( $a, $b ) => $a['total_bytes'] <=> $b['total_bytes'] );

	foreach ( $rows as $r ) {
		printf(
			"%-20s %10d %10d %8d %8d %10d %7d\n",
			$r['label'], $r['document_bytes'], $r['inline_css'], $r['css_requests'], $r['js_requests'], $r['total_bytes'], $r['dom_elements']
		);
	}
}

echo "\nMethod: same install, same content, same URL set; bytes are uncompressed; total = document + all CSS/JS/font subresources.\n";
