<?php
/**
 * صفحه اصلی. اگر برگه صفحه اصلی با المنتور ساخته شده باشد فقط محتوای آن نمایش داده می‌شود؛
 * وگرنه سکشن‌های روشن به ترتیب ementash_home_sections() از template-parts/home چاپ می‌شوند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

if ( ementash_home_is_elementor() ) {
	get_header();
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	get_footer();
	return;
}

// هدر شفاف فقط وقتی معنا دارد که هیرو زیر آن باشد.
get_header( null, array( 'variant' => ementash_section_enabled( 'hero' ) ? 'overlay' : 'grid' ) );

foreach ( array_keys( ementash_home_sections() ) as $ementash_section ) {
	if ( ementash_section_enabled( $ementash_section ) ) {
		get_template_part( 'template-parts/home/' . $ementash_section );
	}
}

get_footer();
