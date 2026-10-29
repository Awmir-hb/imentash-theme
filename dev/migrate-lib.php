<?php
/**
 * توابع مشترک اسکریپت‌های انتقال از سایت قدیمی (migrate-posts و migrate-products). فقط CLI.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

/**
 * متای یک پست قدیمی: کلید ← آرایه مقدارها.
 */
function em_old_meta( mysqli $db, $id ) {
	$out = array();
	$res = $db->query( 'SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id=' . (int) $id );
	while ( $m = $res->fetch_assoc() ) {
		$out[ $m['meta_key'] ][] = $m['meta_value'];
	}
	return $out;
}

/**
 * پاک‌سازی HTML کپی‌شده: فقط تگ‌ها و ویژگی‌های معنایی می‌مانند. عکس‌ها با تابع $img جایگزین می‌شوند.
 */
function em_clean_html( $html, callable $img ) {
	$keep = array(
		'a'  => array( 'href', 'target', 'rel' ),
		'th' => array( 'scope', 'colspan', 'rowspan' ),
		'td' => array( 'colspan', 'rowspan' ),
		'ol' => array( 'start' ),
	);
	$doc  = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="em-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root  = $doc->getElementById( 'em-root' );
	$xpath = new DOMXPath( $doc );

	// عکس‌ها با نشانگر متنی جایگزین می‌شوند و بعد از ذخیره HTML برمی‌گردند (DOMDocument و kses به آن دست نمی‌زنند).
	$images = array();
	foreach ( iterator_to_array( $xpath->query( './/img', $root ) ) as $node ) {
		$html_img = $img( $node->getAttribute( 'src' ), $node->getAttribute( 'alt' ) );
		$token    = '';
		if ( $html_img ) {
			$token            = 'EMIMG' . count( $images ) . 'EM';
			$images[ $token ] = $html_img;
		}
		$node->parentNode->replaceChild( $doc->createTextNode( $token ), $node );
	}
	foreach ( iterator_to_array( $xpath->query( './/h1', $root ) ) as $node ) {
		$h2 = $doc->createElement( 'h2' );
		while ( $node->firstChild ) {
			$h2->appendChild( $node->firstChild );
		}
		$node->parentNode->replaceChild( $h2, $node );
	}
	// span و div فقط بسته‌بندی‌اند؛ محتوایشان می‌ماند. div با خط خالی جدا می‌شود تا پاراگراف‌ها نچسبند.
	foreach ( array_reverse( iterator_to_array( $xpath->query( './/span|.//div', $root ) ) ) as $node ) {
		$parent = $node->parentNode;
		if ( 'div' === $node->nodeName ) {
			$parent->insertBefore( $doc->createTextNode( "\n\n" ), $node );
		}
		while ( $node->firstChild ) {
			$parent->insertBefore( $node->firstChild, $node );
		}
		if ( 'div' === $node->nodeName ) {
			$parent->insertBefore( $doc->createTextNode( "\n\n" ), $node );
		}
		$parent->removeChild( $node );
	}
	foreach ( iterator_to_array( $xpath->query( './/*', $root ) ) as $node ) {
		$allowed = isset( $keep[ $node->nodeName ] ) ? $keep[ $node->nodeName ] : array();
		foreach ( iterator_to_array( $node->attributes ) as $attr ) {
			if ( ! in_array( $attr->name, $allowed, true ) ) {
				$node->removeAttribute( $attr->name );
			}
		}
		if ( 'a' === $node->nodeName && '_blank' === $node->getAttribute( 'target' ) ) {
			$node->setAttribute( 'rel', 'noopener' );
		}
	}

	$out = '';
	foreach ( $root->childNodes as $child ) {
		$out .= $doc->saveHTML( $child );
	}
	$out = preg_replace( "/\n{3,}/", "\n\n", $out );
	return strtr( trim( wp_kses_post( $out ) ), $images );
}
