<?php

$root = dirname( __DIR__ );
$fail = array();
$warn = array();

function zl_files( $root, $pattern ) {
	$out = array();
	$it  = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $it as $file ) {
		$path = $file->getPathname();
		if ( preg_match( '~/(\.git|dist|node_modules)/~', $path ) ) {
			continue;
		}
		if ( preg_match( $pattern, $path ) ) {
			$out[] = $path;
		}
	}
	sort( $out );
	return $out;
}

$php_files      = zl_files( $root, '~\.php$~' );
$shipped_php    = array_values( array_filter( $php_files, fn( $f ) => ! str_contains( $f, '/tools/' ) ) );
$block_files    = array_merge( zl_files( $root, '~/(templates|parts)/.*\.html$~' ), zl_files( $root, '~/patterns/.*\.php$~' ) );
$css_min        = zl_files( $root, '~/assets/css/[^/]*\.min\.css$~' );
$style_css      = file_get_contents( $root . '/style.css' );

$rel = fn( $f ) => str_replace( $root . '/', '', $f );

// 1. Required files.
foreach ( array( 'style.css', 'theme.json', 'readme.txt', 'templates/index.html', 'functions.php' ) as $required ) {
	if ( ! file_exists( $root . '/' . $required ) ) {
		$fail[] = "missing required file: {$required}";
	}
}
if ( ! file_exists( $root . '/screenshot.png' ) && ! file_exists( $root . '/screenshot.jpg' ) ) {
	$fail[] = 'missing screenshot.png';
}

// 2. JSON validity.
foreach ( array_merge( array( $root . '/theme.json' ), zl_files( $root, '~/styles/.*\.json$~' ) ) as $json_file ) {
	json_decode( file_get_contents( $json_file ), true );
	if ( JSON_ERROR_NONE !== json_last_error() ) {
		$fail[] = $rel( $json_file ) . ': invalid JSON - ' . json_last_error_msg();
	}
}

// 3. Block attribute JSON inside templates, parts and patterns.
foreach ( $block_files as $file ) {
	$body = file_get_contents( $file );
	if ( preg_match_all( '~<!--\s*/?wp:[a-z0-9/-]+\s+(\{.*?\})\s*/?-->~s', $body, $m ) ) {
		foreach ( $m[1] as $attrs ) {
			if ( str_contains( $attrs, '<?php' ) ) {
				continue;
			}
			json_decode( $attrs, true );
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				$fail[] = $rel( $file ) . ': invalid block attributes - ' . json_last_error_msg();
			}
		}
	}
}

// 4. Text domain.
foreach ( $shipped_php as $file ) {
	$body = file_get_contents( $file );
	if ( preg_match_all( '~\b(?:__|_e|_x|_ex|_n|_nx|esc_html__|esc_html_e|esc_html_x|esc_attr__|esc_attr_e|esc_attr_x)\s*\([^;]*?\)~s', $body, $m ) ) {
		foreach ( $m[0] as $call ) {
			if ( ! str_contains( $call, "'zactonz-lite'" ) ) {
				$fail[] = $rel( $file ) . ': translation call without the zactonz-lite text domain - ' . trim( preg_replace( '~\s+~', ' ', substr( $call, 0, 90 ) ) );
			}
		}
	}
}

// 5. Prefixing of declared functions and constants.
foreach ( $shipped_php as $file ) {
	$body = file_get_contents( $file );
	if ( preg_match_all( '~^\s*function\s+([a-zA-Z0-9_]+)~m', $body, $m ) ) {
		foreach ( $m[1] as $fn ) {
			if ( ! str_starts_with( $fn, 'zactonz_lite_' ) ) {
				$fail[] = $rel( $file ) . ": function {$fn}() is not prefixed with zactonz_lite_";
			}
		}
	}
	if ( preg_match_all( '~define\(\s*[\'"]([A-Z0-9_]+)[\'"]~', $body, $m ) ) {
		foreach ( $m[1] as $const ) {
			if ( ! str_starts_with( $const, 'ZACTONZ_LITE_' ) ) {
				$fail[] = $rel( $file ) . ": constant {$const} is not prefixed with ZACTONZ_LITE_";
			}
		}
	}
}

// 6. Plugin-territory head removals (Theme Check treats these as REQUIRED failures).
foreach ( $shipped_php as $file ) {
	if ( preg_match( '~remove_action\(\s*[\'"]wp_head[\'"]~', file_get_contents( $file ) ) ) {
		$fail[] = $rel( $file ) . ': remove_action on wp_head is plugin territory and fails Theme Check';
	}
}

// 7. Cache eligibility.
$cache_hostile = array(
	'setcookie'          => 'sets a cookie, which makes the response ineligible for edge caching',
	'session_start'      => 'starts a session, which makes the response uncacheable',
	'human_time_diff'    => 'relative dates go stale as soon as a page is cached; use an absolute date',
	'is_user_logged_in'  => 'varying front-end output by login state breaks full-page caching',
);
foreach ( $shipped_php as $file ) {
	$body = file_get_contents( $file );
	foreach ( $cache_hostile as $needle => $why ) {
		if ( preg_match( '~\b' . preg_quote( $needle, '~' ) . '\s*\(~', $body ) ) {
			$fail[] = $rel( $file ) . ": {$needle}() - {$why}";
		}
	}
}

