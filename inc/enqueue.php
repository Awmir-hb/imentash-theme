<?php
/**
 * بارگذاری CSS، JS و پیش‌بارگذاری فونت‌ها.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

/**
 * نسخه فایل بر اساس زمان تغییر، برای پاک شدن کش مرورگر بعد از هر ویرایش.
 */
function imentash_asset_ver( $rel ) {
	$file = IMENTASH_DIR . '/' . $rel;
	return file_exists( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
}

function imentash_enqueue_assets() {
	wp_enqueue_style( 'imentash-main', IMENTASH_URI . '/assets/css/main.css', array(), imentash_asset_ver( 'assets/css/main.css' ) );

	wp_enqueue_script(
		'imentash-main',
		IMENTASH_URI . '/assets/js/main.js',
		array(),
		imentash_asset_ver( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'imentash_enqueue_assets' );

/**
 * فقط زیرمجموعه عربی/فارسی فونت‌ها پیش‌بارگذاری می‌شود؛ لاتین در صورت نیاز لود می‌شود.
 */
function imentash_preload_fonts() {
	$fonts = array( 'vazirmatn-arabic-wght.woff2', 'readex-pro-arabic-wght.woff2' );
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( IMENTASH_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'imentash_preload_fonts', 1 );
