<?php
/**
 * ساخت برگه‌های صنعت از پست‌تایپ project سایت قدیمی (فقط CLI).
 *
 * - نامک از custom_permalink (آدرس واقعی سایت فعلی)؛ post_name قدیمی این پست‌ها از دموی قالب قبلی
 *   مانده و با عنوان نمی‌خواند، پس به _wp_old_slug اضافه نمی‌شود (ریدایرکت اشتباه می‌ساخت).
 * - پستی که custom_permalink نداشت (/project/صنایع-کاغذ-و-چاپ/) با نامک خودش ساخته می‌شود؛
 *   آدرس قدیمی‌اش باید در افزونه ریدایرکت ثبت شود.
 * - محتوا در سایت فعلی خالی است؛ برگه خالی با قالب «صنعت» ساخته می‌شود. عکس قدیمی منتقل نمی‌شود.
 *
 * php -c <php.ini سایت Local> dev/migrate-industries.php            ← فقط فهرست
 * php -c <php.ini سایت Local> dev/migrate-industries.php --confirm  ← ساخت برگه‌ها
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );
$old     = new mysqli( '127.0.0.1', 'root', 'root', 'ementash_old', (int) ini_get( 'mysqli.default_port' ) );
$old->set_charset( 'utf8mb4' );

$res = $old->query(
	"SELECT p.ID, p.post_title, p.post_name, p.menu_order, p.post_content,
	(SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID AND meta_key='custom_permalink' LIMIT 1) cp
	FROM wp_posts p WHERE p.post_type='project' AND p.post_status='publish' ORDER BY p.ID"
);

echo $confirm ? "ساخت:\n" : "فهرست (بدون --confirm چیزی ساخته نمی‌شود):\n";
$order = 0;
while ( $p = $res->fetch_assoc() ) {
	$order += 10;
	// custom_permalink کدشده (با حروف بزرگ) ذخیره شده؛ نسخه خوانا به وردپرس داده می‌شود تا خودش کد کند.
	$slug   = rawurldecode( null !== $p['cp'] ? trim( $p['cp'], '/' ) : $p['post_name'] );
	$exists = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'meta_key'    => '_em_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => (int) $p['ID'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	$clash  = get_page_by_path( $slug, OBJECT, array( 'page', 'post', 'product' ) );
	printf(
		"  #%d %s → /%s/%s%s\n",
		$p['ID'],
		$p['post_title'],
		$slug,
		null === $p['cp'] ? ' (قبلاً /project/' . $slug . '/؛ ریدایرکت لازم)' : '',
		$exists ? ' | قبلاً ساخته شده #' . $exists[0] : ( $clash ? ' | ! نامک گرفته شده: ' . $clash->post_type . ' #' . $clash->ID : '' )
	);
	if ( ! $confirm || $exists || $clash ) {
		continue;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $p['post_title'],
			'post_name'    => $slug,
			'post_content' => wp_kses_post( $p['post_content'] ),
			'menu_order'   => $order,
			'meta_input'   => array(
				'_wp_page_template' => ementash_industry_template(),
				'_em_old_id'        => (int) $p['ID'],
			),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, '  ! ' . $id->get_error_message() . "\n" );
		continue;
	}
	$path = rawurldecode( wp_parse_url( get_permalink( $id ), PHP_URL_PATH ) );
	echo '    ✓ #' . $id . ' ' . $path . ( trim( $path, '/' ) === $slug ? '' : '  ! آدرس با نامک نمی‌خواند' ) . "\n";
}
