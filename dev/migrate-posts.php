<?php
/**
 * انتقال نوشته‌های سایت قدیمی (دیتابیس ementash_old روی Local) به سایت جدید (فقط CLI).
 *
 * - نامک: اگر نوشته custom_permalink داشته، همان (آدرس قدیمی عوض نمی‌شود)؛ وگرنه نامک قدیمی.
 *   نامک‌های قبلی به _wp_old_slug می‌روند تا وردپرس خودش ریدایرکت کند.
 * - محتوا: کلاس، style، data-* و span/div اضافه پاک می‌شود؛ h1 به h2؛ عکس‌های داخل متن با فایل اصلی
 *   از _source/old-uploads وارد کتابخانه رسانه می‌شوند.
 * - تصویر شاخص، تاریخ‌ها، متای Yoast (عنوان، توضیح، کلمه کلیدی و...) منتقل می‌شوند.
 * - دوباره اجرا شدن چیزی تکراری نمی‌سازد (متای _em_old_id).
 *
 * php -c <php.ini سایت Local> dev/migrate-posts.php            ← فقط فهرست (چیزی نوشته نمی‌شود)
 * php -c <php.ini سایت Local> dev/migrate-posts.php --confirm  ← انتقال
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$confirm = in_array( '--confirm', $argv, true );
$uploads = dirname( __DIR__ ) . '/_source/old-uploads';

// پورت MySQL سایت Local با هر بار روشن شدن عوض می‌شود؛ از php.ini همان سایت خوانده می‌شود.
$old = new mysqli( '127.0.0.1', 'root', 'root', 'ementash_old', (int) ini_get( 'mysqli.default_port' ) );
$old->set_charset( 'utf8mb4' );

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
wp_set_current_user( $admins[0]->ID );

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
 * پیوست قدیمی ← شناسه پیوست در سایت جدید (با فایل اصلی از old-uploads). 0 اگر فایل نبود.
 */
function em_import_attachment( mysqli $db, $old_id, $uploads, $confirm, $parent = 0 ) {
	static $map = array();
	$old_id = (int) $old_id;
	if ( isset( $map[ $old_id ] ) ) {
		return $map[ $old_id ];
	}
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $old_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $found ) {
		return $map[ $old_id ] = (int) $found[0];
	}
	$meta = em_old_meta( $db, $old_id );
	$file = isset( $meta['_wp_attached_file'][0] ) ? $uploads . '/' . $meta['_wp_attached_file'][0] : '';
	if ( ! $file || ! is_file( $file ) ) {
		return $map[ $old_id ] = 0;
	}
	if ( ! $confirm ) {
		return $map[ $old_id ] = -1; // فقط فهرست: یعنی «وارد می‌شود».
	}
	$row = $db->query( 'SELECT post_title FROM wp_posts WHERE ID=' . $old_id )->fetch_assoc();
	$tmp = wp_tempnam( basename( $file ) );
	copy( $file, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => wp_basename( $file ),
			'tmp_name' => $tmp,
		),
		$parent,
		$row ? $row['post_title'] : ''
	);
	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, "  ! attachment $old_id: " . $id->get_error_message() . "\n" );
		return $map[ $old_id ] = 0;
	}
	update_post_meta( $id, '_em_old_id', $old_id );
	if ( ! empty( $meta['_wp_attachment_image_alt'][0] ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $meta['_wp_attachment_image_alt'][0] ) );
	}
	return $map[ $old_id ] = (int) $id;
}

/**
 * شناسه پیوست قدیمی از آدرس یک عکس (نسخه کوچک‌شده مثل ‎-300x200 به فایل اصلی برمی‌گردد).
 */
