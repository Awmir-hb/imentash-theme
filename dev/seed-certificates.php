<?php
/**
 * گواهینامه‌های برگه درباره ما (em_certificate با نوع «گواهینامه»)، فقط CLI.
 *
 * - فهرست و ترتیب از «گواهینامه ها.docx» کارفرما (_source/client-docs/تصاویر سایت/)؛ عنوان‌ها از روی خود اسکن‌ها
 *   (توضیح‌های برگه گواهینامه‌های سایت قدیمی جابه‌جا بودند).
 * - اسکن‌ها از _source/sorted/04-certificates-scans (عدد اول نام فایل = شناسه پیوست قدیمی).
 *   نسخه‌های قدیمی‌تر 1386، 1395 و 1401 (همان پروانه‌های 2038، 2037 و 2039) وارد نمی‌شوند.
 * - ISO: فعلاً نشان ISO به‌جای عکس + لینک PDF؛ عکس اصلی را کارفرما از PDF می‌فرستد.
 * - SGS، SIPIEM، فهرست بلند وزارت نفت و دانش‌بنیان اسکن ندارند؛ فعلاً با نشان (تصمیم کارفرما).
 *   EN1568 و NFPA در فهرست کارفرما نیستند و وارد نمی‌شوند.
 * - ویترین/نشان، گروه کارت ویترین و دسته شمارنده سربرگ هم اینجا تعیین می‌شود (template-parts/about/credentials.php).
 * - دوباره اجرا شدن تکراری نمی‌سازد (_em_source)؛ عنوان، ترتیب و متای موارد موجود به‌روز می‌شود.
 *
 * php -c <php.ini سایت Local> dev/seed-certificates.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-certificates.php --confirm  ← نوشتن
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
$src     = dirname( __DIR__ ) . '/_source';
$scans   = $src . '/sorted/04-certificates-scans';
$docs    = $src . '/client-docs/تصاویر سایت';
$new     = $src . '/client-docs/new';

/**
 * اسکن با شناسه قدیمی (عدد اول نام فایل).
 */
function em_cert_scan( $dir, $old ) {
	$found = glob( $dir . '/' . $old . '-*' );
	return $found ? $found[0] : '';
}

// نشان‌ها عنوان کوتاه دارند (روی نشان)؛ توضیح کوتاه خط دوم آن است.
// [کلید، عنوان، توضیح کوتاه، عکس، PDF، نمایش (showcase/badge)، گروه ویترین، دسته شمارنده]
$items = array(
	array( 'std-3778-1', 'گواهینامه استاندارد ملی فوم ۱-۳۷۷۸', 'پروانه کاربرد علامت استاندارد تشویقی', em_cert_scan( $scans, 2038 ), '', 'showcase', 'استاندارد ملی فوم ۳۷۷۸', 'national' ),
	array( 'std-3778-3', 'گواهینامه استاندارد ملی فوم ۳-۳۷۷۸', 'پروانه کاربرد علامت استاندارد تشویقی', em_cert_scan( $scans, 2037 ), '', 'showcase', 'استاندارد ملی فوم ۳۷۷۸', 'national' ),
	array( 'std-3434', 'گواهینامه استاندارد ملی پودر ۳۴۳۴', 'پروانه کاربرد علامت استاندارد اجباری', em_cert_scan( $scans, 2039 ), '', 'showcase', 'استاندارد ملی پودر ۳۴۳۴', 'national' ),
	array( 'naci', 'گواهینامه تأیید صلاحیت آزمایشگاه', 'مرکز ملی تأیید صلاحیت ایران (NACI)', em_cert_scan( $scans, 1412 ), '', 'showcase', 'صلاحیت آزمایشگاه', 'national' ),
	array( 'tuv-foam-chemical', 'گواهینامه تأیید نوع TÜV NORD آلمان', 'فوم‌های شیمیایی AFFF، مقاوم الکلی و پرتوسعه (ISO 7203)', em_cert_scan( $scans, 1413 ), '', 'showcase', 'TÜV NORD آلمان', 'international' ),
	array( 'tuv-foam-protein', 'گواهینامه تأیید نوع TÜV NORD آلمان', 'فوم‌های پروتئینی و فلوروپروتئینی (ISO 7203)', em_cert_scan( $scans, 1414 ), '', 'showcase', 'TÜV NORD آلمان', 'international' ),
	array( 'tuv-powder-sodium', 'گواهینامه تأیید نوع TÜV NORD آلمان', 'پودر بی‌کربنات سدیم (EN 615)', em_cert_scan( $scans, 1415 ), '', 'showcase', 'TÜV NORD آلمان', 'international' ),
	array( 'tuv-powder-potassium', 'گواهینامه تأیید نوع TÜV NORD آلمان', 'پودر بی‌کربنات پتاسیم (EN 615)', em_cert_scan( $scans, 1416 ), '', 'showcase', 'TÜV NORD آلمان', 'international' ),
	array( 'sgs', 'SGS', 'گواهینامه استاندارد بین‌المللی', $docs . '/SGS.png', '', 'badge', '', 'international' ),
	array( 'sipiem', 'عضو SIPIEM', 'انجمن سازندگان تجهیزات صنعت نفت ایران', $src . '/old-uploads/2025/01/image1.png', '', 'badge', '', 'membership' ),
	array( 'avl', 'فهرست بلند وزارت نفت', 'عضویت در فهرست منابع (AVL)', $docs . '/فهرست-بلند-وزارت-نفت.jpg', '', 'badge', '', 'membership' ),
	array( 'iso-14001', 'ISO 14001:2015', 'سیستم مدیریت محیط زیست', $docs . '/ایزو14001.jpg', $new . '/14001.pdf', 'badge', '', 'international' ),
	array( 'iso-9001', 'ISO 9001:2015', 'سیستم مدیریت کیفیت', $docs . '/ایزو9001.png', $new . '/9001.pdf', 'badge', '', 'international' ),
	array( 'iso-45001', 'ISO 45001:2018', 'مدیریت ایمنی و بهداشت حرفه‌ای', $docs . '/ایزو18001.jpg', $new . '/45001.pdf', 'badge', '', 'international' ),
	array( 'hafsa', 'عضویت در شبکه ملی آزمایشگاه‌های حفاظت فنی', 'تمدید عضویت آزمایشگاه ایمن تاش (حفصا)', em_cert_scan( $scans, 1410 ), '', 'showcase', 'صلاحیت آزمایشگاه', 'membership' ),
	array( 'knowledge-based', 'شرکت دانش‌بنیان', 'تأییدیه مرکز شرکت‌های دانش‌بنیان', $docs . '/دانش بنیان.png', '', 'badge', '', 'national' ),
	array( 'mcls-foam', 'تأییدیه ایمنی کف‌های خاموش‌کننده آتش', 'وزارت تعاون، کار و رفاه اجتماعی', em_cert_scan( $scans, 1406 ), '', 'showcase', 'تأییدیه ایمنی وزارت کار', 'national' ),
	array( 'mcls-powder', 'تأییدیه ایمنی پودرهای خاموش‌کننده آتش', 'وزارت تعاون، کار و رفاه اجتماعی', em_cert_scan( $scans, 1405 ), '', 'showcase', 'تأییدیه ایمنی وزارت کار', 'national' ),
);

