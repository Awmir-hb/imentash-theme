<?php
/**
 * قالب پشتیبان؛ برگه نوشته‌ها (وبلاگ) هم از همین قالب استفاده می‌کند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( is_singular() ? 'single' : 'archive' ) ) {
	get_template_part( 'template-parts/loop' );
}

get_footer();
