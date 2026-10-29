<?php
/**
 * بارگذاری CSS، JS و پیش‌بارگذاری فونت‌ها.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * نسخه فایل بر اساس زمان تغییر، برای پاک شدن کش مرورگر بعد از هر ویرایش.
 */
function ementash_asset_ver( $rel ) {
	$file = EMENTASH_DIR . '/' . $rel;
	return file_exists( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
}

function ementash_enqueue_assets() {
	wp_enqueue_style( 'ementash-main', EMENTASH_URI . '/assets/css/main.css', array(), ementash_asset_ver( 'assets/css/main.css' ) );
	// کارت‌ها و صفحه‌بندی؛ فقط به‌عنوان وابستگی home، shop و blog بارگذاری می‌شود.
	wp_register_style( 'ementash-cards', EMENTASH_URI . '/assets/css/cards.css', array( 'ementash-main' ), ementash_asset_ver( 'assets/css/cards.css' ) );

	wp_enqueue_script(
		'ementash-main',
		EMENTASH_URI . '/assets/js/main.js',
		array(),
		ementash_asset_ver( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// صفحه اصلی خود قالب (نه برگه المنتوری).
	if ( is_front_page() && ! ementash_home_is_elementor() ) {
		wp_enqueue_style( 'ementash-home', EMENTASH_URI . '/assets/css/home.css', array( 'ementash-cards' ), ementash_asset_ver( 'assets/css/home.css' ) );
		wp_enqueue_script(
			'ementash-home',
			EMENTASH_URI . '/assets/js/home.js',
			array(),
			ementash_asset_ver( 'assets/js/home.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_enqueue_assets' );

/**
 * بدون JS ویدئوی هیرو بارگذاری نمی‌شود؛ لودر پنهان می‌ماند تا پوستر از همان ابتدا دیده شود.
 */
function ementash_hero_noscript() {
	if ( is_front_page() && ! ementash_home_is_elementor() ) {
		echo '<noscript><style>.em-hero__loader{display:none!important}</style></noscript>' . "\n";
	}
}
add_action( 'wp_head', 'ementash_hero_noscript' );

/**
 * اسکریپت ایموجی وردپرس در مرورگرهای قدیمی فایل از s.w.org می‌گیرد (درخواست خارجی، در ایران کند).
 * ایموجی‌ها با فونت سیستم نمایش داده می‌شوند.
 */
function ementash_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'ementash_disable_emoji' );

/**
 * فقط زیرمجموعه عربی/فارسی فونت‌ها پیش‌بارگذاری می‌شود؛ لاتین در صورت نیاز لود می‌شود.
 * هر فونت یک فایل variable است و همه وزن‌ها را دارد؛ پس فقط همین دو فایل.
 */
function ementash_preload_fonts() {
	$fonts = array( 'vazirmatn-arabic-wght.woff2', 'readex-pro-arabic-wght.woff2' );
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( EMENTASH_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'ementash_preload_fonts', 1 );
