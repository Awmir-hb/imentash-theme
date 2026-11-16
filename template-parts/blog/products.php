<?php
/**
 * محصولات مرتبط زیر متن مقاله: کارت محصولات انتخاب‌شده در کادر «محصولات مرتبط» + دکمه استعلام قیمت.
 * با یک محصول، دکمه نام همان محصول را در فرم تماس از پیش پر می‌کند. اگر محصولی انتخاب نشده باشد چیزی چاپ نمی‌شود.
 *
 * $args: post_id (پیش‌فرض نوشته فعلی).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_products = ementash_post_products( isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID() );
if ( ! $ementash_products || ! function_exists( 'wc_get_template_part' ) ) {
	return;
}
$ementash_inquiry = ementash_contact_url();
if ( '' !== $ementash_inquiry && 1 === count( $ementash_products ) ) {
	$ementash_inquiry = ementash_product_inquiry_url( $ementash_products[0]->ID );
}
?>
<section class="em-section em-section--steel em-bproducts" aria-labelledby="em-bproducts-title">
	<div class="em-container">
		<div class="em-head">
			<div>
				<h2 class="em-h2" id="em-bproducts-title"><?php esc_html_e( 'محصولات مرتبط با این مقاله', 'ementash' ); ?></h2>
				<p class="em-head__text"><?php esc_html_e( 'برای مشخصات فنی، نمونه یا قیمت با کارشناسان ما در تماس باشید.', 'ementash' ); ?></p>
			</div>
			<?php if ( $ementash_inquiry ) : ?>
				<a class="em-btn em-btn--brand" href="<?php echo esc_url( $ementash_inquiry ); ?>"><?php esc_html_e( 'استعلام قیمت', 'ementash' ); ?></a>
			<?php endif; ?>
		</div>
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
