<?php
/**
 * آرشیو و دسته محصولات. location «archive» برای Woo Builder المنتور پرو؛ طراحی کامل در فاز ۴.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'archive' ) ) {
	get_template_part( 'template-parts/page-head', null, array( 'title' => woocommerce_page_title( false ) ) );
	?>
	<div class="em-section">
		<div class="em-container">
			<?php
			if ( woocommerce_product_loop() ) {
				do_action( 'woocommerce_before_shop_loop' );
				woocommerce_product_loop_start();
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'product' );
				}
				woocommerce_product_loop_end();
				do_action( 'woocommerce_after_shop_loop' );
			} else {
				do_action( 'woocommerce_no_products_found' );
			}
			?>
		</div>
	</div>
	<?php
}

get_footer();