// 8. No jQuery.
foreach ( array_merge( $shipped_php, $block_files, $css_min ) as $file ) {
	if ( preg_match( '~jquery~i', file_get_contents( $file ) ) ) {
		$fail[] = $rel( $file ) . ': references jQuery';
	}
}

// 9. No remote resources in shipped code.
foreach ( array_merge( $shipped_php, $block_files, zl_files( $root, '~/assets/css/.*\.css$~' ) ) as $file ) {
	$body = file_get_contents( $file );
	if ( preg_match_all( '~https?://[^\s\'"<>()]+~', $body, $m ) ) {
		foreach ( $m[0] as $url ) {
			if ( preg_match( '~(gnu\.org|wordpress\.org|schemas\.wp\.org|zactonz\.com|github\.com|scripts\.sil\.org)~', $url ) ) {
				continue;
			}
			$fail[] = $rel( $file ) . ": remote resource {$url}";
		}
	}
}

// 10. Minified CSS must ship with its readable source.
foreach ( $css_min as $file ) {
	$source = preg_replace( '~\.min\.css$~', '.css', $file );
	if ( ! file_exists( $source ) ) {
		$fail[] = $rel( $file ) . ': minified CSS without its readable source beside it';
	} elseif ( filesize( $file ) > filesize( $source ) ) {
		$warn[] = $rel( $file ) . ': minified file is larger than its source';
	}
}

// 11. Version agreement.
preg_match( '~^Version:\s*(.+)$~m', $style_css, $v1 );
preg_match( '~ZACTONZ_LITE_VERSION\',\s*\'([^\']+)~', file_get_contents( $root . '/functions.php' ), $v2 );
preg_match( '~^Stable tag:\s*(.+)$~m', file_get_contents( $root . '/readme.txt' ), $v3 );
$versions = array_unique( array_map( 'trim', array( $v1[1] ?? 'a', $v2[1] ?? 'b', $v3[1] ?? 'c' ) ) );
if ( count( $versions ) !== 1 ) {
	$fail[] = 'version mismatch between style.css, functions.php and readme.txt: ' . implode( ', ', $versions );
}

// 12. Theme tags must be ones WordPress recognises.
$valid_tags = array(
	'blog', 'e-commerce', 'education', 'entertainment', 'food-and-drink', 'holiday', 'news', 'photography', 'portfolio',
	'accessibility-ready', 'block-patterns', 'block-styles', 'custom-background', 'custom-colors', 'custom-header',
	'custom-logo', 'editor-style', 'featured-image-header', 'featured-images', 'footer-widgets', 'full-site-editing',
	'full-width-template', 'post-formats', 'sticky-post', 'style-variations', 'template-editing', 'theme-options',
	'grid-layout', 'one-column', 'two-columns', 'three-columns', 'four-columns', 'left-sidebar', 'right-sidebar', 'wide-blocks',
);
if ( preg_match( '~^Tags:\s*(.+)$~m', $style_css, $tm ) ) {
	foreach ( array_map( 'trim', explode( ',', $tm[1] ) ) as $tag ) {
		if ( '' !== $tag && ! in_array( $tag, $valid_tags, true ) ) {
			$fail[] = "style.css: '{$tag}' is not a tag WordPress recognises";
		}
	}
}

// 13. Text domain header must equal the slug derived from the theme name.
preg_match( '~^Theme Name:\s*(.+)$~m', $style_css, $nm );
preg_match( '~^Text Domain:\s*(.+)$~m', $style_css, $dm );
$derived = strtolower( trim( preg_replace( '~[^a-zA-Z0-9]+~', '-', $nm[1] ?? '' ), '-' ) );
if ( $derived !== trim( $dm[1] ?? '' ) ) {
	$fail[] = "style.css: Text Domain '" . trim( $dm[1] ?? '' ) . "' does not match the slug '{$derived}' derived from the Theme Name";
}

// 14. Attribution must be the house identity only.
foreach ( zl_files( $root, '~\.(php|css|html|json|txt|md)$~' ) as $file ) {
	if ( __FILE__ === $file ) {
		continue;
	}
	if ( preg_match( '~(co-authored-by|claude\.ai|claude code|generated with)~i', file_get_contents( $file ) ) ) {
		$fail[] = $rel( $file ) . ': contains a non-house attribution marker';
	}
}

foreach ( $warn as $w ) {
	echo "warn  {$w}\n";
}
foreach ( $fail as $f ) {
	echo "FAIL  {$f}\n";
}

printf( "\n%d checks groups run, %d failures, %d warnings\n", 14, count( $fail ), count( $warn ) );
exit( $fail ? 1 : 0 );
