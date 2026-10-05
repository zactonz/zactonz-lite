<?php

function zl_fetch( $url ) {
	$ch = curl_init( $url );
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_ENCODING       => '',
		)
	);
	$body = curl_exec( $ch );
	$code = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
	return array( 'body' => (string) $body, 'code' => $code );
}

function zl_absolute( $href, $base ) {
	if ( preg_match( '~^https?://~i', $href ) ) {
		return $href;
	}
	if ( 0 === strpos( $href, '//' ) ) {
		return 'http:' . $href;
	}
	$parts = wp_parse_url_lite( $base );
	if ( 0 === strpos( $href, '/' ) ) {
		return $parts['origin'] . $href;
	}
	return rtrim( $base, '/' ) . '/' . $href;
}

function wp_parse_url_lite( $url ) {
	$p = parse_url( $url );
	return array( 'origin' => $p['scheme'] . '://' . $p['host'] . ( isset( $p['port'] ) ? ':' . $p['port'] : '' ) );
}

function zl_measure( $url, $label = '' ) {
	$page = zl_fetch( $url );
	$html = $page['body'];

	$inline_css = 0;
	if ( preg_match_all( '~<style[^>]*>(.*?)</style>~s', $html, $m ) ) {
		foreach ( $m[1] as $block ) {
			$inline_css += strlen( $block );
		}
	}

	$inline_js = 0;
	if ( preg_match_all( '~<script(?![^>]*\ssrc=)[^>]*>(.*?)</script>~s', $html, $m ) ) {
		foreach ( $m[1] as $block ) {
			$inline_js += strlen( $block );
		}
	}

	preg_match_all( '~<link[^>]+rel=["\']stylesheet["\'][^>]*>~i', $html, $css_links );
	preg_match_all( '~<link[^>]+rel=["\']stylesheet["\'][^>]*href=["\']([^"\']+)~i', $html, $css_href );
	preg_match_all( '~<script[^>]+src=["\']([^"\']+)~i', $html, $js_src );
	preg_match_all( '~<link[^>]*\\bas=["\']font["\'][^>]*>~i', $html, $font_tags );
	$font_href = array( array(), array() );
	foreach ( $font_tags[0] as $tag ) {
		if ( preg_match( '~\\bhref=["\']([^"\']+)~i', $tag, $hm ) ) {
			$font_href[1][] = $hm[1];
		}
	}
	preg_match_all( '~@font-face~i', $html, $font_faces );

	$sub_bytes = 0;
	$sub_count = 0;
	foreach ( array_merge( $css_href[1], $js_src[1], $font_href[1] ) as $href ) {
		$res        = zl_fetch( zl_absolute( html_entity_decode( $href ), $url ) );
		$sub_bytes += strlen( $res['body'] );
		++$sub_count;
	}

	$dom_nodes = preg_match_all( '~<[a-zA-Z][^>]*>~', $html );

	return array(
		'label'            => $label,
		'url'              => $url,
		'status'           => $page['code'],
		'document_bytes'   => strlen( $html ),
		'inline_css'       => $inline_css,
		'inline_js'        => $inline_js,
		'css_requests'     => count( $css_links[0] ),
		'js_requests'      => count( $js_src[1] ),
		'font_preloads'    => count( $font_href[1] ),
		'font_face_rules'  => count( $font_faces[0] ),
		'subresource_reqs' => $sub_count,
		'subresource_bytes'=> $sub_bytes,
		'total_bytes'      => strlen( $html ) + $sub_bytes,
		'dom_elements'     => $dom_nodes,
	);
}

if ( PHP_SAPI === 'cli' && isset( $argv[1] ) ) {
	$rows = array();
	for ( $i = 1; $i < $argc; $i++ ) {
		$rows[] = zl_measure( $argv[ $i ] );
	}
	foreach ( $rows as $r ) {
		printf( "%s  [%d]\n", $r['url'], $r['status'] );
		printf(
			"  document %6d  inline-css %6d  inline-js %5d  css-req %d  js-req %d  total %6d  dom %d\n",
			$r['document_bytes'], $r['inline_css'], $r['inline_js'], $r['css_requests'], $r['js_requests'], $r['total_bytes'], $r['dom_elements']
		);
	}
	exit( 0 );
}
