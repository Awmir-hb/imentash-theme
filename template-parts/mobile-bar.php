<?php
/**
 * نوار ثابت پایین در موبایل: تماس + استعلام قیمت.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_phone = ementash_main_phone();
// در صفحه محصول، استعلام همان محصول (فرم تماس نام محصول را پر می‌کند).
$ementash_quote = is_singular( 'product' ) && function_exists( 'ementash_product_inquiry_url' )
	? ementash_product_inquiry_url( get_queried_object_id() )
	: ementash_contact_url();
?>
<div class="em-mbar">
	<?php if ( $ementash_phone ) : ?>
		<a href="<?php echo esc_url( ementash_tel_href( $ementash_phone ), array( 'tel' ) ); ?>" class="em-mbar__call">
			<?php echo ementash_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'تماس', 'ementash' ); ?>
		</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( $ementash_quote ); ?>" class="em-mbar__quote"><?php echo ementash_bdi( ementash_get( 'quote_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
</div>
