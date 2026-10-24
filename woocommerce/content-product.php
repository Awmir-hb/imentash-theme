<?php
/**
 * کارت محصول (آرشیو، دسته، محصولات مرتبط). بدون قیمت و دکمه خرید.
 * عکس تزئینی است (alt خالی) چون نام محصول کنارش آمده است.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$ementash_cat = ementash_product_main_cat( $product->get_id() );
$ementash_sku = $product->get_sku();
?>
<li class="em-pcard-wrap">
	<a class="em-pcard" href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<span class="em-pcard__img">
			<?php
			if ( $product->get_image_id() ) {
				echo wp_get_attachment_image(
					$product->get_image_id(),
					'ementash-card',
					false,
					array(
						'alt'     => '',
						'loading' => 'lazy',
						'sizes'   => '(max-width: 560px) 100vw, (max-width: 1000px) 50vw, 340px',
					)
				);
			} else {
				echo '<span class="em-ph" aria-hidden="true"></span>';
			}
			?>
		</span>
		<div class="em-pcard__body">
			<?php if ( $ementash_cat || '' !== $ementash_sku ) : ?>
				<span class="em-pcard__meta">
					<span><?php echo $ementash_cat ? ementash_bdi( $ementash_cat->name ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php if ( '' !== $ementash_sku ) : ?>
						<span class="em-pcard__sku"><?php echo ementash_code( $ementash_sku ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<h3 class="em-pcard__title"><?php echo ementash_bdi( $product->get_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
			<span class="em-pcard__more" aria-hidden="true">
				<?php esc_html_e( 'مشاهده محصول', 'ementash' ); ?>
				<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
	</a>
</li>
