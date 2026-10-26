<?php
/**
 * آرشیو وبلاگ (برگه نوشته‌ها، دسته، برچسب، نویسنده، تاریخ): سربرگ، دسته‌ها، کارت‌های ۴:۳ و صفحه‌بندی.
 * index.php و archive.php همین را صدا می‌زنند. جستجو قالب جدای خودش را دارد (search.php).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_desc = '';
if ( is_home() ) {
	$ementash_title = ementash_blog_title();
	$ementash_desc  = ementash_page_subtitle( absint( get_option( 'page_for_posts' ) ) );
} elseif ( is_category() || is_tag() ) {
	$ementash_title = single_term_title( '', false );
	if ( is_tag() ) {
		/* translators: %s: نام برچسب */
		$ementash_title = sprintf( __( 'برچسب: %s', 'ementash' ), $ementash_title );
	}
	$ementash_desc = trim( wp_strip_all_tags( term_description() ) );
} elseif ( is_archive() ) {
	$ementash_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$ementash_title = get_bloginfo( 'name' );
}

get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title'  => $ementash_title,
		'desc'   => $ementash_desc,
		'crumbs' => ementash_blog_crumbs(),
	)
);
?>
<div class="em-section em-blogarc">
	<div class="em-container">
		<?php get_template_part( 'template-parts/blog/cats' ); ?>

		<?php if ( have_posts() ) : ?>
			<ul class="em-posts em-posts--line">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/blog/card', null, array( 'heading' => 'h2' ) );
				}
				?>
			</ul>
			<?php get_template_part( 'template-parts/pagination', null, array( 'label' => __( 'صفحه‌بندی مقاله‌ها', 'ementash' ) ) ); ?>
		<?php else : ?>
			<p class="em-empty"><?php esc_html_e( 'هنوز مقاله‌ای در این بخش منتشر نشده است.', 'ementash' ); ?></p>
		<?php endif; ?>
	</div>
</div>
