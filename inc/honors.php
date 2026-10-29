<?php
/**
 * نوار افتخارات: داده، فایل‌های CSS/JS و شورت‌کد [ementash_honors] برای المنتور.
 * خروجی در template-parts/home/honors.php است و صفحه اصلی و شورت‌کد هر دو از همان استفاده می‌کنند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * موارد em_certificate که نوعشان «افتخار» است، به ترتیب menu_order.
 *
 * @return WP_Post[]
 */
function ementash_get_honors() {
	return ementash_get_certificates( 'honor' );
}

function ementash_honors_assets() {
	wp_enqueue_style( 'ementash-honors', EMENTASH_URI . '/assets/css/honors.css', array( 'ementash-main' ), ementash_asset_ver( 'assets/css/honors.css' ) );
	wp_enqueue_script(
		'ementash-honors',
		EMENTASH_URI . '/assets/js/honors.js',
		array(),
		ementash_asset_ver( 'assets/js/honors.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * بارگذاری در head، هرجا از قبل معلوم است نوار نمایش داده می‌شود.
 * اگر شورت‌کد جای دیگری (مثل ویجت المنتور) بیاید، خود شورت‌کد فایل‌ها را صدا می‌زند.
 */
function ementash_honors_enqueue() {
	$home  = is_front_page() && ! ementash_home_is_elementor() && ementash_section_enabled( 'honors' );
	$code  = is_singular() && has_shortcode( (string) get_post_field( 'post_content', get_queried_object_id() ), 'ementash_honors' );
	if ( $home || $code ) {
		ementash_honors_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_honors_enqueue', 20 );

/**
 * [ementash_honors title="..." text="..."]؛ بدون ویژگی، تیتر و متن از Customizer.
 * title="" تیتر را حذف می‌کند.
 */
function ementash_honors_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => null,
			'text'  => null,
		),
		$atts,
		'ementash_honors'
	);

	ementash_honors_assets();
	ob_start();
	get_template_part(
		'template-parts/home/honors',
		null,
		array(
			'title' => null === $atts['title'] ? null : sanitize_text_field( $atts['title'] ),
			'text'  => null === $atts['text'] ? null : sanitize_text_field( $atts['text'] ),
		)
	);
	return (string) ob_get_clean();
}
add_shortcode( 'ementash_honors', 'ementash_honors_shortcode' );
