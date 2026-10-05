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

	/*
	 * Scripts a tag never names. Core's emoji detection writes its own <script> at
	 * runtime, so counting src attributes reports zero while the browser still
	 * fetches wp-emoji-release.min.js. Any inline block that hands a .js URL to the
	 * page is counted here instead of being missed.
	 */
	$dynamic_js = array();
	if ( preg_match_all( '~<script(?![^>]*\ssrc=)[^>]*>(.*?)</script>~s', $html, $inline_blocks ) ) {
		foreach ( $inline_blocks[1] as $block ) {
			if ( preg_match_all( '~https?://[^"\'\s\\)]+?\.js(?:\?[^"\'\s\\)]*)?~i', $block, $found ) ) {
				foreach ( $found[0] as $src ) {
					$dynamic_js[] = html_entity_decode( $src );
				}
			}
		}
	}
	$dynamic_js = array_values( array_diff( array_unique( $dynamic_js ), $js_src[1] ) );

	/*
	 * Images a browser fetches while rendering, kept apart from the ones it defers.
	 * Folding lazy images into the total would report a weight no visitor pays on
	 * load. srcset is not expanded: the src is measured as the representative
	 * candidate, since only one is ever fetched.
	 */
	$img_eager  = array();
	$img_lazy   = array();
	$img_srcset = 0;
	if ( preg_match_all( '~<img[^>]*>~i', $html, $img_tags ) ) {
		foreach ( $img_tags[0] as $tag ) {
			if ( ! preg_match( '~\ssrc=["\']([^"\']+)~i', $tag, $sm ) ) {
				continue;
			}
			$src = html_entity_decode( $sm[1] );
			if ( preg_match( '~\ssrcset=~i', $tag ) ) {
				++$img_srcset;
			}
			if ( preg_match( '~\sloading=["\']lazy["\']~i', $tag ) ) {
				$img_lazy[] = $src;
			} else {
				$img_eager[] = $src;
			}
		}
	}
	$img_eager = array_values( array_unique( $img_eager ) );
	$img_lazy  = array_values( array_diff( array_unique( $img_lazy ), $img_eager ) );

	$sub_bytes = 0;
	$sub_count = 0;
	foreach ( array_merge( $css_href[1], $js_src[1], $dynamic_js, $font_href[1] ) as $href ) {
		$res        = zl_fetch( zl_absolute( html_entity_decode( $href ), $url ) );
		$sub_bytes += strlen( $res['body'] );
		++$sub_count;
	}

	$img_eager_bytes = 0;
	foreach ( $img_eager as $href ) {
		$img_eager_bytes += strlen( zl_fetch( zl_absolute( $href, $url ) )['body'] );
	}

	$img_lazy_bytes = 0;
	foreach ( $img_lazy as $href ) {
		$img_lazy_bytes += strlen( zl_fetch( zl_absolute( $href, $url ) )['body'] );
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
		'js_dynamic'       => count( $dynamic_js ),
		'js_dynamic_srcs'  => $dynamic_js,
		'font_preloads'    => count( $font_href[1] ),
		'font_face_rules'  => count( $font_faces[0] ),
		'img_eager'        => count( $img_eager ),
		'img_eager_bytes'  => $img_eager_bytes,
		'img_lazy'         => count( $img_lazy ),
		'img_lazy_bytes'   => $img_lazy_bytes,
		'subresource_reqs' => $sub_count,
		'subresource_bytes'=> $sub_bytes,
		'img_srcset'       => $img_srcset,
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
			"  document %6d  inline-css %6d  inline-js %5d  css-req %d  js-req %d (+%d dynamic)  total %6d  dom %d\n  images: %d eager (<=%d bytes), %d lazy (<=%d bytes), %d with srcset\n",
			$r['document_bytes'], $r['inline_css'], $r['inline_js'], $r['css_requests'], $r['js_requests'], $r['js_dynamic'], $r['total_bytes'], $r['dom_elements'],
			$r['img_eager'], $r['img_eager_bytes'], $r['img_lazy'], $r['img_lazy_bytes'], $r['img_srcset']
		);
		foreach ( $r['js_dynamic_srcs'] as $src ) {
			printf( "  dynamic script: %s\n", $src );
		}
	}
	exit( 0 );
}
