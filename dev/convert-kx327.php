<?php
/**
 * تبدیل محصول نمونه «رطوبت‌دهنده سریع» (KX-327) به محصول واقعی، از روی «کاتالوگ وتینگ ایجنت.pdf» کارفرما (فقط CLI).
 *
 * - سایت قدیمی این محصول را نداشت؛ نامک جدید rapid-wetting-agent-kx-327 (تصمیم کارفرما).
 * - دسته واقعی «کمک‌نساجی» (textile-auxiliaries) ساخته می‌شود؛ دسته نمونه با demo-cleanup پاک می‌شود.
 * - عکس موقت: بریده عکس داخل کاتالوگ (_source/derived/kx-327-catalog.jpg) تا عکاسی جدید.
 * - PDF کاتالوگ در Customizer ← صفحه اصلی: کمک‌نساجی ← فایل کاتالوگ.
 * - متای _em_demo برداشته می‌شود تا demo-cleanup آن را پاک نکند. دوباره اجرا شدن تکراری نمی‌سازد (_em_source).
 *
 * php -c <php.ini سایت Local> dev/convert-kx327.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/convert-kx327.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$confirm = in_array( '--confirm', $argv, true );
$src_dir = dirname( __DIR__ ) . '/_source';

$product_id = 133;
$product    = wc_get_product( $product_id );
if ( ! $product || 'KX-327' !== $product->get_sku() ) {
	exit( "محصول 133 با کد KX-327 پیدا نشد.\n" );
}

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
wp_set_current_user( $admins[0]->ID );

$title = 'وتینگ ایجنت تخصصی صنایع نساجی'; // نام نهایی کارفرما؛ دسته: dev/seed-specialty-cats.php
$slug  = 'rapid-wetting-agent-kx-327';

$intro = '<p>محصول KX-327 یک افزودنی تخصصی و عملکردی ویژه صنایع نساجی است که مطابق با استانداردهای این حوزه، با کاهش کشش سطحی و بهبود دینامیک ترشوندگی، سرعت خیس‌شوندگی الیاف و پارچه را به‌طور قابل توجهی افزایش می‌دهد. این محصول راهکاری مؤثر برای پخش سریع، یکنواخت و نفوذ عمیق‌تر محلول در ساختار الیاف فراهم می‌کند و در نهایت کیفیت و یکنواختی فرآیندهای رنگرزی، سفیدگری و آماده‌سازی را بهبود می‌بخشد.</p>
<h3>مزایا و ویژگی‌ها</h3>
<ul>
<li>قدرت ترکنندگی بسیار بالای الیاف</li>
<li>نفوذ سریع و یکنواخت محلول در ساختار انواع الیاف طبیعی و مصنوعی</li>
<li>ارتقای عملکرد فرمولاسیون، افزایش راندمان و کاهش زمان فرآیند</li>
<li>فرمولاسیون تخصصی صنایع نساجی و سازگاری با مواد کمک‌نساجی</li>
<li>امکان سفارشی‌سازی محصول بر اساس فرمول خط تولید مشتری</li>
<li>بهبود کیفیت محصول نهایی</li>
</ul>
<h3>مقدار مصرف معمول</h3>
<p>میزان دقیق بر اساس شرایط فرآیند، نوع ماشین‌آلات و الیاف و پس از بررسی در مقیاس آزمایشگاهی تعیین می‌شود. مقدار مصرف پیشنهادی ۰٫۵ تا ۲ گرم در لیتر است.</p>
<p>مشخصات فنی بر اساس درخواست مشتری قابل تنظیم است.</p>';

$applications = '<ul>
<li>در رنگرزی: موجب افزایش یکنواختی و جذب رنگ.</li>
<li>در سفیدگری: موجب بهبود نفوذ مواد سفیدکننده در ساختار الیاف.</li>
<li>در چاپ پارچه: باعث افزایش نفوذ خمیر چاپ.</li>
<li>در تکمیل نساجی: موجب بهبود توزیع مواد تکمیلی روی سطح الیاف می‌شود.</li>
</ul>
<h3>روش مصرف</h3>
<ul>
<li>مستقیماً به حمام فرآیند افزوده شود.</li>
<li>پیش از مصرف در مقیاس کامل، سازگاری با سایر مواد کمکی بررسی شود.</li>
<li>پیش از تولید، آزمایش در مقیاس آزمایشگاهی انجام شود.</li>
</ul>';

$packaging = '<p>قابل بسته‌بندی در بشکه‌های ۶۰ و ۲۲۰ لیتری و مخازن ۱۰۰۰ لیتری (IBC) پلی‌اتیلنی.</p>
<h3>شرایط نگهداری</h3>
<p>در محیط خنک و خشک نگهداری شود؛ از یخ‌زدگی و گرمای زیاد محافظت و درب ظرف کاملاً بسته نگه داشته شود. اصول ایمنی استاندارد حمل مواد صنعتی رعایت شود؛ برای اطلاعات کامل ایمنی به برگه اطلاعات ایمنی (SDS) مراجعه کنید.</p>';

// جدول مشخصات فنی کاتالوگ (ویژگی‌های محلی محصول). در مقدار ویژگی ووکامرس «|» جداکننده است.
$specs = array(
	'ظاهر'                         => 'مایع آبی',
	'چگالی (g/cm³ در ۲۵°C)'         => '1.02 ± 0.03',
	'ماهیت یونی'                    => 'آنیونی / غیریونی',
	'pH (محلول ۱٪ در ۲۵°C)'         => '6 تا 8',
	'میزان کف'                      => 'کم تا متوسط',
	'رسوب (%V/V)'                  => '0',
	'حلالیت در آب'                  => 'کاملاً محلول',
	'کشش سطحی (1 g/L در ۲۵°C)'      => '≤ 28 mN/m',
);

// عکس محصول از کارفرما، برش مربعی ۱:۱ (۱۰۵۹×۱۰۵۹)؛ عکس دسته از کارفرما (۱۲۵۴×۱۲۵۴).
// نسخه‌های کم‌کیفیت قبلی (بریده از PDF) به زباله‌دان می‌روند.
$image_file   = $src_dir . '/derived/kx-327-hq-square.jpg';
$cat_image    = $src_dir . '/derived/textile-category.webp';
$catalog_file = $src_dir . '/client-docs/تصاویر سایت/کاتالوگ وتینگ ایجنت.pdf';
foreach ( array( $image_file, $cat_image, $catalog_file ) as $f ) {
	if ( ! is_readable( $f ) ) {
		exit( "فایل پیدا نشد: $f\n" );
	}
}

/**
 * پیوست یک فایل محلی؛ اگر با همین _em_source قبلاً وارد شده همان را برمی‌گرداند.
 */
