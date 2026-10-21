<?php
/**
 * نوار ثابت پایین در موبایل: تماس + استعلام قیمت.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_phone = imentash_main_phone();
?>
<div class="em-mbar">
	<?php if ( $imentash_phone ) : ?>
		<a href="<?php echo esc_url( imentash_tel_href( $imentash_phone ), array( 'tel' ) ); ?>" class="em-mbar__call">
			<?php echo imentash_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'تماس', 'imentash' ); ?>
		</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( imentash_contact_url() ); ?>" class="em-mbar__quote"><?php echo esc_html( imentash_get( 'quote_label' ) ); ?></a>
</div>
