<?php
/**
 * Template Name: درباره ما
 *
 * صفحه درباره ما (About.dc.html). با هر نامکی کار می‌کند: قالب را در ویرایشگر برگه انتخاب کنید
 * یا برگه را در Customizer ← برگه‌ها تعیین کنید. location «single» المنتور اولویت دارد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();

		if ( ementash_is_elementor() ) {
			the_content();
			continue;
		}

		// ترتیب بخش‌ها؛ برای جابه‌جایی همین فهرست را مرتب کنید.
		get_template_part( 'template-parts/about/hero' );
		get_template_part( 'template-parts/about/story' );
		get_template_part( 'template-parts/about/timeline' );
		get_template_part( 'template-parts/about/credentials' );
		get_template_part( 'template-parts/about/rd' );
		get_template_part( 'template-parts/about/values' );
	}
}

get_footer();
