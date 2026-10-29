<?php
/**
 * محتوای برگه درباره ما برای طرح «داستان کارخانه» (فقط CLI، روی Local).
 *
 * - سه عکس از _source/sorted/05-factory-company به کتابخانه می‌رود: ساختمان کارخانه (تصویر شاخص = سربرگ)،
 *   عکس جمعی کارکنان (کنار متن شرکت؛ همان عکس بخش تاریخچه سایت قدیمی) و پژوهشگاه (تحقیق و توسعه).
 * - اگر محتوای برگه خالی باشد، بخش «تاریخچه» برگه about-us سایت قدیمی (ementash_old) به آن منتقل می‌شود.
 * - نقل‌قول ساخته نمی‌شود؛ متن و گوینده‌اش را کارفرما در متاباکس وارد می‌کند.
 * - دوباره اجرا شدن عکس تکراری نمی‌سازد (متای _em_source روی پیوست).
 *
 * php -c <php.ini سایت Local> dev/seed-about.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-about.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once __DIR__ . '/migrate-lib.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$confirm = in_array( '--confirm', $argv, true );
$page_id = ementash_page_id( 'about' );
if ( ! $page_id ) {
	exit( "About page not found.\n" );
}

$src    = dirname( __DIR__ ) . '/_source/sorted/05-factory-company/';
$images = array(
	'hero'  => '3055-IMG-20251222-WA0000-copy-6.webp',
	'story' => '799-SAVE_20240508_243706.jpg',
	'rd'    => '3099-R-and-D.webp',
);

/**
 * پیوست یک فایل محلی؛ اگر قبلاً وارد شده همان را برمی‌گرداند.
 */
function em_seed_attach( $file, $parent ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => basename( $file ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$upload = wp_upload_bits( basename( $file ), null, (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		exit( 'Upload failed: ' . $upload['error'] . "\n" );
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => pathinfo( $file, PATHINFO_FILENAME ),
			'post_status'    => 'inherit',
		),
		$upload['file'],
		$parent
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_em_source', basename( $file ) );
	return (int) $id;
}

// متن تاریخچه: از تیتر «تاریخچه» تا تیتر بعدی، فقط پاراگراف‌ها.
$story = '';
if ( '' === trim( (string) get_post_field( 'post_content', $page_id ) ) ) {
	$old = new mysqli( '127.0.0.1', 'root', 'root', 'ementash_old', (int) ini_get( 'mysqli.default_port' ) );
	$old->set_charset( 'utf8mb4' );
	$raw = (string) $old->query( "SELECT post_content FROM wp_posts WHERE post_type='page' AND post_name='about-us' LIMIT 1" )->fetch_row()[0];
	if ( preg_match( '#<h3>(تاریخچه[^<]*)</h3>(.*?)<h[1-6][ >]#su', $raw, $m ) && preg_match_all( '#<p[^>]*>.*?</p>#su', $m[2], $ps ) ) {
		$body  = em_clean_html( implode( "\n", $ps[0] ), static fn() => '' );
		$story = '<h2>' . esc_html( trim( $m[1] ) ) . "</h2>\n" . $body;
	}
}

printf( "About page: %d\n", $page_id );
foreach ( $images as $role => $file ) {
	printf( "  %-5s %s%s\n", $role, $file, is_readable( $src . $file ) ? '' : '  (MISSING)' );
}
printf( "  story text: %s\n", '' !== $story ? mb_strlen( wp_strip_all_tags( $story ) ) . ' chars from old site' : 'kept (page already has content or not found)' );

if ( ! $confirm ) {
	exit( "Dry run. Add --confirm to write.\n" );
}

$ids = array();
foreach ( $images as $role => $file ) {
	$ids[ $role ] = em_seed_attach( $src . $file, $page_id );
}
set_post_thumbnail( $page_id, $ids['hero'] );
update_post_meta( $page_id, '_ementash_about_story_image', $ids['story'] );
update_post_meta( $page_id, '_ementash_about_rd_image', $ids['rd'] );
if ( '' !== $story ) {
	wp_update_post(
		array(
			'ID'           => $page_id,
			'post_content' => $story,
		)
	);
}
echo "Done.\n";
