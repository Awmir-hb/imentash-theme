<?php
/**
 * نوشته تکی. location «single» المنتور اولویت دارد؛ نوشته ساخته‌شده با المنتور فقط محتوای خودش را دارد.
 * نوشته وبلاگ: سربرگ با تاریخ و دسته، ستون خواندن، برچسب‌ها و نوشته‌های مرتبط. بقیه نوع‌ها (مثل پیوست): سربرگ و محتوا.
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

		if ( 'post' !== get_post_type() ) {
			get_template_part(
				'template-parts/page-head',
				null,
				array(
					'title'  => get_the_title(),
					'crumbs' => ementash_page_crumbs(),
				)
			);
			get_template_part( 'template-parts/entry-content' );
			continue;
		}

		$ementash_cat = ementash_post_main_cat( get_the_ID() );
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'  => get_the_title(),
				'crumbs' => ementash_blog_crumbs(),
				'meta'   => array(
					array(
						'text'     => get_the_date(),
						'datetime' => get_the_date( 'c' ),
					),
					array( 'text' => $ementash_cat ? $ementash_cat->name : '' ),
				),
			)
		);
		get_template_part( 'template-parts/blog/article' );
		get_template_part( 'template-parts/blog/related' );
	}
}

get_footer();
