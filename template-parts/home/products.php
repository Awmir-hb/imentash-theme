<?php
/**
 * صفحه اصلی: محصولات. دسته‌های سطح اول ووکامرس به‌صورت تب (با کلیک، نه هاور)
 * و پنل تصویری دسته انتخاب‌شده (تصویر، توضیح و SKU محصولات همان دسته).
 *
 * بدون JS پنل اول نمایش داده می‌شود؛ تعویض تب در assets/js/home.js.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

if ( ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$ementash_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'orderby'    => 'menu_order',
		'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
	)
);
if ( is_wp_error( $ementash_terms ) || ! $ementash_terms ) {
	return;
}

$ementash_title = (string) ementash_get( 'products_title' );
$ementash_label = (string) ementash_get( 'products_link_label' );
$ementash_total = (int) wp_count_posts( 'product' )->publish;

$ementash_cats = array();
foreach ( $ementash_terms as $ementash_term ) {
	// تعداد ووکامرس زیر‌دسته‌ها را هم حساب می‌کند.
	$ementash_count = get_term_meta( $ementash_term->term_id, 'product_count_product_cat', true );
	$ementash_count = '' !== $ementash_count ? (int) $ementash_count : (int) $ementash_term->count;

	$ementash_codes = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$ementash_products = wc_get_products(
			array(
				'status'   => 'publish',
				'category' => array( $ementash_term->slug ),
				'limit'    => 6,
				'orderby'  => 'menu_order',
				'order'    => 'ASC',
			)
		);
		foreach ( $ementash_products as $ementash_product ) {
			if ( '' !== $ementash_product->get_sku() ) {
				$ementash_codes[] = $ementash_product->get_sku();
			}
		}
	}

	$ementash_cats[] = array(
		'id'    => $ementash_term->term_id,
		'name'  => $ementash_term->name,
		'url'   => get_term_link( $ementash_term ),
		'desc'  => wp_trim_words( wp_strip_all_tags( $ementash_term->description ), 30 ),
		/* translators: %s: تعداد محصولات */
		'count' => sprintf( _n( '%s محصول', '%s محصول', $ementash_count, 'ementash' ), ementash_fa_num( $ementash_count ) ),
		'image' => absint( get_term_meta( $ementash_term->term_id, 'thumbnail_id', true ) ),
		'codes' => $ementash_codes,
	);
}
?>
<section class="em-section em-section--soft em-products"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-products-title"' : ''; ?>>
	<div class="em-container">
		<div class="em-head">
			<?php if ( '' !== $ementash_title ) : ?>
				<h2 class="em-h2" id="em-products-title"><?php echo esc_html( $ementash_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $ementash_label ) : ?>
				<a class="em-link em-products__all" href="<?php echo esc_url( ementash_shop_url() ); ?>"><?php echo esc_html( str_replace( '{count}', ementash_fa_num( $ementash_total ), $ementash_label ) ); ?></a>
			<?php endif; ?>
		</div>

		<div class="em-prod" data-em-tabs>
			<div class="em-pcats" role="tablist" aria-orientation="vertical"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-products-title"' : ''; ?>>
				<?php foreach ( $ementash_cats as $ementash_i => $ementash_cat ) : ?>
					<button type="button" class="em-pcat" role="tab" id="em-pcat-<?php echo esc_attr( $ementash_cat['id'] ); ?>" aria-controls="em-ppanel-<?php echo esc_attr( $ementash_cat['id'] ); ?>" aria-selected="<?php echo 0 === $ementash_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $ementash_i ? '0' : '-1'; ?>">
						<span class="em-pcat__bar" aria-hidden="true"></span>
						<span class="em-pcat__thumb" aria-hidden="true">
							<?php
							echo $ementash_cat['image']
								? wp_get_attachment_image( $ementash_cat['image'], 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) )
								: '<span class="em-ph"></span>';
							?>
						</span>
						<span class="em-pcat__name"><?php echo esc_html( $ementash_cat['name'] ); ?></span>
						<span class="em-pcat__count"><?php echo esc_html( $ementash_cat['count'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="em-ppanels">
				<?php foreach ( $ementash_cats as $ementash_i => $ementash_cat ) : ?>
					<div class="em-ppanel" role="tabpanel" id="em-ppanel-<?php echo esc_attr( $ementash_cat['id'] ); ?>" aria-labelledby="em-pcat-<?php echo esc_attr( $ementash_cat['id'] ); ?>"<?php echo 0 === $ementash_i ? '' : ' hidden'; ?>>
						<a class="em-card em-ppanel__card" href="<?php echo esc_url( $ementash_cat['url'] ); ?>">
							<span class="em-card__img em-ppanel__img">
								<?php
								echo $ementash_cat['image']
									? wp_get_attachment_image(
										$ementash_cat['image'],
										'large',
										false,
										array(
											'alt'     => '',
											'loading' => 'lazy',
											'sizes'   => '(max-width: 900px) 100vw, 760px',
										)
									)
									: '<span class="em-ph"></span>';
								?>
							</span>
							<span class="em-ppanel__shade" aria-hidden="true"></span>
							<span class="em-ppanel__body">
								<span class="em-ppanel__count"><?php echo esc_html( $ementash_cat['count'] ); ?></span>
								<span class="em-ppanel__title"><?php echo esc_html( $ementash_cat['name'] ); ?></span>
								<?php if ( '' !== $ementash_cat['desc'] ) : ?>
									<span class="em-ppanel__desc"><?php echo esc_html( $ementash_cat['desc'] ); ?></span>
								<?php endif; ?>
								<?php if ( $ementash_cat['codes'] ) : ?>
									<span class="em-ppanel__codes" dir="ltr">
										<?php foreach ( $ementash_cat['codes'] as $ementash_code ) : ?>
											<span><?php echo esc_html( $ementash_code ); ?></span>
										<?php endforeach; ?>
									</span>
								<?php endif; ?>
								<span class="em-btn em-btn--light em-ppanel__btn">
									<?php esc_html_e( 'مشاهده محصولات این دسته', 'ementash' ); ?>
									<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
