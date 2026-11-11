<?php
/**
 * درباره ما: ارزش‌ها؛ تیتر، متن اختیاری (مثلاً مأموریت) و فهرست شماره‌دار (ردیف‌های متاباکس). آیکون ردیف‌ها اینجا استفاده نمی‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_page  = get_the_ID();
$ementash_items = (array) ementash_meta( $ementash_page, 'about_values_items' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_meta( $ementash_page, 'about_values_title' );
// پاراگراف‌ها با خط خالی از هم جدا می‌شوند.
$ementash_paras = array_filter( array_map( 'trim', preg_split( '/\R\s*\R/u', (string) ementash_meta( $ementash_page, 'about_values_text' ) ) ), 'strlen' );
?>
<section class="em-section em-vals-sec"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-values-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<h2 class="em-h2 em-vals__h2" id="em-values-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php endif; ?>
		<?php if ( $ementash_paras ) : ?>
			<div class="em-vals__intro">
				<?php foreach ( $ementash_paras as $ementash_para ) : ?>
					<p><?php echo ementash_bdi( $ementash_para ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<ol class="em-vals">
			<?php foreach ( array_values( $ementash_items ) as $ementash_n => $ementash_item ) : ?>
				<li class="em-val">
					<span class="em-val__num" aria-hidden="true"><?php echo ementash_bdi( sprintf( '%02d', $ementash_n + 1 ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3 class="em-val__title"><?php echo ementash_bdi( isset( $ementash_item['title'] ) ? $ementash_item['title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<?php if ( ! empty( $ementash_item['text'] ) ) : ?>
						<p class="em-val__text"><?php echo ementash_bdi( $ementash_item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
