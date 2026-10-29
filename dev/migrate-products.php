<?php
/**
 * انتقال ۲۶ محصول و ۷ دسته سایت قدیمی (دیتابیس ementash_old روی Local) به ووکامرس سایت جدید (فقط CLI).
 *
 * - نامک: custom_permalink اگر بود، وگرنه نامک قدیمی؛ نامک دسته‌ها و والدشان همان قدیمی.
 * - متن به سه تب تقسیم می‌شود (بخش‌بندی با تیترها):
 *   «کاربرد…» ← _ementash_applications؛ «بسته‌بندی…»، «شرایط نگهداری»، «اطلاعات سفارش» ← _ementash_packaging؛
 *   بقیه ← توضیحات (تب «معرفی»). تیتر اول (نام محصول)، «معرفی محصول» و تیتر اولین بخش هر تب حذف و
 *   باقی تیترها h3 می‌شوند. عکس‌های قدیمی منتقل نمی‌شوند (عکاسی جدید).
 * - کلاس آتش فقط برای محصولاتی که نام یا متنشان قطعی است. متای Yoast منتقل می‌شود.
 * - دوباره اجرا شدن چیزی تکراری نمی‌سازد (متای _em_old_id روی محصول و دسته).
 *
 * php -c <php.ini سایت Local> dev/migrate-products.php            ← فقط فهرست (چیزی نوشته نمی‌شود)
 * php -c <php.ini سایت Local> dev/migrate-products.php --confirm  ← انتقال
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once __DIR__ . '/migrate-lib.php';

$confirm = in_array( '--confirm', $argv, true );

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

// اصلاح نام از کارفرما (شناسه قدیمی ← جایگزینی).
$renames = array(
	890 => array( 'ضد الکل' => 'مقاوم الکلی' ),
	893 => array( 'FFFP 1-14' => 'FFFP کد 1-14' ),
);

// کلاس آتش: فقط جایی که نام یا متن محصول قطعی است.
$fire = array(
	884  => array( 'b', 'c' ), // PO-600A، «گروه BC».
	886  => array( 'b', 'c' ), // PO-700، «گروه BC».
	888  => array( 'a', 'b', 'c' ), // PO-800، «گروه ABC».
	1115 => array( 'd' ), // PO-900B، «گروه D».
	892  => array( 'b' ), // FP: «(کلاس B)» در متن.
	894  => array( 'b' ), // FP-AR: «(کلاس B)» در متن.
	902  => array( 'b' ), // FFFP-AR: «(کلاس B)» در متن.
	908  => array( 'b' ), // ICAO Level B: «(کلاس B)» در متن.
	1120 => array( 'b' ), // Bio Foam: «کلاس B» در متن.
);

// متای Yoast که منتقل نمی‌شود (امتیازها، زمان مطالعه، شناسه‌های قدیمی و اطلاعات ریدایرکت).
$yoast_skip = array( '_yoast_wpseo_content_score', '_yoast_wpseo_estimated-reading-time-minutes', '_yoast_wpseo_linkdex', '_yoast_wpseo_primary_product_cat', '_yoast_wpseo_primary_product_brand' );

/**
 * تب مقصد یک بخش از روی تیترش: use، pack یا intro.
 */
function em_product_tab( $heading ) {
	foreach ( array( 'کاربرد' => 'use', 'بسته' => 'pack', 'شرایط نگهداری' => 'pack', 'اطلاعات سفارش' => 'pack', 'مشخصات فنی و بسته' => 'pack' ) as $prefix => $tab ) {
		if ( 0 === mb_strpos( $heading, $prefix ) ) {
			return $tab;
		}
	}
	return 'intro';
}

/**
 * تقسیم HTML پاک‌شده به سه تب. خروجی: array( tab => array( 'html' => ..., 'heads' => array( تیترها ) ) ).
 */
function em_split_product( $html ) {
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="em-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root = $doc->getElementById( 'em-root' );

	// بخش‌ها: هر تیتر سطح بالا یک بخش تازه شروع می‌کند.
	$sections = array(
		array(
			'head'  => null,
			'nodes' => array(),
		),
	);
	foreach ( iterator_to_array( $root->childNodes ) as $node ) {
		if ( XML_ELEMENT_NODE === $node->nodeType && preg_match( '/^h[1-6]$/', $node->nodeName ) ) {
			$sections[] = array(
				'head'  => $node,
				'nodes' => array(),
			);
			continue;
		}
		$sections[ count( $sections ) - 1 ]['nodes'][] = $node;
	}

	$out = array(
		'intro' => array(
			'html'  => '',
			'heads' => array(),
		),
		'use'   => array(
			'html'  => '',
			'heads' => array(),
		),
		'pack'  => array(
			'html'  => '',
			'heads' => array(),
		),
	);
	$first = true;
	foreach ( $sections as $section ) {
		$body = '';
		foreach ( $section['nodes'] as $node ) {
			$body .= $doc->saveHTML( $node );
		}
		$head = null === $section['head'] ? '' : trim( preg_replace( '/[\s:：]+$/u', '', trim( $section['head']->textContent ) ) );
		if ( null === $section['head'] ) {
			$out['intro']['html'] .= $body;
			continue;
		}
		$tab = em_product_tab( $head );
		// تیتر اول متن نام محصول است؛ «معرفی محصول» هم همان اسم تب است.
		$drop  = ( $first && '' === trim( $out['intro']['html'] ) ) || 0 === mb_strpos( $head, 'معرفی محصول' );
		$first = false;
		// تیتر اولین بخش هر تب کاربردها و بسته‌بندی را اسم تب جایگزین می‌کند.
		if ( 'intro' !== $tab && '' === $out[ $tab ]['html'] ) {
			$drop = true;
		}
		$out[ $tab ]['heads'][] = $drop ? '(' . $head . ')' : $head;
		$out[ $tab ]['html']   .= ( $drop ? '' : '<h3>' . esc_html( $head ) . "</h3>\n" ) . $body;
	}
	foreach ( $out as $tab => $data ) {
		$out[ $tab ]['html'] = trim( preg_replace( "/\n{3,}/", "\n\n", $data['html'] ) );
	}
	return $out;
}

