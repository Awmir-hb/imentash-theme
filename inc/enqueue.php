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
		wp_enqueue_style( 'ementash-home', EMENTASH_URI . '/assets/css/home.css', array( 'ementash-main' ), ementash_asset_ver( 'assets/css/home.css' ) );
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
 * فقط زیرمجموعه عربی/فارسی فونت‌ها پیش‌بارگذاری می‌شود؛ لاتین در صورت نیاز لود می‌شود.
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
