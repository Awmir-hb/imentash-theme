<?php
/**
 * عکس محصولات طبق پیام کارفرما (۱۴۰۵/۰۸/۲۵) و آیکون دسته‌ها. فقط CLI، روی Local.
 *
 * عکس‌ها همان‌هایی‌اند که از _source/image_product/web در کتابخانه هستند (با نام فایل پیدا می‌شوند):
 * - فوم آتش‌نشانی (شیمیایی و پروتئینی، همه زیردسته‌ها): پالت بشکه‌های ۲۲۰ لیتری از زاویه کنار.
 * - پودرها: کیسه (عکس اصلی) + سطل (گالری)؛ سدیم: کیسه نارنجی + سطل آبی کم‌رنگ، پتاسیم: کیسه قرمز + همان سطل،
 *   مونوآمونیوم فسفات: کیسه و سطل سبز، گروه D: کیسه برچسب آبی + سطل آبی پررنگ.
 * - آنتی‌فوم و دیفومر: دو گالن ۲۰ لیتری؛ عکس اصلی یکی‌درمیان تا شبکه محصولات یکنواخت نباشد.
 * - بقیه (حفاری، فوم بتن) عکس ندارند و جای عکس آیکون دسته می‌آید.
 * محصول نمونه (_em_demo) دست نمی‌خورد.
 *
 * php -c <php.ini سایت Local> dev/seed-product-images.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-product-images.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );

/**
 * شناسه پیوست از روی نام فایل (بدون پسوند؛ وردپرس jpg را webp کرده است).
 */
function em_img( $name ) {
	global $wpdb;
	$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1", '%/' . $wpdb->esc_like( $name ) . '.%' ) );
	if ( ! $id ) {
		exit( "Image not found: $name\n" );
	}
	return $id;
}

/**
 * دسته با نام (فاصله و نیم‌فاصله نادیده).
 */
function em_cat( $name ) {
	$norm = static fn( $s ) => preg_replace( '/[\s\x{200C}\x{00A0}]+/u', '', $s );
	foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $t ) {
		if ( $norm( $t->name ) === $norm( $name ) ) {
			return $t;
		}
	}
	exit( "Category not found: $name\n" );
}

/**
 * محصولات منتشرشده یک دسته و زیردسته‌هایش، بدون محصولات نمونه.
 */
function em_cat_products( $term_id ) {
	return get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => 'ID',
			'order'       => 'ASC',
			'tax_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy'         => 'product_cat',
					'terms'            => $term_id,
					'include_children' => true,
				),
			),
			'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_em_demo',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);
}

$plan = array(); // شناسه محصول ← [عکس اصلی، گالری[]]

// فوم آتش‌نشانی: بشکه‌ها از زاویه کنار
$drums = em_img( 'ementash-drums-pallet-2' );
foreach ( em_cat_products( em_cat( 'فوم آتش نشانی' )->term_id ) as $p ) {
	// Wet Chemical (کلاس K) فوم نیست و در بشکه فوم عرضه نمی‌شود: بدون عکس، آیکون دسته (تصمیم کارفرما)
	$plan[ $p->ID ] = false !== stripos( $p->post_title, 'Wet Chemical' ) ? array( 0, array() ) : array( $drums, array() );
}

// آنتی‌فوم: دو گالن، یکی‌درمیان
$cans = array( em_img( 'ementash-antifoam-p2-cf5a-jerrycan' ), em_img( 'ementash-antifoam-ta-47-jerrycan' ) );
foreach ( em_cat_products( em_cat( 'آنتی فوم و دیفومر (ضد کف)' )->term_id ) as $i => $p ) {
	$plan[ $p->ID ] = array( $cans[ $i % 2 ], array( $cans[ ( $i + 1 ) % 2 ] ) );
}

// پودرها: کد محصول در عنوان ← کیسه، سطل
$powders = array(
	'PO-600A' => array( 'ementash-powder-sodium-bicarbonate-bag', 'ementash-powder-bc-bucket' ),
	'PO-700'  => array( 'ementash-powder-potassium-bicarbonate-bag', 'ementash-powder-bc-bucket' ),
	'PO-800'  => array( 'ementash-powder-map-bag', 'ementash-powder-abc-bucket' ),
	'PO-900B' => array( 'ementash-powder-d-bag', 'ementash-powder-d-bucket' ),
);
foreach ( em_cat_products( em_cat( 'پودر آتشنشانی' )->term_id ) as $p ) {
	foreach ( $powders as $code => $files ) {
		if ( false !== strpos( $p->post_title, $code ) ) {
			$plan[ $p->ID ] = array( em_img( $files[0] ), array( em_img( $files[1] ) ) );
		}
	}
}

// آیکون دسته‌ها (جای عکس محصول یا دسته بی‌عکس)
$icons = array(
	'فوم آتش نشانی'             => 'foam',
	'پودر آتشنشانی'             => 'powder',
	'آنتی فوم و دیفومر (ضد کف)' => 'antifoam',
	'مواد حفاری'                => 'drill',
	'فوم بتن'                   => 'concrete',
);
foreach ( $icons as $name => $icon ) {
	$t = em_cat( $name );
	echo "icon    {$t->name} → $icon\n";
	if ( $confirm ) {
		update_term_meta( $t->term_id, 'ementash_icon', $icon );
	}
}

foreach ( $plan as $id => list( $main, $gallery ) ) {
	echo '#' . $id . ' ' . get_the_title( $id ) . ' → ' . ( $main ? basename( get_attached_file( $main ) ) : '(بدون عکس، آیکون دسته)' ) . ( $gallery ? ' + ' . basename( get_attached_file( $gallery[0] ) ) : '' ) . "\n";
	if ( $confirm ) {
		if ( $main ) {
			set_post_thumbnail( $id, $main );
		} else {
			delete_post_thumbnail( $id );
		}
		update_post_meta( $id, '_product_image_gallery', implode( ',', $gallery ) );
	}
}

if ( $confirm && function_exists( 'wc_delete_product_transients' ) ) {
	wc_delete_product_transients();
}
echo $confirm ? "Done.\n" : "Report only. Run with --confirm to write.\n";
