<?php
/**
 * نتایج جستجو. طراحی کامل در فاز ۶.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! imentash_elementor_location( 'archive' ) ) {
	get_template_part( 'template-parts/loop' );
}

get_footer();