/**
 * دسته محصول با شناسه قدیمی ← شناسه در سایت جدید (با همان نامک و والد). -1 در حالت فهرست یعنی «ساخته می‌شود».
 */
function em_product_cat( mysqli $db, $old_id, $confirm ) {
	static $map = array();
	$old_id = (int) $old_id;
	if ( isset( $map[ $old_id ] ) ) {
		return $map[ $old_id ];
	}
	$row  = $db->query( "SELECT t.slug, t.name, tt.parent, tt.description FROM wp_terms t JOIN wp_term_taxonomy tt USING(term_id) WHERE tt.taxonomy='product_cat' AND t.term_id=" . $old_id )->fetch_assoc();
	$term = get_term_by( 'slug', $row['slug'], 'product_cat' );
	if ( $term ) {
		return $map[ $old_id ] = (int) $term->term_id;
	}
	$parent = $row['parent'] ? em_product_cat( $db, $row['parent'], $confirm ) : 0;
	if ( ! $confirm ) {
		return $map[ $old_id ] = -1;
	}
	$new = wp_insert_term(
		$row['name'],
		'product_cat',
		array(
			'slug'        => $row['slug'],
			'parent'      => max( 0, $parent ),
			'description' => $row['description'],
		)
	);
	if ( is_wp_error( $new ) ) {
		fwrite( STDERR, "  ! دسته {$row['slug']}: " . $new->get_error_message() . "\n" );
		return $map[ $old_id ] = 0;
	}
	update_term_meta( $new['term_id'], '_em_old_id', $old_id );
	return $map[ $old_id ] = (int) $new['term_id'];
}

$fire_id = function_exists( 'wc_attribute_taxonomy_id_by_name' ) ? wc_attribute_taxonomy_id_by_name( 'fire_class' ) : 0;
if ( ! $fire_id || ! taxonomy_exists( 'pa_fire_class' ) ) {
	fwrite( STDERR, "ویژگی pa_fire_class وجود ندارد؛ اول قالب را دوباره فعال کنید.\n" );
	exit( 1 );
}

$res   = $old->query( "SELECT * FROM wp_posts WHERE post_type='product' AND post_status='publish' ORDER BY menu_order, ID" );
$stats = array(
	'new'    => 0,
	'exists' => 0,
	'yoast'  => 0,
	'fire'   => 0,
);

