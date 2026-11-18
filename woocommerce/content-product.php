<?php
/**
 * کارت محصول (آرشیو، دسته، محصولات مرتبط، صفحه اصلی). بدون قیمت و دکمه خرید.
 *
 * - لینک اصلی روی عنوان است و با ::after کل کارت را می‌پوشاند؛ دکمه «استعلام قیمت» لینک جدای بالای آن است
 *   (لینک تو در تو مجاز نیست).
 * - کلاس‌های آتش (pa_fire_class) به‌صورت برچسب کوچک گوشه عکس.
 * - عکس تزئینی است (alt خالی) چون نام محصول کنارش آمده است.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$ementash_cat   = ementash_product_main_cat( $product->get_id() );
$ementash_sku   = $product->get_sku();
$ementash_name  = $product->get_name();
$ementash_fires = ementash_wc_fire_classes( $product->get_id() );
?>
<li class="em-pcard-wrap">
	<div class="em-pcard">
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
				echo ementash_product_placeholder( $product->get_id(), 'ementash-card', array( 'sizes' => '(max-width: 560px) 100vw, (max-width: 1000px) 50vw, 340px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- عکس دسته یا آیکون SVG فیلترشده.
			}
			?>
			<?php if ( $ementash_fires ) : ?>
				<span class="em-pcard__fire">
					<span class="screen-reader-text"><?php esc_html_e( 'کلاس آتش:', 'ementash' ); ?></span>
					<?php foreach ( $ementash_fires as $ementash_fire ) : ?>
						<span class="em-pcard__fclass"<?php echo '' !== $ementash_fire['label'] ? ' title="' . esc_attr( $ementash_fire['label'] ) . '"' : ''; ?>><?php echo esc_html( $ementash_fire['name'] ); ?></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
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
			<h3 class="em-pcard__title"><a class="em-pcard__link" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo ementash_bdi( $ementash_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></h3>
			<div class="em-pcard__foot">
				<span class="em-pcard__more" aria-hidden="true">
					<?php esc_html_e( 'مشاهده محصول', 'ementash' ); ?>
					<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<a class="em-pcard__quote" href="<?php echo esc_url( ementash_product_inquiry_url( $product->get_id() ) ); ?>">
					<?php esc_html_e( 'استعلام قیمت', 'ementash' ); ?><span class="screen-reader-text"> <?php echo esc_html( $ementash_name ); ?></span>
				</a>
			</div>
		</div>
	</div>
</li>
