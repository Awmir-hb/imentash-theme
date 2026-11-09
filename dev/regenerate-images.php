<?php
/**
 * ساخت دوباره سایزهای تصویر (با فیلتر قالب، سایزها WebP ساخته می‌شوند). فقط CLI.
 * فایل اصلی و سایزهای قدیمی JPEG پاک نمی‌شوند تا لینک‌های داخل نوشته‌ها نشکنند.
 * بدون --confirm فقط فهرست تصویرها چاپ می‌شود.
 *
 * php -c <php.ini سایت Local> dev/regenerate-images.php [--ids=265,266] [--confirm]
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$confirm = in_array( '--confirm', $argv, true );
$ids     = array();
foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, '--ids=' ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', substr( $arg, 6 ) ) ) );
	}
}
if ( ! $ids ) {
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => array( 'image/jpeg', 'image/png' ),
			'post_status'    => 'inherit',
			'numberposts'    => -1,
			'fields'         => 'ids',
		)
	);
}

$done = 0;
foreach ( $ids as $id ) {
	// همیشه از فایل آپلودشده اصلی، نه نسخه WebP ساخته‌شده (فشرده‌سازی دوباره کیفیت را کم می‌کند).
	$file = wp_get_original_image_path( $id );
	if ( ! $file || ! file_exists( $file ) || ! wp_attachment_is_image( $id ) ) {
		echo "skip {$id}\n";
		continue;
	}
	echo $id . ' ' . wp_basename( $file );
	if ( $confirm ) {
		$meta = wp_generate_attachment_metadata( $id, $file );
		if ( $meta ) {
			wp_update_attachment_metadata( $id, $meta );
			++$done;
			echo ' ✓';
		} else {
			echo ' ✗';
		}
	}
	echo "\n";
}
echo $confirm ? "{$done} تصویر ساخته شد.\n" : count( $ids ) . " تصویر. برای اجرا --confirm بزنید.\n";