foreach ( $items as $item ) {
	foreach ( array( $item[3], $item[4] ) as $f ) {
		if ( '' !== $f && ! is_readable( $f ) ) {
			exit( "فایل پیدا نشد: $f\n" );
		}
	}
	if ( '' === $item[3] ) {
		exit( "اسکن پیدا نشد: {$item[0]}\n" );
	}
}

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
wp_set_current_user( $admins[0]->ID );

/**
 * اولین پست یا پیوست با این _em_source.
 */
function em_cert_find( $type, $source ) {
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

/**
 * پیوست فایل محلی (با _em_source)؛ اگر قبلاً وارد شده همان.
 */
function em_cert_attach( $file, $name, $title, $source, $parent, $alt = '' ) {
	$id = em_cert_find( 'attachment', $source );
	if ( $id ) {
		return $id;
	}
	$upload = wp_upload_bits( $name, null, (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		exit( 'Upload failed: ' . $upload['error'] . "\n" );
	}
	$id = (int) wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file'],
		$parent
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_em_source', $source );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
}

echo $confirm ? "نوشتن:\n" : "گزارش (بدون --confirm چیزی نوشته نمی‌شود):\n";
foreach ( $items as $order => $item ) {
	list( $key, $title, $sub, $image, $pdf, $display, $group, $category ) = $item;
	$source = 'cert:' . $key;
	$exists = em_cert_find( 'em_certificate', $source );
	printf(
		"  %2d. %s%s ← %s%s%s\n",
		$order + 1,
		$title,
		$sub ? " ({$sub})" : '',
		basename( $image ),
		$pdf ? ' + ' . basename( $pdf ) : '',
		$exists ? " [موجود #{$exists}]" : ''
	);
	if ( ! $confirm ) {
		continue;
	}
	$args = array(
		'post_type'   => 'em_certificate',
		'post_status' => 'publish',
		'post_title'  => $title,
		'menu_order'  => $order,
	);
	if ( $exists ) {
		$args['ID'] = $exists;
	}
	$post_id = (int) wp_insert_post( $args );
	if ( ! $post_id ) {
		exit( "ساخت پست ناموفق: $title\n" );
	}
	update_post_meta( $post_id, '_em_source', $source );
	update_post_meta( $post_id, '_ementash_kind', 'certificate' );
	update_post_meta( $post_id, '_ementash_subtitle', $sub );
	update_post_meta( $post_id, '_ementash_display', $display );
	update_post_meta( $post_id, '_ementash_group', $group );
	update_post_meta( $post_id, '_ementash_category', $category );

	$alt = trim( $title . ( $sub ? ' - ' . $sub : '' ) );
	$ext = strtolower( pathinfo( $image, PATHINFO_EXTENSION ) );
	set_post_thumbnail( $post_id, em_cert_attach( $image, 'cert-' . $key . '.' . $ext, $alt, $source . ':image', $post_id, $alt ) );
	if ( $pdf ) {
		update_post_meta( $post_id, '_ementash_pdf', em_cert_attach( $pdf, 'certificate-' . $key . '.pdf', $title, $source . ':pdf', $post_id ) );
	}
}
echo $confirm ? "انجام شد.\n" : '';
