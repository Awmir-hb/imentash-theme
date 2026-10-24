<?php
/**
 * نوار ثابت پایین در موبایل: تماس + استعلام قیمت.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_phone = ementash_main_phone();
?>
<div class="em-mbar">
	<?php if ( $ementash_phone ) : ?>
		<a href="<?php echo esc_url( ementash_tel_href( $ementash_phone ), array( 'tel' ) ); ?>" class="em-mbar__call">
			<?php echo ementash_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'تماس', 'ementash' ); ?>
		</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( ementash_contact_url() ); ?>" class="em-mbar__quote"><?php echo ementash_bdi( ementash_get( 'quote_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
</div>
