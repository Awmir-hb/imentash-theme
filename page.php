<?php
/**
 * برگه پیش‌فرض. location «single» المنتور اولویت دارد؛ برگه ساخته‌شده با المنتور فقط محتوای خودش را دارد.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! imentash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();

		if ( imentash_is_elementor() ) {
			the_content();
			continue;
		}

		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'  => get_the_title(),
				'crumbs' => imentash_page_crumbs(),
			)
		);
		get_template_part( 'template-parts/entry-content' );
	}
}

get_footer();