function em_old_attachment_by_url( mysqli $db, $url ) {
	$path = rawurldecode( (string) parse_url( $url, PHP_URL_PATH ) );
	if ( ! preg_match( '#/wp-content/uploads/(.+)$#', $path, $m ) ) {
		return 0;
	}
	$rel  = preg_replace( '/-\d+x\d+(\.[a-z0-9]+)$/i', '$1', $m[1] );
	$stmt = $db->prepare( "SELECT post_id FROM wp_postmeta WHERE meta_key='_wp_attached_file' AND meta_value=? LIMIT 1" );
	$stmt->bind_param( 's', $rel );
	$stmt->execute();
	$row = $stmt->get_result()->fetch_row();
	return $row ? (int) $row[0] : 0;
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

// متای Yoast که منتقل می‌شود (امتیازها، زمان مطالعه و دسته اصلی با شناسه قدیمی منتقل نمی‌شوند).
$yoast_skip = array( '_yoast_wpseo_content_score', '_yoast_wpseo_estimated-reading-time-minutes', '_yoast_wpseo_primary_category', '_yoast_wpseo_linkdex' );

$res   = $old->query( "SELECT * FROM wp_posts WHERE post_type='post' AND post_status='publish' ORDER BY post_date" );
$rows  = array();
$stats = array(
	'new'    => 0,
	'exists' => 0,
	'thumb'  => 0,
	'inline' => 0,
	'yoast'  => 0,
	'custom' => 0,
);
while ( $p = $res->fetch_assoc() ) {
	$rows[] = $p;
}

echo $confirm ? "انتقال:\n" : "فهرست (بدون --confirm چیزی نوشته نمی‌شود):\n";
foreach ( $rows as $p ) {
	$meta   = em_old_meta( $old, $p['ID'] );
	$custom = isset( $meta['custom_permalink'][0] ) ? trim( $meta['custom_permalink'][0], '/' ) : '';
	// نامک فارسی به همان شکل کدشده دیتابیس قدیمی داده می‌شود تا نویسه‌هایی مثل «؟» و «،» عیناً بمانند.
	$raw    = '' !== $custom ? $custom : $p['post_name'];
	$slug   = rawurldecode( $raw );
	$olds   = array_unique( array_filter( array_merge( '' !== $custom ? array( $p['post_name'] ) : array(), isset( $meta['_wp_old_slug'] ) ? $meta['_wp_old_slug'] : array() ) ) );
	$yoast  = array();
	foreach ( $meta as $k => $v ) {
		if ( 0 === strpos( $k, '_yoast_wpseo_' ) && ! in_array( $k, $yoast_skip, true ) && '' !== $v[0] ) {
			$yoast[ $k ] = $v[0];
		}
	}
	$existing = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => 'any',
			'meta_key'    => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => (int) $p['ID'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	preg_match_all( '/<img\b/i', $p['post_content'], $inline );

	printf(
		"  #%d → /%s/%s | شاخص:%s | عکس متن:%d | Yoast:%d%s\n",
		$p['ID'],
		$slug,
		'' !== $custom ? ' (custom)' : '',
		isset( $meta['_thumbnail_id'][0] ) ? 'دارد' : 'ندارد',
		count( $inline[0] ),
		count( $yoast ),
		$existing ? ' | قبلاً منتقل شده #' . $existing[0] : ''
	);
	$stats['custom'] += '' !== $custom ? 1 : 0;
	$stats['yoast']  += $yoast ? 1 : 0;
	if ( $existing ) {
		++$stats['exists'];
		continue;
	}
	if ( ! $confirm ) {
		++$stats['new'];
		$stats['thumb']  += isset( $meta['_thumbnail_id'][0] ) ? 1 : 0;
		$stats['inline'] += count( $inline[0] );
		continue;
	}

	$content = em_clean_html(
		$p['post_content'],
		static function ( $src, $alt ) use ( $old, $uploads ) {
			$aid = em_old_attachment_by_url( $old, $src );
			$nid = $aid ? em_import_attachment( $old, $aid, $uploads, true ) : 0;
			if ( ! $nid ) {
				fwrite( STDERR, "  ! عکس متن پیدا نشد: $src\n" );
				return '';
			}
			$GLOBALS['em_inline_count'] = ( isset( $GLOBALS['em_inline_count'] ) ? $GLOBALS['em_inline_count'] : 0 ) + 1;
			return wp_get_attachment_image( $nid, 'large', false, array( 'alt' => '' !== $alt ? $alt : get_post_meta( $nid, '_wp_attachment_image_alt', true ) ) );
		}
	);

	$new_id = wp_insert_post(
		array(
			'post_type'         => 'post',
			'post_status'       => 'publish',
			'post_title'        => $p['post_title'],
			'post_name'         => $raw,
			'post_content'      => $content,
			'post_excerpt'      => $p['post_excerpt'],
			'post_date'         => $p['post_date'],
			'post_date_gmt'     => $p['post_date_gmt'],
			'post_modified'     => $p['post_modified'],
			'post_modified_gmt' => $p['post_modified_gmt'],
			'post_author'       => $admins[0]->ID,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
		),
		true
	);
	if ( is_wp_error( $new_id ) ) {
		fwrite( STDERR, '  ! ' . $new_id->get_error_message() . "\n" );
		continue;
	}
	update_post_meta( $new_id, '_em_old_id', (int) $p['ID'] );
	foreach ( $olds as $s ) {
		add_post_meta( $new_id, '_wp_old_slug', $s );
	}
	foreach ( $yoast as $k => $v ) {
		update_post_meta( $new_id, $k, $v );
	}
	if ( isset( $meta['_thumbnail_id'][0] ) ) {
		$tid = em_import_attachment( $old, $meta['_thumbnail_id'][0], $uploads, true, $new_id );
		if ( $tid ) {
			set_post_thumbnail( $new_id, $tid );
			++$stats['thumb'];
		} else {
			fwrite( STDERR, "  ! تصویر شاخص #{$p['ID']} پیدا نشد\n" );
		}
	}
	++$stats['new'];
	// آدرس جدید باید همان آدرس قدیمی باشد.
	$new_path = rawurldecode( wp_parse_url( get_permalink( $new_id ), PHP_URL_PATH ) );
	if ( trim( $new_path, '/' ) !== $slug ) {
		fwrite( STDERR, "  ! آدرس عوض شد: /$slug/ → $new_path\n" );
	}
}
$stats['inline'] = $confirm ? ( isset( $GLOBALS['em_inline_count'] ) ? $GLOBALS['em_inline_count'] : 0 ) : $stats['inline'];
printf(
	"\n%s: %d نوشته | قبلاً منتقل‌شده: %d | با custom_permalink: %d | تصویر شاخص: %d | عکس داخل متن: %d | با متای Yoast: %d\n",
	$confirm ? 'منتقل شد' : 'منتقل می‌شود',
	$stats['new'],
	$stats['exists'],
	$stats['custom'],
	$stats['thumb'],
	$stats['inline'],
	$stats['yoast']
);
