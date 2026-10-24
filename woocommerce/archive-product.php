<?php
/**
 * فروشگاه و صفحه دسته محصولات (Products.dc.html). ووکامرس همین قالب را برای taxonomy-product_cat هم استفاده می‌کند.
 * location «archive» المنتور پرو (Woo Builder) اولویت دارد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'archive' ) ) {
	$ementash_term = is_product_category() ? get_queried_object() : null;
	$ementash_desc = $ementash_term ? wp_strip_all_tags( term_description( $ementash_term ) ) : (string) ementash_get( 'shop_intro' );

	get_template_part(
		'template-parts/page-head',
		null,
		array(
			// عنوان ووکامرس (مثلاً جستجو) entity دارد؛ متن خام به page-head داده می‌شود.
			'title'  => html_entity_decode( wp_strip_all_tags( woocommerce_page_title( false ) ), ENT_QUOTES, 'UTF-8' ),
			'desc'   => trim( $ementash_desc ),
			'crumbs' => ementash_shop_crumbs(),
		)
	);
	?>
	<div class="em-section em-shop">
		<div class="em-container em-shop__in">
			<?php get_template_part( 'template-parts/shop/sidebar' ); ?>

			<div class="em-shop__main">
				<?php get_template_part( 'template-parts/shop/toolbar' ); ?>

				<?php if ( woocommerce_product_loop() ) : ?>
					<ul class="em-pgrid">
						<?php
						while ( have_posts() ) {
							the_post();
							wc_get_template_part( 'content', 'product' );
						}
						?>
					</ul>
					<?php get_template_part( 'template-parts/shop/pagination' ); ?>
				<?php else : ?>
					<p class="em-shop__empty"><?php esc_html_e( 'محصولی در این بخش پیدا نشد.', 'ementash' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

get_footer();
