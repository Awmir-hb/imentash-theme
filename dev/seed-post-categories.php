<?php
/**
 * دسته‌بندی مقاله‌های منتقل‌شده (فقط CLI، روی Local).
 * سایت قدیمی دسته نداشت؛ دسته‌ها از روی عنوان هر مقاله انتخاب شده‌اند و از پیشخوان قابل تغییرند.
 * مقاله‌ها با متای _em_old_id (شناسه سایت قدیمی) پیدا می‌شوند، چون شناسه جدید در هر نصب فرق دارد.
 * دسته پیش‌فرض «Uncategorized» به «سایر» تغییر نام می‌دهد (نامکش همان می‌ماند).
 *
 * php -c <php.ini سایت Local> dev/seed-post-categories.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-post-categories.php --confirm  ← اعمال
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );

// نامک ← نام، توضیح، شناسه‌های قدیمی مقاله‌ها
$cats = array(
	'firefighting-foam'          => array(
		'name'  => 'فوم آتش‌نشانی',
		'posts' => array( 1789, 1914, 1948, 2118, 2119, 2120, 3160, 3180, 3227, 3244, 3289, 3291 ),
	),
	'fire-extinguishing-powder'  => array(
		'name'  => 'پودر آتش‌نشانی',
		'posts' => array( 1933, 2110, 2112, 3165, 3211, 3219, 3224, 3225, 3236, 3238, 3239, 3240, 3241, 3242, 3243, 3277 ),
	),
	'fluorine-free-foam'         => array(
		'name'  => 'فوم بدون فلوئور و PFAS',
		'posts' => array( 2043, 2111, 2113, 2121, 2127, 3152, 3174 ),
	),
	'antifoam'                   => array(
		'name'  => 'آنتی‌فوم و ضدکف',
		'posts' => array( 3137, 3213, 3275, 3280, 3284, 3286, 3293, 3295 ),
	),
	'extinguisher-maintenance'   => array(
		'name'  => 'نگهداری و شارژ کپسول',
		'posts' => array( 3147, 3185, 3223, 3226 ),
	),
	'drilling-materials'         => array(
		'name'  => 'مواد حفاری',
		'posts' => array( 3234 ),
	),
);

$default_id = (int) get_option( 'default_category' );
$default    = get_term( $default_id, 'category' );
if ( $default && ! is_wp_error( $default ) && 'سایر' !== $default->name ) {
	printf( "دسته پیش‌فرض: %s ← سایر\n", $default->name );
	if ( $confirm ) {
		wp_update_term( $default_id, 'category', array( 'name' => 'سایر' ) );
	}
}

foreach ( $cats as $slug => $cat ) {
	$term    = get_term_by( 'slug', $slug, 'category' );
	$term_id = $term ? (int) $term->term_id : 0;
	if ( ! $term_id && $confirm ) {
		$r       = wp_insert_term( $cat['name'], 'category', array( 'slug' => $slug ) );
		$term_id = is_wp_error( $r ) ? 0 : (int) $r['term_id'];
	}
	printf( "\n%s (%s)%s\n", $cat['name'], $slug, $term ? '' : ' ← ساخته می‌شود' );

	foreach ( $cat['posts'] as $old_id ) {
		$ids = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $old_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( ! $ids ) {
			printf( "  !! مقاله قدیمی %d پیدا نشد\n", $old_id );
			continue;
		}
		printf( "  #%d %s\n", $ids[0], get_the_title( $ids[0] ) );
		if ( $confirm && $term_id ) {
			wp_set_post_categories( $ids[0], array( $term_id ), false );
		}
	}
}

echo $confirm ? "\nانجام شد.\n" : "\nبدون --confirm چیزی تغییر نکرد.\n";
