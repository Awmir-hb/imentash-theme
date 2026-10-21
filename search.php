<?php
/**
 * نتایج جستجو. طراحی کامل در فاز ۶.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'archive' ) ) {
	get_template_part( 'template-parts/loop' );
}

get_footer();
