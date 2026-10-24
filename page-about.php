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

		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'  => get_the_title(),
				'desc'   => ementash_page_subtitle(),
				'crumbs' => ementash_page_crumbs(),
			)
		);

		// ترتیب بخش‌ها؛ برای جابه‌جایی همین فهرست را مرتب کنید.
		get_template_part( 'template-parts/about/story' );
		get_template_part( 'template-parts/about/stats' );
		get_template_part( 'template-parts/about/timeline' );
		get_template_part( 'template-parts/about/certificates' );
		get_template_part( 'template-parts/home/honors' );
		get_template_part( 'template-parts/about/cards', null, array( 'group' => 'rd' ) );
		get_template_part( 'template-parts/about/cards', null, array( 'group' => 'values' ) );
	}
}

get_footer();
