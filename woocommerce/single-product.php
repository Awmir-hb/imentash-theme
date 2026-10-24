<?php
/**
 * صفحه محصول (Product.dc.html): گالری و اطلاعات، تب‌ها، محصولات مرتبط.
 * location «single» المنتور پرو اولویت دارد؛ محصولی که با المنتور ساخته شده فقط محتوای خودش را دارد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header( null, array( 'variant' => 'plain' ) );

if ( ! ementash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();

		if ( ementash_is_elementor() ) {
			the_content();
			continue;
		}

		get_template_part( 'template-parts/product/summary' );
		get_template_part( 'template-parts/product/tabs' );
		get_template_part( 'template-parts/product/related' );
	}
}

get_footer();
