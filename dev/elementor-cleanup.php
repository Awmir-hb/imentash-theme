<?php
/**
 * پاک‌سازی باقی‌مانده‌های المنتورِ غیرفعال از دیتابیس (فقط CLI، فقط وقتی المنتور غیرفعال است).
 * برگه‌های ساخته‌شده با المنتور و متای _elementor_* آن‌ها دست نمی‌خورند (مثل برگه em-demo-elementor-test).
 * بدون --confirm فقط فهرست چیزهایی که حذف می‌شوند چاپ می‌شود.
 *
 * php -c <php.ini سایت Local> dev/elementor-cleanup.php [--confirm]
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( is_plugin_active( 'elementor/elementor.php' ) ) {
	exit( "المنتور فعال است؛ اول غیرفعالش کنید.\n" );
}

$confirm = in_array( '--confirm', $argv, true );
global $wpdb;

// گزینه‌ها و transientهای المنتور (نام‌ها از مقایسه دیتابیس قبل و بعد از نصب).
$options = array_merge(
	$wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'elementor\\_%' OR option_name LIKE '\\_elementor\\_%'" ),
	$wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_%elementor%' OR option_name LIKE '\\_transient\\_timeout\\_%elementor%'" )
);
// کیت پیش‌فرض و هر قالب کتابخانه المنتور
$library = get_posts(
	array(
		'post_type'   => 'elementor_library',
		'post_status' => 'any',
		'numberposts' => -1,
	)
);
$crons   = array();
foreach ( (array) _get_cron_array() as $hooks ) {
	foreach ( array_keys( $hooks ) as $hook ) {
		if ( 0 === strpos( $hook, 'elementor' ) ) {
			$crons[ $hook ] = true;
		}
	}
}
$tables  = $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->prefix . 'e_' ) . '%' ) );
$uploads = trailingslashit( wp_upload_dir()['basedir'] ) . 'elementor';

echo $confirm ? "حذف می‌شود:\n" : "فهرست (بدون --confirm چیزی حذف نمی‌شود):\n";
foreach ( array_unique( $options ) as $o ) {
	echo "  option $o\n";
}
foreach ( $library as $p ) {
	echo "  post #{$p->ID} {$p->post_type} | {$p->post_title} (و متاها و نسخه‌های آن)\n";
}
foreach ( array_keys( $crons ) as $hook ) {
	echo "  cron $hook\n";
}
foreach ( $tables as $t ) {
	echo "  table $t (" . (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$t`" ) . " ردیف)\n"; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}
if ( is_dir( $uploads ) ) {
	echo "  folder $uploads (فایل‌های CSS ساخته‌شده)\n";
}
if ( ! $confirm ) {
	exit( 0 );
}

foreach ( array_unique( $options ) as $o ) {
	delete_option( $o );
}
foreach ( $library as $p ) {
	wp_delete_post( $p->ID, true );
}
foreach ( array_keys( $crons ) as $hook ) {
	wp_clear_scheduled_hook( $hook );
}
foreach ( $tables as $t ) {
	$wpdb->query( "DROP TABLE IF EXISTS `$t`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.SchemaChange
}
if ( is_dir( $uploads ) ) {
	$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $uploads, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
	foreach ( $it as $f ) {
		$f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
	}
	rmdir( $uploads );
}
echo "انجام شد.\n";
