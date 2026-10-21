<?php
/**
 * پشتیبانی‌های قالب، منوها و سایز تصاویر.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

function imentash_setup() {
	load_theme_textdomain( 'imentash', IMENTASH_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
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
			'primary' => __( 'منوی اصلی (هدر)', 'imentash' ),
			'mobile'  => __( 'منوی موبایل (اگر خالی باشد منوی اصلی نمایش داده می‌شود)', 'imentash' ),
			'footer'  => __( 'دسترسی سریع فوتر', 'imentash' ),
		)
	);

	// نسبت‌ها مطابق طراحی: کارت ۴:۳، محصول ۱:۱، گواهینامه ۳:۴.
	add_image_size( 'imentash-card', 640, 480, true );
	add_image_size( 'imentash-square', 800, 800, true );
	add_image_size( 'imentash-portrait', 480, 640, true );
}
add_action( 'after_setup_theme', 'imentash_setup' );

function imentash_content_width() {
	$GLOBALS['content_width'] = 1320;
}
add_action( 'after_setup_theme', 'imentash_content_width', 0 );
