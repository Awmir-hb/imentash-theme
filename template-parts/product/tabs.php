<?php
/**
 * صفحه محصول: تب‌ها. معرفی = توضیحات محصول؛ مشخصات فنی = SKU و ویژگی‌های قابل نمایش (شامل کلاس آتش)؛
 * کاربردها و بسته‌بندی = دو متاباکس wp_editor. تب بدون محتوا نمایش داده نمی‌شود.
 * بدون JS همه پنل‌ها زیر هم دیده می‌شوند؛ تعویض تب در assets/js/shop.js.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * HTML ویرایشگر (توضیحات و متاباکس‌ها) بدون فیلترهای the_content افزونه‌ها.
 */
$ementash_html = static function ( $content ) {
	$content = trim( (string) $content );
	return '' === $content ? '' : ementash_bdi_html( wp_kses_post( wpautop( do_shortcode( $content ) ) ) );
};

// ردیف‌های جدول مشخصات.
$ementash_rows = array();
if ( '' !== $product->get_sku() ) {
	$ementash_rows[] = array( __( 'کد محصول', 'ementash' ), ementash_code( $product->get_sku() ) );
}
foreach ( $product->get_attributes() as $ementash_attr ) {
	if ( ! $ementash_attr->get_visible() ) {
		continue;
	}
	$ementash_values = $ementash_attr->is_taxonomy()
		? wc_get_product_terms( $product->get_id(), $ementash_attr->get_name(), array( 'fields' => 'names' ) )
		: $ementash_attr->get_options();
	if ( ! $ementash_values ) {
		continue;
	}
	$ementash_rows[] = array(
		wc_attribute_label( $ementash_attr->get_name(), $product ),
		implode( esc_html__( '، ', 'ementash' ), array_map( 'ementash_bdi', $ementash_values ) ),
	);
}

$ementash_tabs = array(
	'intro' => array(
		'label' => __( 'معرفی', 'ementash' ),
		'html'  => $ementash_html( $product->get_description() ),
	),
	'spec'  => array(
		'label' => __( 'مشخصات فنی', 'ementash' ),
		'html'  => '',
	),
	'use'   => array(
		'label' => __( 'کاربردها', 'ementash' ),
		'html'  => $ementash_html( ementash_meta( $product->get_id(), 'applications' ) ),
	),
	'pack'  => array(
		'label' => __( 'بسته‌بندی', 'ementash' ),
		'html'  => $ementash_html( ementash_meta( $product->get_id(), 'packaging' ) ),
	),
);
if ( $ementash_rows ) {
	ob_start();
	?>
	<div class="em-spec">
		<table>
			<caption class="screen-reader-text"><?php esc_html_e( 'مشخصات فنی', 'ementash' ); ?></caption>
			<tbody>
				<?php foreach ( $ementash_rows as $ementash_row ) : ?>
					<tr>
						<th scope="row"><?php echo ementash_bdi( $ementash_row[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td><?php echo $ementash_row[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- مقدارها بالا escape شده‌اند. ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	$ementash_tabs['spec']['html'] = (string) ob_get_clean();
}

$ementash_tabs = array_filter(
	$ementash_tabs,
	static function ( $tab ) {
		return '' !== $tab['html'];
	}
);
if ( ! $ementash_tabs ) {
	return;
}
$ementash_first = array_key_first( $ementash_tabs );
?>
<section class="em-ptabs" aria-label="<?php esc_attr_e( 'اطلاعات محصول', 'ementash' ); ?>" data-em-tabs>
	<div class="em-container">
		<div class="em-ptabs__list" role="tablist" aria-label="<?php esc_attr_e( 'اطلاعات محصول', 'ementash' ); ?>" hidden data-em-tablist>
			<?php foreach ( $ementash_tabs as $ementash_id => $ementash_tab ) : ?>
				<button type="button" class="em-ptab" role="tab" id="em-ptab-<?php echo esc_attr( $ementash_id ); ?>" aria-controls="em-ppane-<?php echo esc_attr( $ementash_id ); ?>" aria-selected="<?php echo $ementash_first === $ementash_id ? 'true' : 'false'; ?>" tabindex="<?php echo $ementash_first === $ementash_id ? '0' : '-1'; ?>"><?php echo esc_html( $ementash_tab['label'] ); ?></button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $ementash_tabs as $ementash_id => $ementash_tab ) : ?>
			<div class="em-ppane em-ppane--<?php echo esc_attr( $ementash_id ); ?>" role="tabpanel" id="em-ppane-<?php echo esc_attr( $ementash_id ); ?>" aria-labelledby="em-ptab-<?php echo esc_attr( $ementash_id ); ?>" tabindex="0">
				<?php // بدون JS عنوان هر بخش دیده می‌شود؛ با JS فقط برای صفحه‌خوان. ?>
				<h2 class="em-ppane__title"><?php echo esc_html( $ementash_tab['label'] ); ?></h2>
				<?php echo $ementash_tab['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- بالا با wp_kses_post یا escape ساخته شده. ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
