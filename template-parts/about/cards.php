<?php
/**
 * درباره ما: کارت‌های آیکون‌دار از متاباکس برگه.
 * $args['group']: rd (تحقیق و توسعه: تیتر، متن، کارت عنوان‌دار) یا values (مشتری‌مداری: کارت عنوان و متن).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_group = isset( $args['group'] ) && 'values' === $args['group'] ? 'values' : 'rd';
$ementash_page  = get_the_ID();
$ementash_items = (array) ementash_meta( $ementash_page, 'about_' . $ementash_group . '_items' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_meta( $ementash_page, 'about_' . $ementash_group . '_title' );
$ementash_text  = 'rd' === $ementash_group ? (string) ementash_meta( $ementash_page, 'about_rd_text' ) : '';
$ementash_id    = 'em-' . $ementash_group . '-title';
?>
<section class="em-section em-acards em-acards--<?php echo esc_attr( $ementash_group ); ?>"<?php echo '' !== $ementash_title ? ' aria-labelledby="' . esc_attr( $ementash_id ) . '"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<h2 class="em-h2 em-acards__h2" id="<?php echo esc_attr( $ementash_id ); ?>"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $ementash_text ) : ?>
			<p class="em-acards__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<ul class="em-acards__list">
			<?php foreach ( $ementash_items as $ementash_item ) : ?>
				<li class="em-acard">
					<?php if ( ! empty( $ementash_item['icon'] ) ) : ?>
						<span class="em-acard__icon"><?php echo ementash_icon( (string) $ementash_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<h3 class="em-acard__title"><?php echo ementash_bdi( isset( $ementash_item['title'] ) ? $ementash_item['title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<?php if ( ! empty( $ementash_item['text'] ) ) : ?>
						<p class="em-acard__text"><?php echo ementash_bdi( $ementash_item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
