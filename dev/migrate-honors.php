<?php
/**
 * افتخارات گروه صنعتی تاش (em_certificate با نوع honor) از فهرست کارفرما (_source/client-docs/تصاویر سایت/افتخارات.docx)
 * و عکس تندیس‌ها (_source/sorted/03-honors-trophies و بقیه از اسلایدر افتخارات صفحه اصلی سایت قدیمی در
 * _source/old-uploads/2025/05؛ ۲۲ تندیس). فقط CLI، روی Local.
 *
 * - عکس هر مورد با خواندن متن پلاک تندیس پیدا شده؛ موردی که تندیسش پیدا نشد بدون عکس ساخته می‌شود.
 * - افتخارات نمونه قبلی («تندیس واحد نمونه» بدون عکس) به زباله‌دان می‌روند، نه حذف دائمی.
 * - دوباره اجرا شدن تکراری نمی‌سازد (متای _em_source روی پست و پیوست).
 *
 * php -c <php.ini سایت Local> dev/migrate-honors.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/migrate-honors.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php'; // wp_tempnam

$confirm = in_array( '--confirm', $argv, true );
$src     = dirname( __DIR__ ) . '/_source/sorted/03-honors-trophies/';

// افتخارات همه شرکت‌های گروه صنعتی تاش (ایمن تاش، مهرتاش سپاهان، آراد صنعت ماهان و...) می‌آیند؛
// نام شرکت دیگر در «توضیح کوتاه» (subtitle) نوشته می‌شود. ایمن تاش و بدون نام = خالی.
// عنوان، سال، فایل تندیس (خالی = بدون عکس)، شرکت. ترتیب = ترتیب نمایش (بر اساس سال).
$honors = array(
	array( 'تولیدکننده نمونه', '1385', '', '' ),
	array( 'تولیدکننده نمونه', '1386', '1336-038.png', '' ),          // پلاک: واحد نمونه صنعت و معدن استان اصفهان ۱۳۸۶، ایمن تاش
	array( 'کارآفرین نمونه', '1386', '1338-08.png', 'مهرتاش سپاهان' ), // پلاک: سومین جشنواره کارآفرینان برتر استان اصفهان
	array( 'پیشکسوت صنعت استان اصفهان', '1386', '1058-07.webp', '' ),
	array( 'تولیدکننده نمونه', '1390', '1340-022.png', '' ),          // لوح: واحد صنعتی نمونه سال ۱۳۹۰، ایمن تاش
	array( 'پنجمین جشنواره ملی مدیران و تولیدکنندگان جوان', '1390', 'old-uploads/2025/05/09.png', '' ), // آبان ۱۳۹۰
	array( 'واحد نمونه صنعت و معدن استان اصفهان', '1391', '1344-034.png', '' ), // پلاک: ایمن تاش، تیر ۱۳۹۱
	array( 'واحد تولیدکننده نمونه کشوری', '1391', 'old-uploads/2025/05/010.png', '' ), // ستاد روز ملی صنعت و معدن، وزارت صمت، تیر ۱۳۹۱
	array( 'واحد تولیدکننده نمونه صنعتی', '1392', '', '' ),
	array( 'واحد تولیدکننده نمونه استاندارد', '1392', 'old-uploads/2025/05/024.png', '' ), // اداره کل استاندارد اصفهان، مهر ۱۳۹۲
	array( 'واحد برگزیده ملی جشنواره قهرمانان صنعت', '1392', '1349-037.png', 'مهرتاش سپاهان' ), // پلاک: آذر ۱۳۹۲
	array( 'نشان زرین کیفیت و مرغوبیت تولیدات', '1393', '1351-039.png', '' ),     // پلاک: ایمن تاش
	array( 'نشان و استاندارد بین‌المللی FCC از آلمان', '1393', '', '' ),
	array( 'واحد منتخب صنعتی استان اصفهان', '1395', '1361-05.png', 'آراد صنعت ماهان' ), // بیست‌ویکمین بزرگداشت روز صنعت و معدن، تیر ۱۳۹۵
	array( 'واحد نمونه استانی استاندارد استان اصفهان', '1395', '1362-012.png', '' ),    // اداره کل استاندارد، مهر ۱۳۹۵
	array( 'لوح واحد شایان تقدیر استاندارد استان اصفهان', '1395', 'old-uploads/2025/05/026.png', '' ), // روز جهانی استاندارد، مهر ۱۳۹۵
	array( 'برگزیده جشنواره شرکت‌های دانش‌بنیان کشور', '1395', '1360-04.png', 'مهرتاش سپاهان' ), // آبان ۱۳۹۵
	array( 'افتخار ملی، چهاردهمین جشنواره تولید ملی', '1395', '1355-027.png', '' ),     // اسفند ۱۳۹۵
	array( 'کارآفرین برتر و کارآمد، چهاردهمین جشنواره تولید ملی', '1395', 'old-uploads/2025/05/018.png', 'مهرتاش سپاهان' ), // لوح، اسفند ۱۳۹۵
	array( 'افتخار ملی، پانزدهمین جشنواره تولید ملی', '1396', '1364-032.png', '' ),     // بهمن ۱۳۹۶
	array( 'واحد نمونه صنعت استان اصفهان', '1397', '1366-017.png', 'مهرتاش سپاهان' ),
	array( 'افتخار ملی، شانزدهمین جشنواره تولید ملی', '1397', 'old-uploads/2025/05/030.png', '' ), // بهمن ۱۳۹۷
	array( 'نشان کارفرمای سرآمد', '1401', '1379-029.png', '' ),
	array( 'نشان زرین سازمان پیشرو در صنعت نفت و صنایع وابسته', '', 'old-uploads/2025/05/02.png', '' ), // همایش زنجیره موفقیت سازمانی؛ سال روی تندیس نیست
	array( 'تقدیر انجمن مدیران صنایع استان اصفهان', '', '1367-014.png', '' ),          // سال روی تندیس نیست
);

/**
 * حاشیه شفاف دور تندیس بریده می‌شود تا در ویترین بزرگ و روی پایه دیده شود (۳٪ حاشیه می‌ماند).
 * خروجی PNG موقت؛ اگر تصویر شفافیت نداشت یا GD نبود، همان فایل اصلی.
 */
