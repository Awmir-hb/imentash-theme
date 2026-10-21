<?php
/**
 * Template Name: تمام‌عرض (بدون کانتینر)
 * Template Post Type: page
 *
 * هدر و فوتر قالب + محتوای برگه بدون کانتینر و بدون سربرگ.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
}

get_footer();
