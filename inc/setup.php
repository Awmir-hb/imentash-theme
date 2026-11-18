<?php
/**
 * پشتیبانی‌های قالب، منوها و سایز تصاویر.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

function ementash_setup() {
	load_theme_textdomain( 'ementash', EMENTASH_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	// خلاصه برگه = زیرعنوان سربرگ صفحات داخلی.
	add_post_type_support( 'page', 'excerpt' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 56,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی (هدر)', 'ementash' ),
			'mobile'  => __( 'منوی موبایل (اگر خالی باشد منوی اصلی نمایش داده می‌شود)', 'ementash' ),
			'footer'  => __( 'دسترسی سریع فوتر', 'ementash' ),
		)
	);

	// نسبت‌ها مطابق طراحی: کارت ۴:۳، محصول ۱:۱، گواهینامه ۳:۴.
	add_image_size( 'ementash-card', 640, 480, true );
	add_image_size( 'ementash-square', 800, 800, true );
	add_image_size( 'ementash-portrait', 480, 640, true );
}
add_action( 'after_setup_theme', 'ementash_setup' );

/**
 * سایزهای ساخته‌شده از JPEG و PNG به WebP ذخیره می‌شوند (حدود ۳۰٪ سبک‌تر با همان کیفیت).
 * فایل اصلی آپلودشده دست نمی‌خورد. فقط اگر سرور WebP را پشتیبانی کند.
 * PNG پالت‌دار (۸ بیتی، مثل لوگوهای کوچک) PNG می‌ماند: GD آن را به WebP تبدیل نمی‌کند و فایل خالی می‌سازد.
 *
 * @param array  $formats  نگاشت نوع ورودی ← نوع خروجی.
 * @param string $filename مسیر فایل در حال پردازش.
 * @param string $mime     نوع فایل.
 */
function ementash_webp_subsizes( $formats, $filename = '', $mime = '' ) {
	if ( ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $formats;
	}
	$formats['image/jpeg'] = 'image/webp';
	if ( 'image/png' !== $mime || ! ementash_is_palette_png( $filename ) ) {
		$formats['image/png'] = 'image/webp';
	}
	return $formats;
}
add_filter( 'image_editor_output_format', 'ementash_webp_subsizes', 10, 3 );

/**
 * PNG پالت‌دار؟ (بایت نوع رنگ در سرآیند IHDR = ۳).
 */