echo $confirm ? "انتقال:\n" : "فهرست (بدون --confirm چیزی نوشته نمی‌شود):\n";
while ( $p = $res->fetch_assoc() ) {
	$id     = (int) $p['ID'];
	$meta   = em_old_meta( $old, $id );
	$custom = isset( $meta['custom_permalink'][0] ) ? preg_replace( '#^product/#', '', trim( $meta['custom_permalink'][0], '/' ) ) : '';
	// نامک فارسی به همان شکل کدشده دیتابیس قدیمی داده می‌شود.
	$raw   = '' !== $custom ? $custom : $p['post_name'];
	$slug  = rawurldecode( $raw );
	$olds  = array_merge( '' !== $custom ? array( $p['post_name'] ) : array(), isset( $meta['_wp_old_slug'] ) ? $meta['_wp_old_slug'] : array() );
	$olds  = array_unique( array_filter( $olds, static function ( $s ) use ( $raw ) {
		return '' !== $s && false === strpos( $s, '__trashed' ) && strtolower( $s ) !== strtolower( $raw );
	} ) );
	$title = trim( preg_replace( '/ {2,}/u', ' ', $p['post_title'] ) );
	if ( isset( $renames[ $id ] ) ) {
		$title = strtr( $title, $renames[ $id ] );
	}
	$yoast = array();
	foreach ( $meta as $k => $v ) {
		if ( 0 === strpos( $k, '_yoast_wpseo_' ) && ! in_array( $k, $yoast_skip, true ) && '' !== $v[0] ) {
			$yoast[ $k ] = $v[0];
		}
	}

	$cats = array();
	$r    = $old->query( "SELECT tt.term_id FROM wp_term_relationships tr JOIN wp_term_taxonomy tt USING(term_taxonomy_id) WHERE tt.taxonomy='product_cat' AND tr.object_id=" . $id );
	while ( $c = $r->fetch_row() ) {
		$cats[] = em_product_cat( $old, $c[0], $confirm );
	}
	$cat_slugs = array();
	$r         = $old->query( "SELECT t.slug FROM wp_term_relationships tr JOIN wp_term_taxonomy tt USING(term_taxonomy_id) JOIN wp_terms t USING(term_id) WHERE tt.taxonomy='product_cat' AND tr.object_id=" . $id );
	while ( $c = $r->fetch_row() ) {
		$cat_slugs[] = $c[0];
	}

	$no_img = static function () {
		return '';
	};
	$tabs   = em_split_product( em_clean_html( $p['post_content'], $no_img ) );
	$short  = '' !== trim( $p['post_excerpt'] ) ? em_clean_html( $p['post_excerpt'], $no_img ) : '';
	$class  = isset( $fire[ $id ] ) ? $fire[ $id ] : array();

	$existing = get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'any',
			'meta_key'    => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);

	printf(
		"\n  #%d %s%s\n     /product/%s/%s | دسته: %s | کلاس آتش: %s | Yoast:%d | توضیح کوتاه:%s%s\n     معرفی: %s\n     کاربردها: %s\n     بسته‌بندی: %s\n",
		$id,
		$title,
		isset( $renames[ $id ] ) ? '  (نام اصلاح شد؛ قبلاً: ' . $p['post_title'] . ')' : '',
		$slug,
		'' !== $custom ? ' (custom_permalink)' : '',
		implode( '، ', $cat_slugs ),
		$class ? strtoupper( implode( '', $class ) ) : '-',
		count( $yoast ),
		'' !== $short ? 'دارد' : 'ندارد',
		$existing ? ' | قبلاً منتقل شده #' . $existing[0] : '',
		implode( ' ، ', $tabs['intro']['heads'] ) ? implode( ' ، ', $tabs['intro']['heads'] ) : '-',
		$tabs['use']['heads'] ? implode( ' ، ', $tabs['use']['heads'] ) : '-',
		$tabs['pack']['heads'] ? implode( ' ، ', $tabs['pack']['heads'] ) : '-'
	);
	$stats['yoast'] += $yoast ? 1 : 0;
	$stats['fire']  += $class ? 1 : 0;
	if ( $existing ) {
		++$stats['exists'];
		continue;
	}
	if ( ! $confirm ) {
		++$stats['new'];
		continue;
	}

	$attrs = array();
	if ( $class ) {
		$attr = new WC_Product_Attribute();
		$attr->set_id( $fire_id );
		$attr->set_name( 'pa_fire_class' );
		$attr->set_options(
			array_map(
				static function ( $c ) {
					return (int) get_term_by( 'slug', $c, 'pa_fire_class' )->term_id;
				},
				$class
			)
		);
		$attr->set_visible( true );
		$attr->set_variation( false );
		$attrs[] = $attr;
	}

	$product = new WC_Product_Simple();
	$product->set_name( $title );
	$product->set_slug( $raw );
	$product->set_status( 'publish' );
	$product->set_description( $tabs['intro']['html'] );
	$product->set_short_description( $short );
	$product->set_category_ids( array_filter( $cats ) );
	$product->set_menu_order( (int) $p['menu_order'] );
	$product->set_date_created( $p['post_date_gmt'] . '+00:00' );
	$product->set_reviews_allowed( false );
	$product->set_attributes( $attrs );
	$new_id = $product->save();
	if ( ! $new_id ) {
		fwrite( STDERR, "  ! ساخت محصول #$id ناموفق بود\n" );
		continue;
	}
	update_post_meta( $new_id, '_em_old_id', $id );
	update_post_meta( $new_id, '_ementash_applications', $tabs['use']['html'] );
	update_post_meta( $new_id, '_ementash_packaging', $tabs['pack']['html'] );
	foreach ( $olds as $s ) {
		add_post_meta( $new_id, '_wp_old_slug', $s );
	}
	foreach ( $yoast as $k => $v ) {
		update_post_meta( $new_id, $k, $v );
	}
	++$stats['new'];
	// آدرس جدید باید همان آدرس قدیمی باشد.
	$new_path = rawurldecode( wp_parse_url( get_permalink( $new_id ), PHP_URL_PATH ) );
	if ( '/product/' . $slug . '/' !== $new_path ) {
		fwrite( STDERR, "  ! آدرس عوض شد: /product/$slug/ → $new_path\n" );
	}
}

printf(
	"\n%s: %d محصول | قبلاً منتقل‌شده: %d | با کلاس آتش: %d | با متای Yoast: %d\n",
	$confirm ? 'منتقل شد' : 'منتقل می‌شود',
	$stats['new'],
	$stats['exists'],
	$stats['fire'],
	$stats['yoast']
);
