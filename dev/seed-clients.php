<?php
/**
 * ورود لوگوی مشتریان از _source/sorted/02-clients-logos به پست‌تایپ em_client (فقط CLI).
 *
 * - ترتیب همان کاروسل صفحه اصلی سایت قدیمی (شناسه پیوست قدیمی = عدد اول نام فایل) ← menu_order.
 *   پیوست قدیمی 753 (انجمن سازندگان تجهیزات صنعت نفت) عضویت است نه مشتری؛ وارد نمی‌شود.
 * - نام از نام فایل (بدون عدد اول، خط تیره ← فاصله)؛ به‌جز فولاد که نام فایلش نام شرکت نیست.
 * - حاشیه خالی دور لوگو بریده می‌شود تا در کادر ۱۱۲ پیکسلی نوار درشت دیده شود؛ زمینه رنگی (JPG فولاد) شفاف می‌شود.
 * - دوباره اجرا شدن تکراری نمی‌سازد (متای _em_source روی پست و پیوست).
 *
 * php -c <php.ini سایت Local> dev/seed-clients.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-clients.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$confirm = in_array( '--confirm', $argv, true );
$dir     = dirname( __DIR__ ) . '/_source/sorted/02-clients-logos';

// نام فایل‌هایی که نام شرکت نیستند.
$names = array(
	971 => 'شرکت ملی فولاد ایران',
);

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
wp_set_current_user( $admins[0]->ID );

/**
 * زمینه (شفاف یا هم‌رنگ گوشه) شفاف و حاشیه خالی بریده می‌شود؛ ۴٪ حاشیه می‌ماند. خروجی PNG موقت.
 */
function em_client_trim( $file ) {
	$im = imagecreatefromstring( (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! $im ) {
		return $file;
	}
	imagepalettetotruecolor( $im );
	$w      = imagesx( $im );
	$h      = imagesy( $im );
	$corner = imagecolorat( $im, 1, 1 );
	$solid  = ( ( $corner >> 24 ) & 0x7F ) < 100; // گوشه مات = زمینه رنگی
	$bg     = array( ( $corner >> 16 ) & 255, ( $corner >> 8 ) & 255, $corner & 255 );

	$out = imagecreatetruecolor( $w, $h );
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
	$clear = imagecolorallocatealpha( $out, 255, 255, 255, 127 );
	$x0    = $w;
	$y0    = $h;
	$x1    = 0;
	$y1    = 0;
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x++ ) {
			$c     = imagecolorat( $im, $x, $y );
			$alpha = ( $c >> 24 ) & 0x7F;
			$empty = $alpha >= 110;
			if ( $solid && ! $empty ) {
				$d     = abs( ( ( $c >> 16 ) & 255 ) - $bg[0] ) + abs( ( ( $c >> 8 ) & 255 ) - $bg[1] ) + abs( ( $c & 255 ) - $bg[2] );
				$empty = $d < 30;
			}
			if ( $empty ) {
				imagesetpixel( $out, $x, $y, $clear );
				continue;
			}
			imagesetpixel( $out, $x, $y, $c );
			$x0 = min( $x0, $x );
			$y0 = min( $y0, $y );
			$x1 = max( $x1, $x );
			$y1 = max( $y1, $y );
		}
	}
	if ( $x1 <= $x0 || $y1 <= $y0 ) {
		return $file;
	}
	$pad  = (int) round( max( $x1 - $x0, $y1 - $y0 ) * .04 );
	$x0   = max( 0, $x0 - $pad );
	$y0   = max( 0, $y0 - $pad );
	$crop = imagecrop(
		$out,
		array(
			'x'      => $x0,
			'y'      => $y0,
			'width'  => min( $w, $x1 + $pad + 1 ) - $x0,
			'height' => min( $h, $y1 + $pad + 1 ) - $y0,
		)
	);
	imagealphablending( $crop, false );
	imagesavealpha( $crop, true );
	$tmp = wp_tempnam( 'client' ) . '.png';
	imagepng( $crop, $tmp, 9 );
	return $tmp;
}

/**
 * اولین پست یا پیوست با این _em_source.
 */
function em_client_find( $type, $source ) {
	$found = get_posts(
		array(
			'post_type'   => $type,
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $source, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $found ? (int) $found[0] : 0;
}

$files = glob( $dir . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE );
$list  = array();
foreach ( $files as $file ) {
	$base = pathinfo( $file, PATHINFO_FILENAME );
	if ( ! preg_match( '/^(\d+)-(.+)$/u', $base, $m ) ) {
		continue;
	}
	$old          = (int) $m[1];
	$list[ $old ] = array(
		'file' => $file,
		'name' => isset( $names[ $old ] ) ? $names[ $old ] : trim( str_replace( '-', ' ', $m[2] ) ),
	);
}
ksort( $list );

echo $confirm ? "نوشتن:\n" : "گزارش (بدون --confirm چیزی نوشته نمی‌شود):\n";
$order = 0;
foreach ( $list as $old => $item ) {
	$source = 'old-client:' . $old;
	$exists = em_client_find( 'em_client', $source );
	printf( "  %2d. %s ← %s%s\n", $order + 1, $item['name'], basename( $item['file'] ), $exists ? " (موجود #{$exists}، به‌روزرسانی ترتیب)" : '' );
	if ( $confirm ) {
		$post_id = $exists ? $exists : (int) wp_insert_post(
			array(
				'post_type'   => 'em_client',
				'post_status' => 'publish',
				'post_title'  => $item['name'],
				'menu_order'  => $order,
			),
			true
		);
		if ( ! $post_id ) {
			exit( "ساخت پست ناموفق: {$item['name']}\n" );
		}
		if ( $exists ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'menu_order' => $order,
				)
			);
		}
		update_post_meta( $post_id, '_em_source', $source );

		$att = em_client_find( 'attachment', $source );
		if ( ! $att ) {
			$slug   = 'client-' . $old . '.png';
			$upload = wp_upload_bits( $slug, null, (string) file_get_contents( em_client_trim( $item['file'] ) ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( ! empty( $upload['error'] ) ) {
				exit( 'Upload failed: ' . $upload['error'] . "\n" );
			}
			$att = (int) wp_insert_attachment(
				array(
					'post_mime_type' => 'image/png',
					'post_title'     => 'لوگو ' . $item['name'],
					'post_status'    => 'inherit',
				),
				$upload['file'],
				$post_id
			);
			wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $upload['file'] ) );
			update_post_meta( $att, '_em_source', $source );
			update_post_meta( $att, '_wp_attachment_image_alt', 'لوگو ' . $item['name'] );
		}
		set_post_thumbnail( $post_id, $att );
	}
	++$order;
}
echo $confirm ? "انجام شد.\n" : '';
