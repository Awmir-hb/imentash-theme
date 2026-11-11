<?php
/**
 * رنگ‌ها و فونت‌های قالب را در Global Colors و Global Fonts کیت فعال المنتور می‌نویسد (فقط CLI).
 * مقدار رنگ‌ها از :root فایل assets/css/main.css خوانده می‌شود، پس با تغییر توکن‌ها فقط همین را دوباره اجرا کنید.
 * بقیه تنظیمات کیت دست نمی‌خورد. بدون --confirm فقط چاپ می‌کند.
 *
 * php -c <php.ini سایت Local> dev/elementor-kit.php [--confirm]
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! did_action( 'elementor/loaded' ) ) {
	exit( "المنتور فعال نیست.\n" );
}
$confirm = in_array( '--confirm', $argv, true );

// توکن‌های :root
$css = (string) file_get_contents( EMENTASH_DIR . '/assets/css/main.css' );
preg_match( '/:root\s*\{(.*?)\n\}/s', $css, $root );
preg_match_all( '/--([a-z0-9-]+)\s*:\s*(#[0-9a-fA-F]{3,8})\s*;/', isset( $root[1] ) ? $root[1] : '', $m );
$tokens = array_combine( $m[1], $m[2] );

// system: نقش‌های پیش‌فرض المنتور؛ custom: بقیه توکن‌های اصلی CLAUDE.md
$system = array(
	'primary'   => array( 'blue', 'برند (blue)' ),
	'secondary' => array( 'navy-900', 'سرمه‌ای تیره (navy-900)' ),
	'text'      => array( 'ink', 'متن (ink)' ),
	'accent'    => array( 'blue-light', 'آبی روی تیره (blue-light)' ),
);
$custom = array( 'navy-800', 'blue-hover', 'bg-soft', 'muted', 'line', 'white', 'on-dark' );

$colors = array();
foreach ( $system as $id => $def ) {
	if ( isset( $tokens[ $def[0] ] ) ) {
		$colors['system'][] = array(
			'_id'   => $id,
			'title' => $def[1],
			'color' => strtoupper( $tokens[ $def[0] ] ),
		);
	}
}
foreach ( $custom as $name ) {
	if ( isset( $tokens[ $name ] ) ) {
		$colors['custom'][] = array(
			'_id'   => 'em' . str_replace( '-', '', $name ),
			'title' => $name,
			'color' => strtoupper( $tokens[ $name ] ),
		);
	}
}

// فونت‌ها: تیتر Readex Pro (Light مثل --fw-display)، متن Vazirmatn
preg_match( '/--fw-display\s*:\s*(\d+)/', $css, $fw );
$display = isset( $fw[1] ) ? $fw[1] : '300';
$font    = function ( $id, $title, $family, $weight ) {
	return array(
		'_id'                    => $id,
		'title'                  => $title,
		'typography_typography'  => 'custom',
		'typography_font_family' => $family,
		'typography_font_weight' => $weight,
	);
};
$fonts = array(
	$font( 'primary', 'تیتر (Readex Pro)', 'Readex Pro', $display ),
	$font( 'secondary', 'زیرتیتر (Readex Pro)', 'Readex Pro', '400' ),
	$font( 'text', 'متن (Vazirmatn)', 'Vazirmatn', '400' ),
	$font( 'accent', 'دکمه (Vazirmatn)', 'Vazirmatn', '600' ),
);

$kit_id = absint( get_option( 'elementor_active_kit' ) );
if ( ! $kit_id ) {
	exit( "کیت فعال المنتور پیدا نشد؛ یک بار ویرایشگر المنتور را باز کنید.\n" );
}
foreach ( array_merge( $colors['system'], isset( $colors['custom'] ) ? $colors['custom'] : array() ) as $c ) {
	printf( "%-12s %s\n", $c['color'], $c['title'] );
}
foreach ( $fonts as $f ) {
	printf( "%-12s %s %s\n", 'font', $f['typography_font_family'], $f['typography_font_weight'] );
}

if ( ! $confirm ) {
	exit( "کیت {$kit_id}. برای نوشتن --confirm بزنید.\n" );
}
$settings                      = (array) get_post_meta( $kit_id, '_elementor_page_settings', true );
$settings['system_colors']     = $colors['system'];
$settings['custom_colors']     = isset( $colors['custom'] ) ? $colors['custom'] : array();
$settings['system_typography'] = $fonts;
update_post_meta( $kit_id, '_elementor_page_settings', $settings );
\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "کیت {$kit_id} به‌روز شد و کش CSS المنتور پاک شد.\n";
