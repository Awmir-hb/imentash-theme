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
