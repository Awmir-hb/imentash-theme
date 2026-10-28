<?php
/**
 * محصولات یک صنعت (کارت‌های همان فهرست محصولات). اگر محصولی تیک نخورده باشد چیزی چاپ نمی‌شود.
 *
 * $args: page_id (شناسه برگه صنعت).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_products = ementash_industry_products( isset( $args['page_id'] ) ? $args['page_id'] : 0 );
if ( ! $ementash_products || ! function_exists( 'wc_get_template_part' ) ) {
	return;
}
$ementash_uid = wp_unique_id( 'em-industry-products-' );
?>
<section class="em-section em-iproducts" aria-labelledby="<?php echo esc_attr( $ementash_uid ); ?>">
	<div class="em-container">
		<h2 class="em-h2 em-iproducts__title" id="<?php echo esc_attr( $ementash_uid ); ?>"><?php esc_html_e( 'محصولات مناسب این صنعت', 'ementash' ); ?></h2>
		<ul class="em-pgrid">
			<?php
			global $post;
			foreach ( $ementash_products as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- برای the_post محصول ووکامرس.
				setup_postdata( $post );
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