function em_honor_trim( $file ) {
	if ( ! function_exists( 'imagecreatefromstring' ) ) {
		return $file;
	}
	$im = imagecreatefromstring( (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! $im ) {
		return $file;
	}
	$w = imagesx( $im );
	$h = imagesy( $im );
	$x0 = $w;
	$y0 = $h;
	$x1 = 0;
	$y1 = 0;
	for ( $y = 0; $y < $h; $y += 2 ) {
		for ( $x = 0; $x < $w; $x += 2 ) {
			// alpha در GD: ۰ مات، ۱۲۷ کاملاً شفاف
			if ( ( ( imagecolorat( $im, $x, $y ) >> 24 ) & 0x7F ) < 100 ) {
				$x0 = min( $x0, $x );
				$y0 = min( $y0, $y );
				$x1 = max( $x1, $x );
				$y1 = max( $y1, $y );
			}
		}
	}
	if ( $x1 <= $x0 || $y1 <= $y0 || ( $x1 - $x0 > $w * .97 && $y1 - $y0 > $h * .97 ) ) {
		return $file;
	}
	$pad = (int) round( max( $x1 - $x0, $y1 - $y0 ) * .03 );
	$x0  = max( 0, $x0 - $pad );
	$y0  = max( 0, $y0 - $pad );
	$cw  = min( $w, $x1 + $pad ) - $x0;
	$ch  = min( $h, $y1 + $pad ) - $y0;
	$out = imagecreatetruecolor( $cw, $ch );
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
	imagecopy( $out, $im, 0, 0, $x0, $y0, $cw, $ch );
	$tmp = wp_tempnam( 'honor' ) . '.png';
	imagepng( $out, $tmp, 9 );
	return $tmp;
}

/**
 * پیوست یک فایل محلی (بریده‌شده)؛ اگر قبلاً وارد شده همان را برمی‌گرداند.
 */
function em_honor_attach( $file, $alt ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => 'trim:' . basename( $file ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$name   = 'honor-' . pathinfo( $file, PATHINFO_FILENAME ) . '.png';
	$upload = wp_upload_bits( $name, null, (string) file_get_contents( em_honor_trim( $file ) ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
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
	update_post_meta( $id, '_em_source', 'trim:' . basename( $file ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

// نمونه‌های قبلی: افتخارهایی که از این اسکریپت نیامده‌اند.
$old = array_filter(
	ementash_get_certificates( 'honor' ),
	static fn( $p ) => '' === (string) get_post_meta( $p->ID, '_em_source', true )
);
foreach ( $old as $p ) {
	echo 'trash   #' . $p->ID . ' ' . $p->post_title . "\n";
	if ( $confirm ) {
		wp_trash_post( $p->ID );
	}
}

foreach ( $honors as $i => list( $title, $year, $file, $company ) ) {
	$key  = 'honor-' . $year . '-' . md5( $title );
	$have = get_posts(
		array(
			'post_type'   => 'em_certificate',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	echo ( $have ? 'update  ' : 'create  ' ) . $year . ' ' . $title . ( $company ? ' (' . $company . ')' : '' ) . ( $file ? ' [' . $file . ']' : ' [بدون عکس]' ) . "\n";
	if ( ! $confirm ) {
		continue;
	}
	$id = wp_insert_post(
		array(
			'ID'          => $have ? (int) $have[0] : 0,
			'post_type'   => 'em_certificate',
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
	update_post_meta( $id, '_ementash_kind', 'honor' );
	update_post_meta( $id, '_ementash_year', $year );
	update_post_meta( $id, '_ementash_subtitle', $company );
	if ( $file ) {
		// نام تنها = پوشه 03-honors-trophies؛ با مسیر = نسبت به _source (عکس‌هایی که در پوشه مرتب‌شده نبودند)
		$path = false !== strpos( $file, '/' ) ? dirname( __DIR__ ) . '/_source/' . $file : $src . $file;
		set_post_thumbnail( $id, em_honor_attach( $path, trim( $title . ' ' . $year ) ) );
	}
}

echo $confirm ? "Done.\n" : "Report only. Run with --confirm to write.\n";
