<?php
/**
 * حذف داده دمو (فقط CLI). فقط مواردی که نامکشان با em-demo- شروع می‌شود یا متای _em_demo دارند.
 * بدون --confirm فقط فهرست چیزهایی که حذف می‌شوند چاپ می‌شود و چیزی پاک نمی‌شود.
 *
 * php -c <php.ini سایت Local> dev/demo-cleanup.php            ← فقط فهرست
 * php -c <php.ini سایت Local> dev/demo-cleanup.php --confirm  ← حذف و برگرداندن تنظیمات صفحه اول
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );
global $wpdb;

// پست‌ها (همه نوع‌ها، شامل پیوست و محصول)
$post_ids = get_posts(
	array(
		'post_type'      => 'any',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_key'       => '_em_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	)
);
$by_slug  = get_posts(
	array(
		'post_type'      => array( 'post', 'page', 'product', 'attachment' ),
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);
foreach ( $by_slug as $id ) {
	if ( 0 === strpos( (string) get_post_field( 'post_name', $id ), 'em-demo-' ) ) {
		$post_ids[] = $id;
	}
}
$post_ids = array_unique( array_map( 'intval', $post_ids ) );

$terms = array();
foreach ( array( 'category', 'post_tag', 'product_cat' ) as $tax ) {
	foreach ( get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) ) as $t ) {
		if ( 0 === strpos( $t->slug, 'em-demo-' ) || get_term_meta( $t->term_id, '_em_demo', true ) ) {
			$terms[] = $t;
		}
	}
}
$backup = get_option( 'em_demo_backup' );

echo $confirm ? "حذف می‌شود:\n" : "فهرست (بدون --confirm چیزی حذف نمی‌شود):\n";
foreach ( $post_ids as $id ) {
	printf( "  %s #%d %s | %s [%s]\n", get_post_type( $id ), $id, get_post_field( 'post_name', $id ), get_the_title( $id ), get_post_status( $id ) );
}
foreach ( $terms as $t ) {
	printf( "  term %s #%d %s | %s\n", $t->taxonomy, $t->term_id, $t->slug, $t->name );
}
if ( is_array( $backup ) ) {
	printf( "  گزینه em_demo_backup ← برگرداندن show_on_front=%s، page_on_front=%s، page_for_posts=%s\n", $backup['show_on_front'], $backup['page_on_front'], $backup['page_for_posts'] );
}
if ( ! $confirm ) {
	exit( 0 );
}

if ( is_array( $backup ) ) {
	foreach ( $backup as $key => $value ) {
		update_option( $key, $value );
	}
	delete_option( 'em_demo_backup' );
}
foreach ( $post_ids as $id ) {
	if ( 'attachment' === get_post_type( $id ) ) {
		wp_delete_attachment( $id, true );
	} elseif ( 'product' === get_post_type( $id ) && function_exists( 'wc_get_product' ) && wc_get_product( $id ) ) {
		wc_get_product( $id )->delete( true );
	} else {
		wp_delete_post( $id, true );
	}
}
foreach ( $terms as $t ) {
	wp_delete_term( $t->term_id, $t->taxonomy );
}
echo "انجام شد.\n";
