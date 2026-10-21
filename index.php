<?php
/**
 * قالب پشتیبان.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! imentash_elementor_location( is_singular() ? 'single' : 'archive' ) ) {
	get_template_part( 'template-parts/loop' );
}

get_footer();
