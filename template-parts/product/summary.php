<?php
/**
 * صفحه محصول: مسیر صفحه، گالری (عکس اصلی + بندانگشتی‌ها) و اطلاعات
 * (دسته، SKU، نام، توضیح کوتاه، کلاس‌های آتش، دکمه استعلام، نوار ویژگی‌ها). بدون قیمت و دکمه خرید.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

$ementash_name    = $product->get_name();
$ementash_cat     = ementash_product_main_cat( $product->get_id() );
$ementash_sku     = $product->get_sku();
$ementash_images  = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
$ementash_classes = ementash_wc_fire_classes( $product->get_id() );
$ementash_label   = (string) ementash_get( 'shop_inquiry_label' );
$ementash_badges  = ementash_get_lines( 'shop_badges' );

/**
 * alt عکس محصول: متن جایگزین کتابخانه رسانه، وگرنه نام محصول.
 */
$ementash_alt = static function ( $id ) use ( $ementash_name ) {
	$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	return '' !== $alt ? $alt : $ementash_name;
};
?>
<section class="em-section em-product">
	<div class="em-container">
		<nav class="em-crumbs em-crumbs--light" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'ementash' ); ?>">
			<?php foreach ( ementash_shop_crumbs() as $ementash_i => $ementash_crumb ) : ?>
				<?php if ( $ementash_i > 0 ) : ?>
					<span class="em-crumbs__sep" aria-hidden="true">/</span>
				<?php endif; ?>
				<?php if ( '' !== $ementash_crumb['url'] ) : ?>
					<a href="<?php echo esc_url( $ementash_crumb['url'] ); ?>"><?php echo ementash_bdi( $ementash_crumb['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php else : ?>
					<span aria-current="page"><?php echo ementash_bdi( $ementash_crumb['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<div class="em-pd">
			<div class="em-pd__gallery" data-em-gallery>
				<div class="em-pd__main">
					<?php
					if ( $ementash_images ) {
						// تصویر اصلی بالای صفحه است؛ lazy نیست.
						echo wp_get_attachment_image(
							$ementash_images[0],
							'ementash-square',
							false,
							array(
								'alt'           => $ementash_alt( $ementash_images[0] ),
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'sizes'         => '(max-width: 900px) 100vw, 620px',
								'data-em-main'  => '',
							)
						);
					} else {
						echo '<span class="em-ph" aria-hidden="true"></span>';
					}
					?>
				</div>

				<?php if ( count( $ementash_images ) > 1 ) : ?>
					<ul class="em-pd__thumbs">
						<?php
						foreach ( $ementash_images as $ementash_n => $ementash_id ) :
							$ementash_full = wp_get_attachment_image_src( $ementash_id, 'ementash-square' );
							?>
							<li>
								<button type="button" class="em-pd__thumb" aria-pressed="<?php echo 0 === $ementash_n ? 'true' : 'false'; ?>"
									data-em-src="<?php echo esc_url( $ementash_full ? $ementash_full[0] : '' ); ?>"
									data-em-srcset="<?php echo esc_attr( (string) wp_get_attachment_image_srcset( $ementash_id, 'ementash-square' ) ); ?>"
									data-em-alt="<?php echo esc_attr( $ementash_alt( $ementash_id ) ); ?>"
									aria-label="<?php /* translators: %s: شماره تصویر */ echo esc_attr( sprintf( __( 'نمایش تصویر %s', 'ementash' ), ementash_fa_num( $ementash_n + 1 ) ) ); ?>">
									<?php echo wp_get_attachment_image( $ementash_id, 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="em-pd__info">
				<?php if ( $ementash_cat || '' !== $ementash_sku ) : ?>
					<div class="em-pd__meta">
						<?php if ( $ementash_cat ) : ?>
							<a class="em-pd__cat" href="<?php echo esc_url( get_term_link( $ementash_cat ) ); ?>"><?php echo ementash_bdi( $ementash_cat->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php endif; ?>
						<?php if ( '' !== $ementash_sku ) : ?>
							<span class="em-pd__sku"><?php echo ementash_code( $ementash_sku ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<h1 class="em-pd__title"><?php echo ementash_bdi( $ementash_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>

				<?php if ( $product->get_short_description() ) : ?>
					<div class="em-pd__lead"><?php echo ementash_bdi_html( wp_kses_post( wpautop( $product->get_short_description() ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post؛ فقط bdi اضافه می‌شود. ?></div>
				<?php endif; ?>

				<?php if ( $ementash_classes ) : ?>
					<p class="em-pd__label" id="em-fire-label"><?php esc_html_e( 'مناسب کلاس‌های آتش', 'ementash' ); ?></p>
					<ul class="em-fire" aria-labelledby="em-fire-label">
						<?php foreach ( $ementash_classes as $ementash_class ) : ?>
							<li class="em-fire__item">
								<b class="em-fire__code"><?php echo ementash_bdi( $ementash_class['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></b>
								<?php if ( '' !== $ementash_class['label'] ) : ?>
									<span><?php echo ementash_bdi( $ementash_class['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== $ementash_label ) : ?>
					<div class="em-pd__actions">
						<a class="em-btn em-btn--brand" href="<?php echo esc_url( ementash_product_inquiry_url( $product->get_id() ) ); ?>"><?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					</div>
				<?php endif; ?>

				<?php if ( $ementash_badges ) : ?>
					<ul class="em-pd__badges">
						<?php foreach ( $ementash_badges as $ementash_badge ) : ?>
							<li><?php echo ementash_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo ementash_bdi( $ementash_badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
