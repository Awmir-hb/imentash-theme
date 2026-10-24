<?php
/**
 * صفحه اصلی. اگر برگه صفحه اصلی با المنتور ساخته شده باشد فقط محتوای آن نمایش داده می‌شود؛
 * وگرنه سکشن‌های روشن به ترتیب فهرست زیر از template-parts/home چاپ می‌شوند.
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

// ترتیب سکشن‌ها؛ برای جابه‌جایی همین فهرست را مرتب کنید.
// هر نام = template-parts/home/{نام}.php و کلید روشن/خاموش آن در Customizer (ementash_home_sections).
$ementash_sections = array(
	'hero',
	'about',
	'honors',
	'industries',
	'clients',
	'services',
	'products',
	'production-line',
	'gallery',
	'textile',
	'blog',
);

// هدر شفاف فقط وقتی معنا دارد که هیرو زیر آن باشد.
$ementash_overlay = 'hero' === $ementash_sections[0] && ementash_section_enabled( 'hero' );
get_header( null, array( 'variant' => $ementash_overlay ? 'overlay' : 'grid' ) );

foreach ( $ementash_sections as $ementash_section ) {
	if ( ementash_section_enabled( $ementash_section ) ) {
		get_template_part( 'template-parts/home/' . $ementash_section );
	}
}

get_footer();
