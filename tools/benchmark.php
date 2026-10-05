<?php

require_once __DIR__ . '/measure.php';

$wp_root = getenv( 'ZL_WP_ROOT' ) ?: dirname( __DIR__, 2 ) . '/wp-test';
$base    = getenv( 'ZL_BASE_URL' ) ?: 'http://localhost:8734';
$themes  = explode( ',', getenv( 'ZL_THEMES' ) ?: 'zactonz-lite,twentytwentyfive,generatepress,neve' );
$paths   = explode( ',', getenv( 'ZL_PATHS' ) ?: '/,/?p=1,/?p=1788' );

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

$fixture = $wp_root . '/wp-content/mu-plugins/zl-benchmark-plugin.php';
$with_plugin = (bool) getenv( 'ZL_WITH_PLUGIN' );

if ( $with_plugin ) {
	if ( ! is_dir( dirname( $fixture ) ) ) {
		mkdir( dirname( $fixture ), 0755, true );
	}
	copy( __DIR__ . '/fixtures/zl-benchmark-plugin.php', $fixture );
}

register_shutdown_function(
	function () use ( $fixture ) {
		if ( file_exists( $fixture ) ) {
			unlink( $fixture );
		}
	}
);

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
	printf( "%-20s %10s %10s %8s %8s %8s %10s %7s\n", 'theme', 'document', 'inline-css', 'css-req', 'js-req', 'js-dyn', 'total', 'dom' );

	$rows = array_values( array_filter( $results, fn( $r ) => $r['path'] === $path ) );
	usort( $rows, fn( $a, $b ) => $a['total_bytes'] <=> $b['total_bytes'] );

	foreach ( $rows as $r ) {
		printf(
			"%-20s %10d %10d %8d %8d %8d %10d %7d\n",
			$r['label'], $r['document_bytes'], $r['inline_css'], $r['css_requests'], $r['js_requests'], $r['js_dynamic'], $r['total_bytes'], $r['dom_elements']
		);
	}
}

$img = array();
foreach ( $results as $r ) {
	if ( $r['img_eager'] || $r['img_lazy'] ) {
		$img[ $r['path'] ] = sprintf(
			'%d eager (<=%s bytes), %d lazy (<=%s bytes), %d with srcset',
			$r['img_eager'],
			number_format( $r['img_eager_bytes'] ),
			$r['img_lazy'],
			number_format( $r['img_lazy_bytes'] ),
			$r['img_srcset']
		);
	}
}

if ( $img ) {
	echo "\nImages on these pages, identical for every theme:\n";
	foreach ( $img as $path => $line ) {
		printf( "  %-12s %s\n", $path, $line );
	}
}

printf(
	"\nMethod: same install, same content, same URL set%s; bytes are uncompressed.\n",
	$with_plugin ? ', with the benchmark plugin fixture active' : ''
);
echo "total = document + CSS/JS/font subresources, including scripts an inline block loads at runtime (js-dyn).\n";
echo "Images are listed separately: they are content, not theme, and src is the largest srcset candidate, so those bytes are an upper bound.\n";