function em_kx_attach( $file, $name, $title, $source, $alt = '' ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_em_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $source, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$upload = wp_upload_bits( $name, null, (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		exit( 'Upload failed: ' . $upload['error'] . "\n" );
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_em_source', $source );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return (int) $id;
}

$cat = get_term_by( 'slug', 'textile-auxiliaries', 'product_cat' );

echo $confirm ? "نوشتن:\n" : "گزارش (بدون --confirm چیزی نوشته نمی‌شود):\n";
printf( "  محصول #%d: «%s» (%s) ← «%s» (%s)\n", $product_id, $product->get_name(), $product->get_slug(), $title, $slug );
printf( "  دسته: %s\n", $cat ? 'موجود #' . $cat->term_id : 'ساخت «کمک‌نساجی» (textile-auxiliaries)، سطح اول' );
printf( "  ویژگی‌ها: %d ردیف؛ تب کاربردها و بسته‌بندی از کاتالوگ\n", count( $specs ) );
printf( "  عکس شاخص: %s (به‌جای عکس فعلی #%d)\n", basename( $image_file ), $product->get_image_id() );
printf( "  عکس دسته: %s\n", basename( $cat_image ) );
printf( "  Customizer textile_catalog ← %s\n", basename( $catalog_file ) );
if ( ! $confirm ) {
	exit( 0 );
}

if ( ! $cat ) {
	$res = wp_insert_term(
		'مواد شیمیائی تخصصی کمک نساجی',
		'product_cat',
		array(
			'slug'        => 'textile-auxiliaries',
			'description' => 'مواد کمکی تخصصی فرآیندهای نساجی: رنگرزی، سفیدگری، چاپ و تکمیل.',
		)
	);
	if ( is_wp_error( $res ) ) {
		exit( $res->get_error_message() . "\n" );
	}
	$cat_id = (int) $res['term_id'];
	update_term_meta( $cat_id, '_em_source', 'catalog:kx-327' );
} else {
	$cat_id = (int) $cat->term_id;
}

$attrs = array();
$pos   = 0;
foreach ( $specs as $label => $value ) {
	$a = new WC_Product_Attribute();
	$a->set_id( 0 );
	$a->set_name( $label );
	$a->set_options( array( $value ) );
	$a->set_position( $pos++ );
	$a->set_visible( true );
	$a->set_variation( false );
	$attrs[] = $a;
}

$image_id   = em_kx_attach( $image_file, 'kx-327-wetting-agent.jpg', $title, 'client:kx-327-image', 'وتینگ ایجنت KX-327 روی الیاف' );
$cat_img_id = em_kx_attach( $cat_image, 'textile-auxiliaries.webp', 'کمک‌نساجی', 'client:textile-category-image', 'محصولات کمک‌نساجی ایمن تاش سپاهان' );
update_term_meta( $cat_id, 'thumbnail_id', $cat_img_id );
$catalog_id = em_kx_attach( $catalog_file, 'kx-327-catalog.pdf', 'کاتالوگ وتینگ ایجنت KX-327', 'catalog:kx-327-pdf' );

$product->set_name( $title );
$product->set_slug( $slug );
$product->set_description( $intro );
$product->set_short_description( 'وتینگ ایجنت (ترکننده سریع) برای رنگرزی، سفیدگری، چاپ و تکمیل نساجی.' );
$product->set_category_ids( array( $cat_id ) );
$product->set_attributes( $attrs );
$product->set_image_id( $image_id );
$product->save();

update_post_meta( $product_id, '_ementash_applications', $applications );
update_post_meta( $product_id, '_ementash_packaging', $packaging );
update_post_meta( $product_id, '_em_source', 'catalog:kx-327' );
delete_post_meta( $product_id, '_em_demo' );
wp_update_post(
	array(
		'ID'          => $image_id,
		'post_parent' => $product_id,
	)
);

set_theme_mod( 'textile_catalog', $catalog_id );

// عکس‌های کم‌کیفیت اجراهای قبلی (بریده از PDF، اگر جای دیگری استفاده نشده) به زباله‌دان.
$old_images = get_posts(
	array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => -1,
		'fields'      => 'ids',
		'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_em_source',
				'value'   => array( 'catalog:kx-327-image', 'catalog:kx-327-image-square' ),
				'compare' => 'IN',
			),
		),
	)
);
foreach ( $old_images as $old_id ) {
	$used = get_posts(
		array(
			'post_type'   => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $old_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! $used ) {
		wp_trash_post( $old_id );
		printf( "  عکس قبلی #%d به زباله‌دان رفت.\n", $old_id );
	}
}

printf( "انجام شد: %s\n", get_permalink( $product_id ) );
