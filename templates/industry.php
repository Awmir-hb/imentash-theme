<?php
/**
 * Template Name: صنعت
 * Template Post Type: page
 *
 * برگه صنعت: سربرگ، متن برگه (اختیاری؛ اگر خالی باشد بخشش چاپ نمی‌شود) و محصولاتی که در صفحه
 * ویرایش محصول برای این صنعت تیک خورده‌اند. برگه المنتوری فقط محتوای خودش را دارد؛
 * شبکه محصولات آنجا با شورت‌کد [ementash_industry_products] می‌آید.
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
		if ( '' !== trim( wp_strip_all_tags( get_the_content(), true ) ) || has_blocks() ) {
			get_template_part( 'template-parts/entry-content' );
		}
		get_template_part( 'template-parts/industry/products', null, array( 'page_id' => get_the_ID() ) );
	}
}

get_footer();