function ementash_is_palette_png( $filename ) {
	if ( '' === $filename || ! is_readable( $filename ) ) {
		return false;
	}
	$head = file_get_contents( $filename, false, null, 0, 26 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_string( $head ) && 26 === strlen( $head ) && 3 === ord( $head[25] );
}

/**
 * کیفیت WebP: ۷۵ (پیش‌فرض وردپرس ۸۶). در عکس‌های پرجزئیات حدود ۴۰٪ سبک‌تر از JPEG وردپرس، بدون افت دیدنی.
 */
function ementash_webp_quality( $quality, $mime_type ) {
	return 'image/webp' === $mime_type ? 75 : $quality;
}
add_filter( 'wp_editor_set_quality', 'ementash_webp_quality', 10, 2 );

function ementash_content_width() {
	$GLOBALS['content_width'] = 1320;
}
add_action( 'after_setup_theme', 'ementash_content_width', 0 );

/**
 * برگه‌ای که در Customizer ← برگه‌ها برای «درباره ما» یا «تماس با ما» انتخاب شده، قالب همان را می‌گیرد
 * حتی اگر در ویرایشگر قالبی انتخاب نشده باشد. قالبی که دستی انتخاب شده (مثل تمام‌عرض) اولویت دارد.
 */
function ementash_special_page_template( $template ) {
	if ( ! is_page() || get_page_template_slug() ) {
		return $template;
	}
	foreach ( ementash_page_templates() as $key => $file ) {
		if ( (int) get_queried_object_id() === ementash_page_id( $key ) ) {
			$found = locate_template( $file );
			return $found ? $found : $template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'ementash_special_page_template', 20 );

/**
 * زیرعنوان سربرگ برگه‌ها: فقط خلاصه‌ای که دستی نوشته شده (نه خلاصه خودکار از متن).
 */
function ementash_page_subtitle( $post_id = 0 ) {
	$post = get_post( $post_id ? $post_id : get_the_ID() );
	return $post && has_excerpt( $post ) ? trim( wp_strip_all_tags( $post->post_excerpt ) ) : '';
}

/**
 * آیا صفحه فعلی برگه ویژه (about یا contact) با خروجی خود قالب است؟ (برای بارگذاری CSS/JS)
 * برگه‌ای که با المنتور ساخته شده حساب نمی‌شود.
 */
function ementash_is_special_view( $key ) {
	if ( ! is_page() ) {
		return false;
	}
	$templates = ementash_page_templates();
	$id        = (int) get_queried_object_id();
	// قالب انتخاب‌شده، برگه Customizer، یا قالبی که سلسله‌مراتب وردپرس برای این برگه پیدا کرده است.
	$match     = is_page_template( $templates[ $key ] ) || $id === ementash_page_id( $key ) || basename( (string) get_page_template() ) === $templates[ $key ];
	return $match && ! ementash_is_elementor( $id );
}

function ementash_is_about_view() {
	return ementash_is_special_view( 'about' );
}

function ementash_is_contact_view() {
	return ementash_is_special_view( 'contact' );
}

/* ---------- آپلود SVG (فقط مدیر سایت؛ برای آیکون شبکه‌های اجتماعی و مانند آن) ----------
   SVG می‌تواند اسکریپت داشته باشد؛ پس فقط کاربر manage_options، و فایل هنگام آپلود با فهرست سفید
   تگ و ویژگی پاک‌سازی می‌شود (script، foreignObject، on*، href خارجی و style حذف). قالب SVG آپلودی را
   فقط با <img> نمایش می‌دهد که در آن اسکریپت اجرا نمی‌شود. */

function ementash_svg_upload_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'ementash_svg_upload_mimes' );

/**
 * تشخیص نوع SVG (وردپرس پسوند svg را بدون این فیلتر رد می‌کند).
 */
function ementash_svg_filetype( $data, $file, $filename, $mimes ) {
	if ( current_user_can( 'manage_options' ) && 'svg' === strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'ementash_svg_filetype', 10, 4 );

/**
 * تگ‌ها و ویژگی‌های مجاز SVG آپلودی (شکل، رنگ، گرادیان؛ بدون اسکریپت و لینک).
 */
function ementash_svg_upload_allowed() {
	$attrs = array_fill_keys(
		array( 'id', 'class', 'd', 'x', 'y', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'points', 'x1', 'x2', 'y1', 'y2', 'fx', 'fy', 'transform', 'fill', 'fill-rule', 'fill-opacity', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'opacity', 'clip-rule', 'clip-path', 'mask', 'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform', 'viewbox', 'xmlns', 'version', 'preserveaspectratio' ),
		true
	);
	$tags  = array( 'svg', 'g', 'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon', 'defs', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'title', 'desc' );
	return array_fill_keys( $tags, $attrs );
}

/**
 * پاک‌سازی فایل SVG پیش از ذخیره؛ اگر چیزی از آن نماند، آپلود رد می‌شود.
 */
function ementash_svg_sanitize_upload( $file ) {
	$is_svg = 'image/svg+xml' === ( $file['type'] ?? '' ) || 'svg' === strtolower( pathinfo( (string) ( $file['name'] ?? '' ), PATHINFO_EXTENSION ) );
	if ( ! $is_svg ) {
		return $file;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		$file['error'] = __( 'فقط مدیر سایت می‌تواند فایل SVG آپلود کند.', 'ementash' );
		return $file;
	}
	$raw = (string) file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	// اعلان XML، DOCTYPE (با بخش داخلی [...] و entityها) و توضیحات حذف؛
	// script، style و foreignObject با محتوایشان (wp_kses فقط تگ را برمی‌دارد و متن داخلش می‌ماند).
	$raw   = preg_replace( '/<\?xml.*?\?>|<!DOCTYPE[^\[>]*(\[.*?\])?\s*>|<!--.*?-->/is', '', $raw );
	$raw   = preg_replace( '#<(script|style|foreignObject)\b[^>]*>.*?</\1\s*>#is', '', $raw );
	$clean = trim( wp_kses( $raw, ementash_svg_upload_allowed() ) );
	if ( ! preg_match( '/^<svg\b/i', $clean ) ) {
		$file['error'] = __( 'فایل SVG معتبر نیست یا بعد از پاک‌سازی چیزی از آن نماند.', 'ementash' );
		return $file;
	}
	// wp_kses نام ویژگی‌ها را کوچک می‌کند؛ SVG به بزرگی حروف حساس است.
	$clean = str_replace(
		array( ' viewbox=', ' preserveaspectratio=', '<lineargradient', '</lineargradient>', '<radialgradient', '</radialgradient>', ' gradientunits=', ' gradienttransform=', '<clippath', '</clippath>' ),
		array( ' viewBox=', ' preserveAspectRatio=', '<linearGradient', '</linearGradient>', '<radialGradient', '</radialGradient>', ' gradientUnits=', ' gradientTransform=', '<clipPath', '</clipPath>' ),
		$clean
	);
	if ( false === strpos( $clean, 'xmlns=' ) ) {
		$clean = preg_replace( '/^<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $clean );
	}
	file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'ementash_svg_sanitize_upload' );
