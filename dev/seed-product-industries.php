<?php
/**
 * اتصال محصولات به برگه‌های صنعت (متای _ementash_industry) از جدول پیش‌نویس
 * _source/report/product-industry-draft.csv. فقط CLI، روی Local.
 *
 * - ✓ = در متن محصول در سایت فعلی به آن صنعت اشاره شده (پیش‌فرض فقط همین‌ها).
 * - ○ = پیشنهاد بر اساس نوع محصول؛ فقط با --with-suggested.
 * - صنایع هر محصولی که در جدول هست از نو نوشته می‌شود؛ محصولات دیگر دست نمی‌خورند.
 *
 * php -c <php.ini سایت Local> dev/seed-product-industries.php                       ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-product-industries.php --confirm             ← فقط ✓
 * php -c <php.ini سایت Local> dev/seed-product-industries.php --confirm --with-suggested
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm   = in_array( '--confirm', $argv, true );
$suggested = in_array( '--with-suggested', $argv, true );
$csv       = dirname( __DIR__ ) . '/_source/report/product-industry-draft.csv';

// نام ستون جدول ← عنوان برگه صنعت
$columns = array(
	'نفت، گاز و پتروشیمی'    => 'صنایع نفت، گاز و پتروشیمی',
	'هواپیمایی و فرودگاه‌ها' => 'سازمان هواپیمایی کشوری و فرودگاه ها',
	'نیروگاهی'               => 'صنایع نیروگاهی',
	'فولاد، آهن و مس'        => 'صنایع فولاد، آهن و مس',
	'نظامی'                  => 'صنایع و سازمان های نظامی',
	'سازمان‌های آتش‌نشانی'   => 'سازمان های آتش نشانی',
	'غذایی'                  => 'صنایع غذایی',
	'نساجی'                  => 'صنایع نساجی',
	'کاغذ و چاپ'             => 'صنایع کاغذ و چاپ',
);

// نام محصول در جدول ← بخشی از عنوان محصول در سایت (نام‌ها کامل یکی نیستند)
$products = array(
	'AFFF کد 1-46'                          => 'AFFF کد 46-1',
	'AFFF-AR مقاوم الکلی (1-45)'            => 'AFFF-AR (1-45)',
	'AFFF 3% ICAO Level B (فوم فرودگاهی)'   => 'ICAO Level B',
	'فوم پرتوسعه High Expansion (1-36)'     => 'High Expansion',
	'بایو فوم Bio Foam'                      => 'Bio Foam',
	'Training Foam'                          => 'Training Foam',
	'فوم فلوروپروتئینی (FP)'                => 'فوم فلورو پروتئینی (FP)',
	'فوم فلوروپروتئینی مقاوم الکلی (FP-AR)' => '(FP-AR)',
	'FFFP کد 1-14'                           => 'FFFP کد 1-14',
	'FFFP-AR کد 1-16'                        => 'FFFP-AR',
	'پودر بی‌کربنات سدیم BC / PO-600A'      => 'سدیم (گروه BC) / PO-600A',
	'پودر بی‌کربنات پتاسیم BC / PO-700'     => 'PO-700',
	'پودر مونوآمونیوم فسفات ABC / PO-800'   => 'PO-800',
	'پودر گروه فلزات D / PO-900B'           => 'PO-900B',
	'آنتی‌فوم الکلی صنعتی'                  => 'فوم الکلی صنعتی',
	'آنتی‌فوم سیلیکونی'                     => 'فوم سیلیکونی',
	'آنتی‌فوم غذایی مجاز'                   => 'فوم غذایی مجاز',
	'آنتی‌فوم فتی‌اسیدی'                    => 'فوم فتی اسیدی',
	'آنتی‌فوم‌های تخصصی'                    => 'آنتی‌فوم‌های تخصصی',
	'فوم حفاری آب شیرین'                    => 'فوم حفاری آب شیرین',
	'فوم حفاری آب شور'                      => 'فوم حفاری آب شور',
	'فوم حفاری تونل مترو'                   => 'فوم حفاری تونل مترو',
	'دیفومر گل حفاری'                       => 'دیفومر گل حفاری',
	'دیفومر سیمان حفاری'                    => 'دیفومر سیمان حفاری',
	'فوم بتن پروتئینی'                      => 'فوم بتن پروتئینی',
	'فوم بتن شیمیایی سبک'                   => 'فوم بتن شیمیایی سبک',
	'وتینگ ایجنت تخصصی صنایع نساجی'         => 'رطوبت‌دهنده سریع', // KX-327
	'خاموش‌کننده آتش کلاس K'                => 'کلاس K',
	'وت کمیکال (Wet Chemical)'              => 'Wet Chemical',
);

$pages = array();
foreach ( $columns as $col => $title ) {
	$p = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'title'       => $title,
			'numberposts' => 1,
		)
	);
	if ( ! $p ) {
		exit( "Industry page not found: $title\n" );
	}
	$pages[ $col ] = (int) $p[0]->ID;
}

$all = get_posts(
	array(
		'post_type'   => 'product',
		'post_status' => 'publish',
		'numberposts' => -1,
	)
);
$find = static function ( $needle ) use ( $all ) {
	foreach ( $all as $p ) {
		if ( false !== mb_strpos( $p->post_title, $needle ) ) {
			return $p;
		}
	}
	return null;
};

$rows   = array_map( 'str_getcsv', file( $csv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );
$header = array_map( static fn( $h ) => trim( $h, "\xEF\xBB\xBF \t" ), array_shift( $rows ) );
$plan   = array(); // شناسه محصول ← شناسه برگه‌ها (دو ردیف جدول ممکن است یک محصول باشند)
$names  = array();

foreach ( $rows as $row ) {
	$name = isset( $row[1] ) ? trim( $row[1] ) : '';
	if ( ! isset( $products[ $name ] ) ) {
		continue; // راهنما و نکته پایین جدول
	}
	$product = $find( $products[ $name ] );
	if ( ! $product ) {
		echo "product not found: $name\n";
		continue;
	}
	$plan[ $product->ID ]  = isset( $plan[ $product->ID ] ) ? $plan[ $product->ID ] : array();
	$names[ $product->ID ] = $product->post_title;
	foreach ( $header as $i => $col ) {
		if ( ! isset( $pages[ $col ] ) ) {
			continue;
		}
		$mark = isset( $row[ $i ] ) ? trim( $row[ $i ] ) : '';
		if ( '✓' === $mark || ( $suggested && '○' === $mark ) ) {
			$plan[ $product->ID ][] = $pages[ $col ];
		}
	}
}

$labels = array_flip( $pages );
foreach ( $plan as $id => $ids ) {
	$ids = array_values( array_unique( $ids ) );
	echo '#' . $id . ' ' . $names[ $id ] . ' → ' . ( $ids ? implode( '، ', array_map( static fn( $p ) => $labels[ $p ], $ids ) ) : '(هیچ صنعتی)' ) . "\n";
	if ( $confirm ) {
		delete_post_meta( $id, '_ementash_industry' );
		foreach ( $ids as $page_id ) {
			add_post_meta( $id, '_ementash_industry', $page_id );
		}
	}
}

echo $confirm ? "Done.\n" : "Report only. Run with --confirm to write.\n";
