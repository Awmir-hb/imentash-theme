<?php
/**
 * صفحه محصول: محصولات مرتبط (همان دسته، ۳ مورد) با همان کارت آرشیو.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

$ementash_ids = wc_get_related_products( $product->get_id(), 3 );
if ( ! $ementash_ids ) {
	return;
}

$ementash_product = $product;
?>
<section class="em-section em-related" aria-labelledby="em-related-title">
	<div class="em-container">
		<h2 class="em-h2 em-related__title" id="em-related-title"><?php esc_html_e( 'محصولات مرتبط', 'ementash' ); ?></h2>
		<ul class="em-pgrid em-pgrid--related">
			<?php
			foreach ( $ementash_ids as $ementash_id ) {
				$post = get_post( $ementash_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $post );
				$product = wc_get_product( $ementash_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			$product = $ementash_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			?>
		</ul>
	</div>
</section>
