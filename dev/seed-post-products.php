<?php
/**
 * انتخاب اولیه «محصولات مرتبط» مقاله‌ها (متای _ementash_products). فقط CLI، روی Local.
 * پیش‌نویس از روی عنوان مقاله و نوع محصول است؛ کارفرما از کادر «محصولات مرتبط» در ویرایش مقاله اصلاحش می‌کند.
 * مقاله‌ها با _em_old_id و محصولات با بخشی از عنوان پیدا می‌شوند. مقاله‌های داخل جدول از نو نوشته می‌شوند.
 *
 * php -c <php.ini سایت Local> dev/seed-post-products.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-post-products.php --confirm  ← اعمال
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );

// کلید کوتاه ← بخشی از عنوان محصول
$p = array(
	'afff'    => 'AFFF کد 46-1',
	'afff_ar' => 'AFFF-AR (1-45)',
	'icao'    => 'ICAO Level B',
	'hx'      => 'High Expansion',
	'bio'     => 'Bio Foam',
	'fp'      => 'فوم فلورو پروتئینی (FP)',
	'fp_ar'   => '(FP-AR)',
	'fffp_ar' => 'FFFP-AR',
	'po600'   => 'PO-600A',
	'po700'   => 'PO-700',
	'po800'   => 'PO-800',
	'po900'   => 'PO-900B',
	'alc'     => 'فوم الکلی صنعتی',
	'sil'     => 'فوم سیلیکونی',
	'fat'     => 'فوم فتی اسیدی',
	'food'    => 'فوم غذایی مجاز',
	'fresh'   => 'حفاری آب شیرین',
	'salt'    => 'حفاری آب شور',
	'metro'   => 'تونل مترو',
);

// شناسه قدیمی مقاله ← محصولات (به ترتیب نمایش). مقاله‌های بی‌ربط به محصول (نانو، فوم دریایی) نیستند.
$map = array(
	1789 => array( 'afff', 'afff_ar', 'fp' ),       // فوم آتش‌نشانی چیست
	1914 => array( 'afff', 'afff_ar', 'hx' ),       // مکانیزم اطفا با فوم
	1933 => array( 'po800' ),                       // پودر ABC چیست
	1948 => array( 'afff', 'po800', 'po600' ),      // مقایسه فوم و پودر
	2043 => array( 'bio' ),                         // فوم‌های بدون فلوئور
	2127 => array( 'bio' ),                         // ممنوعیت PFAS
	2121 => array( 'bio' ),                         // پاک‌سازی PFAS
	2120 => array( 'afff', 'hx' ),                  // CAFS و آب‌مه
	2119 => array( 'bio' ),                         // فوم زیستی
	2112 => array( 'po800', 'po700' ),              // پودر سبز
	2111 => array( 'icao', 'bio' ),                 // پروژه FIRE و فرودگاه‌ها
	2110 => array( 'po900' ),                       // باتری لیتیوم و کلاس D
	2113 => array( 'bio', 'afff' ),                 // چالش‌های AFFF
	3137 => array( 'sil', 'alc', 'fat' ),           // آنتی‌فوم چیست
	3147 => array( 'po800', 'po600' ),              // شارژ مجدد کپسول
	3160 => array( 'afff', 'afff_ar', 'fp' ),       // انواع فوم
	3165 => array( 'po800', 'po600', 'po700' ),     // کپسول ABC و BC
	3174 => array( 'bio' ),                         // فوم F3
	3180 => array( 'afff', 'afff_ar', 'icao' ),     // AFFF چیست
	3185 => array( 'afff', 'po800' ),               // عمر مفید و نگهداری
	3211 => array( 'po800', 'po700', 'po600' ),     // سپیکو در نفت و گاز
	3213 => array( 'food' ),                        // آنتی‌فوم خوراکی
	3219 => array( 'po800', 'po600' ),              // سپیکو ایمنی صنعتی
	3223 => array( 'po800', 'po600' ),              // چک‌لیست سالیانه
	3152 => array( 'bio' ),                         // حذف PFAS و F3
	3224 => array( 'po800' ),                       // ABC در کارگاه
	3225 => array( 'po800', 'po600', 'po700' ),     // راهنمای خرید سپیکو
	3226 => array( 'po800', 'po700' ),              // شارژ در نفت و گاز
	3227 => array( 'afff_ar', 'afff', 'fp_ar' ),    // فوم نفت و پتروشیمی
	3234 => array( 'fresh', 'salt', 'metro' ),      // فوم حفاری
	3236 => array( 'po800', 'po600' ),              // ABC و BC
	3238 => array( 'po800' ),                       // ABC منازل و ادارات
	3239 => array( 'po800' ),                       // ABC سپیکو
	3240 => array( 'po800' ),                       // کپسول خودرو
	3241 => array( 'po800', 'po600' ),              // چرا سپیکو
	3242 => array( 'po800', 'po700' ),              // سپیکو و رقبا
	3243 => array( 'po800' ),                       // مقایسه پودر ABC سپیکو
	3275 => array( 'food' ),                        // خرید آنتی‌فوم خوراکی
	3277 => array( 'po800', 'po600', 'po700' ),     // ABC و BC در کارخانه
	3280 => array( 'food', 'sil' ),                 // ضدکف خوراکی
	3284 => array( 'food' ),                        // تولید شکر
	3286 => array( 'alc', 'sil' ),                  // فاضلاب
	3289 => array( 'afff', 'afff_ar', 'fp' ),       // تولیدکننده فوم
	3291 => array( 'afff', 'afff_ar', 'fffp_ar' ),  // AFFF و AR-AFFF
	3293 => array( 'alc' ),                         // کاغذسازی
	3295 => array( 'food' ),                        // مرباسازی
);

// پیدا کردن شناسه محصولات
$all = get_posts(
	array(
		'post_type'      => 'product',
		'post_status'    => 'any',
		'posts_per_page' => -1,
	)
);
$ids = array();
foreach ( $p as $key => $needle ) {
	foreach ( $all as $product ) {
		if ( false !== mb_strpos( $product->post_title, $needle ) && 0 !== strpos( $product->post_name, 'em-demo-' ) ) {
			$ids[ $key ] = (int) $product->ID;
			break;
		}
	}
	if ( empty( $ids[ $key ] ) ) {
		printf( "!! محصول «%s» پیدا نشد\n", $needle );
	}
}

foreach ( $map as $old_id => $keys ) {
	$post_ids = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $old_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! $post_ids ) {
		printf( "!! مقاله قدیمی %d پیدا نشد\n", $old_id );
		continue;
	}
	$products = array();
	foreach ( $keys as $key ) {
		if ( ! empty( $ids[ $key ] ) ) {
			$products[] = $ids[ $key ];
		}
	}
	printf( "#%d %s\n   ← %s\n", $post_ids[0], get_the_title( $post_ids[0] ), implode( ' / ', array_map( 'get_the_title', $products ) ) );
	if ( $confirm && $products ) {
		update_post_meta( $post_ids[0], '_ementash_products', $products );
	}
}

echo $confirm ? "\nانجام شد.\n" : "\nبدون --confirm چیزی تغییر نکرد.\n";
