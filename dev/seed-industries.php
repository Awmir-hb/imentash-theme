<?php
/**
 * کارت‌های صنایع صفحه اصلی (em_industry) از ۹ صنعت سایت قدیمی (پست‌تایپ project). فقط CLI، روی Local.
 *
 * - هر کارت: عنوان قدیمی، تصویر شاخص قدیمی (موقت، تا عکاسی جدید) و لینک به برگه صنعت هم‌نام
 *   (برگه‌ها را dev/migrate-industries.php ساخته است). همان عکس تصویر شاخص برگه صنعت هم می‌شود.
 * - صنایع در سایت قدیمی متن نداشتند؛ متن برگه‌ها دست نمی‌خورد.
 * - کارت‌های نمونه قبلی (بدون _em_source) به زباله‌دان می‌روند. دوباره اجرا شدن تکراری نمی‌سازد.
 *
 * php -c <php.ini سایت Local> dev/seed-industries.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-industries.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$confirm = in_array( '--confirm', $argv, true );
$uploads = dirname( __DIR__ ) . '/_source/old-uploads/';
$old     = new mysqli( '127.0.0.1', 'root', 'root', 'ementash_old', (int) ini_get( 'mysqli.default_port' ) );
$old->set_charset( 'utf8mb4' );

// ترتیب نمایش: صنایع اصلی مشتریان اول.
$order = array(
	'صنایع نفت، گاز و پتروشیمی',
	'صنایع فولاد، آهن و مس',
	'صنایع نیروگاهی',
	'سازمان های آتش نشانی',
	'سازمان هواپیمایی کشوری و فرودگاه ها',
	'صنایع و سازمان های نظامی',
	'صنایع نساجی',
	'صنایع غذایی',
	'صنایع کاغذ و چاپ',
);

$res = $old->query(
	"SELECT p.post_title, f.meta_value file
	FROM wp_posts p
	LEFT JOIN wp_postmeta t ON t.post_id=p.ID AND t.meta_key='_thumbnail_id'
	LEFT JOIN wp_postmeta f ON f.post_id=t.meta_value AND f.meta_key='_wp_attached_file'
	WHERE p.post_type='project' AND p.post_status='publish'"
);
$items = array();
while ( $r = $res->fetch_assoc() ) {
	// بعضی عنوان‌های قدیمی فاصله نشکن (U+00A0) دارند
	$items[ trim( preg_replace( '/[\s\x{00A0}]+/u', ' ', $r['post_title'] ) ) ] = (string) $r['file'];
}

/**
 * پیوست یک فایل محلی؛ اگر قبلاً وارد شده همان را برمی‌گرداند.
 */
function em_ind_attach( $file, $alt ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => 'industry:' . basename( $file ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$upload = wp_upload_bits( 'industry-' . basename( $file ), null, (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		exit( 'Upload failed: ' . $upload['error'] . "\n" );
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $alt,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_em_source', 'industry:' . basename( $file ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

// کارت‌های نمونه
foreach ( ementash_get_items( 'em_industry' ) as $p ) {
	if ( '' === (string) get_post_meta( $p->ID, '_em_source', true ) ) {
		echo 'trash   #' . $p->ID . ' ' . $p->post_title . "\n";
		if ( $confirm ) {
			wp_trash_post( $p->ID );
		}
	}
}

foreach ( $order as $i => $title ) {
	if ( ! isset( $items[ $title ] ) ) {
		echo "missing in old site: $title\n";
		continue;
	}
	$page = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'title'       => $title,
			'numberposts' => 1,
		)
	);
	$file = $uploads . $items[ $title ];
	$key  = 'industry-' . md5( $title );
	$have = get_posts(
		array(
			'post_type'   => 'em_industry',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	echo ( $have ? 'update  ' : 'create  ' ) . $title
		. ( $page ? ' → برگه #' . $page[0]->ID : ' → [برگه پیدا نشد]' )
		. ( is_readable( $file ) ? ' [' . basename( $file ) . ']' : ' [عکس پیدا نشد]' ) . "\n";
	if ( ! $confirm ) {
		continue;
	}
	$id = wp_insert_post(
		array(
			'ID'          => $have ? (int) $have[0] : 0,
			'post_type'   => 'em_industry',
			'post_status' => 'publish',
			'post_title'  => $title,
			'menu_order'  => $i,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		exit( $id->get_error_message() . "\n" );
	}
	update_post_meta( $id, '_em_source', $key );
	update_post_meta( $id, '_ementash_link', $page ? get_permalink( $page[0] ) : '' );
	if ( is_readable( $file ) ) {
		$img = em_ind_attach( $file, $title );
		set_post_thumbnail( $id, $img );
		if ( $page && ! has_post_thumbnail( $page[0] ) ) {
			set_post_thumbnail( $page[0]->ID, $img );
		}
	}
}

echo $confirm ? "Done.\n" : "Report only. Run with --confirm to write.\n";
