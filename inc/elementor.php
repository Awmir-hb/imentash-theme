<?php
/**
 * سازگاری با Elementor و Elementor Pro (Theme Builder و Woo Builder).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * ثبت locationهای header، footer، single و archive برای Theme Builder.
 */
function ementash_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'ementash_register_elementor_locations' );

/**
 * اگر Theme Builder برای این location قالب داشته باشد آن را چاپ می‌کند و true برمی‌گرداند.
 * false یعنی قالب خودمان باید خروجی بدهد.
 */
function ementash_elementor_location( $location ) {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/**
 * آیا این نوشته/برگه/محصول با المنتور ساخته شده است؟
 */
function ementash_is_elementor( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * وضعیت هدر خود قالب؛ منوی موبایل فقط وقتی چاپ می‌شود که هدر قالب چاپ شده باشد.
 */
function ementash_own_header( $value = null ) {
	static $own = false;
	if ( null !== $value ) {
		$own = (bool) $value;
	}
	return $own;
}
